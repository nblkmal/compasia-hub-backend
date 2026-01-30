<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Imports\ProductsImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\ProductMasterList;

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

        return response()->json([
            'status' => 'success',
            'data' => $products->paginate(10)
        ]);
    }

    public function upload(Request $request)
    {
        info('file uploaded');
        // $request->validate([
        //     'file' => 'required|file|mimes:csv,xlsx,xls', // 10MB max
        // ]);

        // $file = $request->file('file');
        // $fileName = time() . '_' . $file->getClientOriginalName();
        // $filePath = $file->storeAs('uploads', $fileName, 'local');

        

        // try {
        //     Excel::queueImport(new ProductsImport, $filePath);
        // } catch (\Throwable $e) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'File upload failed: ' . $e->getMessage(),
        //     ]);
        // }

        return response()->json([
            'success' => true,
            'message' => 'File uploaded and processing started',
        ]);
    }
}
