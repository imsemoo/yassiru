<?php

namespace Tests\Feature;

use App\Models\City;
use App\Models\GroupWedding;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WeddingTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private string $token;
    private GroupWedding $wedding;
    private City $city;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RoleSeeder::class);
        $this->city = City::create(['name' => 'القاهرة', 'country' => 'مصر', 'country_code' => 'EG', 'avg_marriage_cost' => 150000, 'currency' => 'EGP']);

        $this->user = User::factory()->create(['has_certificate' => true]);
        $this->user->assignRole('user');
        $this->token = $this->user->createToken('auth')->plainTextToken;

        $this->wedding = GroupWedding::create([
            'city_id' => $this->city->id, 'title' => 'عرس اختبار', 'venue_name' => 'قاعة',
            'wedding_date' => now()->addMonths(2), 'max_grooms' => 20,
            'price_per_groom' => 15000, 'original_price' => 45000,
            'registration_deadline' => now()->addMonth(),
        ]);
    }

    public function test_can_list_weddings(): void
    {
        $response = $this->getJson('/api/weddings');
        $response->assertStatus(200);
    }

    public function test_can_view_wedding(): void
    {
        $response = $this->getJson("/api/weddings/{$this->wedding->id}");
        $response->assertStatus(200)
            ->assertJsonFragment(['title' => 'عرس اختبار']);
    }

    public function test_certified_user_can_register_for_wedding(): void
    {
        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->postJson("/api/weddings/{$this->wedding->id}/register");

        $response->assertStatus(201);

        $this->assertDatabaseHas('wedding_registrations', [
            'wedding_id' => $this->wedding->id,
            'user_id' => $this->user->id,
        ]);
    }

    public function test_uncertified_user_cannot_register(): void
    {
        $user = User::factory()->create(['has_certificate' => false]);
        $user->assignRole('user');
        $token = $user->createToken('auth')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson("/api/weddings/{$this->wedding->id}/register");

        $response->assertStatus(403);
    }

    public function test_cannot_register_twice(): void
    {
        $this->withHeader('Authorization', "Bearer {$this->token}")
            ->postJson("/api/weddings/{$this->wedding->id}/register");

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->postJson("/api/weddings/{$this->wedding->id}/register");

        $response->assertStatus(422);
    }

    public function test_vendors_endpoint(): void
    {
        $response = $this->getJson('/api/vendors');
        $response->assertStatus(200);
    }
}
