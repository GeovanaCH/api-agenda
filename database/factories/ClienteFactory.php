<?php

namespace Database\Factories;

use App\Models\Cliente;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Cliente>
 */
class ClienteFactory extends Factory
{
    protected $model = Cliente::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nome' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'cpf' => $this->uniqueValidCpf(),
            'telefone' => fake()->optional()->numerify('(##) #####-####'),
        ];
    }

    private function uniqueValidCpf(): string
    {
        do {
            $base = fake()->unique()->numerify('#########');

            $cpf = $base.$this->checkDigit($base).$this->checkDigit($base.$this->checkDigit($base));
        } while (preg_match('/^(\d)\1{10}$/', $cpf) === 1);

        return $cpf;
    }

    private function checkDigit(string $partial): string
    {
        $length = strlen($partial);
        $sum = 0;

        for ($i = 0; $i < $length; $i++) {
            $sum += (int) $partial[$i] * ($length + 1 - $i);
        }

        $digit = ($sum * 10) % 11;

        return (string) ($digit === 10 ? 0 : $digit);
    }
}
