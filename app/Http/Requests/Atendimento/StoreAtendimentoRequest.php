<?php

namespace App\Http\Requests\Atendimento;

use App\Enums\AtendimentoStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAtendimentoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'cliente_id' => [
                'required',
                'integer',
                Rule::exists('clientes', 'id')->whereNull('excluido_em'),
            ],
            'data_hora' => ['required', 'date'],
            'status' => ['required', 'string', Rule::in(AtendimentoStatus::values())],
            'assunto' => ['required', 'string', 'max:200'],
            'observacoes' => ['nullable', 'string'],
        ];
    }
}
