<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private string $token;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RoleSeeder::class);
        $this->seed(\Database\Seeders\CourseSeeder::class);

        $this->user = User::factory()->create();
        $this->user->assignRole('user');
        $this->token = $this->user->createToken('auth')->plainTextToken;
    }

    public function test_can_list_courses(): void
    {
        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->getJson('/api/courses');

        $response->assertStatus(200);
    }

    public function test_can_view_single_course(): void
    {
        $course = Course::first();

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->getJson("/api/courses/{$course->id}");

        $response->assertStatus(200)
            ->assertJsonFragment(['title' => $course->title]);
    }

    public function test_can_view_lesson(): void
    {
        $course = Course::first();
        $lesson = Lesson::where('course_id', $course->id)->first();

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->getJson("/api/courses/{$course->id}/lessons/{$lesson->id}");

        $response->assertStatus(200);
    }

    public function test_can_complete_lesson(): void
    {
        $course = Course::first();
        $lesson = Lesson::where('course_id', $course->id)->first();

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->postJson("/api/courses/{$course->id}/lessons/{$lesson->id}/complete");

        $response->assertStatus(200);

        $this->assertDatabaseHas('course_progress', [
            'user_id' => $this->user->id,
            'lesson_id' => $lesson->id,
        ]);
    }

    public function test_can_get_quiz(): void
    {
        $course = Course::first();

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->getJson("/api/courses/{$course->id}/quiz");

        $response->assertStatus(200);
    }

    public function test_unauthenticated_cannot_access_courses(): void
    {
        $response = $this->getJson('/api/courses');
        $response->assertStatus(401);
    }
}
