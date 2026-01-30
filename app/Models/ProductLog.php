<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductLog extends Model
{
    protected $fillable = [
        'product_master_list_id',
        'status',
        'quantity',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(ProductMasterList::class);
    }
}
