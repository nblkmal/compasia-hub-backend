<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductMasterList extends Model
{
    protected $fillable = [
        'id',
        'type',
        'brand',
        'model',
        'capacity',
        'updated_at',   // simple flag to know when the latest the item is updated
    ];
}
