<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ClienteTest extends TestCase
{
    use RefreshDatabase;

    private const VALID_CPF = '52998224725';

    private const ANOTHER_VALID_CPF = '39053344705';

    public function test_guests_cannot_access_clientes(): void
    {
        $this->getJson('/api/clientes')->assertUnauthorized();
        $this->postJson('/api/clientes', [])->assertUnauthorized();
    }

    public function test_it_lists_clientes_with_pagination(): void
    {
        $this->actingAsUser();
        Cliente::factory()->count(3)->create();

        $response = $this->getJson('/api/clientes');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    ['id', 'nome', 'email', 'cpf', 'telefone', 'created_at', 'updated_at'],
                ],
                'links',
                'meta',
            ])
            ->assertJsonCount(3, 'data');
    }

    public function test_it_searches_clientes_by_nome_email_or_cpf(): void
    {
        $this->actingAsUser();

        $match = Cliente::factory()->create([
            'nome' => 'Ana Paula',
            'email' => 'ana@example.com',
            'cpf' => self::VALID_CPF,
        ]);
        Cliente::factory()->create([
            'nome' => 'Bruno Silva',
            'email' => 'bruno@example.com',
            'cpf' => self::ANOTHER_VALID_CPF,
        ]);

        $this->getJson('/api/clientes?q=Ana')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $match->id);

        $this->getJson('/api/clientes?q=ana@example.com')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $match->id);

        $this->getJson('/api/clientes?q=529.982.247-25')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $match->id);
    }

    public function test_it_creates_a_cliente(): void
    {
        $this->actingAsUser();

        $payload = [
            'nome' => 'Geovana Chaves',
            'email' => 'geovana@example.com',
            'cpf' => '529.982.247-25',
            'telefone' => '(11) 98888-7777',
        ];

        $response = $this->postJson('/api/clientes', $payload);

        $response->assertCreated()
            ->assertJsonPath('data.nome', 'Geovana Chaves')
            ->assertJsonPath('data.email', 'geovana@example.com')
            ->assertJsonPath('data.cpf', self::VALID_CPF);

        $this->assertDatabaseHas('clientes', [
            'email' => 'geovana@example.com',
            'cpf' => self::VALID_CPF,
            'excluido_em' => null,
        ]);
    }

    public function test_it_validates_required_fields_and_cpf(): void
    {
        $this->actingAsUser();

        $response = $this->postJson('/api/clientes', [
            'nome' => '',
            'email' => 'not-an-email',
            'cpf' => '11111111111',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['nome', 'email', 'cpf']);
    }

    public function test_it_rejects_duplicate_email_and_cpf_among_active(): void
    {
        $this->actingAsUser();

        Cliente::factory()->create([
            'email' => 'taken@example.com',
            'cpf' => self::VALID_CPF,
        ]);

        $this->postJson('/api/clientes', [
            'nome' => 'Outro',
            'email' => 'taken@example.com',
            'cpf' => self::ANOTHER_VALID_CPF,
        ])->assertUnprocessable()->assertJsonValidationErrors(['email']);

        $this->postJson('/api/clientes', [
            'nome' => 'Outro',
            'email' => 'other@example.com',
            'cpf' => self::VALID_CPF,
        ])->assertUnprocessable()->assertJsonValidationErrors(['cpf']);
    }

    public function test_soft_deleted_email_and_cpf_can_be_reused(): void
    {
        $this->actingAsUser();

        $cliente = Cliente::factory()->create([
            'email' => 'reuse@example.com',
            'cpf' => self::VALID_CPF,
        ]);
        $cliente->delete();

        $this->postJson('/api/clientes', [
            'nome' => 'Novo Cliente',
            'email' => 'reuse@example.com',
            'cpf' => '529.982.247-25',
            'telefone' => null,
        ])->assertCreated()
            ->assertJsonPath('data.email', 'reuse@example.com')
            ->assertJsonPath('data.cpf', self::VALID_CPF);
    }

    public function test_it_shows_updates_and_soft_deletes_a_cliente(): void
    {
        $this->actingAsUser();

        $cliente = Cliente::factory()->create([
            'nome' => 'Antes',
            'email' => 'antes@example.com',
            'cpf' => self::VALID_CPF,
        ]);

        $this->getJson("/api/clientes/{$cliente->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $cliente->id);

        $this->putJson("/api/clientes/{$cliente->id}", [
            'nome' => 'Depois',
            'email' => 'depois@example.com',
        ])->assertOk()
            ->assertJsonPath('data.nome', 'Depois')
            ->assertJsonPath('data.email', 'depois@example.com');

        $this->patchJson("/api/clientes/{$cliente->id}", [
            'telefone' => '11999990000',
        ])->assertOk()
            ->assertJsonPath('data.telefone', '11999990000');

        $this->deleteJson("/api/clientes/{$cliente->id}")
            ->assertNoContent();

        $this->assertSoftDeleted(
            table: 'clientes',
            data: ['id' => $cliente->id],
            deletedAtColumn: 'excluido_em',
        );

        $this->getJson("/api/clientes/{$cliente->id}")
            ->assertNotFound();
    }

    private function actingAsUser(): void
    {
        Sanctum::actingAs(User::factory()->create());
    }
}
