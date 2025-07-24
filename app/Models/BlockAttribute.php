<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlockAttribute extends Model
{
    protected $fillable = ['block_id', 'name', 'type', 'slug', 'text', 'html', 'image_path'];

    public function block()
    {
        return $this->belongsTo(Block::class);
    }
}

