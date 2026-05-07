<?php

namespace Tests\Unit;

use App\Services\BoletoReaderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class BoletoReaderServiceTest extends TestCase
{
    /**
     * Test basic functionality of BoletoReaderService.
     */
    public function test_service_can_be_instantiated()
    {
        $service = new BoletoReaderService();
        $this->assertInstanceOf(BoletoReaderService::class, $service);
    }

    /**
     * Test that the service can handle a mock file (without actually processing).
     */
    public function test_service_handles_uploaded_file()
    {
        $service = new BoletoReaderService();

        // Create a mock uploaded file
        $file = UploadedFile::fake()->image('boleto.jpg');

        // This should not throw an exception, even if processing fails
        try {
            $result = $service->read($file);
            $this->assertIsArray($result);
            $this->assertArrayHasKey('barcode', $result);
        } catch (\RuntimeException $e) {
            // Expected if tools are not available or image processing fails
            $this->assertTrue(
                str_contains($e->getMessage(), 'não foi possível') ||
                str_contains($e->getMessage(), 'Nenhum código de barras')
            );
        }
    }
}