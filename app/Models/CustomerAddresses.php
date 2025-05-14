<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerAddresses extends Model
{

    protected $table = 'customer_addresses';
    protected $fillable = [
        'name',
        'country_code',
        'phone_no',
        'user_id',
        'address_line_1',
        'address_line_2',
        'landmark',
        'city',
        'state',
        'zip_code',
        'country',
        'is_default',
        'type'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
