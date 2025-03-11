<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    protected $relatedProducts;

    public function __construct($resource, $relatedProducts = [])
    {
        parent::__construct($resource);
        $this->relatedProducts = (object) $relatedProducts;
    }

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'name' => $this->name,
            'article_code' => $this->article_code,
            'manufacture_code' => $this->manufacture_code,
            'brand_id' => $this->brand_id,
            'department_id' => $this->department_id,
            'product_type_id' => $this->product_type_id,
            'price' => number_format($this->price, 2),
            'sale_price' => number_format($this->sale_price, 2),
            'sale_start' => $this->sale_start,
            'sale_end' => $this->sale_end,
            'season' => $this->season,
            'brand' =>  [
                'id' => optional($this->brand)->id,
                'name' => optional($this->brand)->name ?? '',
                'slug' => optional($this->brand)->slug,
                "short_name" => optional($this->brand)->short_name,
            ],
            'department' => [
                'id' => optional($this->department)->id,
                'name' => optional($this->department)->name,
                'slug' => optional($this->department)->slug ?? '',
                "description" => optional($this->department)->description ?? '',
            ],
            'productType' =>  [
                'id' => optional($this->productType)->id,
                'name' => optional($this->productType)->name,
                'slug' => optional($this->productType)->slug,
                "short_name" => optional($this->productType)->short_name,
            ],
            'webInfo' =>  [
                "id" => optional($this->webInfo)->id,
                "summary" => optional($this->webInfo)->summary,
                "description" => optional($this->webInfo)->description,
                "is_splitted_with_colors" => optional($this->webInfo)->is_splitted_with_colors,
                "heading" => optional($this->webInfo)->heading,
                "meta_title" => optional($this->webInfo)->meta_title,
                "meta_keywords" => optional($this->webInfo)->meta_keywords,
                "meta_description" => optional($this->webInfo)->meta_description,
            ],
            'images' => $this->webImage->map(function ($image) {
                return [
                    "id" => $image->id,
                    "product_color_id" => $image->product_color_id,
                    "path" => $image->path,
                    "alt" => $image->alt,
                    "is_default" => $image->is_default,
                ];
            }),
            'specifications' => $this->specifications->map(function ($specification) {
                return [
                    "label" => $specification->key,
                    "value" => $specification->value,
                ];
            }),
            'sizes' => $this->sizes->map(function ($size) {
                return [
                    'id' => $size->id,
                    'product_id' => $size->product_id,
                    'size_id' => $size->size_id,
                    'mrp' => $size->mrp,
                    'price' => $size->web_price,
                    'sale_price' => $size->web_sale_price,
                    'detail' => $size->sizeDetail ? [
                        "id" => $size->sizeDetail->id,
                        "name" => $size->sizeDetail->size,
                        "code" => $size->sizeDetail->new_code,
                        "length" => $size->sizeDetail->length,
                    ] : null,
                ];
            }),
            'colors' => $this->colors->map(function ($color) {
                return [
                    'id' => $color->id,
                    'color_id' => $color->color_id,
                    'supplier_color_code' => $color->supplier_color_code,
                    'supplier_color_name' => $color->supplier_color_name,
                    'swatch_image_path' => $color->swatch_image_path,
                    'detail' => $color->colorDetail ? [
                        "id" => $color->colorDetail->id,
                        "name" => $color->colorDetail->name,
                        "slug" => $color->colorDetail->slug,
                        "short_name" => $color->colorDetail->short_name,
                        "code" => $color->colorDetail->code,
                        "ui_color_code" => $color->colorDetail->ui_color_code,
                    ] : null,
                ];
            }),
            'variants' => $this->quantities->map(function ($variant) {
                return [
                    'id' => $variant->id,
                    'product_color_id' => $variant->product_color_id,
                    'product_size_id' => $variant->product_size_id,
                    'sku' => $variant->sku,
                    'quantity' => $variant->quantity,
                ];
            }),
            'relatedProducts' => $this->relatedProducts->map(function ($relatedProduct) {
                return [
                    'id' => $relatedProduct->id,
                    'slug' => $relatedProduct->slug,
                    'name' => $relatedProduct->name,
                    'price'     => number_format($relatedProduct->price, 2),
                    'sale_price' => number_format($relatedProduct->sale_price, 2),
                    'sale_start' => $relatedProduct->sale_start,
                    'sale_end' => $relatedProduct->sale_end,
                    'sizes' => $relatedProduct->sizes->map(function ($size) {
                        return [
                            'id' => $size->id,
                            'size_id' => $size->size_id,
                            'price' => number_format($size->web_price, 2),
                            'sale_price' => number_format($size->web_sale_price, 2),
                            'detail' => [
                                'id' => optional($size->sizeDetail)->id,
                                'size' => optional($size->sizeDetail)->size,
                            ]
                        ];
                    }),
                ];
            }),
        ];
    }
}
