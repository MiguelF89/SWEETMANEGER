<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\BoletoReadRequest;
use App\Services\BoletoReaderService;
use Illuminate\Http\JsonResponse;

class BoletoApiController extends Controller
{
    public function __construct(
        private readonly BoletoReaderService $boletoReader
    ) {}

    /**
     * POST /api/boleto/read
     *
     * Accepts: multipart/form-data with a 'file' field (image or PDF).
     * Returns: parsed boleto data as JSON.
     */
    public function read(BoletoReadRequest $request): JsonResponse
    {
        try {
            $data = $this->boletoReader->read($request->file('file'));

            return response()->json([
                'success' => true,
                'data'    => $data,
            ]);
        } catch (\RuntimeException $e) {
            return response()->json([
                'success' => false,
                'error'   => $e->getMessage(),
            ], 422);
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'success' => false,
                'error'   => 'Erro interno ao processar o boleto.',
            ], 500);
        }
    }
}
