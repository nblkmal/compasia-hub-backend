<?php

namespace App\Imports;

use App\Models\ProductMasterList;
use App\Models\ProductLog;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use App\Events\ImportCompleted;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterImport;
use App\Enums\ProductStatus;

class ProductsImport implements ToCollection, WithHeadingRow, WithChunkReading, WithBatchInserts, ShouldQueue, WithEvents
{
    public function collection(Collection $rows)
    {
        // find the product by product_id
        // if $row['status'] === 'Sold', minus by 1
        // assume minus by 1 because no colum for quantity retrieved from the excel
        // if $row['status'] === 'Buy', add by 1
        // assume add by 1 because no colum for quantity retrieved from the excel
        $products = ProductMasterList::whereIn(
            'id',
            collect($rows)->pluck('product_id')
        )->get()->keyBy('id');

        try {
            DB::transaction(function () use ($rows, $products) {
                foreach ($rows as $row) {
                    $product = $products->get($row['product_id']);

                    if (! $product) {
                        logger()->warning('Product not found', [
                            'product_id' => $row['product_id'],
                        ]);
                        continue;
                    }

                    match ($row['status']) {
                        ProductStatus::SOLD->value => $product->quantity >= 1 ? $product->decrement('quantity', 1) : null,
                        ProductStatus::BUY->value  => $product->increment('quantity', 1),
                        default => null,
                    };

                    ProductLog::create([
                        'product_master_list_id' => $product->id,
                        'status' => $row['status'],
                        'quantity' => 1,
                    ]);
                }
            });
        } catch (\Throwable $th) {
            logger()->error('Error importing products', [
                'error' => $th->getMessage(),
            ]);
        }
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    public function batchSize(): int
    {
        return 1000;
    }

    public function registerEvents(): array
    {
        return [
            AfterImport::class => function(AfterImport $event) {
                // broadcast the event to notify the client side
                broadcast(new ImportCompleted());
            },
        ];
    }
}
