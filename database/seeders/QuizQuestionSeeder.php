<?php

namespace Database\Seeders;

use App\Models\QuizQuestion;
use Illuminate\Database\Seeder;

class QuizQuestionSeeder extends Seeder
{
    public function run(): void
    {
        $questions = array_merge(
            $this->shariahQuestions(),
            $this->psychologyQuestions(),
            $this->financialQuestions(),
            $this->practicalQuestions(),
        );

        foreach ($questions as $q) {
            QuizQuestion::create($q);
        }
    }

    private function shariahQuestions(): array
    {
        return [
            ['track' => 'shariah', 'question' => 'ما هي أركان عقد الزواج في الإسلام؟', 'options' => ['الإيجاب والقبول فقط', 'الإيجاب والقبول والولي والشهود', 'المهر والشهود', 'الولي فقط'], 'correct_option' => 1, 'difficulty' => 'easy'],
            ['track' => 'shariah', 'question' => 'ما حكم النظر للمخطوبة في الإسلام؟', 'options' => ['محرم مطلقاً', 'مباح بشروط', 'واجب', 'مكروه'], 'correct_option' => 1, 'difficulty' => 'easy'],
            ['track' => 'shariah', 'question' => 'ما هو الحق الأول للزوجة على زوجها؟', 'options' => ['الترفيه', 'النفقة والسكن', 'السفر', 'العمل'], 'correct_option' => 1, 'difficulty' => 'easy'],
            ['track' => 'shariah', 'question' => 'ما هي القوامة في الإسلام؟', 'options' => ['تسلط الرجل', 'مسؤولية الرعاية والإنفاق', 'حق الطلاق فقط', 'التحكم في كل شيء'], 'correct_option' => 1, 'difficulty' => 'medium'],
            ['track' => 'shariah', 'question' => 'ما هو الترتيب الصحيح لحل الخلافات الزوجية؟', 'options' => ['الطلاق ثم النصيحة', 'النصيحة ثم الهجر ثم التحكيم', 'التحكيم مباشرة', 'الهجر فقط'], 'correct_option' => 1, 'difficulty' => 'medium'],
            ['track' => 'shariah', 'question' => 'متى يكون الطلاق مباحاً؟', 'options' => ['عند أي خلاف', 'عند استحالة العشرة', 'لا يباح أبداً', 'عند طلب الزوجة'], 'correct_option' => 1, 'difficulty' => 'medium'],
            ['track' => 'shariah', 'question' => 'ما حكم إفشاء أسرار العلاقة الزوجية؟', 'options' => ['مباح', 'محرم', 'مكروه فقط', 'جائز للنساء'], 'correct_option' => 1, 'difficulty' => 'easy'],
            ['track' => 'shariah', 'question' => 'ما واجب الزوج تجاه زوجته عند الخلاف؟', 'options' => ['الضرب', 'الصبر والنصيحة بالحسنى', 'الطلاق', 'الهجر المطلق'], 'correct_option' => 1, 'difficulty' => 'easy'],
            ['track' => 'shariah', 'question' => 'هل يجوز للمرأة أن تشترط في عقد الزواج؟', 'options' => ['لا يجوز مطلقاً', 'نعم ما لم يحل حراماً أو يحرم حلالاً', 'فقط شرط واحد', 'بإذن القاضي فقط'], 'correct_option' => 1, 'difficulty' => 'medium'],
            ['track' => 'shariah', 'question' => 'ما هي صلاة الاستخارة في سياق الزواج؟', 'options' => ['صلاة واجبة قبل العقد', 'سنة لطلب التوفيق من الله', 'صلاة لمعرفة المستقبل', 'ليست من السنة'], 'correct_option' => 1, 'difficulty' => 'easy'],
            // 10 additional
            ['track' => 'shariah', 'question' => 'ما الحد الأدنى للمهر في الإسلام؟', 'options' => ['ألف دينار', 'لا حد أدنى محدد شرعاً ويجوز بأي شيء ذي قيمة', 'عشر جرامات ذهب', 'ربع دينار فقط'], 'correct_option' => 1, 'difficulty' => 'hard'],
            ['track' => 'shariah', 'question' => 'ما حكم زواج المسيار في الإسلام؟', 'options' => ['حرام مطلقاً', 'جائز بشروط عند بعض العلماء مع استيفاء أركان العقد', 'واجب', 'مكروه دائماً'], 'correct_option' => 1, 'difficulty' => 'hard'],
            ['track' => 'shariah', 'question' => 'ما هي عدة المطلقة غير الحامل؟', 'options' => ['شهر واحد', 'ثلاث حيضات أو ثلاثة أشهر', 'ستة أشهر', 'سنة كاملة'], 'correct_option' => 1, 'difficulty' => 'medium'],
            ['track' => 'shariah', 'question' => 'من له حق الولاية في الزواج؟', 'options' => ['الأم فقط', 'الأب ثم الجد ثم الأخ ثم العم', 'أي شخص', 'القاضي فقط'], 'correct_option' => 1, 'difficulty' => 'medium'],
            ['track' => 'shariah', 'question' => 'ما حكم الخلوة بالمخطوبة قبل العقد؟', 'options' => ['جائزة', 'محرمة لأنها أجنبية حتى العقد', 'مكروهة فقط', 'جائزة بإذن وليها'], 'correct_option' => 1, 'difficulty' => 'easy'],
            ['track' => 'shariah', 'question' => 'ما هو حق الزوجة في العمل؟', 'options' => ['ممنوع مطلقاً', 'جائز بالتشاور والتراضي بين الزوجين', 'واجب عليها', 'حق مطلق بدون إذن'], 'correct_option' => 1, 'difficulty' => 'medium'],
            ['track' => 'shariah', 'question' => 'ما حكم النفقة على الزوجة الناشز؟', 'options' => ['واجبة دائماً', 'تسقط النفقة عند النشوز الثابت', 'تتضاعف', 'يقررها القاضي فقط'], 'correct_option' => 1, 'difficulty' => 'hard'],
            ['track' => 'shariah', 'question' => 'ما أهمية الشهود في عقد الزواج؟', 'options' => ['تزيينية', 'ركن أساسي لصحة العقد وإعلانه', 'مستحبة فقط', 'للتوثيق الرسمي فقط'], 'correct_option' => 1, 'difficulty' => 'easy'],
            ['track' => 'shariah', 'question' => 'ما حكم تأخير المهر (المهر المؤجل)؟', 'options' => ['حرام', 'جائز باتفاق الطرفين ويبقى ديناً على الزوج', 'مكروه', 'يسقط بعد سنة'], 'correct_option' => 1, 'difficulty' => 'medium'],
            ['track' => 'shariah', 'question' => 'ما هو الخُلع في الإسلام؟', 'options' => ['الطلاق العادي', 'افتداء الزوجة نفسها بمقابل مالي تدفعه للزوج', 'فسخ القاضي للعقد', 'هجر الزوجة'], 'correct_option' => 1, 'difficulty' => 'hard'],
        ];
    }

