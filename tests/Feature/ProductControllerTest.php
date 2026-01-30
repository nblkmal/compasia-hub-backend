<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Set up fake storage for file uploads
        Storage::fake('local');
    }

    /**
     * Test successful file upload with valid xlsx file.
     */
    public function test_upload_file_successfully(): void
    {
        // Mock Excel import to prevent actual processing
        Excel::fake();

        // Copy the actual test file to simulate upload
        $testFilePath = base_path('public/product_status_list.xlsx');
        $uploadedFile = new UploadedFile(
            $testFilePath,
            'product_status_list.xlsx',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            null,
            true // Mark as test file
        );

        $response = $this->postJson('/api/upload-file', [
            'file' => $uploadedFile,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'File uploaded and processing started',
            ]);

        // Assert file was stored
        $files = Storage::disk('local')->files('uploads');
        $this->assertCount(1, $files);
        $this->assertStringContainsString('product_status_list.xlsx', $files[0]);
    }

    /**
     * Test upload fails when file is missing.
     */
    public function test_upload_file_validation_fails_when_file_is_missing(): void
    {
        $response = $this->postJson('/api/upload-file', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['file']);
    }

    /**
     * Test upload fails with invalid file type.
     */
    public function test_upload_file_validation_fails_with_invalid_file_type(): void
    {
        Storage::fake('local');

        $file = UploadedFile::fake()->create('document.pdf', 100);

        $response = $this->postJson('/api/upload-file', [
            'file' => $file,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['file']);
    }

    /**
     * Test upload with valid CSV file.
     */
    public function test_upload_file_accepts_csv_format(): void
    {
        Excel::fake();
        Storage::fake('local');

        $file = UploadedFile::fake()->create('products.csv', 100, 'text/csv');

        $response = $this->postJson('/api/upload-file', [
            'file' => $file,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'File uploaded and processing started',
            ]);
    }

    /**
     * Test upload with valid XLS file.
     */
    public function test_upload_file_accepts_xls_format(): void
    {
        Excel::fake();
        Storage::fake('local');

        $file = UploadedFile::fake()->create('products.xls', 100, 'application/vnd.ms-excel');

        $response = $this->postJson('/api/upload-file', [
            'file' => $file,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'File uploaded and processing started',
            ]);
    }
}
