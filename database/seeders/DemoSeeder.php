<?php

namespace Database\Seeders;

use App\Enums\AtendimentoStatus;
use App\Models\Atendimento;
use App\Models\Cliente;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    public const DEMO_EMAIL = 'demo@api-agenda.test';

    public const DEMO_PASSWORD = 'Password1!';

    public function run(): void
    {
        $user = User::query()->updateOrCreate(
            ['email' => self::DEMO_EMAIL],
            [
                'name' => 'Demo User',
                'password' => Hash::make(self::DEMO_PASSWORD),
                'email_verified_at' => now(),
            ]
        );

        $ana = Cliente::query()->updateOrCreate(
            ['email' => 'ana@example.com'],
            [
                'nome' => 'Ana Paula',
                'cpf' => '52998224725',
                'telefone' => '(11) 98888-7777',
            ]
        );

        $bruno = Cliente::query()->updateOrCreate(
            ['email' => 'bruno@example.com'],
            [
                'nome' => 'Bruno Silva',
                'cpf' => '39053344705',
                'telefone' => '(21) 97777-6666',
            ]
        );

        Atendimento::query()->updateOrCreate(
            [
                'cliente_id' => $ana->id,
                'assunto' => 'Primeira consulta',
            ],
            [
                'data_hora' => now()->addDays(3)->setTime(14, 30),
                'status' => AtendimentoStatus::Agendado,
                'observacoes' => 'Trazer documentos',
            ]
        );

        Atendimento::query()->updateOrCreate(
            [
                'cliente_id' => $bruno->id,
                'assunto' => 'Retorno',
            ],
            [
                'data_hora' => now()->subDays(2)->setTime(10, 0),
                'status' => AtendimentoStatus::Concluido,
                'observacoes' => 'Cliente compareceu',
            ]
        );

        $this->command?->info(sprintf(
            'Demo ready: user id=%d email=%s password=%s',
            $user->id,
            self::DEMO_EMAIL,
            self::DEMO_PASSWORD
        ));
    }
}