    private function psychologyQuestions(): array
    {
        return [
            ['track' => 'psychology', 'question' => 'ما هو أساس التواصل الفعال بين الزوجين؟', 'options' => ['المجادلة', 'الاستماع الفعال', 'فرض الرأي', 'الصمت'], 'correct_option' => 1, 'difficulty' => 'easy'],
            ['track' => 'psychology', 'question' => 'ما معنى لغات الحب الخمس؟', 'options' => ['لغات أجنبية', 'طرق مختلفة للتعبير عن الحب', 'أنواع الهدايا', 'مراحل الحب'], 'correct_option' => 1, 'difficulty' => 'easy'],
            ['track' => 'psychology', 'question' => 'ما هو أفضل أسلوب للتعبير عن المشاعر؟', 'options' => ['أنت دائماً تخطئ', 'أنا أشعر بالضيق عندما...', 'لا أريد التحدث', 'أنت السبب'], 'correct_option' => 1, 'difficulty' => 'easy'],
            ['track' => 'psychology', 'question' => 'ما سبب صدمة الواقع في الأشهر الأولى من الزواج؟', 'options' => ['عدم التوافق', 'التوقعات غير الواقعية', 'الزواج خطأ', 'تدخل الأهل'], 'correct_option' => 1, 'difficulty' => 'medium'],
            ['track' => 'psychology', 'question' => 'كيف تتعامل مع الغضب في العلاقة الزوجية؟', 'options' => ['التعبير الفوري بصراخ', 'توقف، تنفس، أجّل', 'الانسحاب الدائم', 'تجاهل المشكلة'], 'correct_option' => 1, 'difficulty' => 'easy'],
            ['track' => 'psychology', 'question' => 'ما هو الذكاء العاطفي؟', 'options' => ['الذكاء الأكاديمي', 'القدرة على فهم وإدارة المشاعر', 'التحكم بالآخرين', 'إخفاء المشاعر'], 'correct_option' => 1, 'difficulty' => 'easy'],
            ['track' => 'psychology', 'question' => 'ما الفرق بين الشريك المناسب والمثالي؟', 'options' => ['لا فرق', 'المناسب واقعي والمثالي خيالي', 'المثالي أفضل دائماً', 'المناسب أقل جودة'], 'correct_option' => 1, 'difficulty' => 'medium'],
            ['track' => 'psychology', 'question' => 'متى يجب طلب استشارة متخصصة؟', 'options' => ['عند أي خلاف', 'عند تأثر الحياة اليومية والصحة النفسية', 'لا يجب أبداً', 'فقط قبل الطلاق'], 'correct_option' => 1, 'difficulty' => 'medium'],
            ['track' => 'psychology', 'question' => 'ما أهمية الاعتذار الصادق؟', 'options' => ['ضعف', 'يبني الثقة ويصلح العلاقة', 'غير ضروري', 'مطلوب من الزوجة فقط'], 'correct_option' => 1, 'difficulty' => 'easy'],
            ['track' => 'psychology', 'question' => 'كيف تتعامل مع ضغوط العمل دون التأثير على الزواج؟', 'options' => ['إلقاء اللوم على الشريك', 'التعامل كفريق واحد', 'الانعزال', 'تجاهل الضغوط'], 'correct_option' => 1, 'difficulty' => 'medium'],
            // 10 additional
            ['track' => 'psychology', 'question' => 'ما هي نظرية التعلق وعلاقتها بالزواج؟', 'options' => ['نظرية عن التعليم', 'أنماط الارتباط العاطفي التي تتشكل في الطفولة وتؤثر على العلاقات', 'طريقة لحل النزاعات', 'مرحلة من مراحل الحب'], 'correct_option' => 1, 'difficulty' => 'hard'],
            ['track' => 'psychology', 'question' => 'ما هو مفهوم "الحساب البنكي العاطفي"؟', 'options' => ['حساب مالي مشترك', 'رصيد المشاعر الإيجابية المتراكمة بين الزوجين', 'حساب المصاريف', 'قائمة الأخطاء'], 'correct_option' => 1, 'difficulty' => 'medium'],
            ['track' => 'psychology', 'question' => 'ما هو أخطر سلوك في العلاقة الزوجية حسب دراسات غوتمان؟', 'options' => ['الصراخ', 'الازدراء والاحتقار', 'النسيان', 'الصمت المؤقت'], 'correct_option' => 1, 'difficulty' => 'hard'],
            ['track' => 'psychology', 'question' => 'كيف تتعامل مع اختلاف الشخصيات بين الزوجين؟', 'options' => ['تغيير الشريك', 'التفهم والتكيف واحترام الاختلاف', 'تجاهل الاختلاف', 'إجبار الشريك على التغيير'], 'correct_option' => 1, 'difficulty' => 'easy'],
            ['track' => 'psychology', 'question' => 'ما دور الامتنان في تقوية العلاقة الزوجية؟', 'options' => ['لا دور له', 'يزيد الرضا والسعادة ويقلل النزاعات', 'يضعف الشخصية', 'مطلوب من طرف واحد فقط'], 'correct_option' => 1, 'difficulty' => 'easy'],
            ['track' => 'psychology', 'question' => 'ما هو السلوك الدفاعي في النقاشات الزوجية؟', 'options' => ['الاستماع بانتباه', 'تبرير النفس وإلقاء اللوم على الطرف الآخر بدل تحمل المسؤولية', 'الاعتذار', 'طلب المساعدة'], 'correct_option' => 1, 'difficulty' => 'medium'],
            ['track' => 'psychology', 'question' => 'ما أهمية قضاء وقت نوعي (Quality Time) مع الشريك؟', 'options' => ['غير مهم', 'يعزز الترابط العاطفي ويقوي العلاقة', 'رفاهية فقط', 'مهم للأطفال فقط'], 'correct_option' => 1, 'difficulty' => 'easy'],
            ['track' => 'psychology', 'question' => 'كيف يؤثر الهاتف المحمول على العلاقة الزوجية؟', 'options' => ['لا يؤثر', 'الاستخدام المفرط يقلل التواصل ويسبب الشعور بالإهمال', 'يحسن العلاقة دائماً', 'تأثيره إيجابي فقط'], 'correct_option' => 1, 'difficulty' => 'easy'],
            ['track' => 'psychology', 'question' => 'ما هي مراحل التكيف في السنة الأولى من الزواج؟', 'options' => ['لا توجد مراحل', 'شهر العسل ثم صدمة الواقع ثم التكيف والاستقرار', 'مرحلة واحدة ثابتة', 'صراع مستمر'], 'correct_option' => 1, 'difficulty' => 'medium'],
            ['track' => 'psychology', 'question' => 'ما أثر تدخل الأهل السلبي في الحياة الزوجية؟', 'options' => ['دائماً إيجابي', 'يسبب توتراً ويضعف استقلالية الزوجين', 'لا أثر له', 'يقوي العلاقة'], 'correct_option' => 1, 'difficulty' => 'easy'],
        ];
    }

