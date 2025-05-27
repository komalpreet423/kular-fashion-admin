<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Validator;
use App\Http\Resources\BrandCollection;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use App\Models\Brand;
use Exception;

class BrandController extends Controller
{
    public function brands(Request $request){

        $brands = Brand::where('status','Active')->paginate($request->input('length', 42));
        
        if($brands)
        {
            return new BrandCollection($brands); 
        }
    }
}
