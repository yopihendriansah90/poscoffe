import './bootstrap';

const formatCurrency = (value) => new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    maximumFractionDigits: 0,
}).format(Number(value));

const setupLuminaPos = () => {
    const root = document.querySelector('[data-pos-root]');

    if (!root) {
        return;
    }

    const productCards = [...root.querySelectorAll('[data-product-card]')];
    const categoryButtons = [...root.querySelectorAll('[data-category-filter]')];
    const searchInput = root.querySelector('[data-product-search]');
    const cartList = root.querySelector('[data-cart-list]');
    const subtotalNode = root.querySelector('[data-subtotal]');
    const taxNode = root.querySelector('[data-tax]');
    const discountNode = root.querySelector('[data-discount-label]');
    const totalNode = root.querySelector('[data-total]');
    const cartOpenButtons = [...root.querySelectorAll('[data-cart-open]')];
    const cartCloseButtons = [...root.querySelectorAll('[data-cart-close]')];
    const productTabButton = root.querySelector('[data-product-tab]');
    const sidebar = root.querySelector('.lumina-sidebar');
    const mobileNav = root.querySelector('.lumina-mobile-nav');
    const cartBadge = root.querySelector('[data-cart-badge]');
    const mobileCartCount = root.querySelector('[data-mobile-cart-count]');
    const taxRate = Number(root.dataset.taxRate || 0.1);
    const discount = Number(root.dataset.discount || 0);
    let activeCategory = 'all';
    let cart = [];

    const productMap = new Map(
        JSON.parse(root.dataset.products || '[]').map((product) => [product.id, product]),
    );

    const initialCart = JSON.parse(root.dataset.initialCart || '[]');
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    const productFromCard = (card) => ({
        id: card.dataset.id,
        name: card.dataset.name,
        category: card.dataset.category,
        slug: card.dataset.slug,
        type: card.dataset.type,
        discount: card.dataset.discount === 'true',
        price: Number(card.dataset.price),
        image: card.dataset.image,
    });

    const addProduct = (product, qty = 1) => {
        const existing = cart.find((item) => item.id === product.id);

        if (existing) {
            existing.qty += qty;
        } else {
            cart.push({ ...product, qty });
        }

        renderCart();
    };

    const getFlyTargetRect = () => {
        const isMobile = window.matchMedia('(max-width: 700px)').matches;
        const target = isMobile && !root.classList.contains('is-cart-open')
            ? cartOpenButtons[0]
            : cartList;

        return target?.getBoundingClientRect();
    };

    const animateProductToCart = (card) => {
        if (prefersReducedMotion || !card?.isConnected) {
            return;
        }

        const image = card.querySelector('img');
        const sourceRect = image?.getBoundingClientRect();
        const targetRect = getFlyTargetRect();

        if (!image || !sourceRect || !targetRect) {
            return;
        }

        const clone = image.cloneNode();
        clone.className = 'lumina-fly-to-cart';
        clone.setAttribute('aria-hidden', 'true');
        clone.style.left = `${sourceRect.left}px`;
        clone.style.top = `${sourceRect.top}px`;
        clone.style.width = `${sourceRect.width}px`;
        clone.style.height = `${sourceRect.height}px`;
        document.body.appendChild(clone);

        const targetX = targetRect.left + targetRect.width / 2 - (sourceRect.left + sourceRect.width / 2);
        const targetY = targetRect.top + targetRect.height / 2 - (sourceRect.top + sourceRect.height / 2);

        clone.animate(
            [
                {
                    opacity: 0.96,
                    transform: 'translate3d(0, 0, 0) scale(1)',
                },
                {
                    opacity: 0.82,
                    transform: `translate3d(${targetX * 0.62}px, ${targetY * 0.34 - 28}px, 0) scale(0.62)`,
                },
                {
                    opacity: 0,
                    transform: `translate3d(${targetX}px, ${targetY}px, 0) scale(0.2)`,
                },
            ],
            {
                duration: 560,
                easing: 'cubic-bezier(.2,.8,.2,1)',
            },
        ).finished.finally(() => clone.remove());
    };

    const changeQty = (id, delta) => {
        cart = cart
            .map((item) => (item.id === id ? { ...item, qty: item.qty + delta } : item))
            .filter((item) => item.qty > 0);

        renderCart();
    };

    const setCartOpen = (isOpen) => {
        root.classList.toggle('is-cart-open', isOpen);
        productTabButton?.classList.toggle('is-active', !isOpen);
        cartOpenButtons.forEach((button) => button.classList.toggle('is-active', isOpen));
    };

    const renderCart = () => {
        if (!cart.length) {
            cartList.innerHTML = `
                <div class="lumina-cart-empty">
                    <strong>Keranjang masih kosong</strong><br>
                    Pilih menu dari daftar produk untuk mulai membuat pesanan.
                </div>
            `;
        } else {
            cartList.innerHTML = cart
                .map(
                    (item) => `
                        <article class="lumina-cart-item">
                            <img src="${item.image}" alt="${item.name}">
                            <div>
                                <h4>${item.name}</h4>
                                <div class="lumina-cart-price">${formatCurrency(item.price)}</div>
                                <button class="lumina-note" type="button">
                                    <span class="material-symbols-outlined">edit</span>
                                    <span>Catatan</span>
                                </button>
                            </div>
                            <div class="lumina-qty">
                                <button type="button" data-cart-decrease="${item.id}" aria-label="Kurangi ${item.name}">
                                    <span class="material-symbols-outlined">remove</span>
                                </button>
                                <strong>${item.qty}</strong>
                                <button type="button" data-cart-increase="${item.id}" aria-label="Tambah ${item.name}">
                                    <span class="material-symbols-outlined">add</span>
                                </button>
                            </div>
                        </article>
                    `,
                )
                .join('');
        }

        const subtotal = cart.reduce((sum, item) => sum + item.price * item.qty, 0);
        const tax = subtotal * taxRate;
        const appliedDiscount = subtotal > 0 ? discount : 0;
        const total = Math.max(0, subtotal + tax - appliedDiscount);
        const itemCount = cart.reduce((sum, item) => sum + item.qty, 0);

        subtotalNode.textContent = formatCurrency(subtotal);
        taxNode.textContent = formatCurrency(tax);
        discountNode.textContent = `-${formatCurrency(appliedDiscount)}`;
        totalNode.textContent = formatCurrency(total);

        if (cartBadge) {
            cartBadge.textContent = itemCount;
            cartBadge.hidden = itemCount === 0;
        }

        if (mobileCartCount) {
            mobileCartCount.textContent = `${itemCount} item`;
        }
    };

    const filterProducts = () => {
        const query = searchInput.value.trim().toLowerCase();

        productCards.forEach((card) => {
            const matchesSearch = card.dataset.name.toLowerCase().includes(query)
                || card.dataset.category.toLowerCase().includes(query)
                || card.dataset.type.toLowerCase().includes(query);
            const matchesCategory = activeCategory === 'all'
                || card.dataset.slug === activeCategory
                || card.dataset.type === activeCategory
                || (activeCategory === 'discount' && card.dataset.discount === 'true');

            card.classList.toggle('hidden-by-search', !matchesSearch);
            card.classList.toggle('hidden-by-category', !matchesCategory);
        });
    };

    productCards.forEach((card) => {
        const addFromCard = () => {
            animateProductToCart(card);
            addProduct(productFromCard(card));
        };

        card.addEventListener('click', addFromCard);
        card.addEventListener('keydown', (event) => {
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                addFromCard();
            }
        });
    });

    categoryButtons.forEach((button) => {
        button.addEventListener('click', () => {
            activeCategory = button.dataset.categoryFilter;
            categoryButtons.forEach((item) => item.classList.toggle('is-active', item === button));
            filterProducts();
        });
    });

    searchInput.addEventListener('input', filterProducts);

    cartOpenButtons.forEach((button) => {
        button.addEventListener('click', () => setCartOpen(true));
    });

    cartCloseButtons.forEach((button) => {
        button.addEventListener('click', () => setCartOpen(false));
    });

    productTabButton?.addEventListener('click', () => setCartOpen(false));

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            setCartOpen(false);
        }
    });

    document.addEventListener('pointerdown', (event) => {
        if (!root.classList.contains('is-cart-open')) {
            return;
        }

        if (sidebar?.contains(event.target) || mobileNav?.contains(event.target)) {
            return;
        }

        setCartOpen(false);
    });

    cartList.addEventListener('click', (event) => {
        const increase = event.target.closest('[data-cart-increase]');
        const decrease = event.target.closest('[data-cart-decrease]');

        if (increase) {
            changeQty(increase.dataset.cartIncrease, 1);
        }

        if (decrease) {
            changeQty(decrease.dataset.cartDecrease, -1);
        }
    });

    initialCart.forEach((entry) => {
        const product = entry.name ? entry : productMap.get(entry.id);

        if (product) {
            addProduct({ ...product, price: Number(product.price) }, Number(entry.qty || 1));
        }
    });

    if (!initialCart.length) {
        renderCart();
    }
};

document.addEventListener('DOMContentLoaded', setupLuminaPos);
