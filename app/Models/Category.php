<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'icon',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * @return array<string, string>
     */
    public static function materialIconOptions(): array
    {
        return [
            'restaurant_menu' => 'Semua Menu / Restaurant Menu',
            'local_offer' => 'Diskon / Promo',
            'restaurant' => 'Makanan',
            'local_cafe' => 'Minuman / Cafe',
            'lunch_dining' => 'Sandwich / Burger',
            'breakfast_dining' => 'Pastry / Sarapan',
            'donut_small' => 'Donat',
            'cake' => 'Kue / Cake',
            'bakery_dining' => 'Roti / Bakery',
            'icecream' => 'Es Krim / Dessert',
            'ramen_dining' => 'Mie / Ramen',
            'rice_bowl' => 'Rice Bowl',
            'set_meal' => 'Paket Makan',
            'kebab_dining' => 'Kebab',
            'tapas' => 'Snack / Tapas',
            'emoji_food_beverage' => 'Tea / Beverage',
            'coffee' => 'Kopi',
            'liquor' => 'Mocktail / Minuman Dingin',
            'brunch_dining' => 'Brunch',
            'category' => 'Kategori Umum',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function materialIconOptionsWithPreview(): array
    {
        return collect(static::materialIconOptions())
            ->mapWithKeys(fn (string $label, string $icon): array => [
                $icon => static::materialIconPreviewHtml($icon, $label),
            ])
            ->all();
    }

    public static function materialIconPreviewHtml(string $icon, ?string $label = null): string
    {
        $safeIcon = e($icon);
        $safeLabel = e($label ?? static::materialIconOptions()[$icon] ?? $icon);

        return <<<HTML
            <span class="lumina-filament-icon-option">
                <span class="lumina-filament-icon-preview" aria-hidden="true">{$safeIcon}</span>
                <span>{$safeLabel}</span>
                <span class="lumina-filament-icon-name">{$safeIcon}</span>
            </span>
        HTML;
    }
}
