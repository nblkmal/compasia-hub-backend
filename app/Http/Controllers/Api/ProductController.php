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

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = ProductMasterList::query();

        if ($request->has('search')) {
            $products->where('id', 'like', '%' . $request->search . '%')
                ->orWhere('type', 'like', '%' . $request->search . '%')
                ->orWhere('brand', 'like', '%' . $request->search . '%')
                ->orWhere('model', 'like', '%' . $request->search . '%');
        }

        // intentionally reduced to only 3 per page so can see pagination
        return ProductListResource::collection($products->paginate(3))
        ->additional(['status' => 'success']);
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,xlsx,xls', // 10MB max
        ]);

        $file = $request->file('file');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('uploads', $fileName, 'local');

        try {
            Excel::queueImport(new ProductsImport, $filePath);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'File upload failed: ' . $e->getMessage(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'File uploaded and processing started',
        ]);
    }

    public function logs()
    {
        $logs = ProductLog::with(['product:id'])->paginate(10);
        return ProductLogResource::collection($logs)
        ->additional(['status' => 'success']);
    }
}
