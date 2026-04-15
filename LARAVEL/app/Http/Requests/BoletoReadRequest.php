<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BoletoReadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Auth handled at route level (auth:sanctum middleware)
    }

    public function rules(): array
    {
        return [
            'file' => [
                'required',
                'file',
                // Max 10 MB — boleto images are never this large, but PDFs can be
                'max:10240',
                'mimes:jpg,jpeg,png,gif,webp,pdf',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'file.required' => 'Nenhum arquivo enviado.',
            'file.file'     => 'O campo enviado não é um arquivo válido.',
            'file.max'      => 'O arquivo não pode ser maior que 10MB.',
            'file.mimes'    => 'Formato não suportado. Use JPG, PNG, ou PDF.',
        ];
    }
}
