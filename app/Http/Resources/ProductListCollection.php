<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Resources\BaseResource;

class ProductListCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
        public function toArray(Request $request)
        {

            return $this->collection->map(function ($product) {
                    return [
                        'id' => $product->id,
                        'slug' => $product->slug,
                        'name' => $product->name,
                        'article_code' => $product->article_code,
                        'manufacture_code' => $product->manufacture_code,
                        'brand_id' => $product->brand_id,
                        'department_id' => $product->department_id,
                        'product_type_id' => $product->product_type_id,
                        'price'     => $product->price,
                        'sale_price' => $product->sale_price,
                        'sale_start' => $product->sale_start,
                        'sale_end' => $product->sale_end,
                        'season' => $product->season,
                        'size_scale_id' => $product->size_scale_id,
                        'min_size_id' => $product->min_size_id,
                        'max_size_id' => $product->max_size_id,
                        'default_image' => setting('default_product_image'),
                        'brand' => [
                            'id' => optional($product->brand)->id,
                            'name' => optional($product->brand)->name,
                            'slug' => optional($product->brand)->slug,
                        ],
                        'department' => [
                            'id' => optional($product->department)->id,
                            'name' => optional($product->department)->name,
                            'slug' => optional($product->department)->slug,
                            'image' => optional($product->department)->image,
                        ],
                        
                        'productType' => [
                            'id' => optional($product->productType)->id,
                            'name' => optional($product->productType)->name,
                            'slug' => optional($product->productType)->slug,
                        ],
                        'images' => $product->webImage->map(function ($image) {
                            return [
                                "id"=> $image->id,
                                "product_id"=> $image->product_id,
                                "product_color_id"=> $image->product_color_id,
                                "path"=> $image->path,
                                "alt"=> $image->alt,
                                "is_default"=> $image->is_default,
                            ];
                        }),
                        'specifications' => $product->specifications->map(function($specification){
                            return [
                                "id" => $specification->id,
                                "product_id"=> $specification->product_id,
                                "key"=> $specification->key,
                                "value"=> $specification->value,
                            ];
                        }),
                        'colors' => $product->colors->map(function ($color) {
                            return [
                                'id' => $color->id,
                                'color_id' => $color->color_id,
                                'supplier_color_code' => $color->supplier_color_code,
                                'supplier_color_name' => $color->supplier_color_name,
                                'swatch_image_path'=> $color->swatch_image_path,
                                'detail' => [
                                    'id' => optional($color->colorDetail)->id,
                                    'name' => optional($color->colorDetail)->name,
                                    'slug' => optional($color->colorDetail)->slug,
                                    'code' => optional($color->colorDetail)->code,
                                    'ui_color_code' => optional($color->colorDetail)->ui_color_code,
                                ]
                            ];
                        }),
                        'sizes' => $product->sizes->map(function ($size) {
                            return [
                                'id' => $size->id,
                                'size_id' => $size->size_id,
                                'price' => $size->web_price,
                                'sale_price' => $size->web_sale_price,
                                'detail' => [
                                    'id' => optional($size->sizeDetail)->id,
                                    'size' => optional($size->sizeDetail)->size,
                                ]
                            ];
                        }),
                    
                    ];
                });
        }

    public function paginationInformation($request, $paginated, $default)
    {
        $default['pagination']['current_page'] = $default['meta']['current_page'];
        $default['pagination']['from'] = $default['meta']['from'];
        $default['pagination']['last_page'] = $default['meta']['last_page'];
        $default['pagination']['per_page'] = $default['meta']['per_page'];
        $default['pagination']['to'] = $default['meta']['to'];
        $default['pagination']['total'] = $default['meta']['total'];

        unset($default['links']);
        unset($default['meta']);

        return $default;
    }
}
