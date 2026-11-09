<?php

namespace App\Http\Requests\Cliente;

use App\Models\Cliente;
use App\Rules\ValidCpf;
use App\Support\Cpf;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateClienteRequest extends FormRequest
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
        /** @var Cliente $cliente */
        $cliente = $this->route('cliente');

        return [
            'nome' => ['sometimes', 'required', 'string', 'max:120'],
            'email' => [
                'sometimes',
                'required',
                'string',
                'email',
                'max:150',
                Rule::unique('clientes', 'email')
                    ->whereNull('excluido_em')
                    ->ignore($cliente->id),
            ],
            'cpf' => [
                'sometimes',
                'required',
                'string',
                'size:11',
                new ValidCpf,
                Rule::unique('clientes', 'cpf')
                    ->whereNull('excluido_em')
                    ->ignore($cliente->id),
            ],
            'telefone' => ['sometimes', 'nullable', 'string', 'max:20'],
        ];
    }
}
