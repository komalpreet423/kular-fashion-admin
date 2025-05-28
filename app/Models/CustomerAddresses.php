<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerAddresses extends Model
{

    protected $table = 'customer_addresses';
    protected $guarded=[];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