    private function financialQuestions(): array
    {
        return [
            ['track' => 'financial', 'question' => 'ما هي قاعدة 50/30/20 للميزانية؟', 'options' => ['ادخار/ترفيه/ضروريات', 'ضروريات 50%/رغبات 30%/ادخار 20%', 'أقساط/طعام/ملابس', 'إيجار/مواصلات/ترفيه'], 'correct_option' => 1, 'difficulty' => 'easy'],
            ['track' => 'financial', 'question' => 'ما هي أولوية الإنفاق عند تأسيس المنزل؟', 'options' => ['الأثاث الفاخر', 'الاحتياجات الأساسية أولاً', 'الأجهزة الكهربائية', 'الديكور'], 'correct_option' => 1, 'difficulty' => 'easy'],
            ['track' => 'financial', 'question' => 'ما خطورة الاستدانة في بداية الزواج؟', 'options' => ['لا خطورة', 'ضغط مالي ونفسي يهدد الاستقرار', 'أمر طبيعي', 'ضروري للجميع'], 'correct_option' => 1, 'difficulty' => 'easy'],
            ['track' => 'financial', 'question' => 'ما أهمية صندوق الطوارئ؟', 'options' => ['للترفيه', 'لتغطية المصاريف غير المتوقعة', 'غير ضروري', 'للاستثمار'], 'correct_option' => 1, 'difficulty' => 'easy'],
            ['track' => 'financial', 'question' => 'كيف تتحدث مع شريكك عن المال دون خلاف؟', 'options' => ['تجنب الموضوع', 'حوار دوري هادئ ومنظم', 'فرض القرارات', 'إخفاء المصاريف'], 'correct_option' => 1, 'difficulty' => 'medium'],
            ['track' => 'financial', 'question' => 'ما هي المسؤولية المالية في الزواج؟', 'options' => ['على الزوج فقط', 'مسؤولية مشتركة بالتشاور', 'على الزوجة', 'على الأهل'], 'correct_option' => 1, 'difficulty' => 'easy'],
            ['track' => 'financial', 'question' => 'متى يجب البدء بالتخطيط المالي؟', 'options' => ['بعد الزواج بسنة', 'من قبل الزواج', 'عند الحاجة', 'عند الإنجاب'], 'correct_option' => 1, 'difficulty' => 'easy'],
            ['track' => 'financial', 'question' => 'هل الشراء المستعمل عيب؟', 'options' => ['نعم', 'لا، بل ذكاء مالي', 'أحياناً', 'للفقراء فقط'], 'correct_option' => 1, 'difficulty' => 'easy'],
            ['track' => 'financial', 'question' => 'ما أهمية التأمين الصحي للأسرة؟', 'options' => ['غير مهم', 'حماية من المفاجآت الطبية المكلفة', 'رفاهية', 'للمسنين فقط'], 'correct_option' => 1, 'difficulty' => 'easy'],
            ['track' => 'financial', 'question' => 'ما أفضل طريقة لزيادة الدخل بشكل حلال؟', 'options' => ['القروض', 'تنمية المهارات والعمل الإضافي', 'المقامرة', 'الاعتماد على الأهل'], 'correct_option' => 1, 'difficulty' => 'easy'],
            // 10 additional
            ['track' => 'financial', 'question' => 'ما الفرق بين الحاجات والرغبات في الإنفاق؟', 'options' => ['لا فرق', 'الحاجات ضرورية للحياة والرغبات يمكن تأجيلها', 'الرغبات أهم', 'كلاهما ضروري'], 'correct_option' => 1, 'difficulty' => 'easy'],
            ['track' => 'financial', 'question' => 'كم يجب أن يكون حجم صندوق الطوارئ؟', 'options' => ['شهر واحد', 'من 3 إلى 6 أشهر من المصاريف الشهرية', 'سنة كاملة', 'لا يهم المبلغ'], 'correct_option' => 1, 'difficulty' => 'medium'],
            ['track' => 'financial', 'question' => 'ما هو التضخم وكيف يؤثر على المدخرات؟', 'options' => ['زيادة الرواتب', 'ارتفاع الأسعار الذي يقلل القوة الشرائية للنقود مع الوقت', 'انخفاض الأسعار', 'لا يؤثر على المدخرات'], 'correct_option' => 1, 'difficulty' => 'hard'],
            ['track' => 'financial', 'question' => 'ما أفضل طريقة لتتبع المصاريف الشهرية؟', 'options' => ['لا حاجة للتتبع', 'تطبيق ميزانية أو جدول شهري مفصل بالدخل والمصروفات', 'الاعتماد على الذاكرة', 'حساب المبلغ المتبقي فقط'], 'correct_option' => 1, 'difficulty' => 'easy'],
            ['track' => 'financial', 'question' => 'ما حكم القرض الربوي لتجهيز الزواج؟', 'options' => ['جائز للضرورة دائماً', 'محرم شرعاً ويجب البحث عن بدائل حلال', 'مكروه فقط', 'جائز بشروط'], 'correct_option' => 1, 'difficulty' => 'medium'],
            ['track' => 'financial', 'question' => 'ما هو مفهوم "الاستثمار في العلاقة"؟', 'options' => ['شراء هدايا غالية', 'بذل الوقت والجهد والمال بحكمة لتقوية العلاقة الزوجية', 'توفير المال فقط', 'السفر المستمر'], 'correct_option' => 1, 'difficulty' => 'medium'],
            ['track' => 'financial', 'question' => 'كيف تتعامل مع فرق الدخل بين الزوجين؟', 'options' => ['الخلاف حتمي', 'التشاور والشفافية والاتفاق على آلية مشتركة للإنفاق', 'يتحكم الأعلى دخلاً', 'كل واحد ينفق على نفسه'], 'correct_option' => 1, 'difficulty' => 'medium'],
            ['track' => 'financial', 'question' => 'ما أهمية وجود حساب مشترك وحسابات شخصية؟', 'options' => ['غير مهم', 'حساب مشترك للمصاريف العامة وشخصي لكل طرف يعزز الثقة والاستقلالية', 'حساب واحد فقط', 'حسابات منفصلة تماماً'], 'correct_option' => 1, 'difficulty' => 'medium'],
            ['track' => 'financial', 'question' => 'ما هي علامات الإنفاق العاطفي (Emotional Spending)؟', 'options' => ['التخطيط الجيد', 'الشراء المندفع عند الحزن أو التوتر كوسيلة لتحسين المزاج', 'شراء الضروريات', 'الادخار الزائد'], 'correct_option' => 1, 'difficulty' => 'hard'],
            ['track' => 'financial', 'question' => 'ما الفرق بين الادخار والاستثمار؟', 'options' => ['لا فرق', 'الادخار حفظ المال بأمان، والاستثمار تنميته مع تحمل مخاطر محسوبة', 'الاستثمار أأمن', 'الادخار أربح'], 'correct_option' => 1, 'difficulty' => 'medium'],
        ];
    }

