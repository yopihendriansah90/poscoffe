<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\DiningTable;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class PosSeeder extends Seeder
{
    public function run(): void
    {
        $categories = collect([
            ['name' => 'Sandwich', 'slug' => 'sandwich', 'icon' => 'lunch_dining', 'sort_order' => 10],
            ['name' => 'Pastry', 'slug' => 'pastry', 'icon' => 'breakfast_dining', 'sort_order' => 20],
            ['name' => 'Donut', 'slug' => 'donut', 'icon' => 'donut_small', 'sort_order' => 30],
            ['name' => 'Cake', 'slug' => 'cake', 'icon' => 'cake', 'sort_order' => 40],
            ['name' => 'Bread', 'slug' => 'bread', 'icon' => 'bakery_dining', 'sort_order' => 50],
            ['name' => 'Drink', 'slug' => 'drink', 'icon' => 'local_cafe', 'sort_order' => 60],
        ])->mapWithKeys(function (array $category) {
            return [
                $category['slug'] => Category::updateOrCreate(
                    ['slug' => $category['slug']],
                    $category + ['is_active' => true],
                ),
            ];
        });

        collect([
            [
                'name' => 'Beef Crowich',
                'slug' => 'beef-crowich',
                'category' => 'sandwich',
                'type' => 'food',
                'price' => 55000,
                'image_path' => 'images/products/beef-crowich.webp',
                'is_discountable' => true,
                'sort_order' => 10,
            ],
            [
                'name' => 'Buttermelt Croissant',
                'slug' => 'buttermelt-croissant',
                'category' => 'pastry',
                'type' => 'food',
                'price' => 40000,
                'image_path' => 'images/products/buttermelt-croissant.webp',
                'sort_order' => 20,
            ],
            [
                'name' => 'Cereal Cream Donut',
                'slug' => 'cereal-cream-donut',
                'category' => 'donut',
                'type' => 'food',
                'price' => 24500,
                'image_path' => 'images/products/cereal-cream-donut.webp',
                'is_discountable' => true,
                'sort_order' => 30,
            ],
            [
                'name' => 'Cheesy Cheesecake',
                'slug' => 'cheesy-cheesecake',
                'category' => 'cake',
                'type' => 'food',
                'price' => 37500,
                'image_path' => 'images/products/cheesy-cheesecake.webp',
                'sort_order' => 40,
            ],
            [
                'name' => 'Cheezy Sourdough',
                'slug' => 'cheezy-sourdough',
                'category' => 'bread',
                'type' => 'food',
                'price' => 45000,
                'image_path' => 'images/products/cheezy-sourdough.webp',
                'sort_order' => 50,
            ],
            [
                'name' => 'Iced Latte',
                'slug' => 'iced-latte',
                'category' => 'drink',
                'type' => 'drink',
                'price' => 32000,
                'image_path' => 'images/products/iced-latte.webp',
                'is_discountable' => true,
                'sort_order' => 60,
            ],
            [
                'name' => 'Lemon Tea',
                'slug' => 'lemon-tea',
                'category' => 'drink',
                'type' => 'drink',
                'price' => 22000,
                'image_path' => 'images/products/lemon-tea.webp',
                'sort_order' => 70,
            ],
        ])->each(function (array $product) use ($categories) {
            Product::updateOrCreate(
                ['slug' => $product['slug']],
                [
                    'category_id' => $categories[$product['category']]->id,
                    'name' => $product['name'],
                    'type' => $product['type'],
                    'price' => $product['price'],
                    'image_path' => $product['image_path'],
                    'stock_status' => 'available',
                    'is_discountable' => $product['is_discountable'] ?? false,
                    'is_active' => true,
                    'sort_order' => $product['sort_order'],
                ],
            );
        });

        foreach (range(1, 5) as $number) {
            DiningTable::updateOrCreate(
                ['code' => 'T' . str_pad((string) $number, 2, '0', STR_PAD_LEFT)],
                [
                    'name' => 'Meja ' . str_pad((string) $number, 2, '0', STR_PAD_LEFT),
                    'capacity' => $number <= 2 ? 2 : 4,
                    'status' => 'available',
                    'is_active' => true,
                ],
            );
        }

        $cashier = User::query()->first();

        if (! $cashier || Order::query()->exists()) {
            return;
        }

        $products = Product::query()->get()->keyBy('slug');
        $table = DiningTable::query()->where('code', 'T05')->first();

        collect([
            ['code' => 'TRX-1024', 'time' => '13:42', 'method' => 'qris', 'items' => [['beef-crowich', 2], ['iced-latte', 2]]],
            ['code' => 'TRX-1023', 'time' => '13:18', 'method' => 'cash', 'items' => [['buttermelt-croissant', 1], ['lemon-tea', 2]]],
            ['code' => 'TRX-1022', 'time' => '12:54', 'method' => 'card', 'items' => [['cheesy-cheesecake', 2], ['cereal-cream-donut', 2]]],
            ['code' => 'TRX-1021', 'time' => '12:27', 'method' => 'qris', 'items' => [['cheezy-sourdough', 1], ['iced-latte', 1]]],
            ['code' => 'TRX-1020', 'time' => '11:59', 'method' => 'qris', 'items' => [['beef-crowich', 2], ['cheesy-cheesecake', 2], ['lemon-tea', 1]]],
        ])->each(function (array $transaction) use ($cashier, $products, $table) {
            $orderedAt = Carbon::today(config('app.timezone'))->setTimeFromTimeString($transaction['time']);
            $subtotal = collect($transaction['items'])->sum(function (array $item) use ($products) {
                return $products[$item[0]]->price * $item[1];
            });
            $taxAmount = (int) round($subtotal * 0.1);
            $discountAmount = min($subtotal, 10000);
            $total = $subtotal + $taxAmount - $discountAmount;

            $order = Order::create([
                'code' => $transaction['code'],
                'user_id' => $cashier->id,
                'dining_table_id' => $table?->id,
                'order_type' => 'dine_in',
                'status' => 'completed',
                'subtotal' => $subtotal,
                'tax_rate' => 10,
                'tax_amount' => $taxAmount,
                'discount_amount' => $discountAmount,
                'discount_name' => 'Member',
                'total' => $total,
                'ordered_at' => $orderedAt,
            ]);

            foreach ($transaction['items'] as [$slug, $quantity]) {
                $product = $products[$slug];

                $order->items()->create([
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => $quantity,
                    'unit_price' => $product->price,
                    'line_total' => $product->price * $quantity,
                ]);
            }

            $order->payment()->create([
                'method' => $transaction['method'],
                'status' => 'paid',
                'amount_paid' => $transaction['method'] === 'cash' ? $total + 6000 : $total,
                'change_amount' => $transaction['method'] === 'cash' ? 6000 : 0,
                'reference' => Str::upper($transaction['method']) . '-' . $transaction['code'],
                'paid_at' => $orderedAt,
            ]);
        });
    }
}
