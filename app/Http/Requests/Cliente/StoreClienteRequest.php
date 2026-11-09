<?php

namespace App\Http\Requests\Cliente;

use App\Rules\ValidCpf;
use App\Support\Cpf;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreClienteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('cpf')) {
            $this->merge([
                'cpf' => Cpf::clean((string) $this->input('cpf')),
            ]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'nome' => ['required', 'string', 'max:120'],
            'email' => [
                'required',
                'string',
                'email',
                'max:150',
                Rule::unique('clientes', 'email')->whereNull('excluido_em'),
            ],
            'cpf' => [
                'required',
                'string',
                'size:11',
                new ValidCpf,
                Rule::unique('clientes', 'cpf')->whereNull('excluido_em'),
            ],
            'telefone' => ['nullable', 'string', 'max:20'],
        ];
    }
}