    private function practicalQuestions(): array
    {
        return [
            ['track' => 'practical', 'question' => 'كيف يتم تقسيم مهام المنزل بين الزوجين؟', 'options' => ['كلها على الزوجة', 'بالتشاور والمشاركة', 'كلها على الزوج', 'لا يهم'], 'correct_option' => 1, 'difficulty' => 'easy'],
            ['track' => 'practical', 'question' => 'ما أهمية إدارة الوقت في الحياة الزوجية؟', 'options' => ['غير مهمة', 'تحقيق التوازن بين العمل والبيت والعلاقة', 'مهمة للعمل فقط', 'مهمة للزوجة فقط'], 'correct_option' => 1, 'difficulty' => 'easy'],
            ['track' => 'practical', 'question' => 'كيف تبني علاقة صحية مع أهل الشريك؟', 'options' => ['تجنبهم', 'الاحترام والحدود الصحية', 'إرضاؤهم دائماً', 'القطيعة'], 'correct_option' => 1, 'difficulty' => 'easy'],
            ['track' => 'practical', 'question' => 'ما هي الحدود الصحية مع الأهل بعد الزواج؟', 'options' => ['لا حدود', 'الاحترام مع الحفاظ على استقلالية البيت', 'القطيعة', 'الأهل يقررون كل شيء'], 'correct_option' => 1, 'difficulty' => 'medium'],
            ['track' => 'practical', 'question' => 'ما أهم شيء في الأشهر الثلاثة الأولى؟', 'options' => ['التأثيث', 'التعرف على عادات الشريك والتكيف', 'الإنجاب', 'السفر'], 'correct_option' => 1, 'difficulty' => 'easy'],
            ['track' => 'practical', 'question' => 'كيف تتعامل مع عادات مزعجة عند الشريك؟', 'options' => ['الطلاق', 'الحوار بلطف والتقبل التدريجي', 'التجاهل الدائم', 'الشكوى للأهل'], 'correct_option' => 1, 'difficulty' => 'easy'],
            ['track' => 'practical', 'question' => 'ما هي علامات الحاجة لاستشارة أسرية؟', 'options' => ['أي خلاف', 'تكرار نفس المشكلة وعدم القدرة على الحل', 'لا توجد علامات', 'بعد سنة من الزواج'], 'correct_option' => 1, 'difficulty' => 'medium'],
            ['track' => 'practical', 'question' => 'متى يُفضل التخطيط للإنجاب؟', 'options' => ['فوراً بعد الزواج', 'بعد استقرار العلاقة والوضع المالي', 'لا يحتاج تخطيط', 'بعد 5 سنوات'], 'correct_option' => 1, 'difficulty' => 'easy'],
            ['track' => 'practical', 'question' => 'ما أهمية بناء تقاليد خاصة بالزوجين؟', 'options' => ['غير مهمة', 'تعزز الروابط وتخلق ذكريات مشتركة', 'مضيعة للوقت', 'للأطفال فقط'], 'correct_option' => 1, 'difficulty' => 'easy'],
            ['track' => 'practical', 'question' => 'ما أهم مهارة في السنة الأولى؟', 'options' => ['الطبخ', 'المرونة والصبر', 'كسب المال', 'إرضاء الأهل'], 'correct_option' => 1, 'difficulty' => 'easy'],
            // 10 additional
            ['track' => 'practical', 'question' => 'كيف تختار السكن المناسب كزوجين جدد؟', 'options' => ['الأكبر دائماً', 'حسب الميزانية والقرب من العمل وحاجات المرحلة الحالية', 'بجوار الأهل حتماً', 'الأبعد عن الأهل'], 'correct_option' => 1, 'difficulty' => 'easy'],
            ['track' => 'practical', 'question' => 'ما أهمية وجود "مساحة شخصية" لكل زوج؟', 'options' => ['تدل على مشكلة', 'ضرورية لصحة العلاقة والحفاظ على الذات', 'غير مقبولة', 'للرجل فقط'], 'correct_option' => 1, 'difficulty' => 'medium'],
            ['track' => 'practical', 'question' => 'كيف تتعامل مع الضيوف المتكررين بدون إرهاق؟', 'options' => ['منع الزيارات', 'وضع نظام واضح للزيارات بالتشاور بين الزوجين', 'قبول كل الزيارات', 'الشكوى المستمرة'], 'correct_option' => 1, 'difficulty' => 'easy'],
            ['track' => 'practical', 'question' => 'ما أهمية الاتفاق على أسلوب تربية الأطفال مبكراً؟', 'options' => ['غير مهم', 'يمنع الخلافات المستقبلية ويوفر بيئة مستقرة للأطفال', 'يمكن الاتفاق لاحقاً', 'كل طرف يربي بطريقته'], 'correct_option' => 1, 'difficulty' => 'medium'],
            ['track' => 'practical', 'question' => 'كيف تتعامل مع مقارنة حياتك الزوجية بالآخرين؟', 'options' => ['طبيعي ومفيد', 'تجنب المقارنة لأن لكل زوجين ظروفهم وكل بيت له خصوصيته', 'المقارنة تحفّز', 'شارك المقارنة مع شريكك'], 'correct_option' => 1, 'difficulty' => 'easy'],
            ['track' => 'practical', 'question' => 'ما دور التخطيط المشترك في نجاح الزواج؟', 'options' => ['غير ضروري', 'يوحّد الأهداف ويقلل المفاجآت ويعزز الشراكة', 'يقيّد الحرية', 'مهم للمال فقط'], 'correct_option' => 1, 'difficulty' => 'easy'],
            ['track' => 'practical', 'question' => 'كيف تتعامل مع الاختلاف في مستوى النظافة والترتيب؟', 'options' => ['الانفعال', 'التفاوض على معايير مشتركة مقبولة للطرفين', 'فرض معاييرك', 'السكوت والتحمل'], 'correct_option' => 1, 'difficulty' => 'easy'],
            ['track' => 'practical', 'question' => 'ما أهمية وجود أصدقاء مشتركين كزوجين؟', 'options' => ['غير مهم', 'يوفر شبكة دعم اجتماعي ويعزز الحياة الاجتماعية المشتركة', 'يسبب مشاكل', 'ممنوع بعد الزواج'], 'correct_option' => 1, 'difficulty' => 'easy'],
            ['track' => 'practical', 'question' => 'كيف تتعامل مع الروتين في الحياة الزوجية؟', 'options' => ['أمر طبيعي لا يحتاج علاج', 'كسر الروتين بأنشطة مشتركة جديدة ومفاجآت بسيطة', 'علامة على فشل الزواج', 'التجاهل'], 'correct_option' => 1, 'difficulty' => 'medium'],
            ['track' => 'practical', 'question' => 'ما أول خطوة عند حدوث خلاف كبير مع الشريك؟', 'options' => ['الاتصال بالأهل', 'التهدئة أولاً ثم اختيار وقت مناسب للحوار الهادئ', 'المغادرة فوراً', 'الصراخ لإيصال الرسالة'], 'correct_option' => 1, 'difficulty' => 'easy'],
        ];
    }
}
