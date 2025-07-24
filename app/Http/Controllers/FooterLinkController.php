<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Block;
use App\Models\BlockAttribute;
use Illuminate\Support\Facades\Storage;

class FooterLinkController extends Controller
{
    public function index()
    {
        $blocks = Block::with('attributes')->orderBy('created_at', 'desc')->get();
        return view('footer-links.index', compact('blocks'));
    }

    public function create()
    {
        $block = new Block();
        return view('footer-links.create', compact('block'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'key' => 'required|string|max:255|unique:blocks,key',
            'description' => 'nullable|string',
            'content_items' => 'required|json',
            'image_upload' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $contentItems = json_decode($validated['content_items'], true);

        $block = Block::create([
            'name' => $validated['name'],
            'key' => $validated['key'],
            'description' => $validated['description'],
        ]);

        $this->processContentItems($block, $contentItems, $request);

        return redirect()->route('footer-links.index')->with('success', 'Block created successfully');
    }

    public function edit(Block $block)
    {
        $block->load('attributes');
        return view('footer-links.edit', compact('block'));
    }

    public function update(Request $request, Block $block)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'key' => 'required|string|max:255|unique:blocks,key,' . $block->id,
            'description' => 'nullable|string',
            'content_items' => 'required|json',


        ]);

        $contentItems = json_decode($validated['content_items'], true);

        $block->update([
            'name' => $validated['name'],
            'key' => $validated['key'],
            'description' => $validated['description'],
        ]);

        $block->attributes()->delete();
        $this->processContentItems($block, $contentItems, $request);

        return redirect()->route('footer-links.index')->with('success', 'Block updated successfully');
    }

    public function destroy(Block $block)
    {
        try {
            $block->attributes()->delete();
            $block->delete();

            return response()->json([
                'success' => true,
                'message' => 'Block deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Delete failed: ' . $e->getMessage()
            ], 500);
        }
    }

    protected function processContentItems(Block $block, array $contentItems, Request $request)
    {
        foreach ($contentItems as $item) {
            $attribute = new BlockAttribute();
            $attribute->block_id = $block->id;
            $attribute->type = $item['type'] ?? 'link';

            switch ($attribute->type) {
                case 'menu':
                    $attribute->name = $item['data']['menu_name'] ?? null;
                    $attribute->slug = $item['data']['menu_slug'] ?? null;
                    break;

                case 'link':
                    $attribute->name = $item['data']['link_name'] ?? null;
                    $attribute->slug = $item['data']['link_slug'] ?? null;
                    break;

                case 'text':
                    $attribute->text = $item['data']['text_content'] ?? null;
                    break;

                case 'html':
                    $attribute->html = $item['data']['html_content'] ?? null;
                    break;

                case 'image':
                    $attribute->name = $item['data']['image_alt'] ?? 'Image';

                    if (!empty($item['data']['image_url']) && strpos($item['data']['image_url'], 'data:image') === 0) {
                        $imageData = explode(',', $item['data']['image_url'])[1];
                        $imageData = str_replace(' ', '+', $imageData);
                        $imageName = 'block-images/image_' . time() . '_' . uniqid() . '.png';
                        Storage::disk('public')->put($imageName, base64_decode($imageData));
                        $attribute->image_path = $imageName;
                    } elseif (!empty($item['data']['image_path'])) {
                        $attribute->image_path = $item['data']['image_path'];
                    }

                    break;
            }



            $attribute->save();
        }
    }
}
