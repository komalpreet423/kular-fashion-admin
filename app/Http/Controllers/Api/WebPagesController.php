<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WebPages;
class WebPagesController extends Controller
{
     public function index()
    {
        $webPages = WebPages::select('title', 'slug')->get();

        return response()->json([
            'success' => true,
            'data' => $webPages
        ]);
    }
    public function getwebpagebyslug(string $slug)
{
   $webPage = WebPages::where('slug', $slug)->first();
    if (!$webPage) {
        return response()->json([
            'success' => false,
            'message' => 'Web Page not found.'
        ], 404);
    }

    return response()->json([
        'success' => true,
        'data' => [
            'title' => $webPage->title,
            'slug' => $webPage->slug,
            'page_content' => $webPage->page_content,
            'heading' => $webPage->heading,
            'meta_title' => $webPage->meta_title,
            'meta_keywords' => $webPage->meta_keywords,
            'meta_description' => $webPage->meta_description,
            'created_at' => $webPage->created_at->toDateTimeString(),
            'updated_at' => $webPage->updated_at->toDateTimeString(),
        ]
    ]);
}
}
