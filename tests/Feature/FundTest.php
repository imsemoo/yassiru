<?php

namespace Tests\Feature;

use App\Models\City;
use App\Models\FundCircle;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FundTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private string $token;
    private City $city;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RoleSeeder::class);
        $this->city = City::create(['name' => 'القاهرة', 'country' => 'مصر', 'country_code' => 'EG', 'avg_marriage_cost' => 150000, 'currency' => 'EGP']);

        $this->user = User::factory()->create(['has_certificate' => true]);
        $this->user->assignRole('user');
        $this->token = $this->user->createToken('auth')->plainTextToken;
    }

    public function test_can_list_circles(): void
    {
        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->getJson('/api/circles');

        $response->assertStatus(200);
    }

    public function test_certified_user_can_create_circle(): void
    {
        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->postJson('/api/circles', [
                'name' => 'حلقة الاختبار',
                'city_id' => $this->city->id,
                'max_members' => 10,
                'monthly_amount' => 500,
                'currency' => 'EGP',
                'payout_method' => 'priority',
            ]);

        $response->assertStatus(201)
            ->assertJsonFragment(['name' => 'حلقة الاختبار']);

        $this->assertDatabaseHas('fund_circles', ['name' => 'حلقة الاختبار']);
        $this->assertDatabaseHas('circle_members', [
            'user_id' => $this->user->id,
            'payout_order' => 1,
        ]);
    }

    public function test_uncertified_user_cannot_create_circle(): void
    {
        $user = User::factory()->create(['has_certificate' => false]);
        $user->assignRole('user');
        $token = $user->createToken('auth')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/circles', [
                'name' => 'حلقة',
                'city_id' => $this->city->id,
                'max_members' => 10,
                'monthly_amount' => 500,
                'currency' => 'EGP',
                'payout_method' => 'priority',
            ]);

        $response->assertStatus(403);
    }

    public function test_can_join_forming_circle(): void
    {
        $circle = FundCircle::create([
            'name' => 'حلقة', 'city_id' => $this->city->id, 'created_by' => $this->user->id,
            'max_members' => 10, 'monthly_amount' => 500, 'currency' => 'EGP',
            'cycle_months' => 10, 'payout_method' => 'priority',
        ]);

        $joiner = User::factory()->create();
        $joiner->assignRole('user');
        $joinerToken = $joiner->createToken('auth')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$joinerToken}")
            ->postJson("/api/circles/{$circle->id}/join");

        $response->assertStatus(200);
    }

    public function test_cannot_join_active_circle(): void
    {
        $circle = FundCircle::create([
            'name' => 'حلقة', 'city_id' => $this->city->id, 'created_by' => $this->user->id,
            'max_members' => 10, 'monthly_amount' => 500, 'currency' => 'EGP',
            'cycle_months' => 10, 'payout_method' => 'priority', 'status' => 'active',
        ]);

        $joiner = User::factory()->create();
        $joinerToken = $joiner->createToken('auth')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$joinerToken}")
            ->postJson("/api/circles/{$circle->id}/join");

        $response->assertStatus(422);
    }
}
