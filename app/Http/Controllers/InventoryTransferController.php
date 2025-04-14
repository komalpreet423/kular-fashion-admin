<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Branch;
use App\Models\InventoryTransfer;
use App\Models\InventoryItem;
use App\Models\ProductQuantity;
use App\Models\StoreInventory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class InventoryTransferController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if(!Gate::allows('view inventory transfer')) {
            abort(403);
        }
        $branches = Branch::where('status','Active')->get();
        return view('inventory-transfer.index',compact('branches'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function inventoryTransferItems(Request $request)
    {
        $transferData = $request->all();
        $fromStoreId = $transferData['from_store_id'];
        $toStoreId = $transferData['to_store_id'];
        $items = $transferData['items'];
    
        $inventoryTransfer = InventoryTransfer::create([
            'sent_from' => $fromStoreId,
            'sent_to'   => $toStoreId,
            'sent_by'   => Auth::id()
        ]);

        foreach ($items as $value) {
            $productQuantityId = $value['product_quantity_id'];
            $quantity = $value['quantity'];

            $productQuantity = ProductQuantity::where('id', $productQuantityId)->with('sizes.sizeDetail', 'colors.colorDetail')->first();

            InventoryItem::create([
                'inventroy_transfer_id' => $inventoryTransfer->id,
                'product_id'            => $productQuantity->product_id,
                'product_quantity_id'   => $productQuantityId,
                'product_color_id'      => $productQuantity->colors->colorDetail->id,
                'product_size_id'       => $productQuantity->sizes->sizeDetail->id,
                'brand_id'              => $value['brand_id'],
                'quantity'              => $quantity,
            ]);
            
            // If any store expecting default is transfering the item, need to add quantity 0 or get existing quantity
            if($fromStoreId > 1){
                $fromStoreInventory = StoreInventory::firstOrCreate(
                    [
                        'store_id' => $fromStoreId,
                        'product_quantity_id' => $productQuantityId,
                    ],
                    [
                        'product_id' => $productQuantity->product_id,
                        'product_color_id' => $productQuantity->product_color_id,
                        'product_size_id' => $productQuantity->product_size_id,
                        'brand_id' => $value['brand_id'],
                        'quantity' => 0,
                        'total_quantity' => 0,
                    ]
                );
    
                $fromStoreInventory->update([
                    'quantity' => $fromStoreInventory->quantity - $quantity,
                ]);
            }

            // Update stock in default store is tranfered to/from default store
            if ($toStoreId > 1 && $fromStoreId === 1) {
                $productQuantity->update([
                    'quantity' => $productQuantity->quantity - $quantity,
                ]);
            } else if ($toStoreId === 1) {
                $productQuantity->update([
                    'quantity' => $productQuantity->quantity + $quantity,
                    //'total_quantity' => $productQuantity->total_quantity + $quantity,
                ]);
            }

            if ($toStoreId > 1) {
                $inventory = StoreInventory::where([
                    'store_id'             => $toStoreId,
                    'product_quantity_id'  => $productQuantityId,
                ])->first();

                if ($inventory) {
                    $inventory->update([
                        'quantity'       => $inventory->quantity + $quantity,
                        'total_quantity' => $inventory->total_quantity + $quantity,
                    ]);
                } else {
                    StoreInventory::create([
                        'store_id'              => $toStoreId,
                        'product_id'            => $productQuantity->product_id,
                        'product_quantity_id'   => $productQuantityId,
                        'product_color_id'      => $productQuantity->product_color_id,
                        'product_size_id'       => $productQuantity->product_size_id,
                        'brand_id'              => $value['brand_id'],
                        'quantity'              => $quantity,
                        'total_quantity'        => $quantity
                    ]);
                }
            }
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Items transferred successfully.'
        ]);
    }

    public function inventoryHistory()
    {
        $inventoryTransfer = InventoryTransfer::with('sentFrom', 'sentTo', 'sentBy')->get();
       
        return view('inventory-transfer.history', ['inventory_transfer' => $inventoryTransfer]);
    }

    public function inventoryTransferShow($id)
    {
        
        $inventoryTransfer = InventoryTransfer::with(
            'sentFrom', 
            'sentTo', 
            'sentBy', 
            'inventoryItems',
            'inventoryItems.product', 
            'inventoryItems.productColor', 
            'inventoryItems.productSize', 
            'inventoryItems.brand'
        )->findOrFail($id);
        return view('inventory-transfer.show', compact('inventoryTransfer'));
    }
}
