<?php

namespace App\Models;

use Money\Money;
use Money\Currency;
use NumberFormatter;
use App\Models\Image;
use App\Casts\MoneyCast;
use App\Models\ProductVariant;
use Money\Currencies\ISOCurrencies;
use Illuminate\Database\Eloquent\Model;
use Money\Formatter\IntlMoneyFormatter;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    public $casts = [
        'price' => MoneyCast::class,
        'amount_tax' => MoneyCast::class,
        'amount_total' => MoneyCast::class,
        'amount_subtotal' => MoneyCast::class,
        'amount_discount' => MoneyCast::class,
    ];

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function image(): HasOne
    {
        return $this->hasOne(Image::class)->ofMany('featured', 'max');
    }

    public function images(): HasMany
    {
        return $this->hasMany(Image::class);
    }

    protected function formattedPrice(): Attribute
    {
        return Attribute::make(
            get: function () {
                $money = new Money($this->price, new Currency('MXN'));
                $currencies = new ISOCurrencies();
                $numberFormatter = new NumberFormatter('es_MX', \NumberFormatter::CURRENCY);
                $moneyFormatter = new IntlMoneyFormatter($numberFormatter, $currencies);
                
                return $moneyFormatter->format($money);
            },
            set: function ($value) {
                // Convert the formatted string back to a Money object
                $numberFormatter = new NumberFormatter('es_MX', \NumberFormatter::CURRENCY);
                $parsedValue = $numberFormatter->parseCurrency($value, $currency);
                
                // Store the value as an integer in the smallest currency unit
                return (int) $parsedValue;
            }
        );
    }
}
