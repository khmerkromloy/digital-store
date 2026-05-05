<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminBranchCrudTest extends TestCase
{
    use RefreshDatabase;

    private function loginAsAdmin(): User
    {
        $user = User::create([
            'name' => 'A', 'email' => 'a@a.com', 'password' => Hash::make('password'),
            'user_type' => 'admin', 'status' => 'active', 'email_verified_at' => now(),
        ]);
        $this->actingAs($user);

        return $user;
    }

    public function test_branch_index_renders(): void
    {
        $this->loginAsAdmin();
        $this->get(route('admin.branches.index'))->assertOk();
    }

    public function test_branch_data_endpoint_returns_json(): void
    {
        $this->loginAsAdmin();
        Branch::factory()->count(3)->create();

        $response = $this->getJson(route('admin.branches.data').'?draw=1&start=0&length=10');
        $response->assertOk()->assertJsonStructure(['data', 'recordsTotal', 'recordsFiltered']);
        $this->assertCount(3, $response->json('data'));
    }

    public function test_can_create_branch(): void
    {
        $this->loginAsAdmin();

        $response = $this->post(route('admin.branches.store'), [
            'name' => 'Test Branch',
            'name_kh' => 'សាខាសាកល្បង',
            'currency' => 'USD',
            'is_active' => 1,
        ]);

        $response->assertRedirect(route('admin.branches.index'));
        $this->assertDatabaseHas('branches', ['name' => 'Test Branch']);
    }

    public function test_can_update_branch(): void
    {
        $this->loginAsAdmin();
        $branch = Branch::factory()->create();

        $this->put(route('admin.branches.update', $branch), [
            'name' => 'Renamed',
            'currency' => 'USD',
        ])->assertRedirect(route('admin.branches.index'));

        $this->assertDatabaseHas('branches', ['id' => $branch->id, 'name' => 'Renamed']);
    }

    public function test_can_delete_branch(): void
    {
        $this->loginAsAdmin();
        $branch = Branch::factory()->create();

        $this->delete(route('admin.branches.destroy', $branch))
            ->assertRedirect(route('admin.branches.index'));

        $this->assertSoftDeleted('branches', ['id' => $branch->id]);
    }

    public function test_branch_validation(): void
    {
        $this->loginAsAdmin();

        $this->post(route('admin.branches.store'), [])
            ->assertSessionHasErrors('name');
    }
}
