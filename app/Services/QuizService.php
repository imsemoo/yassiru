<?php

namespace App\Services;

use App\Models\QuizAttempt;
use App\Models\QuizQuestion;
use App\Models\User;

class QuizService
{
    public const PASSING_SCORE = 7;
    public const TOTAL_QUESTIONS = 10;
    public const MAX_ATTEMPTS = 3;

    public function getAttemptsUsed(User $user, int $courseId): int
    {
        return QuizAttempt::where('user_id', $user->id)
            ->where('course_id', $courseId)
            ->count();
    }

    public function hasPassed(User $user, int $courseId): bool
    {
        return QuizAttempt::where('user_id', $user->id)
            ->where('course_id', $courseId)
            ->where('passed', true)
            ->exists();
    }

    public function getBestAttempt(User $user, int $courseId): ?QuizAttempt
    {
        return QuizAttempt::where('user_id', $user->id)
            ->where('course_id', $courseId)
            ->where('passed', true)
            ->first();
    }

    public function grade(array $answers, string $track): array
    {
        $questions = $this->getQuestions($track);
        $score = 0;

        foreach ($questions as $i => $q) {
            if (isset($answers[$i]) && $answers[$i] === $q['correct']) {
                $score++;
            }
        }

        return [
            'score' => $score,
            'total' => self::TOTAL_QUESTIONS,
            'passed' => $score >= self::PASSING_SCORE,
        ];
    }

    public function recordAttempt(User $user, int $courseId, int $score, bool $passed, array $answers, int $attemptNumber): QuizAttempt
    {
        return QuizAttempt::create([
            'user_id' => $user->id,
            'course_id' => $courseId,
            'score' => $score,
            'passed' => $passed,
            'answers' => $answers,
            'attempt_number' => $attemptNumber,
        ]);
    }

    /**
     * Get randomized questions from DB. Falls back to hardcoded if DB is empty.
     */
    public function getRandomizedQuestions(string $track, int $count = 10): array
    {
        $dbQuestions = QuizQuestion::active()
            ->forTrack($track)
            ->inRandomOrder()
            ->limit($count)
            ->get();

        if ($dbQuestions->count() >= $count) {
            return $dbQuestions->map(function ($q) {
                // Shuffle options and track correct answer
                $options = $q->options;
                $correctText = $options[$q->correct_option];

                $shuffled = $options;
                shuffle($shuffled);

                return [
                    'id' => $q->id,
                    'question' => $q->question,
                    'options' => $shuffled,
                    'correct' => array_search($correctText, $shuffled),
                ];
            })->values()->toArray();
        }

        // Fallback to hardcoded questions
        return $this->getQuestions($track);
    }

    /**
     * Grade answers against randomized questions (with question IDs)
     */
    public function gradeRandomized(array $answers, array $questionIds): array
    {
        $score = 0;
        $questions = QuizQuestion::whereIn('id', $questionIds)->get()->keyBy('id');

        foreach ($questionIds as $i => $qId) {
            $q = $questions[$qId] ?? null;
            if ($q && isset($answers[$i]) && $answers[$i] === $q->correct_option) {
                $score++;
            }
        }

        return [
            'score' => $score,
            'total' => count($questionIds),
            'passed' => $score >= self::PASSING_SCORE,
        ];
    }

