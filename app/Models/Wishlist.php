<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Str;
use App\Models\ProductType;

class Wishlist extends Model
{
    protected $guarded = [];

    protected $table = 'wishlist';

    protected $fillable = ['user_id', 'product_id'];

    public $timestamps = true;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
