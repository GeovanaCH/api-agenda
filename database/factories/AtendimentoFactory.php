<?php

namespace Database\Factories;

use App\Enums\AtendimentoStatus;
use App\Models\Atendimento;
use App\Models\Cliente;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Atendimento>
 */
class AtendimentoFactory extends Factory
{
    protected $model = Atendimento::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'cliente_id' => Cliente::factory(),
            'data_hora' => fake()->dateTimeBetween('+1 day', '+30 days'),
            'status' => fake()->randomElement(AtendimentoStatus::values()),
            'assunto' => fake()->sentence(3),
            'observacoes' => fake()->optional()->paragraph(),
        ];
    }
}
