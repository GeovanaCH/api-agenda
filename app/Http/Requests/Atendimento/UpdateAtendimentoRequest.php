<?php

namespace App\Http\Requests\Atendimento;

use App\Enums\AtendimentoStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAtendimentoRequest extends FormRequest
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
                'sometimes',
                'required',
                'integer',
                Rule::exists('clientes', 'id')->whereNull('excluido_em'),
            ],
            'data_hora' => ['sometimes', 'required', 'date'],
            'status' => ['sometimes', 'required', 'string', Rule::in(AtendimentoStatus::values())],
            'assunto' => ['sometimes', 'required', 'string', 'max:200'],
            'observacoes' => ['sometimes', 'nullable', 'string'],
        ];
    }
}
