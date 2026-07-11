<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\DiningTable;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class PosCashierController extends Controller
{
    public function __invoke(): View
    {
        $posTimezone = config('app.timezone', 'Asia/Jakarta');
        $serverNow = now($posTimezone);

        $products = Product::query()
            ->with('category')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $categoryRows = Category::query()
            ->withCount([
                'products' => fn ($query) => $query->where('is_active', true),
            ])
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $categories = collect([
            ['name' => 'Semua Menu', 'count' => $products->count(), 'icon' => 'restaurant_menu', 'slug' => 'all'],
            ['name' => 'All Discount', 'count' => $products->where('is_discountable', true)->count(), 'icon' => 'local_offer', 'slug' => 'discount'],
            ['name' => 'Food', 'count' => $products->where('type', 'food')->count(), 'icon' => 'restaurant', 'slug' => 'food'],
            ['name' => 'Drink', 'count' => $products->where('type', 'drink')->count(), 'icon' => 'local_cafe', 'slug' => 'drink'],
        ])->merge($categoryRows->map(fn (Category $category) => [
            'name' => $category->name,
            'count' => $category->products_count,
            'icon' => $category->icon ?: 'category',
            'slug' => $category->slug,
        ]))->values()->all();

        $productCards = $products->map(fn (Product $product) => [
            'id' => $product->slug,
            'name' => $product->name,
            'category' => $product->category?->name ?? 'Menu',
            'slug' => $product->category?->slug ?? 'menu',
            'type' => $product->type,
            'discount' => $product->is_discountable,
            'price' => $product->price,
            'image' => $this->productImageUrl($product->image_path),
        ])->values()->all();

        $tables = DiningTable::query()
            ->where('is_active', true)
            ->orderBy('code')
            ->get()
            ->map(fn (DiningTable $table) => [
                'id' => (string) $table->id,
                'name' => $table->name,
                'status' => $table->status,
            ])
            ->values()
            ->all();

        $selectedTable = collect($tables)->firstWhere('name', 'Meja 05') ?? ($tables[0] ?? [
            'id' => '1',
            'name' => 'Meja 01',
            'status' => 'available',
        ]);

        $todayOrders = Order::query()
            ->with(['payment', 'user'])
            ->whereDate('ordered_at', $serverNow->toDateString())
            ->latest('ordered_at')
            ->get();

        $revenue = (int) $todayOrders->sum('total');
        $transactionCount = $todayOrders->count();

        $todaySummary = [
            'transactions' => $transactionCount,
            'revenue' => $revenue,
            'average' => $transactionCount > 0 ? (int) round($revenue / $transactionCount) : 0,
            'openOrders' => Order::query()->whereIn('status', ['open', 'pending'])->count(),
            'items' => $todayOrders->take(10)->map(fn (Order $order) => [
                'code' => $order->code,
                'time' => Carbon::parse($order->ordered_at)->timezone($posTimezone)->format('H:i'),
                'cashier' => $order->user?->name ?? 'Kasir',
                'method' => $this->paymentLabel($order->payment?->method),
                'total' => $order->total,
            ])->values()->all(),
        ];

        return view('pos.cashier', [
            'categories' => $categories,
            'products' => $productCards,
            'tables' => $tables,
            'selectedTable' => $selectedTable,
            'initialCart' => [],
            'todaySummary' => $todaySummary,
            'posTimezone' => $posTimezone,
            'serverNow' => $serverNow,
            'posTimezoneLabel' => $this->timezoneLabel($posTimezone),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'order_type' => ['required', 'in:dine_in,take_away'],
            'dining_table_id' => ['nullable', 'integer', 'exists:dining_tables,id'],
            'promo' => ['nullable', 'in:member,happy-hour,none'],
            'payment.method' => ['required', 'in:qris,cash,card'],
            'payment.cash_received' => ['nullable', 'integer', 'min:0'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.id' => ['required', 'string', 'exists:products,slug'],
            'items.*.qty' => ['required', 'integer', 'min:1', 'max:99'],
            'items.*.note' => ['nullable', 'string', 'max:120'],
        ]);

        $products = Product::query()
            ->whereIn('slug', collect($validated['items'])->pluck('id'))
            ->where('is_active', true)
            ->get()
            ->keyBy('slug');

        if ($products->count() !== collect($validated['items'])->pluck('id')->unique()->count()) {
            throw ValidationException::withMessages([
                'items' => 'Beberapa produk tidak tersedia.',
            ]);
        }

        $subtotal = collect($validated['items'])->sum(function (array $item) use ($products) {
            return $products[$item['id']]->price * $item['qty'];
        });

        $taxRate = 10;
        $taxAmount = (int) round($subtotal * ($taxRate / 100));
        [$discountName, $discountAmount] = $this->discountFor($validated['promo'] ?? 'member', $subtotal);
        $total = max(0, $subtotal + $taxAmount - $discountAmount);
        $paymentMethod = $validated['payment']['method'];
        $cashReceived = (int) ($validated['payment']['cash_received'] ?? 0);
        $amountPaid = $paymentMethod === 'cash' ? $cashReceived : $total;

        if ($paymentMethod === 'cash' && $amountPaid < $total) {
            throw ValidationException::withMessages([
                'payment.cash_received' => 'Uang diterima belum cukup.',
            ]);
        }

        $posTimezone = config('app.timezone', 'Asia/Jakarta');
        $orderedAt = now($posTimezone);
        $cashier = $request->user() ?? User::query()->first();

        $order = DB::transaction(function () use ($amountPaid, $cashier, $discountAmount, $discountName, $orderedAt, $paymentMethod, $products, $subtotal, $taxAmount, $taxRate, $total, $validated) {
            $order = Order::create([
                'code' => $this->nextOrderCode(),
                'user_id' => $cashier?->id,
                'dining_table_id' => $validated['order_type'] === 'dine_in' ? $validated['dining_table_id'] : null,
                'order_type' => $validated['order_type'],
                'status' => 'completed',
                'subtotal' => $subtotal,
                'tax_rate' => $taxRate,
                'tax_amount' => $taxAmount,
                'discount_amount' => $discountAmount,
                'discount_name' => $discountName,
                'total' => $total,
                'ordered_at' => $orderedAt,
            ]);

            foreach ($validated['items'] as $item) {
                $product = $products[$item['id']];

                $order->items()->create([
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => $item['qty'],
                    'unit_price' => $product->price,
                    'line_total' => $product->price * $item['qty'],
                    'note' => $item['note'] ?? null,
                ]);
            }

            $order->payment()->create([
                'method' => $paymentMethod,
                'status' => 'paid',
                'amount_paid' => $amountPaid,
                'change_amount' => max(0, $amountPaid - $total),
                'reference' => strtoupper($paymentMethod) . '-' . $order->code,
                'paid_at' => $orderedAt,
            ]);

            return $order->load(['payment', 'user']);
        });

        return response()->json([
            'order' => [
                'code' => $order->code,
                'time' => Carbon::parse($order->ordered_at)->timezone($posTimezone)->format('H:i'),
                'cashier' => $order->user?->name ?? 'Kasir',
                'method' => $this->paymentLabel($order->payment?->method),
                'total' => $order->total,
            ],
            'summary' => [
                'subtotal' => $order->subtotal,
                'tax' => $order->tax_amount,
                'discount' => $order->discount_amount,
                'total' => $order->total,
                'amount_paid' => $order->payment?->amount_paid ?? $order->total,
                'change' => $order->payment?->change_amount ?? 0,
            ],
        ], 201);
    }

    private function paymentLabel(?string $method): string
    {
        return match ($method) {
            'cash' => 'Tunai',
            'card' => 'Kartu',
            'qris' => 'QRIS',
            default => 'QRIS',
        };
    }

    /**
     * @return array{0: string|null, 1: int}
     */
    private function discountFor(string $promo, int $subtotal): array
    {
        return match ($promo) {
            'happy-hour' => ['Happy Hour', (int) round($subtotal * 0.1)],
            'none' => [null, 0],
            default => ['Member', min($subtotal, 10000)],
        };
    }

    private function nextOrderCode(): string
    {
        $nextNumber = Order::query()
            ->where('code', 'like', 'TRX-%')
            ->pluck('code')
            ->map(fn (string $code) => (int) str_replace('TRX-', '', $code))
            ->max() + 1;

        return 'TRX-' . max($nextNumber, 1001);
    }

    private function timezoneLabel(string $timezone): string
    {
        return match ($timezone) {
            'Asia/Jakarta' => 'WIB',
            'Asia/Makassar' => 'WITA',
            'Asia/Jayapura' => 'WIT',
            default => $timezone,
        };
    }

    private function productImageUrl(?string $path): string
    {
        if (! $path) {
            return asset('images/products/beef-crowich.webp');
        }

        if (str_starts_with($path, 'http')) {
            return $path;
        }

        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->url($path);
        }

        return asset($path);
    }
}
