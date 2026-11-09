<?php

namespace Tests\Feature;

use App\Models\Atendimento;
use App\Models\Cliente;
use App\Models\User;
use Database\Seeders\DemoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DemoSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_demo_seeder_creates_login_ready_data(): void
    {
        $this->seed(DemoSeeder::class);

        $user = User::query()->where('email', DemoSeeder::DEMO_EMAIL)->first();

        $this->assertNotNull($user);
        $this->assertTrue(Hash::check(DemoSeeder::DEMO_PASSWORD, $user->password));
        $this->assertGreaterThanOrEqual(2, Cliente::query()->count());
        $this->assertGreaterThanOrEqual(2, Atendimento::query()->count());

        $this->postJson('/api/login', [
            'email' => DemoSeeder::DEMO_EMAIL,
            'password' => DemoSeeder::DEMO_PASSWORD,
        ])->assertOk()
            ->assertJsonStructure(['token', 'user' => ['email']]);
    }
}
