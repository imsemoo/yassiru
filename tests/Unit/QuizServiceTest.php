<?php

namespace Tests\Unit;

use App\Services\QuizService;
use PHPUnit\Framework\TestCase;

class QuizServiceTest extends TestCase
{
    private QuizService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new QuizService();
    }

    public function test_grade_returns_perfect_score_for_all_correct(): void
    {
        // All correct answers are index 1
        $answers = array_fill(0, 10, 1);
        $result = $this->service->grade($answers, 'shariah');

        $this->assertEquals(10, $result['score']);
        $this->assertEquals(10, $result['total']);
        $this->assertTrue($result['passed']);
    }

    public function test_grade_returns_zero_for_all_wrong(): void
    {
        $answers = array_fill(0, 10, 0);
        $result = $this->service->grade($answers, 'shariah');

        $this->assertEquals(0, $result['score']);
        $this->assertFalse($result['passed']);
    }

    public function test_grade_passes_at_score_7(): void
    {
        // 7 correct (index 1) + 3 wrong (index 0)
        $answers = array_merge(array_fill(0, 7, 1), array_fill(0, 3, 0));
        $result = $this->service->grade($answers, 'shariah');

        $this->assertEquals(7, $result['score']);
        $this->assertTrue($result['passed']);
    }

    public function test_grade_fails_at_score_6(): void
    {
        $answers = array_merge(array_fill(0, 6, 1), array_fill(0, 4, 0));
        $result = $this->service->grade($answers, 'shariah');

        $this->assertEquals(6, $result['score']);
        $this->assertFalse($result['passed']);
    }

    public function test_get_questions_returns_10_questions(): void
    {
        foreach (['shariah', 'psychology', 'financial', 'practical'] as $track) {
            $questions = $this->service->getQuestions($track);
            $this->assertCount(10, $questions, "Track {$track} should have 10 questions");
        }
    }

    public function test_get_questions_returns_shariah_for_unknown_track(): void
    {
        $questions = $this->service->getQuestions('unknown');
        $shariahQuestions = $this->service->getQuestions('shariah');

        $this->assertEquals($shariahQuestions, $questions);
    }

    public function test_each_question_has_required_fields(): void
    {
        $questions = $this->service->getQuestions('shariah');

        foreach ($questions as $q) {
            $this->assertArrayHasKey('question', $q);
            $this->assertArrayHasKey('options', $q);
            $this->assertArrayHasKey('correct', $q);
            $this->assertCount(4, $q['options']);
        }
    }

    public function test_constants_are_correct(): void
    {
        $this->assertEquals(7, QuizService::PASSING_SCORE);
        $this->assertEquals(10, QuizService::TOTAL_QUESTIONS);
        $this->assertEquals(3, QuizService::MAX_ATTEMPTS);
    }
}
