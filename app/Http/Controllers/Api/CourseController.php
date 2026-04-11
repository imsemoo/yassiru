<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SubmitQuizRequest;
use App\Models\Course;
use App\Models\CourseProgress;
use App\Models\Lesson;
use App\Services\CertificateService;
use App\Services\QuizService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CourseController extends Controller
{
    public function __construct(
        private QuizService $quizService,
        private CertificateService $certificateService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $courses = Cache::remember('courses:list', 86400, function () {
            return Course::where('is_active', true)
                ->with('lessons:id,course_id,title,duration_minutes,order_index')
                ->orderBy('order_index')
                ->get();
        });

        $coursesData = $courses->map(function ($course) use ($user) {
            $totalLessons = $course->lessons->count();
            $completedLessons = 0;
            $quizPassed = false;

            if ($user) {
                $completedLessons = CourseProgress::where('user_id', $user->id)
                    ->whereIn('lesson_id', $course->lessons->pluck('id'))
                    ->where('completed', true)
                    ->count();

                $quizPassed = $this->quizService->hasPassed($user, $course->id);
            }

            $progress = $totalLessons > 0 ? round(($completedLessons / $totalLessons) * 100) : 0;

            return [
                'id' => $course->id,
                'title' => $course->title,
                'track' => $course->track,
                'description' => $course->description,
                'duration_hours' => $course->duration_hours,
                'lessons_count' => $totalLessons,
                'completed_lessons' => $completedLessons,
                'progress' => $progress,
                'quiz_passed' => $quizPassed,
                'lessons' => $course->lessons->sortBy('order_index')->values(),
            ];
        });

        $totalLessons = $courses->sum(fn($c) => $c->lessons->count());
        $totalCompleted = $coursesData->sum('completed_lessons');
        $overallProgress = $totalLessons > 0 ? round(($totalCompleted / $totalLessons) * 100) : 0;

        return response()->json([
            'courses' => $coursesData,
            'overall_progress' => $overallProgress,
            'has_certificate' => $user ? $user->has_certificate : false,
        ]);
    }

    public function show(Request $request, Course $course): JsonResponse
    {
        $user = $request->user();
        $course->load('lessons');

        $lessons = $course->lessons->sortBy('order_index')->map(function ($lesson) use ($user) {
            $progress = null;
            if ($user) {
                $progress = CourseProgress::where('user_id', $user->id)
                    ->where('lesson_id', $lesson->id)
                    ->first();
            }

            return [
                'id' => $lesson->id,
                'title' => $lesson->title,
                'type' => $lesson->type,
                'duration_minutes' => $lesson->duration_minutes,
                'order_index' => $lesson->order_index,
                'is_completed' => $progress?->completed ?? false,
            ];
        })->values();

        $quizPassed = false;
        $quizScore = null;
        $quizAttempts = 0;

        if ($user) {
            $bestAttempt = $this->quizService->getBestAttempt($user, $course->id);
            $quizPassed = (bool) $bestAttempt;
            $quizScore = $bestAttempt?->score;
            $quizAttempts = $this->quizService->getAttemptsUsed($user, $course->id);
        }

        return response()->json([
            'id' => $course->id,
            'title' => $course->title,
            'track' => $course->track,
            'description' => $course->description,
            'duration_hours' => $course->duration_hours,
            'lessons' => $lessons,
            'quiz_passed' => $quizPassed,
            'quiz_score' => $quizScore,
            'quiz_attempts' => $quizAttempts,
        ]);
    }

    public function lesson(Request $request, Course $course, Lesson $lesson): JsonResponse
    {
        if ($lesson->course_id !== $course->id) {
            return response()->json(['message' => 'الدرس لا ينتمي لهذا المسار'], 404);
        }

        $progress = null;
        if ($request->user()) {
            $progress = CourseProgress::where('user_id', $request->user()->id)
                ->where('lesson_id', $lesson->id)
                ->first();
        }

        return response()->json([
            'id' => $lesson->id,
            'title' => $lesson->title,
            'type' => $lesson->type,
            'content' => $lesson->content,
            'video_url' => $lesson->video_url,
            'duration_minutes' => $lesson->duration_minutes,
            'order_index' => $lesson->order_index,
            'is_completed' => $progress?->completed ?? false,
            'course_title' => $course->title,
        ]);
    }

    public function completeLesson(Request $request, Course $course, Lesson $lesson): JsonResponse
    {
        if ($lesson->course_id !== $course->id) {
            return response()->json(['message' => 'الدرس لا ينتمي لهذا المسار'], 404);
        }

        CourseProgress::updateOrCreate(
            ['user_id' => $request->user()->id, 'lesson_id' => $lesson->id],
            ['completed' => true, 'progress_percent' => 100, 'completed_at' => now()]
        );

        return response()->json(['message' => 'تم إكمال الدرس بنجاح']);
    }

    public function updateProgress(Request $request, Course $course, Lesson $lesson): JsonResponse
    {
        if ($lesson->course_id !== $course->id) {
            return response()->json(['message' => 'الدرس لا ينتمي لهذا المسار'], 404);
        }

        $request->validate(['progress_percent' => 'required|numeric|min:0|max:100']);

        CourseProgress::updateOrCreate(
            ['user_id' => $request->user()->id, 'lesson_id' => $lesson->id],
            ['progress_percent' => $request->progress_percent]
        );

        return response()->json(['message' => 'ok']);
    }

    public function quiz(Request $request, Course $course): JsonResponse
    {
        $questions = $this->quizService->getQuestions($course->track);

        return response()->json([
            'course_id' => $course->id,
            'course_title' => $course->title,
            'questions' => $questions,
            'passing_score' => QuizService::PASSING_SCORE,
            'total_questions' => QuizService::TOTAL_QUESTIONS,
            'max_attempts' => QuizService::MAX_ATTEMPTS,
            'attempts_used' => $this->quizService->getAttemptsUsed($request->user(), $course->id),
        ]);
    }

    public function submitQuiz(SubmitQuizRequest $request, Course $course): JsonResponse
    {
        $user = $request->user();
        $attemptsUsed = $this->quizService->getAttemptsUsed($user, $course->id);

        if ($attemptsUsed >= QuizService::MAX_ATTEMPTS) {
            return response()->json(['message' => 'لقد استنفدت جميع المحاولات (3 محاولات)'], 422);
        }

        if ($this->quizService->hasPassed($user, $course->id)) {
            return response()->json(['message' => 'لقد اجتزت هذا الاختبار مسبقاً'], 422);
        }

        $result = $this->quizService->grade($request->answers, $course->track);

        $this->quizService->recordAttempt(
            $user, $course->id, $result['score'], $result['passed'], $request->answers, $attemptsUsed + 1
        );

        $certificate = null;
        if ($result['passed']) {
            $cert = $this->certificateService->checkAndIssue($user);
            if ($cert) {
                $certificate = ['certificate_number' => $cert->certificate_number];
            }
        }

        return response()->json([
            'score' => $result['score'],
            'total' => $result['total'],
            'passed' => $result['passed'],
            'attempt_number' => $attemptsUsed + 1,
            'remaining_attempts' => QuizService::MAX_ATTEMPTS - ($attemptsUsed + 1),
            'certificate' => $certificate,
            'message' => $result['passed'] ? 'مبارك! لقد اجتزت الاختبار بنجاح' : 'لم تجتز الاختبار. حاول مرة أخرى',
        ]);
    }

    public function certificatePdf(Request $request)
    {
        $pdf = $this->certificateService->generatePdf($request->user());

        if (!$pdf) {
            return response()->json(['message' => 'لم تحصل على الشهادة بعد'], 404);
        }

        return $pdf->download('yassiru-certificate.pdf');
    }

    public function certificate(Request $request): JsonResponse
    {
        $data = $this->certificateService->getUserCertificate($request->user());

        if (!$data) {
            return response()->json(['message' => 'لم تحصل على الشهادة بعد'], 404);
        }

        return response()->json($data);
    }

    public function verifyCertificate(string $number): JsonResponse
    {
        $data = $this->certificateService->verify($number);

        if (!$data) {
            return response()->json(['valid' => false, 'message' => 'رقم الشهادة غير صالح'], 404);
        }

        return response()->json($data);
    }
}
