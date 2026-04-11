<?php

namespace Tests\Feature;

use App\Models\Candidate;
use App\Models\City;
use App\Models\Recommender;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecommenderTest extends TestCase
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

    public function test_user_can_register_as_recommender(): void
    {
        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->postJson('/api/recommender/register', [
                'type' => 'imam',
                'institution' => 'مسجد الرحمة',
                'bio' => 'إمام مسجد منذ 10 سنوات',
            ]);

        $response->assertStatus(201)
            ->assertJsonFragment(['type' => 'imam']);

        $this->assertDatabaseHas('recommenders', [
            'user_id' => $this->user->id,
            'type' => 'imam',
        ]);
    }

    public function test_cannot_register_as_recommender_twice(): void
    {
        Recommender::create([
            'user_id' => $this->user->id,
            'type' => 'imam',
            'honor_pledge_signed' => true,
        ]);

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->postJson('/api/recommender/register', [
                'type' => 'teacher',
                'bio' => 'معلم',
            ]);

        $response->assertStatus(422);
    }

    public function test_approved_recommender_can_add_candidate(): void
    {
        $recommender = Recommender::create([
            'user_id' => $this->user->id,
            'type' => 'imam',
            'is_approved' => true,
            'honor_pledge_signed' => true,
        ]);
        $this->user->syncRoles(['recommender']);

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->postJson('/api/recommender/candidates', [
                'name' => 'أحمد محمد',
                'gender' => 'male',
                'age' => 28,
                'city_id' => $this->city->id,
                'marital_status' => 'single',
                'religiosity_level' => 'committed',
                'guardian_name' => 'محمد أحمد',
                'guardian_phone' => '+201001234567',
                'guardian_relation' => 'أب',
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('candidates', ['name' => 'أحمد محمد']);
    }

    public function test_unapproved_recommender_cannot_add_candidate(): void
    {
        Recommender::create([
            'user_id' => $this->user->id,
            'type' => 'imam',
            'is_approved' => false,
            'honor_pledge_signed' => true,
        ]);
        $this->user->syncRoles(['recommender']);

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->postJson('/api/recommender/candidates', [
                'name' => 'أحمد',
                'gender' => 'male',
                'age' => 28,
                'city_id' => $this->city->id,
                'marital_status' => 'single',
                'religiosity_level' => 'committed',
                'guardian_name' => 'محمد',
                'guardian_phone' => '+201001234567',
                'guardian_relation' => 'أب',
            ]);

        $response->assertStatus(403);
    }

    public function test_recommender_can_view_dashboard(): void
    {
        Recommender::create([
            'user_id' => $this->user->id,
            'type' => 'imam',
            'is_approved' => true,
            'honor_pledge_signed' => true,
        ]);
        $this->user->syncRoles(['recommender']);

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->getJson('/api/recommender/dashboard');

        $response->assertStatus(200)
            ->assertJsonStructure(['recommender', 'candidates', 'recommendations']);
    }

    public function test_recommender_can_update_candidate(): void
    {
        $recommender = Recommender::create([
            'user_id' => $this->user->id,
            'type' => 'imam',
            'is_approved' => true,
            'honor_pledge_signed' => true,
        ]);
        $this->user->syncRoles(['recommender']);

        $candidate = Candidate::create([
            'recommender_id' => $recommender->id,
            'name' => 'أحمد',
            'gender' => 'male',
            'age' => 28,
            'city_id' => $this->city->id,
            'marital_status' => 'single',
            'religiosity_level' => 'committed',
            'guardian_name' => 'محمد',
            'guardian_phone' => '+201001234567',
            'guardian_relation' => 'أب',
        ]);

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->putJson("/api/recommender/candidates/{$candidate->id}", [
                'age' => 30,
                'occupation' => 'مهندس',
            ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('candidates', ['id' => $candidate->id, 'age' => 30, 'occupation' => 'مهندس']);
    }

    public function test_cannot_update_other_recommenders_candidate(): void
    {
        $otherUser = User::factory()->create();
        $otherRecommender = Recommender::create([
            'user_id' => $otherUser->id,
            'type' => 'imam',
            'is_approved' => true,
            'honor_pledge_signed' => true,
        ]);

        Recommender::create([
            'user_id' => $this->user->id,
            'type' => 'teacher',
            'is_approved' => true,
            'honor_pledge_signed' => true,
        ]);
        $this->user->syncRoles(['recommender']);

        $candidate = Candidate::create([
            'recommender_id' => $otherRecommender->id,
            'name' => 'فاطمة',
            'gender' => 'female',
            'age' => 25,
            'city_id' => $this->city->id,
            'marital_status' => 'single',
            'religiosity_level' => 'committed',
            'guardian_name' => 'أحمد',
            'guardian_phone' => '+201001234567',
            'guardian_relation' => 'أب',
        ]);

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->putJson("/api/recommender/candidates/{$candidate->id}", ['age' => 26]);

        $response->assertStatus(403);
    }
}
