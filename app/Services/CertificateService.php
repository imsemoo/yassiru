<?php

namespace App\Services;

use App\Models\Certificate;
use App\Models\Course;
use App\Models\QuizAttempt;
use App\Models\User;
use App\Notifications\CertificateEarned;
use App\Services\AuditService;
use App\Services\TrustScoreService;
use Barryvdh\DomPDF\Facade\Pdf;

class CertificateService
{
    public function checkAndIssue(User $user): ?Certificate
    {
        // Only regular users can earn certificates.
        // Recommenders, counselors, and admins don't need them.
        if ($user->role !== 'user') {
            return null;
        }

        $allCourses = Course::where('is_active', true)->pluck('id');
        $passedCourses = QuizAttempt::where('user_id', $user->id)
            ->where('passed', true)
            ->distinct('course_id')
            ->pluck('course_id');

        if ($allCourses->diff($passedCourses)->isNotEmpty()) {
            return null;
        }

        if (Certificate::where('user_id', $user->id)->exists()) {
            return Certificate::where('user_id', $user->id)->first();
        }

        $certificate = Certificate::create([
            'user_id' => $user->id,
            'certificate_number' => 'YSR-' . strtoupper(substr(md5($user->id . now()), 0, 8)),
            'issued_at' => now(),
        ]);

        $user->has_certificate = true;
        $user->save();
        $user->notify(new CertificateEarned($certificate));

        AuditService::log('certificate_issued', $certificate);

        // Trust score: reward certificate
        app(TrustScoreService::class)->addPoints($user, 'certificate_earned');

        return $certificate;
    }

    public function verify(string $number): ?array
    {
        $certificate = Certificate::where('certificate_number', $number)
            ->with('user:id,name')
            ->first();

        if (!$certificate) {
            return null;
        }

        // Only return first name initial for privacy on public endpoint
        $nameParts = explode(' ', $certificate->user->name);
        $maskedName = $nameParts[0] . (isset($nameParts[1]) ? ' ' . mb_substr($nameParts[1], 0, 1) . '.' : '');

        return [
            'valid' => true,
            'name' => $maskedName,
            'certificate_number' => $certificate->certificate_number,
            'issued_at' => $certificate->issued_at->format('Y-m-d'),
        ];
    }

    public function generatePdf(User $user)
    {
        $data = $this->getUserCertificate($user);

        if (!$data) {
            return null;
        }

        $pdf = Pdf::loadView('pdf.certificate', [
            'userName' => $data['user_name'],
            'certificateNumber' => $data['certificate_number'],
            'issuedAt' => $data['issued_at'],
            'shariahScore' => $data['shariah_score'],
            'psychologyScore' => $data['psychology_score'],
            'financialScore' => $data['financial_score'],
            'practicalScore' => $data['practical_score'],
        ]);

        $pdf->setPaper('a4', 'portrait');
        $pdf->setOption('defaultFont', 'DejaVu Sans');

        return $pdf;
    }

    public function getUserCertificate(User $user): ?array
    {
        // Only regular users can have certificates
        if ($user->role !== 'user') {
            return null;
        }

        $certificate = Certificate::where('user_id', $user->id)->first();

        if (!$certificate) {
            return null;
        }

        $scores = QuizAttempt::where('user_id', $user->id)
            ->where('passed', true)
            ->join('courses', 'quiz_attempts.course_id', '=', 'courses.id')
            ->selectRaw('courses.track, MAX(quiz_attempts.score) as best_score')
            ->groupBy('courses.track')
            ->pluck('best_score', 'track');

        return [
            'certificate_number' => $certificate->certificate_number,
            'issued_at' => $certificate->issued_at->format('Y-m-d'),
            'user_name' => $user->name,
            'shariah_score' => ($scores['shariah'] ?? 0) . '/10',
            'psychology_score' => ($scores['psychology'] ?? 0) . '/10',
            'financial_score' => ($scores['financial'] ?? 0) . '/10',
            'practical_score' => ($scores['practical'] ?? 0) . '/10',
        ];
    }
}
