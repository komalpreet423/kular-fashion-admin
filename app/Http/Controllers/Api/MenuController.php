<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\Product;
use App\Models\ProductType;
use App\Models\HomeImage;



class MenuController extends Controller
{
    public function index(Request $request)
    {
        $departments = Department::where('status', 'Active')
            ->select('id', 'slug', 'name')
            ->with(['products' => function ($query) {
                $query->where('status', 'Active');
            }])
            ->paginate($request->input('length', 5));

        $departments->getCollection()->transform(function ($department) {
            $productTypeIds = $department->products->pluck('product_type_id')->unique()->toArray();

            $productTypes = ProductType::whereIn('id', $productTypeIds)
                ->select('id', 'slug', 'name', 'short_name')
                ->get();

            // Optionally remove the products if you don’t need them in the response
            // unset($department->products);

            $department->product_types = $productTypes;
            return $department;
        });

        return response()->json([
            'success' => true,
            'departments' => $departments
        ]);
    }

    public function getImages()
{
    $sliderImages = HomeImage::where('type', 'slider')->latest()->get()->map(function ($img) {
        return [
            'id' => $img->id,
            'image_url' => asset($img->image_path),
            'created_at' => $img->created_at->toDateTimeString(),
        ];
    });

    $newsletterImages = HomeImage::where('type', 'newsletter')->latest()->get()->map(function ($img) {
        return [
            'id' => $img->id,
            'image_url' => asset($img->image_path),
            'created_at' => $img->created_at->toDateTimeString(),
        ];
    });

    return response()->json([
        'success' => true,
        'slider_images' => $sliderImages,
        'newsletter_images' => $newsletterImages,
    ]);
}
}
