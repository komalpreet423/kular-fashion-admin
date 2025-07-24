<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Block extends Model
{
    protected $fillable = ['name', 'key', 'description'];
     protected $with = ['attributes'];

    public function attributes()
    {
        return $this->hasMany(BlockAttribute::class);
    }
}




