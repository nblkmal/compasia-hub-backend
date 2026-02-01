<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\ProductStatus;

class ProductLog extends Model
{
    protected $fillable = [
        'product_master_list_id',
        'status',
        'quantity',
    ];

    protected $casts = [
        'status' => ProductStatus::class,
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(ProductMasterList::class);
    }
}
