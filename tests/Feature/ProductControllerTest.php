<?php

namespace Tests\Feature;

use App\Models\ProductLog;
use App\Models\ProductMasterList;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Set up fake storage for file uploads
        Storage::fake('local');

        // Fake events to avoid broadcast issues
        Event::fake();
    }

    /**
     * Test successful listing of products with pagination.
     */
    public function test_index_returns_paginated_products(): void
    {
        // Create 10 products
        for ($i = 1; $i <= 10; $i++) {
            ProductMasterList::create([
                'id' => $i,
                'type' => 'Phone',
                'brand' => 'Brand' . $i,
                'model' => 'Model' . $i,
                'capacity' => '128GB',
            ]);
        }

        $response = $this->getJson('/api/products');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'type', 'brand', 'model', 'capacity', 'quantity']
                ],
                'links',
                'meta',
                'success'
            ])
            ->assertJsonCount(3, 'data') // Paginated at 3 per page in controller
            ->assertJson(['success' => true]);
    }

    /**
     * Test searching products by various fields.
     */
    public function test_index_can_search_products(): void
    {
        ProductMasterList::create([
            'id' => 999,
            'type' => 'Tablet',
            'brand' => 'SpecificBrand',
            'model' => 'UniqueModel',
            'capacity' => '256GB',
        ]);

        ProductMasterList::create([
            'id' => 1000,
            'type' => 'Phone',
            'brand' => 'OtherBrand',
            'model' => 'OtherModel',
            'capacity' => '64GB',
        ]);

        // Search by ID
        $response = $this->getJson('/api/products?search=999');
        $response->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', 999);

        // Search by Type
        $response = $this->getJson('/api/products?search=Tablet');
        $response->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.type', 'Tablet');

        // Search by Brand
        $response = $this->getJson('/api/products?search=SpecificBrand');
        $response->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.brand', 'SpecificBrand');

        // Search by Model
        $response = $this->getJson('/api/products?search=UniqueModel');
        $response->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.model', 'UniqueModel');
    }

    /**
     * Test successful file upload with valid xlsx file.
     */
    public function test_upload_file_successfully(): void
    {
        // Mock Excel import to prevent actual processing
        Excel::fake();

        // Create a fake file
        $file = UploadedFile::fake()->create('product_status_list.xlsx', 100, 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        $response = $this->postJson('/api/products/upload-file', [
            'file' => $file,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'File uploaded and processing started',
            ]);
    }

    /**
     * Test upload fails when file is missing.
     */
    public function test_upload_file_validation_fails_when_file_is_missing(): void
    {
        $response = $this->postJson('/api/products/upload-file', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['file']);
    }

    /**
     * Test upload fails with invalid file type.
     */
    public function test_upload_file_validation_fails_with_invalid_file_type(): void
    {
        $file = UploadedFile::fake()->create('document.pdf', 100);

        $response = $this->postJson('/api/products/upload-file', [
            'file' => $file,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['file']);
    }

    /**
     * Test successful listing of product logs.
     */
    public function test_logs_returns_paginated_logs(): void
    {
        $product = ProductMasterList::create([
            'id' => 1,
            'type' => 'Phone',
            'brand' => 'Brand1',
            'model' => 'Model1',
            'capacity' => '128GB',
        ]);

        for ($i = 1; $i <= 15; $i++) {
            ProductLog::create([
                'product_master_list_id' => $product->id,
                'status' => 'imported',
                'quantity' => 1,
            ]);
        }

        $response = $this->getJson('/api/products/logs');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['product_id', 'status', 'quantity']
                ],
                'links',
                'meta',
                'success'
            ])
            ->assertJsonCount(10, 'data') // Paginated at 10 per page in controller
            ->assertJson(['success' => true]);
    }
}
