<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductQuantity extends Model
{
    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($productQuantity) {
            if ($productQuantity->product) {
                $productCode = $productQuantity->product->article_code . '-';
                $size = $productQuantity->sizes->sizeDetail->size . '-' ?? '';
                $color = $productQuantity->colors->colorDetail->name ?? '';
                $productQuantity->sku = strtoupper($productCode . $size . $color);

                $article_code = $productQuantity->product->article_code ?? '';
                $color_code = $productQuantity->colors->colorDetail->code;
                $new_code = $productQuantity->sizes->sizeDetail->new_code;
                $productQuantity->barcode = self::generateCheckDigit($article_code . $color_code . $new_code);
            }
        });
    }

    public function sizes()
    {
        return $this->belongsTo(ProductSize::class, 'product_size_id')->with('sizeDetail');
    }

    public function colors()
    {
        return $this->belongsTo(ProductColor::class, 'product_color_id')->with('colorDetail');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public static function generateCheckDigit($barcode)
    {
        $sum = 0;
        $barcodeArray = str_split($barcode);
        foreach ($barcodeArray as $i => $digit) {
            $sum += $digit * ($i % 2 == 0 ? 3 : 1);
        }
        $checkDigit = (10 - ($sum % 10)) % 10;
        return $barcode . $checkDigit;
    }
    public function getNameAttribute()
    {
        $productName = $this->product->name ?? '';
        $size = $this->sizes->sizeDetail->size ?? '';
        $color = $this->colors->colorDetail->name ?? '';

        return trim("{$productName} - {$size} - {$color}");
    }
}
