<?php

namespace Tests\Feature;

use App\Models\CounselingSession;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CounselingTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private string $token;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RoleSeeder::class);

        $this->user = User::factory()->create(['has_certificate' => true]);
        $this->user->assignRole('user');
        $this->token = $this->user->createToken('auth')->plainTextToken;
    }

    public function test_user_can_list_sessions(): void
    {
        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->getJson('/api/counseling');

        $response->assertStatus(200);
    }

    public function test_user_can_book_session(): void
    {
        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->postJson('/api/counseling', [
                'type' => 'individual',
                'scheduled_at' => now()->addDays(3)->setHour(10)->setMinute(0)->toDateTimeString(),
                'notes' => 'استشارة قبل الزواج',
            ]);

        $response->assertStatus(201)
            ->assertJsonFragment(['type' => 'individual']);

        $this->assertDatabaseHas('counseling_sessions', [
            'user_id' => $this->user->id,
            'type' => 'individual',
        ]);
    }

    public function test_user_cannot_book_duplicate_session(): void
    {
        $date = now()->addDays(3)->setHour(10)->setMinute(0)->toDateTimeString();

        $this->withHeader('Authorization', "Bearer {$this->token}")
            ->postJson('/api/counseling', [
                'type' => 'individual',
                'scheduled_at' => $date,
            ]);

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->postJson('/api/counseling', [
                'type' => 'group',
                'scheduled_at' => $date,
            ]);

        $response->assertStatus(422);
    }

    public function test_user_can_cancel_own_session(): void
    {
        $session = CounselingSession::create([
            'user_id' => $this->user->id,
            'type' => 'individual',
            'scheduled_at' => now()->addDays(5),
            'status' => 'scheduled',
        ]);

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->putJson("/api/counseling/{$session->id}/cancel");

        $response->assertStatus(200);
        $this->assertDatabaseHas('counseling_sessions', ['id' => $session->id, 'status' => 'cancelled']);
    }

    public function test_user_cannot_cancel_others_session(): void
    {
        $otherUser = User::factory()->create();
        $session = CounselingSession::create([
            'user_id' => $otherUser->id,
            'type' => 'individual',
            'scheduled_at' => now()->addDays(5),
            'status' => 'scheduled',
        ]);

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->putJson("/api/counseling/{$session->id}/cancel");

        $response->assertStatus(403);
    }

    public function test_can_check_available_slots(): void
    {
        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->getJson('/api/counseling/slots?' . http_build_query([
                'date' => now()->addDays(3)->toDateString(),
                'type' => 'individual',
            ]));

        $response->assertStatus(200);
    }

    public function test_admin_can_view_all_sessions(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $adminToken = $admin->createToken('auth')->plainTextToken;

        CounselingSession::create([
            'user_id' => $this->user->id,
            'type' => 'individual',
            'scheduled_at' => now()->addDays(5),
        ]);

        $response = $this->withHeader('Authorization', "Bearer {$adminToken}")
            ->getJson('/api/admin/counseling');

        $response->assertStatus(200);
    }

    public function test_admin_can_complete_session(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $adminToken = $admin->createToken('auth')->plainTextToken;

        $session = CounselingSession::create([
            'user_id' => $this->user->id,
            'type' => 'individual',
            'scheduled_at' => now()->subDay(),
            'status' => 'scheduled',
        ]);

        $response = $this->withHeader('Authorization', "Bearer {$adminToken}")
            ->putJson("/api/admin/counseling/{$session->id}/complete");

        $response->assertStatus(200);
        $this->assertDatabaseHas('counseling_sessions', ['id' => $session->id, 'status' => 'completed']);
    }
}
