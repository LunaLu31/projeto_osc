<?php

namespace Tests\Feature;

use App\Models\Adm;
use App\Models\Osc;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class OscAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_an_osc_can_log_in_with_its_registered_credentials(): void
    {
        $osc = $this->createOsc();

        $response = $this->postJson('/osc/login', [
            'email' => $osc->email,
            'password' => 'senha-segura',
        ]);

        $response->assertOk()
            ->assertJsonPath('osc.id', $osc->id_osc)
            ->assertJsonPath('osc.nome', $osc->nome);

        $this->assertAuthenticatedOsc($osc->id_osc);
    }

    public function test_an_osc_cannot_log_in_with_an_invalid_password(): void
    {
        $osc = $this->createOsc();

        $response = $this->postJson('/osc/login', [
            'email' => $osc->email,
            'password' => 'senha-incorreta',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);

        $this->assertGuestOsc();
    }

    public function test_an_authenticated_osc_can_log_out(): void
    {
        $osc = $this->createOsc();
        $this->withSession(['osc_id' => $osc->id_osc]);

        $this->postJson('/osc/logout')->assertNoContent();

        $this->assertGuestOsc();
    }

    private function createOsc(): Osc
    {
        $admin = Adm::create([
            'nome' => 'Administrador',
            'email' => 'admin@example.com',
            'senha_hash' => Hash::make('senha-admin'),
        ]);

        return Osc::create([
            'id_admin' => $admin->id_admin,
            'nome' => 'Organização Exemplo',
            'email' => 'osc@example.com',
            'senha_hash' => Hash::make('senha-segura'),
        ]);
    }

    private function assertAuthenticatedOsc(int $id): void
    {
        $this->assertSame($id, session('osc_id'));
    }

    private function assertGuestOsc(): void
    {
        $this->assertNull(session('osc_id'));
    }
}
