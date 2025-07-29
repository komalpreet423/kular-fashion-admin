<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Block;
use Illuminate\Http\Request;

class BlockController extends Controller
{

    public function index()
    {
        return response()->json(Block::with('attributes')->get());
    }

    public function show($id)
    {
        $block = Block::with('attributes')->find($id);

        if (!$block) {
            return response()->json(['message' => 'Block not found'], 404);
        }

        return response()->json($block);
    }
    public function getSectionBlocks($key)
    {

        $block = Block::with('attributes')->where('key', $key)->first();

        if ($block) {
            return response()->json([
                'section' => $key,
                'block' => $block,
            ]);
        }

        if ($key === 'footer') {
            $blocks = Block::with('attributes')
                ->where('key', 'like', 'footer_%')
                ->get();

            return response()->json([
                'section' => 'footer',
                'blocks' => $blocks,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Section not found',
        ], 404);
    }
}
