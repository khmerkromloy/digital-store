<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminAuthTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $branch = Branch::factory()->state(['is_default' => true, 'is_active' => true])->create();

        return User::create([
            'name' => 'Tester',
            'email' => 'admin@test.local',
            'password' => Hash::make('password'),
            'user_type' => 'admin',
            'status' => 'active',
            'branch_id' => $branch->id,
            'email_verified_at' => now(),
        ]);
    }

    public function test_login_form_renders(): void
    {
        $this->get('/login')->assertOk()->assertSee('Sign in');
    }

    public function test_admin_dashboard_requires_auth(): void
    {
        $this->get('/admin')->assertRedirect('/login');
    }

    public function test_admin_can_log_in(): void
    {
        $this->admin();

        $response = $this->post('/login', [
            'email' => 'admin@test.local',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticated();
    }

    public function test_invalid_credentials_rejected(): void
    {
        $this->admin();
        $this->post('/login', [
            'email' => 'admin@test.local',
            'password' => 'wrong',
        ])->assertSessionHasErrors('email');
    }

    public function test_admin_dashboard_renders_when_authenticated(): void
    {
        $this->actingAs($this->admin())
            ->get('/admin')
            ->assertOk()
            ->assertSee('admin.app_name', false);
    }
}
