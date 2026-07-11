import './bootstrap';

const formatCurrency = (value) => new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    maximumFractionDigits: 0,
}).format(Number(value));

const formatNumber = (value) => new Intl.NumberFormat('id-ID', {
    maximumFractionDigits: 0,
}).format(Number(value || 0));

const escapeHtml = (value) => String(value)
    .replaceAll('&', '&amp;')
    .replaceAll('<', '&lt;')
    .replaceAll('>', '&gt;')
    .replaceAll('"', '&quot;')
    .replaceAll("'", '&#039;');

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
    const currentDateNode = root.querySelector('[data-current-date]');
    const currentTimeNode = root.querySelector('[data-current-time]');
    const historyDateNode = root.querySelector('[data-history-date]');
    const toastStack = root.querySelector('[data-toast-stack]');
    const cartOpenButtons = [...root.querySelectorAll('[data-cart-open]')];
    const cartCloseButtons = [...root.querySelectorAll('[data-cart-close]')];
    const productTabButton = root.querySelector('[data-product-tab]');
    const sidebar = root.querySelector('.lumina-sidebar');
    const mobileNav = root.querySelector('.lumina-mobile-nav');
    const menuPanel = root.querySelector('.lumina-menu-panel');
    const historyPanel = root.querySelector('.lumina-history-panel');
    const checkoutModal = root.querySelector('.lumina-checkout-modal');
    const menuOpenButtons = [...root.querySelectorAll('[data-menu-open]')];
    const menuCloseButtons = [...root.querySelectorAll('[data-menu-close]')];
    const historyOpenButtons = [...root.querySelectorAll('[data-history-open]')];
    const historyCloseButtons = [...root.querySelectorAll('[data-history-close]')];
    const checkoutOpenButton = root.querySelector('[data-checkout-open]');
    const checkoutCloseButtons = [...root.querySelectorAll('[data-checkout-close]')];
    const checkoutSecondaryButton = root.querySelector('[data-checkout-secondary]');
    const checkoutPrimaryButton = root.querySelector('[data-checkout-primary]');
    const checkoutSuccess = root.querySelector('[data-checkout-success]');
    const checkoutSuccessCode = root.querySelector('[data-checkout-success-code]');
    const checkoutItems = root.querySelector('[data-checkout-items]');
    const checkoutOrderType = root.querySelector('[data-checkout-order-type]');
    const checkoutTable = root.querySelector('[data-checkout-table]');
    const checkoutPayment = root.querySelector('[data-checkout-payment]');
    const checkoutPromo = root.querySelector('[data-checkout-promo]');
    const checkoutSubtotal = root.querySelector('[data-checkout-subtotal]');
    const checkoutTax = root.querySelector('[data-checkout-tax]');
    const checkoutDiscount = root.querySelector('[data-checkout-discount]');
    const checkoutTotal = root.querySelector('[data-checkout-total]');
    const cashPanel = root.querySelector('[data-cash-panel]');
    const cashReceivedInput = root.querySelector('[data-cash-received]');
    const cashChangeNode = root.querySelector('[data-cash-change]');
    const noteModal = root.querySelector('.lumina-note-modal');
    const noteCloseButtons = [...root.querySelectorAll('[data-note-close]')];
    const noteItemName = root.querySelector('[data-note-item-name]');
    const noteInput = root.querySelector('[data-note-input]');
    const noteSuggestionButtons = [...root.querySelectorAll('[data-note-suggestion]')];
    const noteClearButton = root.querySelector('[data-note-clear]');
    const noteSaveButton = root.querySelector('[data-note-save]');
    const historyTransactionsNode = root.querySelector('[data-history-transactions]');
    const historyRevenueNode = root.querySelector('[data-history-revenue]');
    const historyAverageNode = root.querySelector('[data-history-average]');
    const historyList = root.querySelector('[data-history-list]');
    const orderTypeButtons = [...root.querySelectorAll('[data-order-type]')];
    const tableField = root.querySelector('[data-table-field]');
    const tableTrigger = root.querySelector('[data-table-trigger]');
    const tableCurrent = root.querySelector('[data-table-current]');
    const tableOptionButtons = [...root.querySelectorAll('[data-table-option]')];
    const promoPicker = root.querySelector('[data-promo-picker]');
    const promoTrigger = root.querySelector('[data-promo-trigger]');
    const promoLabel = root.querySelector('[data-promo-label]');
    const promoOptionButtons = [...root.querySelectorAll('[data-promo-option]')];
    const paymentPicker = root.querySelector('[data-payment-picker]');
    const paymentTrigger = root.querySelector('[data-payment-trigger]');
    const paymentLabel = root.querySelector('[data-payment-label]');
    const paymentOptionButtons = [...root.querySelectorAll('[data-payment-option]')];
    const cartBadge = root.querySelector('[data-cart-badge]');
    const mobileCartCount = root.querySelector('[data-mobile-cart-count]');
    const taxRate = Number(root.dataset.taxRate || 0.1);
    const orderStoreUrl = root.dataset.orderStoreUrl;
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const serverNowMs = Number(root.dataset.serverNowMs || Date.now());
    const serverTimeZone = root.dataset.serverTimezone || 'Asia/Jakarta';
    const serverTimeZoneLabel = root.dataset.serverTimezoneLabel || 'WIB';
    const clientStartMs = Date.now();
    let activeCategory = 'all';
    let orderType = 'dine-in';
    let selectedTable = tableCurrent?.dataset.tableValue || tableOptionButtons[0]?.dataset.tableValue || '1';
    let selectedTableLabel = tableCurrent?.textContent?.trim() || tableOptionButtons[0]?.dataset.tableLabel || 'Meja 01';
    let selectedPromo = 'member';
    let selectedPayment = 'qris';
    let cart = [];
    let suppressProductClick = false;
    let checkoutCompleted = false;
    let lastTransactionCode = '';
    let activeNoteItemId = null;
    let cashReceived = 0;
    let lastSummary = {
        subtotal: 0,
        tax: 0,
        discount: 0,
        total: 0,
        itemCount: 0,
    };
    let historyStats = {
        transactions: Number(historyTransactionsNode?.textContent?.replace(/\D/g, '') || 0),
        revenue: Number(historyRevenueNode?.textContent?.replace(/\D/g, '') || 0),
    };
    let toastSequence = 0;

    const productMap = new Map(
        JSON.parse(root.dataset.products || '[]').map((product) => [product.id, product]),
    );

    const initialCart = JSON.parse(root.dataset.initialCart || '[]');
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    const formatServerDate = (date) => new Intl.DateTimeFormat('id-ID', {
        weekday: 'short',
        day: '2-digit',
        month: 'short',
        year: 'numeric',
        timeZone: serverTimeZone,
    }).format(date).replace(/\./g, '');

    const formatServerTime = (date) => `${new Intl.DateTimeFormat('id-ID', {
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        hour12: false,
        timeZone: serverTimeZone,
    }).format(date).replace(/\./g, ':')} ${serverTimeZoneLabel}`;

    const updateServerClock = () => {
        const serverDate = new Date(serverNowMs + (Date.now() - clientStartMs));
        const dateLabel = formatServerDate(serverDate);

        if (currentDateNode) {
            currentDateNode.textContent = dateLabel;
        }

        if (historyDateNode) {
            historyDateNode.textContent = dateLabel;
        }

        if (currentTimeNode) {
            currentTimeNode.textContent = formatServerTime(serverDate);
        }
    };

    const setCashReceivedValue = (value) => {
        cashReceived = Math.max(0, Number(value || 0));

        if (cashReceivedInput) {
            cashReceivedInput.value = cashReceived > 0 ? formatNumber(cashReceived) : '';
        }
    };

    const toastIcons = {
        success: 'check_circle',
        info: 'info',
        warning: 'warning',
        danger: 'error',
    };

    const showToast = ({ type = 'info', title, message, duration = 3000 }) => {
        if (!toastStack || !title) {
            return;
        }

        const toastId = `lumina-toast-${toastSequence += 1}`;
        const toast = document.createElement('article');
        toast.className = 'lumina-toast';
        toast.dataset.type = type;
        toast.id = toastId;
        toast.innerHTML = `
            <span class="material-symbols-outlined" aria-hidden="true">${toastIcons[type] || toastIcons.info}</span>
            <div>
                <strong>${escapeHtml(title)}</strong>
                ${message ? `<span>${escapeHtml(message)}</span>` : ''}
            </div>
            <button type="button" aria-label="Tutup notifikasi">
                <span class="material-symbols-outlined" aria-hidden="true">close</span>
            </button>
        `;

        const closeToast = () => {
            if (!toast.isConnected || toast.classList.contains('is-leaving')) {
                return;
            }

            toast.classList.add('is-leaving');
            window.setTimeout(() => toast.remove(), 180);
        };

        toast.querySelector('button')?.addEventListener('click', closeToast);
        toastStack.prepend(toast);

        window.setTimeout(closeToast, duration);

        [...toastStack.children].slice(4).forEach((item) => item.remove());
    };

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

    const addProduct = (product, qty = 1, options = {}) => {
        const existing = cart.find((item) => item.id === product.id);

        if (existing) {
            existing.qty += qty;
        } else {
            cart.push({ ...product, qty });
        }

        renderCart();

        if (options.notify) {
            showToast({
                type: 'success',
                title: 'Produk ditambahkan',
                message: `${product.name} masuk ke keranjang.`,
                duration: 2200,
            });
        }
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

    const setMenuOpen = (isOpen) => {
        root.classList.toggle('is-menu-open', isOpen);
    };

    const setHistoryOpen = (isOpen) => {
        root.classList.toggle('is-history-open', isOpen);
    };

    const setCheckoutOpen = (isOpen) => {
        if (isOpen && !cart.length) {
            return;
        }

        root.classList.toggle('is-checkout-open', isOpen);

        if (isOpen) {
            checkoutCompleted = false;
            renderCheckoutPreview();
            updateCheckoutActions();
        } else if (checkoutSuccess) {
            checkoutSuccess.hidden = true;
        }

        if (!isOpen) {
            checkoutCompleted = false;
            setCashReceivedValue(0);
            updateCheckoutActions();
        }
    };

    const setNoteOpen = (isOpen, itemId = null) => {
        activeNoteItemId = isOpen ? itemId : null;
        root.classList.toggle('is-note-open', isOpen);

        if (!isOpen) {
            return;
        }

        const item = cart.find((entry) => entry.id === itemId);

        if (noteItemName) {
            noteItemName.textContent = item?.name || 'Item keranjang';
        }

        if (noteInput) {
            noteInput.value = item?.note || '';
            window.setTimeout(() => noteInput.focus(), 80);
        }
    };

    const saveItemNote = (note) => {
        if (!activeNoteItemId) {
            return;
        }

        const cleanedNote = note.trim();
        const item = cart.find((entry) => entry.id === activeNoteItemId);

        cart = cart.map((entry) => (
            entry.id === activeNoteItemId
                ? { ...entry, note: cleanedNote }
                : entry
        ));

        renderCart();
        setNoteOpen(false);
        showToast({
            type: cleanedNote ? 'success' : 'info',
            title: cleanedNote ? 'Catatan disimpan' : 'Catatan dihapus',
            message: item?.name,
            duration: 2200,
        });
    };

    const updateCheckoutActions = () => {
        const cashIsInsufficient = selectedPayment === 'cash'
            && root.classList.contains('is-checkout-open')
            && !checkoutCompleted
            && cashReceived < lastSummary.total;

        if (checkoutSecondaryButton) {
            checkoutSecondaryButton.textContent = checkoutCompleted ? 'Lihat Riwayat' : 'Batal';
        }

        if (checkoutPrimaryButton) {
            checkoutPrimaryButton.textContent = checkoutCompleted ? 'Pesanan Baru' : 'Selesaikan Pesanan';
            checkoutPrimaryButton.disabled = !checkoutCompleted && (!cart.length || cashIsInsufficient);
        }
    };

    const setTableDropdownOpen = (isOpen) => {
        if (orderType === 'take-away') {
            isOpen = false;
        }

        tableField?.classList.toggle('is-table-open', isOpen);
        tableTrigger?.setAttribute('aria-expanded', String(isOpen));
    };

    const setSelectedTable = (value) => {
        selectedTable = String(value);
        selectedTableLabel = tableOptionButtons.find((button) => button.dataset.tableValue === selectedTable)?.dataset.tableLabel
            || selectedTableLabel;

        if (orderType === 'dine-in' && tableCurrent) {
            tableCurrent.textContent = selectedTableLabel;
            tableCurrent.dataset.tableValue = selectedTable;
        }

        tableOptionButtons.forEach((button) => {
            button.setAttribute('aria-selected', String(button.dataset.tableValue === selectedTable));
        });
    };

    const getPromoName = (button) => button?.querySelector('strong')?.textContent?.trim() || 'Tanpa Promo';

    const getPaymentName = (button) => button?.querySelector('strong')?.textContent?.trim() || 'QRIS';

    const getActivePromoName = () => {
        const activeButton = promoOptionButtons.find((button) => button.dataset.promoId === selectedPromo);

        return selectedPromo === 'none' ? 'Tanpa Promo' : getPromoName(activeButton);
    };

    const getActivePaymentName = () => {
        const activeButton = paymentOptionButtons.find((button) => button.dataset.paymentId === selectedPayment);

        return getPaymentName(activeButton);
    };

    const setPromoOpen = (isOpen) => {
        promoPicker?.classList.toggle('is-choice-open', isOpen);
        promoTrigger?.setAttribute('aria-expanded', String(isOpen));
    };

    const setPaymentOpen = (isOpen) => {
        paymentPicker?.classList.toggle('is-choice-open', isOpen);
        paymentTrigger?.setAttribute('aria-expanded', String(isOpen));
    };

    const setSelectedPromo = (promoId) => {
        selectedPromo = promoId;
        const activeButton = promoOptionButtons.find((button) => button.dataset.promoId === selectedPromo);

        promoOptionButtons.forEach((button) => {
            button.setAttribute('aria-selected', String(button === activeButton));
        });

        if (promoLabel) {
            promoLabel.textContent = selectedPromo === 'none' ? 'Pilih Promo' : getPromoName(activeButton);
        }

        renderCart();
        showToast({
            type: selectedPromo === 'none' ? 'info' : 'success',
            title: selectedPromo === 'none' ? 'Promo dilepas' : 'Promo diterapkan',
            message: getActivePromoName(),
            duration: 2400,
        });

        if (root.classList.contains('is-checkout-open')) {
            renderCheckoutPreview();
        }
    };

    const setSelectedPayment = (paymentId) => {
        selectedPayment = paymentId;
        const activeButton = paymentOptionButtons.find((button) => button.dataset.paymentId === selectedPayment);

        paymentOptionButtons.forEach((button) => {
            button.setAttribute('aria-selected', String(button === activeButton));
        });

        if (paymentLabel) {
            paymentLabel.textContent = getPaymentName(activeButton);
        }

        if (selectedPayment !== 'cash') {
            setCashReceivedValue(0);
        }

        showToast({
            type: 'info',
            title: 'Metode pembayaran dipilih',
            message: getActivePaymentName(),
            duration: 2200,
        });

        if (root.classList.contains('is-checkout-open')) {
            renderCheckoutPreview();
        }
    };

    const calculateDiscount = (subtotal) => {
        if (subtotal <= 0) {
            return 0;
        }

        const activePromo = promoOptionButtons.find((button) => button.dataset.promoId === selectedPromo);
        const promoType = activePromo?.dataset.promoType || 'fixed';
        const promoValue = Number(activePromo?.dataset.promoValue || root.dataset.discount || 0);

        if (promoType === 'percent') {
            return Math.round(subtotal * (promoValue / 100));
        }

        if (promoType === 'fixed') {
            return Math.min(subtotal, promoValue);
        }

        return 0;
    };

    const getCartSummary = () => {
        const subtotal = cart.reduce((sum, item) => sum + item.price * item.qty, 0);
        const tax = subtotal * taxRate;
        const discount = calculateDiscount(subtotal);

        return {
            subtotal,
            tax,
            discount,
            total: Math.max(0, subtotal + tax - discount),
            itemCount: cart.reduce((sum, item) => sum + item.qty, 0),
        };
    };

    const setOrderType = (type) => {
        orderType = type;
        const isTakeAway = orderType === 'take-away';

        orderTypeButtons.forEach((button) => {
            button.classList.toggle('is-active', button.dataset.orderType === orderType);
        });

        tableField?.classList.toggle('is-take-away', isTakeAway);

        if (tableTrigger) {
            tableTrigger.disabled = isTakeAway;
            tableTrigger.setAttribute('aria-disabled', String(isTakeAway));
        }

        if (tableCurrent) {
            tableCurrent.textContent = isTakeAway ? 'Take Away' : selectedTableLabel;
        }

        setTableDropdownOpen(false);
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
                                <button class="lumina-note" type="button" data-cart-note="${item.id}">
                                    <span class="material-symbols-outlined">edit</span>
                                    <span>${item.note ? 'Edit Catatan' : 'Catatan'}</span>
                                </button>
                                ${item.note ? `<p class="lumina-note-preview">${escapeHtml(item.note)}</p>` : ''}
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

        lastSummary = getCartSummary();

        subtotalNode.textContent = formatCurrency(lastSummary.subtotal);
        taxNode.textContent = formatCurrency(lastSummary.tax);
        discountNode.textContent = `-${formatCurrency(lastSummary.discount)}`;
        totalNode.textContent = formatCurrency(lastSummary.total);

        if (cartBadge) {
            cartBadge.textContent = lastSummary.itemCount;
            cartBadge.hidden = lastSummary.itemCount === 0;
        }

        if (mobileCartCount) {
            mobileCartCount.textContent = `${lastSummary.itemCount} item`;
        }

        if (checkoutOpenButton) {
            checkoutOpenButton.disabled = cart.length === 0;
        }

        updateCheckoutActions();
    };

    const renderCheckoutPreview = () => {
        const summary = getCartSummary();

        if (checkoutOrderType) {
            checkoutOrderType.textContent = orderType === 'take-away' ? 'Take Away' : 'Dine In';
        }

        if (checkoutTable) {
            checkoutTable.textContent = orderType === 'take-away' ? '-' : selectedTableLabel;
        }

        if (checkoutPayment) {
            checkoutPayment.textContent = getActivePaymentName();
        }

        if (checkoutPromo) {
            checkoutPromo.textContent = getActivePromoName();
        }

        if (checkoutItems) {
            checkoutItems.innerHTML = cart.map((item) => `
                <article class="lumina-checkout-item">
                    <img src="${item.image}" alt="${escapeHtml(item.name)}">
                    <div>
                        <strong>${escapeHtml(item.name)}</strong>
                        <span>${item.qty} x ${formatCurrency(item.price)}</span>
                        ${item.note ? `<small>${escapeHtml(item.note)}</small>` : ''}
                    </div>
                    <strong>${formatCurrency(item.price * item.qty)}</strong>
                </article>
            `).join('');
        }

        checkoutSubtotal.textContent = formatCurrency(summary.subtotal);
        checkoutTax.textContent = formatCurrency(summary.tax);
        checkoutDiscount.textContent = `-${formatCurrency(summary.discount)}`;
        checkoutTotal.textContent = formatCurrency(summary.total);

        const isCash = selectedPayment === 'cash';
        if (cashPanel) {
            cashPanel.hidden = !isCash;
        }

        if (isCash && cashChangeNode) {
            const change = Math.max(0, cashReceived - summary.total);
            cashChangeNode.textContent = formatCurrency(change);
        }

        updateCheckoutActions();
    };

    const updateHistoryStats = (transactionTotal) => {
        historyStats = {
            transactions: historyStats.transactions + 1,
            revenue: historyStats.revenue + transactionTotal,
        };

        const average = historyStats.transactions > 0
            ? Math.round(historyStats.revenue / historyStats.transactions)
            : 0;

        historyTransactionsNode.textContent = historyStats.transactions;
        historyRevenueNode.textContent = formatCurrency(historyStats.revenue);
        historyAverageNode.textContent = formatCurrency(average);
    };

    const addTransactionToHistory = (transaction) => {
        historyList?.insertAdjacentHTML('afterbegin', `
            <article class="lumina-history-item">
                <div>
                    <strong>${escapeHtml(transaction.code)}</strong>
                    <span>${escapeHtml(transaction.time)} ${escapeHtml(serverTimeZoneLabel)} - ${escapeHtml(transaction.cashier)}</span>
                </div>
                <div>
                    <span>${escapeHtml(transaction.method)}</span>
                    <strong>${formatCurrency(transaction.total)}</strong>
                </div>
            </article>
        `);

        updateHistoryStats(transaction.total);
    };

    const createOrderPayload = () => ({
        order_type: orderType === 'take-away' ? 'take_away' : 'dine_in',
        dining_table_id: orderType === 'take-away' ? null : selectedTable,
        promo: selectedPromo,
        payment: {
            method: selectedPayment,
            cash_received: selectedPayment === 'cash' ? cashReceived : null,
        },
        items: cart.map((item) => ({
            id: item.id,
            qty: item.qty,
            note: item.note || null,
        })),
    });

    const completeCheckout = async () => {
        if (!cart.length) {
            return;
        }

        const completedSummary = getCartSummary();

        if (selectedPayment === 'cash' && cashReceived < completedSummary.total) {
            showToast({
                type: 'warning',
                title: 'Uang diterima belum cukup',
                message: `Kurang ${formatCurrency(completedSummary.total - cashReceived)}.`,
                duration: 2800,
            });
            cashReceivedInput?.focus();
            return;
        }

        updateCheckoutActions();

        try {
            checkoutPrimaryButton.disabled = true;
            checkoutPrimaryButton.textContent = 'Menyimpan...';

            const response = await window.axios.post(orderStoreUrl, createOrderPayload(), {
                headers: csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {},
            });

            lastTransactionCode = response.data.order.code;
            addTransactionToHistory(response.data.order);
            cart = [];
            renderCart();
            setCartOpen(false);
            checkoutCompleted = true;

            if (checkoutSuccess) {
                checkoutSuccess.hidden = false;
            }

            if (checkoutSuccessCode) {
                checkoutSuccessCode.textContent = lastTransactionCode;
            }

            showToast({
                type: 'success',
                title: 'Pesanan berhasil dibuat',
                message: `${lastTransactionCode} masuk ke Riwayat Hari Ini.`,
                duration: 3200,
            });
        } catch (error) {
            const message = error.response?.data?.message
                || Object.values(error.response?.data?.errors || {})?.[0]?.[0]
                || 'Transaksi belum bisa disimpan.';

            showToast({
                type: 'danger',
                title: 'Gagal membuat pesanan',
                message,
                duration: 3600,
            });
        }

        updateCheckoutActions();
    };

    const handleCheckoutPrimary = () => {
        if (checkoutCompleted) {
            setCheckoutOpen(false);
            setCartOpen(false);
            return;
        }

        completeCheckout();
    };

    const handleCheckoutSecondary = () => {
        if (checkoutCompleted) {
            setCheckoutOpen(false);
            setCartOpen(false);
            setMenuOpen(false);
            setHistoryOpen(true);
            return;
        }

        setCheckoutOpen(false);
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

    const hasOpenPanel = () => root.classList.contains('is-cart-open')
        || root.classList.contains('is-menu-open')
        || root.classList.contains('is-history-open')
        || root.classList.contains('is-checkout-open')
        || root.classList.contains('is-note-open');

    productCards.forEach((card) => {
        const addFromCard = (event) => {
            if (hasOpenPanel() || suppressProductClick) {
                event?.preventDefault();
                event?.stopPropagation();
                return;
            }

            animateProductToCart(card);
            addProduct(productFromCard(card), 1, { notify: true });
        };

        card.addEventListener('click', addFromCard);
        card.addEventListener('keydown', (event) => {
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                addFromCard(event);
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

    menuOpenButtons.forEach((button) => {
        button.addEventListener('click', () => setMenuOpen(true));
    });

    menuCloseButtons.forEach((button) => {
        button.addEventListener('click', () => setMenuOpen(false));
    });

    historyOpenButtons.forEach((button) => {
        button.addEventListener('click', () => {
            setMenuOpen(false);
            setHistoryOpen(true);
        });
    });

    historyCloseButtons.forEach((button) => {
        button.addEventListener('click', () => setHistoryOpen(false));
    });

    checkoutOpenButton?.addEventListener('click', () => {
        setPromoOpen(false);
        setPaymentOpen(false);
        setTableDropdownOpen(false);
        setCheckoutOpen(true);
    });

    checkoutCloseButtons.forEach((button) => {
        button.addEventListener('click', () => setCheckoutOpen(false));
    });

    checkoutPrimaryButton?.addEventListener('click', handleCheckoutPrimary);
    checkoutSecondaryButton?.addEventListener('click', handleCheckoutSecondary);

    noteCloseButtons.forEach((button) => {
        button.addEventListener('click', () => setNoteOpen(false));
    });

    noteSuggestionButtons.forEach((button) => {
        button.addEventListener('click', () => {
            if (!noteInput) {
                return;
            }

            const suggestion = button.dataset.noteSuggestion;
            const currentValue = noteInput.value.trim();
            noteInput.value = currentValue ? `${currentValue}, ${suggestion}` : suggestion;
            noteInput.focus();
        });
    });

    noteClearButton?.addEventListener('click', () => saveItemNote(''));
    noteSaveButton?.addEventListener('click', () => saveItemNote(noteInput?.value || ''));

    cashReceivedInput?.addEventListener('input', () => {
        setCashReceivedValue(cashReceivedInput.value.replace(/\D/g, ''));
        renderCheckoutPreview();
    });

    orderTypeButtons.forEach((button) => {
        button.addEventListener('click', () => setOrderType(button.dataset.orderType));
    });

    tableTrigger?.addEventListener('click', () => {
        setTableDropdownOpen(!tableField?.classList.contains('is-table-open'));
    });

    tableOptionButtons.forEach((button) => {
        button.addEventListener('click', () => {
            setSelectedTable(button.dataset.tableValue);
            setTableDropdownOpen(false);
        });
    });

    promoTrigger?.addEventListener('click', () => {
        setPaymentOpen(false);
        setPromoOpen(!promoPicker?.classList.contains('is-choice-open'));
    });

    promoOptionButtons.forEach((button) => {
        button.addEventListener('click', () => {
            setSelectedPromo(button.dataset.promoId);
            setPromoOpen(false);
        });
    });

    paymentTrigger?.addEventListener('click', () => {
        setPromoOpen(false);
        setPaymentOpen(!paymentPicker?.classList.contains('is-choice-open'));
    });

    paymentOptionButtons.forEach((button) => {
        button.addEventListener('click', () => {
            setSelectedPayment(button.dataset.paymentId);
            setPaymentOpen(false);
        });
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            setCartOpen(false);
            setMenuOpen(false);
            setHistoryOpen(false);
            setCheckoutOpen(false);
            setNoteOpen(false);
            setTableDropdownOpen(false);
            setPromoOpen(false);
            setPaymentOpen(false);
        }
    });

    document.addEventListener('pointerdown', (event) => {
        let closedPanel = false;

        if (
            event.target.closest('[data-cart-open]')
            || event.target.closest('[data-menu-open]')
            || event.target.closest('[data-history-open]')
            || event.target.closest('[data-checkout-open]')
            || event.target.closest('[data-cart-note]')
        ) {
            return;
        }

        if (tableField?.classList.contains('is-table-open') && !tableField.contains(event.target)) {
            setTableDropdownOpen(false);
        }

        if (promoPicker?.classList.contains('is-choice-open') && !promoPicker.contains(event.target)) {
            setPromoOpen(false);
        }

        if (paymentPicker?.classList.contains('is-choice-open') && !paymentPicker.contains(event.target)) {
            setPaymentOpen(false);
        }

        if (root.classList.contains('is-cart-open')) {
            if (sidebar?.contains(event.target) || mobileNav?.contains(event.target)) {
                return;
            }

            setCartOpen(false);
            closedPanel = true;
        }

        if (root.classList.contains('is-menu-open')) {
            if (menuPanel?.contains(event.target)) {
                return;
            }

            setMenuOpen(false);
            closedPanel = true;
        }

        if (root.classList.contains('is-history-open')) {
            if (historyPanel?.contains(event.target)) {
                return;
            }

            setHistoryOpen(false);
            closedPanel = true;
        }

        if (root.classList.contains('is-checkout-open')) {
            if (checkoutModal?.contains(event.target)) {
                return;
            }

            setCheckoutOpen(false);
            closedPanel = true;
        }

        if (root.classList.contains('is-note-open')) {
            if (noteModal?.contains(event.target)) {
                return;
            }

            setNoteOpen(false);
            closedPanel = true;
        }

        if (closedPanel) {
            suppressProductClick = true;
            window.setTimeout(() => {
                suppressProductClick = false;
            }, 180);
            event.preventDefault();
            event.stopPropagation();
        }
    }, true);

    cartList.addEventListener('click', (event) => {
        const increase = event.target.closest('[data-cart-increase]');
        const decrease = event.target.closest('[data-cart-decrease]');
        const note = event.target.closest('[data-cart-note]');

        if (increase) {
            changeQty(increase.dataset.cartIncrease, 1);
        }

        if (decrease) {
            changeQty(decrease.dataset.cartDecrease, -1);
        }

        if (note) {
            setNoteOpen(true, note.dataset.cartNote);
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

    setOrderType(orderType);
    updateServerClock();
    window.setInterval(updateServerClock, 1000);
};

document.addEventListener('DOMContentLoaded', setupLuminaPos);
