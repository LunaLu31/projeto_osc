<?php

namespace Tests\Feature;

use App\Models\Adm;
use App\Models\Demanda;
use App\Models\Osc;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminPortalTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_first_admin_can_create_an_account_and_enter_the_portal(): void
    {
        $this->get('/admin')->assertOk()->assertViewIs('admin.setup');

        $response = $this->post('/admin/setup', [
            'nome' => 'Admin Inicial',
            'email' => 'admin@example.com',
            'password' => 'senha-segura-123',
            'password_confirmation' => 'senha-segura-123',
        ]);

        $response->assertRedirect('/admin/dashboard');
        $this->assertDatabaseHas('adms', ['email' => 'admin@example.com']);
        $this->assertNotNull(session('adm_id'));
        $this->get('/admin/setup')->assertRedirect('/admin');
    }

    public function test_an_admin_can_log_in_with_registered_credentials(): void
    {
        $admin = $this->createAdmin();

        $this->post('/admin/login', [
            'email' => $admin->email,
            'password' => 'senha-segura',
        ])->assertRedirect('/admin/dashboard');

        $this->assertSame($admin->id_admin, session('adm_id'));
    }

    public function test_admin_portal_only_lists_its_own_organizations_and_demands(): void
    {
        $admin = $this->createAdmin('admin@example.com');
        $otherAdmin = $this->createAdmin('other@example.com');
        $ownedOsc = $this->createOsc($admin, 'OSC da minha conta');
        $otherOsc = $this->createOsc($otherAdmin, 'OSC de outra conta');

        Demanda::create([
            'id_osc' => $ownedOsc->id_osc,
            'titulo' => 'Projeto da minha conta',
            'status' => 'aberta',
        ]);
        Demanda::create([
            'id_osc' => $otherOsc->id_osc,
            'titulo' => 'Projeto de outra conta',
            'status' => 'aberta',
        ]);

        $this->withSession(['adm_id' => $admin->id_admin])
            ->get('/admin/dashboard')
            ->assertOk()
            ->assertViewIs('admin.dashboard')
            ->assertSee('OSC da minha conta')
            ->assertSee('Projeto da minha conta')
            ->assertDontSee('OSC de outra conta')
            ->assertDontSee('Projeto de outra conta');
    }

    public function test_admin_can_register_an_osc_under_its_own_account(): void
    {
        $admin = $this->createAdmin();

        $this->withSession(['adm_id' => $admin->id_admin])
            ->post('/admin/oscs', [
                'nome' => 'Nova Organização',
                'area_atuacao' => 'Educação',
                'descricao' => 'Atuação comunitária.',
                'endereco' => 'Camaçari',
                'telefone' => '(71) 99999-9999',
                'email' => 'nova@example.com',
                'password' => 'senha-osc-123',
                'password_confirmation' => 'senha-osc-123',
            ])
            ->assertRedirect('/admin/dashboard');

        $this->assertDatabaseHas('oscs', [
            'id_admin' => $admin->id_admin,
            'nome' => 'Nova Organização',
            'email' => 'nova@example.com',
        ]);

        $this->assertTrue(Hash::check(
            'senha-osc-123',
            Osc::where('email', 'nova@example.com')->value('senha_hash'),
        ));
    }

    public function test_a_guest_cannot_register_an_osc(): void
    {
        $admin = $this->createAdmin();

        $this->post('/admin/oscs', [
            'nome' => 'OSC não autorizada',
            'email' => 'nao-autorizada@example.com',
            'password' => 'senha-osc-123',
            'password_confirmation' => 'senha-osc-123',
        ])->assertRedirect('/admin');

        $this->assertDatabaseMissing('oscs', ['email' => 'nao-autorizada@example.com']);
    }

    private function createAdmin(string $email = 'admin@example.com'): Adm
    {
        static $sequence = 0;
        $sequence++;

        return Adm::create([
            'nome' => 'Admin ' . $sequence,
            'email' => $email,
            'senha_hash' => Hash::make('senha-segura'),
        ]);
    }

    private function createOsc(Adm $admin, string $nome): Osc
    {
        static $sequence = 0;
        $sequence++;

        return Osc::create([
            'id_admin' => $admin->id_admin,
            'nome' => $nome,
            'email' => "osc{$sequence}@example.com",
            'senha_hash' => Hash::make('senha-osc'),
        ]);
    }
}