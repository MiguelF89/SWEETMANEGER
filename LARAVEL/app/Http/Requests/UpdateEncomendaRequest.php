<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEncomendaRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check();
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'repassado_cliente' => $this->has('repassado_cliente'),
            'pago' => $this->has('pago'),
        ]);
    }

    public function rules()
    {
        return [
            'cliente' => ['required', 'string', 'max:255'],
            'descricao' => ['required', 'string'],
            'quantidade' => ['required', 'integer', 'min:1'],
            'valor' => ['required', 'numeric', 'min:0'],
            'data_entrega' => ['required', 'date', 'date_format:Y-m-d'],
            'horario_entrega' => ['nullable', 'date_format:H:i'],
            'link_pagamento' => ['nullable', 'url', 'max:255'],
            'repassado_cliente' => ['boolean'],
            'pago' => ['boolean'],
            'observacoes' => ['nullable', 'string'],
        ];
    }
}
