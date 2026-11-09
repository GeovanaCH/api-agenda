<?php

namespace Tests\Feature;

use App\Enums\AtendimentoStatus;
use App\Models\Atendimento;
use App\Models\Cliente;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AtendimentoTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_access_atendimentos(): void
    {
        $this->getJson('/api/atendimentos')->assertUnauthorized();
        $this->postJson('/api/atendimentos', [])->assertUnauthorized();
    }

    public function test_it_lists_atendimentos_with_pagination(): void
    {
        $this->actingAsUser();
        Atendimento::factory()->count(3)->create();

        $this->getJson('/api/atendimentos')
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    ['id', 'cliente_id', 'data_hora', 'status', 'assunto', 'observacoes', 'cliente'],
                ],
                'links',
                'meta',
            ])
            ->assertJsonCount(3, 'data');
    }

    public function test_it_filters_by_cliente_status_and_date_range(): void
    {
        $this->actingAsUser();

        $clienteA = Cliente::factory()->create();
        $clienteB = Cliente::factory()->create();

        $match = Atendimento::factory()->create([
            'cliente_id' => $clienteA->id,
            'status' => AtendimentoStatus::Agendado,
            'data_hora' => '2026-11-15 10:00:00',
            'assunto' => 'Consulta A',
        ]);

        Atendimento::factory()->create([
            'cliente_id' => $clienteA->id,
            'status' => AtendimentoStatus::Cancelado,
            'data_hora' => '2026-11-15 11:00:00',
        ]);

        Atendimento::factory()->create([
            'cliente_id' => $clienteB->id,
            'status' => AtendimentoStatus::Agendado,
            'data_hora' => '2026-11-20 10:00:00',
        ]);

        Atendimento::factory()->create([
            'cliente_id' => $clienteA->id,
            'status' => AtendimentoStatus::Agendado,
            'data_hora' => '2026-12-01 10:00:00',
        ]);

        $response = $this->getJson(
            '/api/atendimentos?cliente_id='.$clienteA->id
            .'&status=agendado'
            .'&data_inicio=2026-11-01 00:00:00'
            .'&data_fim=2026-11-30 23:59:59'
        );

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $match->id)
            ->assertJsonPath('data.0.status', 'agendado');
    }

    public function test_it_creates_an_atendimento(): void
    {
        $this->actingAsUser();
        $cliente = Cliente::factory()->create();

        $payload = [
            'cliente_id' => $cliente->id,
            'data_hora' => '2026-11-20 14:30:00',
            'status' => 'agendado',
            'assunto' => 'Primeira consulta',
            'observacoes' => 'Trazer documentos',
        ];

        $this->postJson('/api/atendimentos', $payload)
            ->assertCreated()
            ->assertJsonPath('data.cliente_id', $cliente->id)
            ->assertJsonPath('data.status', 'agendado')
            ->assertJsonPath('data.assunto', 'Primeira consulta')
            ->assertJsonPath('data.cliente.id', $cliente->id);

        $this->assertDatabaseHas('atendimentos', [
            'cliente_id' => $cliente->id,
            'status' => 'agendado',
            'assunto' => 'Primeira consulta',
            'excluido_em' => null,
        ]);
    }

    public function test_it_validates_status_and_cliente(): void
    {
        $this->actingAsUser();

        $this->postJson('/api/atendimentos', [
            'cliente_id' => 999,
            'data_hora' => 'not-a-date',
            'status' => 'pendente',
            'assunto' => '',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['cliente_id', 'data_hora', 'status', 'assunto']);
    }

    public function test_it_rejects_soft_deleted_cliente(): void
    {
        $this->actingAsUser();
        $cliente = Cliente::factory()->create();
        $cliente->delete();

        $this->postJson('/api/atendimentos', [
            'cliente_id' => $cliente->id,
            'data_hora' => '2026-11-20 14:30:00',
            'status' => 'agendado',
            'assunto' => 'Consulta',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['cliente_id']);
    }

    public function test_it_shows_updates_and_soft_deletes_an_atendimento(): void
    {
        $this->actingAsUser();
        $atendimento = Atendimento::factory()->create([
            'status' => AtendimentoStatus::Agendado,
            'assunto' => 'Antes',
        ]);

        $this->getJson("/api/atendimentos/{$atendimento->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $atendimento->id)
            ->assertJsonPath('data.cliente.id', $atendimento->cliente_id);

        $this->putJson("/api/atendimentos/{$atendimento->id}", [
            'status' => 'concluido',
            'assunto' => 'Depois',
        ])->assertOk()
            ->assertJsonPath('data.status', 'concluido')
            ->assertJsonPath('data.assunto', 'Depois');

        $this->patchJson("/api/atendimentos/{$atendimento->id}", [
            'observacoes' => 'Finalizado',
        ])->assertOk()
            ->assertJsonPath('data.observacoes', 'Finalizado');

        $this->deleteJson("/api/atendimentos/{$atendimento->id}")
            ->assertNoContent();

        $this->assertSoftDeleted(
            table: 'atendimentos',
            data: ['id' => $atendimento->id],
            deletedAtColumn: 'excluido_em',
        );

        $this->getJson("/api/atendimentos/{$atendimento->id}")
            ->assertNotFound();
    }

    public function test_nested_route_lists_atendimentos_for_cliente(): void
    {
        $this->actingAsUser();

        $cliente = Cliente::factory()->create();
        $other = Cliente::factory()->create();

        $owned = Atendimento::factory()->count(2)->create(['cliente_id' => $cliente->id]);
        Atendimento::factory()->create(['cliente_id' => $other->id]);

        $response = $this->getJson("/api/clientes/{$cliente->id}/atendimentos");

        $response->assertOk()
            ->assertJsonCount(2, 'data');

        $ids = collect($response->json('data'))->pluck('id')->sort()->values()->all();
        $this->assertSame($owned->pluck('id')->sort()->values()->all(), $ids);
    }

    private function actingAsUser(): void
    {
        Sanctum::actingAs(User::factory()->create());
    }
}
