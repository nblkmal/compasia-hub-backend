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

    public function scopeSearch($query, string $search = null)
    {
        if (! $search) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
            $q->where('id', 'like', "%{$search}%")
            ->orWhere('type', 'like', "%{$search}%")
            ->orWhere('brand', 'like', "%{$search}%")
            ->orWhere('model', 'like', "%{$search}%");
        });
    }
}