    public function getQuestions(string $track): array
    {
        $questions = [
            'shariah' => [
                ['question' => 'ما هي أركان عقد الزواج في الإسلام؟', 'options' => ['الإيجاب والقبول فقط', 'الإيجاب والقبول والولي والشهود', 'المهر والشهود', 'الولي فقط'], 'correct' => 1],
                ['question' => 'ما حكم النظر للمخطوبة في الإسلام؟', 'options' => ['محرم مطلقاً', 'مباح بشروط', 'واجب', 'مكروه'], 'correct' => 1],
                ['question' => 'ما هو الحق الأول للزوجة على زوجها؟', 'options' => ['الترفيه', 'النفقة والسكن', 'السفر', 'العمل'], 'correct' => 1],
                ['question' => 'ما هي القوامة في الإسلام؟', 'options' => ['تسلط الرجل', 'مسؤولية الرعاية والإنفاق', 'حق الطلاق فقط', 'التحكم في كل شيء'], 'correct' => 1],
                ['question' => 'ما هو الترتيب الصحيح لحل الخلافات الزوجية؟', 'options' => ['الطلاق ثم النصيحة', 'النصيحة ثم الهجر ثم التحكيم', 'التحكيم مباشرة', 'الهجر فقط'], 'correct' => 1],
                ['question' => 'متى يكون الطلاق مباحاً؟', 'options' => ['عند أي خلاف', 'عند استحالة العشرة', 'لا يباح أبداً', 'عند طلب الزوجة'], 'correct' => 1],
                ['question' => 'ما حكم إفشاء أسرار العلاقة الزوجية؟', 'options' => ['مباح', 'محرم', 'مكروه فقط', 'جائز للنساء'], 'correct' => 1],
                ['question' => 'ما واجب الزوج تجاه زوجته عند الخلاف؟', 'options' => ['الضرب', 'الصبر والنصيحة بالحسنى', 'الطلاق', 'الهجر المطلق'], 'correct' => 1],
                ['question' => 'هل يجوز للمرأة أن تشترط في عقد الزواج؟', 'options' => ['لا يجوز مطلقاً', 'نعم ما لم يحل حراماً أو يحرم حلالاً', 'فقط شرط واحد', 'بإذن القاضي فقط'], 'correct' => 1],
                ['question' => 'ما هي صلاة الاستخارة في سياق الزواج؟', 'options' => ['صلاة واجبة قبل العقد', 'سنة لطلب التوفيق من الله', 'صلاة لمعرفة المستقبل', 'ليست من السنة'], 'correct' => 1],
            ],
            'psychology' => [
                ['question' => 'ما هو أساس التواصل الفعال بين الزوجين؟', 'options' => ['المجادلة', 'الاستماع الفعال', 'فرض الرأي', 'الصمت'], 'correct' => 1],
                ['question' => 'ما معنى لغات الحب الخمس؟', 'options' => ['لغات أجنبية', 'طرق مختلفة للتعبير عن الحب', 'أنواع الهدايا', 'مراحل الحب'], 'correct' => 1],
                ['question' => 'ما هو أفضل أسلوب للتعبير عن المشاعر؟', 'options' => ['أنت دائماً تخطئ', 'أنا أشعر بالضيق عندما...', 'لا أريد التحدث', 'أنت السبب'], 'correct' => 1],
                ['question' => 'ما سبب صدمة الواقع في الأشهر الأولى من الزواج؟', 'options' => ['عدم التوافق', 'التوقعات غير الواقعية', 'الزواج خطأ', 'تدخل الأهل'], 'correct' => 1],
                ['question' => 'كيف تتعامل مع الغضب في العلاقة الزوجية؟', 'options' => ['التعبير الفوري بصراخ', 'توقف، تنفس، أجّل', 'الانسحاب الدائم', 'تجاهل المشكلة'], 'correct' => 1],
                ['question' => 'ما هو الذكاء العاطفي؟', 'options' => ['الذكاء الأكاديمي', 'القدرة على فهم وإدارة المشاعر', 'التحكم بالآخرين', 'إخفاء المشاعر'], 'correct' => 1],
                ['question' => 'ما هو الفرق بين الشريك المناسب والشريك المثالي؟', 'options' => ['لا فرق', 'المناسب واقعي والمثالي خيالي', 'المثالي أفضل دائماً', 'المناسب أقل جودة'], 'correct' => 1],
                ['question' => 'متى يجب طلب استشارة متخصصة؟', 'options' => ['عند أي خلاف', 'عند تأثر الحياة اليومية والصحة النفسية', 'لا يجب أبداً', 'فقط قبل الطلاق'], 'correct' => 1],
                ['question' => 'ما أهمية الاعتذار الصادق؟', 'options' => ['ضعف', 'يبني الثقة ويصلح العلاقة', 'غير ضروري', 'مطلوب من الزوجة فقط'], 'correct' => 1],
                ['question' => 'كيف تتعامل مع ضغوط العمل دون التأثير على الزواج؟', 'options' => ['إلقاء اللوم على الشريك', 'التعامل كفريق واحد (نحن ضد المشكلة)', 'الانعزال', 'تجاهل الضغوط'], 'correct' => 1],
            ],
            'financial' => [
                ['question' => 'ما هي قاعدة 50/30/20 للميزانية؟', 'options' => ['ادخار/ترفيه/ضروريات', 'ضروريات 50%/رغبات 30%/ادخار 20%', 'أقساط/طعام/ملابس', 'إيجار/مواصلات/ترفيه'], 'correct' => 1],
                ['question' => 'ما هي أولوية الإنفاق عند تأسيس المنزل؟', 'options' => ['الأثاث الفاخر', 'الاحتياجات الأساسية أولاً', 'الأجهزة الكهربائية', 'الديكور'], 'correct' => 1],
                ['question' => 'ما خطورة الاستدانة في بداية الزواج؟', 'options' => ['لا خطورة', 'ضغط مالي ونفسي يهدد الاستقرار', 'أمر طبيعي', 'ضروري للجميع'], 'correct' => 1],
                ['question' => 'ما أهمية صندوق الطوارئ؟', 'options' => ['للترفيه', 'لتغطية المصاريف غير المتوقعة', 'غير ضروري', 'للاستثمار'], 'correct' => 1],
                ['question' => 'كيف تتحدث مع شريكك عن المال دون خلاف؟', 'options' => ['تجنب الموضوع', 'حوار دوري هادئ ومنظم', 'فرض القرارات', 'إخفاء المصاريف'], 'correct' => 1],
                ['question' => 'ما هي المسؤولية المالية في الزواج؟', 'options' => ['على الزوج فقط', 'مسؤولية مشتركة بالتشاور', 'على الزوجة', 'على الأهل'], 'correct' => 1],
                ['question' => 'متى يجب البدء بالتخطيط المالي؟', 'options' => ['بعد الزواج بسنة', 'من قبل الزواج', 'عند الحاجة', 'عند الإنجاب'], 'correct' => 1],
                ['question' => 'هل الشراء المستعمل عيب؟', 'options' => ['نعم', 'لا، بل ذكاء مالي', 'أحياناً', 'للفقراء فقط'], 'correct' => 1],
                ['question' => 'ما أهمية التأمين الصحي للأسرة؟', 'options' => ['غير مهم', 'حماية من المفاجآت الطبية المكلفة', 'رفاهية', 'للمسنين فقط'], 'correct' => 1],
                ['question' => 'ما أفضل طريقة لزيادة الدخل بشكل حلال؟', 'options' => ['القروض', 'تنمية المهارات والعمل الإضافي', 'المقامرة', 'الاعتماد على الأهل'], 'correct' => 1],
            ],
            'practical' => [
                ['question' => 'كيف يتم تقسيم مهام المنزل بين الزوجين؟', 'options' => ['كلها على الزوجة', 'بالتشاور والمشاركة', 'كلها على الزوج', 'لا يهم'], 'correct' => 1],
                ['question' => 'ما أهمية إدارة الوقت في الحياة الزوجية؟', 'options' => ['غير مهمة', 'تحقيق التوازن بين العمل والبيت والعلاقة', 'مهمة للعمل فقط', 'مهمة للزوجة فقط'], 'correct' => 1],
                ['question' => 'كيف تبني علاقة صحية مع أهل الشريك؟', 'options' => ['تجنبهم', 'الاحترام والحدود الصحية', 'إرضاؤهم دائماً', 'القطيعة'], 'correct' => 1],
                ['question' => 'ما هي الحدود الصحية مع الأهل بعد الزواج؟', 'options' => ['لا حدود', 'الاحترام مع الحفاظ على استقلالية البيت', 'القطيعة', 'الأهل يقررون كل شيء'], 'correct' => 1],
                ['question' => 'ما أهم شيء في الأشهر الثلاثة الأولى من الزواج؟', 'options' => ['التأثيث', 'التعرف على عادات الشريك والتكيف', 'الإنجاب', 'السفر'], 'correct' => 1],
                ['question' => 'كيف تتعامل مع اكتشاف عادات مزعجة عند الشريك؟', 'options' => ['الطلاق', 'الحوار بلطف والتقبل التدريجي', 'التجاهل الدائم', 'الشكوى للأهل'], 'correct' => 1],
                ['question' => 'ما هي علامات الحاجة لاستشارة أسرية؟', 'options' => ['أي خلاف', 'تكرار نفس المشكلة وعدم القدرة على الحل', 'لا توجد علامات', 'بعد سنة من الزواج'], 'correct' => 1],
                ['question' => 'متى يُفضل التخطيط للإنجاب؟', 'options' => ['فوراً بعد الزواج', 'بعد استقرار العلاقة والوضع المالي', 'لا يحتاج تخطيط', 'بعد 5 سنوات'], 'correct' => 1],
                ['question' => 'ما أهمية بناء تقاليد خاصة بالزوجين؟', 'options' => ['غير مهمة', 'تعزز الروابط وتخلق ذكريات مشتركة', 'مضيعة للوقت', 'للأطفال فقط'], 'correct' => 1],
                ['question' => 'ما أهم مهارة يحتاجها الزوجان في السنة الأولى؟', 'options' => ['الطبخ', 'المرونة والصبر', 'كسب المال', 'إرضاء الأهل'], 'correct' => 1],
            ],
        ];

        return $questions[$track] ?? $questions['shariah'];
    }
}
