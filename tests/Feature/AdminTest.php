<?php

namespace Tests\Feature;

use App\Models\Recommender;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private string $token;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RoleSeeder::class);

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->admin->assignRole('admin');
        $this->token = $this->admin->createToken('auth')->plainTextToken;
    }

    public function test_admin_can_access_dashboard(): void
    {
        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->getJson('/api/admin/dashboard');

        $response->assertStatus(200)
            ->assertJsonStructure(['users_count', 'recommenders_count', 'certificates_count']);
    }

    public function test_admin_can_list_users(): void
    {
        User::factory()->count(5)->create();

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->getJson('/api/admin/users');

        $response->assertStatus(200);
    }

    public function test_admin_can_approve_recommender(): void
    {
        $user = User::factory()->create(['role' => 'recommender']);
        $recommender = Recommender::create([
            'user_id' => $user->id,
            'type' => 'imam',
            'honor_pledge_signed' => true,
        ]);

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->putJson("/api/admin/recommenders/{$recommender->id}", ['approved' => true]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('recommenders', [
            'id' => $recommender->id,
            'is_approved' => true,
        ]);
    }

    public function test_non_admin_cannot_access_admin_routes(): void
    {
        $user = User::factory()->create();
        $user->assignRole('user');
        $token = $user->createToken('auth')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/admin/dashboard');

        $response->assertStatus(403);
    }
}
