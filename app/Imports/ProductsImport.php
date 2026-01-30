<?php

namespace App\Imports;

use App\Models\ProductMasterList;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class ProductsImport implements ToCollection, WithHeadingRow, WithChunkReading, WithBatchInserts, ShouldQueue
{
    public function collection(Collection $rows)
    {
        // find the product by product_id
        // if $row['status'] === 'Sold', minus by 1
        // assume minus by 1 because no colum for quantity retrieved from the excel
        // if $row['status'] === 'Buy', add by 1
        // assume add by 1 because no colum for quantity retrieved from the excel
        DB::transaction(function () use ($rows) {
            foreach ($rows as $row) {
                $product = ProductMasterList::firstOrFail($row['product_id']);

                if ($row['status'] === 'Sold') {
                    $product->decrement('quantity', 1);
                } elseif ($row['status'] === 'Buy') {
                    $product->increment('quantity', 1);
                }
            }
        });
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    public function batchSize(): int
    {
        return 1000;
    }
}
