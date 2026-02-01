<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Imports\ProductsImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\ProductMasterList;
use App\Models\ProductLog;
use App\Http\Resources\ProductListResource;
use App\Http\Resources\ProductLogResource;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = ProductMasterList::query()
            ->search($request->string('search'))
            ->paginate(3);
        // intentionally reduced to only 3 per page so can see pagination

        return ProductListResource::collection($products)
        ->additional(['success' => true]);
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,xlsx,xls|max:10240', // 10MB max
        ]);

        $file = $request->file('file');

        try {
            // Excel file will be processed in background using Laravel Queue
            Excel::queueImport(new ProductsImport, $file);
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            // Laravel Excel validation failed
            return response()->json([
                'success' => false,
                'message' => 'Import validation failed',
                'errors' => $e->failures(),
            ], 422);
        } catch (\Throwable $e) {
            Log::error('File upload failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'File upload failed',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'File uploaded and processing started',
        ]);
    }

    // not in requirements, but just put it here for nice to have
    public function logs()
    {
        $logs = ProductLog::with(['product:id'])->paginate(10);
        return ProductLogResource::collection($logs)
        ->additional(['success' => true]);
    }
}
