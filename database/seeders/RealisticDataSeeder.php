<?php

namespace Database\Seeders;

use App\Models\Candidate;
use App\Models\Certificate;
use App\Models\CircleMember;
use App\Models\City;
use App\Models\CommunityPost;
use App\Models\Contribution;
use App\Models\CounselingSession;
use App\Models\Course;
use App\Models\CourseProgress;
use App\Models\FamilyRequest;
use App\Models\FundCircle;
use App\Models\GroupWedding;
use App\Models\Lesson;
use App\Models\Payout;
use App\Models\QuizAttempt;
use App\Models\Recommendation;
use App\Models\Recommender;
use App\Models\Report;
use App\Models\User;
use App\Models\WeddingRegistration;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RealisticDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🌱 Seeding realistic data...');

        $cities = City::all()->keyBy('name');
        if ($cities->isEmpty()) {
            $this->command->error('No cities found! Run CitySeeder first.');
            return;
        }

        // === 1. USERS ===
        $this->command->info('Creating users...');
        $users = $this->createUsers($cities);

        // === 2. RECOMMENDERS ===
        $this->command->info('Creating recommenders...');
        $recommenders = $this->createRecommenders($users);

        // === 3. CANDIDATES ===
        $this->command->info('Creating candidates...');
        $candidates = $this->createCandidates($recommenders, $cities);

        // === 4. RECOMMENDATIONS ===
        $this->command->info('Creating recommendations & family requests...');
        $this->createRecommendations($candidates, $recommenders);

        // === 5. FUND CIRCLES ===
        $this->command->info('Creating fund circles...');
        $this->createFundCircles($users, $cities);

        // === 6. WEDDING REGISTRATIONS ===
        $this->command->info('Creating wedding registrations...');
        $this->createWeddingRegistrations($users);

        // === 7. COURSE PROGRESS ===
        $this->command->info('Creating course progress...');
        $this->createCourseProgress($users);

        // === 8. COUNSELING SESSIONS ===
        $this->command->info('Creating counseling sessions...');
        $this->createCounselingSessions($users);

        // === 9. COMMUNITY POSTS ===
        $this->command->info('Creating community posts...');
        $this->createCommunityPosts($users);

        // === 10. REPORTS ===
        $this->command->info('Creating reports...');
        $this->createReports($users);

        $this->command->info('✅ Realistic data seeded successfully!');
    }

    private function createUsers($cities): array
    {
        $usersData = [
            // Recommenders (will be marked as recommender role later)
            ['name' => 'الشيخ عبدالله الحربي', 'email' => 'imam.abdullah@yassiru.com', 'phone' => '+201001112233', 'gender' => 'male', 'city' => 'القاهرة'],
            ['name' => 'الشيخ محمد الطنطاوي', 'email' => 'imam.tantawi@yassiru.com', 'phone' => '+201001112234', 'gender' => 'male', 'city' => 'الإسكندرية'],
            ['name' => 'الأستاذ إبراهيم العمري', 'email' => 'ibrahim.omari@yassiru.com', 'phone' => '+201001112235', 'gender' => 'male', 'city' => 'القاهرة'],
            ['name' => 'الشيخ يوسف القرضاوي', 'email' => 'imam.youssef@yassiru.com', 'phone' => '+966500001234', 'gender' => 'male', 'city' => 'الرياض'],
            ['name' => 'الأستاذة فاطمة الزهراء', 'email' => 'fatima.zahra@yassiru.com', 'phone' => '+201001112236', 'gender' => 'female', 'city' => 'القاهرة'],
            ['name' => 'الشيخ خالد المطيري', 'email' => 'imam.khaled@yassiru.com', 'phone' => '+966500001235', 'gender' => 'male', 'city' => 'جدة'],
            ['name' => 'الأستاذ عمر الفاروق', 'email' => 'omar.farouk@yassiru.com', 'phone' => '+201001112237', 'gender' => 'male', 'city' => 'المنصورة'],
            ['name' => 'الشيخ سعيد الحضرمي', 'email' => 'said.hadrami@yassiru.com', 'phone' => '+962700001234', 'gender' => 'male', 'city' => 'عمّان'],

            // Regular users (males - looking for marriage)
            ['name' => 'أحمد محمد علي', 'email' => 'ahmed.ali@yassiru.com', 'phone' => '+201005550001', 'gender' => 'male', 'city' => 'القاهرة'],
            ['name' => 'محمد عبدالرحمن', 'email' => 'mohamed.ar@yassiru.com', 'phone' => '+201005550002', 'gender' => 'male', 'city' => 'القاهرة'],
            ['name' => 'يوسف إبراهيم', 'email' => 'youssef.i@yassiru.com', 'phone' => '+201005550003', 'gender' => 'male', 'city' => 'الإسكندرية'],
            ['name' => 'كريم سعيد', 'email' => 'karim.s@yassiru.com', 'phone' => '+201005550004', 'gender' => 'male', 'city' => 'القاهرة'],
            ['name' => 'عمر حسن', 'email' => 'omar.h@yassiru.com', 'phone' => '+201005550005', 'gender' => 'male', 'city' => 'المنصورة'],
            ['name' => 'محمود خالد', 'email' => 'mahmoud.k@yassiru.com', 'phone' => '+201005550006', 'gender' => 'male', 'city' => 'الإسكندرية'],
            ['name' => 'إسلام عبدالله', 'email' => 'islam.a@yassiru.com', 'phone' => '+201005550007', 'gender' => 'male', 'city' => 'القاهرة'],
            ['name' => 'عبدالله السيد', 'email' => 'abdullah.s@yassiru.com', 'phone' => '+201005550008', 'gender' => 'male', 'city' => 'الإسكندرية'],
            ['name' => 'مصطفى أحمد', 'email' => 'mostafa.a@yassiru.com', 'phone' => '+201005550009', 'gender' => 'male', 'city' => 'القاهرة'],
            ['name' => 'حسام محمود', 'email' => 'hossam.m@yassiru.com', 'phone' => '+201005550010', 'gender' => 'male', 'city' => 'المنصورة'],
            ['name' => 'سعد العتيبي', 'email' => 'saad.o@yassiru.com', 'phone' => '+966500002001', 'gender' => 'male', 'city' => 'الرياض'],
            ['name' => 'فيصل الشهري', 'email' => 'faisal.s@yassiru.com', 'phone' => '+966500002002', 'gender' => 'male', 'city' => 'جدة'],
            ['name' => 'بدر القحطاني', 'email' => 'badr.q@yassiru.com', 'phone' => '+966500002003', 'gender' => 'male', 'city' => 'الرياض'],
            ['name' => 'طارق الحربي', 'email' => 'tareq.h@yassiru.com', 'phone' => '+966500002004', 'gender' => 'male', 'city' => 'جدة'],
            ['name' => 'حمزة العنزي', 'email' => 'hamza.o@yassiru.com', 'phone' => '+966500002005', 'gender' => 'male', 'city' => 'الرياض'],

            // Regular users (females - looking for marriage)
            ['name' => 'فاطمة محمد', 'email' => 'fatima.m@yassiru.com', 'phone' => '+201005550101', 'gender' => 'female', 'city' => 'القاهرة'],
            ['name' => 'عائشة أحمد', 'email' => 'aisha.a@yassiru.com', 'phone' => '+201005550102', 'gender' => 'female', 'city' => 'الإسكندرية'],
            ['name' => 'زينب علي', 'email' => 'zeinab.a@yassiru.com', 'phone' => '+201005550103', 'gender' => 'female', 'city' => 'القاهرة'],
            ['name' => 'مريم عبدالله', 'email' => 'mariam.a@yassiru.com', 'phone' => '+201005550104', 'gender' => 'female', 'city' => 'القاهرة'],
            ['name' => 'خديجة حسن', 'email' => 'khadija.h@yassiru.com', 'phone' => '+201005550105', 'gender' => 'female', 'city' => 'المنصورة'],
            ['name' => 'سارة محمود', 'email' => 'sara.m@yassiru.com', 'phone' => '+201005550106', 'gender' => 'female', 'city' => 'الإسكندرية'],
            ['name' => 'هاجر إبراهيم', 'email' => 'hajar.i@yassiru.com', 'phone' => '+201005550107', 'gender' => 'female', 'city' => 'القاهرة'],
            ['name' => 'نور الهدى', 'email' => 'nour.h@yassiru.com', 'phone' => '+201005550108', 'gender' => 'female', 'city' => 'الإسكندرية'],
            ['name' => 'أسماء سعد', 'email' => 'asmaa.s@yassiru.com', 'phone' => '+201005550109', 'gender' => 'female', 'city' => 'القاهرة'],
            ['name' => 'رحمة طارق', 'email' => 'rahma.t@yassiru.com', 'phone' => '+201005550110', 'gender' => 'female', 'city' => 'المنصورة'],
            ['name' => 'منال العبدالله', 'email' => 'manal.a@yassiru.com', 'phone' => '+966500003001', 'gender' => 'female', 'city' => 'الرياض'],
            ['name' => 'ريم القرشي', 'email' => 'reem.q@yassiru.com', 'phone' => '+966500003002', 'gender' => 'female', 'city' => 'جدة'],
            ['name' => 'هند الدوسري', 'email' => 'hind.d@yassiru.com', 'phone' => '+966500003003', 'gender' => 'female', 'city' => 'الرياض'],
            ['name' => 'لمى الغامدي', 'email' => 'lama.g@yassiru.com', 'phone' => '+966500003004', 'gender' => 'female', 'city' => 'جدة'],

            // Already married couples (for testimonials/counseling)
            ['name' => 'حسن الشاذلي', 'email' => 'hassan.sh@yassiru.com', 'phone' => '+201005550201', 'gender' => 'male', 'city' => 'القاهرة'],
            ['name' => 'بسمة الشاذلي', 'email' => 'basma.sh@yassiru.com', 'phone' => '+201005550202', 'gender' => 'female', 'city' => 'القاهرة'],
            ['name' => 'إسلام يسّرو', 'email' => 'islam@yassiru.com', 'phone' => '+201005550203', 'gender' => 'male', 'city' => 'القاهرة'],
        ];

        $users = [];
        foreach ($usersData as $data) {
            $cityId = isset($cities[$data['city']]) ? $cities[$data['city']]->id : null;

            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'phone' => $data['phone'],
                    'password' => Hash::make('password'),
                    'gender' => $data['gender'],
                    'city_id' => $cityId,
                    'is_verified' => true,
                    'has_certificate' => fake()->boolean(60),
                    'trust_score' => fake()->numberBetween(20, 200),
                    'email_verified_at' => now(),
                ]
            );
            $user->assignRole('user');
            $users[] = $user;
        }

        return $users;
    }

    private function createRecommenders(array $users): array
    {
        $recommendersData = [
            ['email' => 'imam.abdullah@yassiru.com', 'type' => 'imam', 'institution' => 'مسجد عمر بن الخطاب — مدينة نصر', 'bio' => 'إمام مسجد ومدرّس قرآن منذ 15 عاماً، أسعى لتيسير زواج الشباب', 'approved' => true, 'matches' => 8],
            ['email' => 'imam.tantawi@yassiru.com', 'type' => 'imam', 'institution' => 'مسجد المرسي أبو العباس — الإسكندرية', 'bio' => 'إمام وخطيب، أحب أن أكون سبباً في إعفاف الشباب', 'approved' => true, 'matches' => 5],
            ['email' => 'ibrahim.omari@yassiru.com', 'type' => 'teacher', 'institution' => 'مدرسة الفاروق الثانوية — القاهرة', 'bio' => 'معلم لغة عربية ومرشد طلابي، أعرف الكثير من الأسر الكريمة', 'approved' => true, 'matches' => 6],
            ['email' => 'imam.youssef@yassiru.com', 'type' => 'imam', 'institution' => 'مسجد الراجحي — الرياض', 'bio' => 'إمام وداعية، أعمل في خدمة المجتمع المسلم', 'approved' => true, 'matches' => 12],
            ['email' => 'fatima.zahra@yassiru.com', 'type' => 'community_leader', 'institution' => 'جمعية الأسرة المسلمة', 'bio' => 'أعمل في الإرشاد الأسري ومساعدة الفتيات على الزواج المبارك', 'approved' => true, 'matches' => 7],
            ['email' => 'imam.khaled@yassiru.com', 'type' => 'imam', 'institution' => 'مسجد الملك عبدالعزيز — جدة', 'bio' => 'إمام وخطيب جمعة، عضو في لجنة الزواج بالمسجد', 'approved' => true, 'matches' => 4],
            ['email' => 'omar.farouk@yassiru.com', 'type' => 'relative', 'institution' => null, 'bio' => 'كبير العائلة، أسعى لتيسير زواج أبناء الحي والأقارب', 'approved' => false, 'matches' => 0],
            ['email' => 'said.hadrami@yassiru.com', 'type' => 'imam', 'institution' => 'مسجد الحسين — عمّان', 'bio' => 'إمام مسجد ومدرّس علوم شرعية', 'approved' => true, 'matches' => 3],
        ];

        $recommenders = [];
        foreach ($recommendersData as $data) {
            $user = User::where('email', $data['email'])->first();
            if (!$user) continue;

            $recommender = Recommender::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'type' => $data['type'],
                    'institution' => $data['institution'],
                    'bio' => $data['bio'],
                    'is_approved' => $data['approved'],
                    'honor_pledge_signed' => true,
                    'approved_at' => $data['approved'] ? now()->subMonths(rand(1, 6)) : null,
                    'successful_matches' => $data['matches'],
                ]
            );

            if ($data['approved']) {
                $user->update(['role' => 'recommender']);
                $user->syncRoles(['recommender']);
            }

            $recommenders[] = $recommender;
        }

        return $recommenders;
    }

    private function createCandidates(array $recommenders, $cities): array
    {
        $maleCandidates = [
            ['name' => 'يوسف الأنصاري', 'age' => 27, 'education' => 'بكالوريوس هندسة', 'occupation' => 'مهندس برمجيات', 'religiosity' => 'committed', 'guardian' => 'محمد الأنصاري', 'guardian_phone' => '+201111222301', 'relation' => 'الأب'],
            ['name' => 'عمر القاسم', 'age' => 29, 'education' => 'ماجستير إدارة أعمال', 'occupation' => 'مدير تسويق', 'religiosity' => 'committed', 'guardian' => 'علي القاسم', 'guardian_phone' => '+201111222302', 'relation' => 'الأب'],
            ['name' => 'حسام الدين عبدالعزيز', 'age' => 26, 'education' => 'بكالوريوس صيدلة', 'occupation' => 'صيدلي', 'religiosity' => 'committed', 'guardian' => 'عبدالعزيز عبدالحميد', 'guardian_phone' => '+201111222303', 'relation' => 'الأب'],
            ['name' => 'كريم الشريف', 'age' => 28, 'education' => 'بكالوريوس تجارة', 'occupation' => 'محاسب', 'religiosity' => 'moderate', 'guardian' => 'سمير الشريف', 'guardian_phone' => '+201111222304', 'relation' => 'الأب'],
            ['name' => 'عبدالرحمن الحسيني', 'age' => 30, 'education' => 'دكتوراه طب', 'occupation' => 'طبيب أسنان', 'religiosity' => 'committed', 'guardian' => 'محمد الحسيني', 'guardian_phone' => '+201111222305', 'relation' => 'الأخ'],
            ['name' => 'إبراهيم النادي', 'age' => 25, 'education' => 'بكالوريوس آثار', 'occupation' => 'باحث آثار', 'religiosity' => 'committed', 'guardian' => 'فؤاد النادي', 'guardian_phone' => '+201111222306', 'relation' => 'الأب'],
            ['name' => 'محمد الجمال', 'age' => 32, 'education' => 'بكالوريوس هندسة مدنية', 'occupation' => 'مقاول', 'religiosity' => 'committed', 'guardian' => 'سعد الجمال', 'guardian_phone' => '+201111222307', 'relation' => 'الأخ'],
            ['name' => 'خالد الزيات', 'age' => 27, 'education' => 'بكالوريوس حاسبات', 'occupation' => 'مطوّر تطبيقات', 'religiosity' => 'moderate', 'guardian' => 'حسن الزيات', 'guardian_phone' => '+201111222308', 'relation' => 'الأب'],
            ['name' => 'أيمن المصري', 'age' => 28, 'education' => 'بكالوريوس إعلام', 'occupation' => 'صحفي', 'religiosity' => 'committed', 'guardian' => 'كمال المصري', 'guardian_phone' => '+201111222309', 'relation' => 'الأب'],
            ['name' => 'وليد الحفناوي', 'age' => 31, 'education' => 'ماجستير شريعة', 'occupation' => 'مدرّس قرآن', 'religiosity' => 'committed', 'guardian' => 'فتحي الحفناوي', 'guardian_phone' => '+201111222310', 'relation' => 'الأب'],
            ['name' => 'عبدالعزيز السعد', 'age' => 29, 'education' => 'بكالوريوس هندسة بترول', 'occupation' => 'مهندس بترول', 'religiosity' => 'committed', 'guardian' => 'سعد السعد', 'guardian_phone' => '+966500444401', 'relation' => 'الأب'],
            ['name' => 'فيصل الدوسري', 'age' => 27, 'education' => 'بكالوريوس طب', 'occupation' => 'طبيب', 'religiosity' => 'committed', 'guardian' => 'محمد الدوسري', 'guardian_phone' => '+966500444402', 'relation' => 'الأب'],
            ['name' => 'سعود البلوي', 'age' => 30, 'education' => 'ماجستير محاسبة', 'occupation' => 'مدير مالي', 'religiosity' => 'moderate', 'guardian' => 'عبدالله البلوي', 'guardian_phone' => '+966500444403', 'relation' => 'الأب'],
            ['name' => 'نواف الحربي', 'age' => 26, 'education' => 'بكالوريوس تربية', 'occupation' => 'معلم', 'religiosity' => 'committed', 'guardian' => 'فهد الحربي', 'guardian_phone' => '+966500444404', 'relation' => 'الأب'],
            ['name' => 'تركي العتيبي', 'age' => 28, 'education' => 'بكالوريوس قانون', 'occupation' => 'محامي', 'religiosity' => 'moderate', 'guardian' => 'سلطان العتيبي', 'guardian_phone' => '+966500444405', 'relation' => 'الأب'],
        ];

        $femaleCandidates = [
            ['name' => 'مروة عبدالرحمن', 'age' => 24, 'education' => 'بكالوريوس آداب', 'occupation' => 'معلمة لغة عربية', 'religiosity' => 'committed', 'guardian' => 'عبدالرحمن سيد', 'guardian_phone' => '+201111333401', 'relation' => 'الأب'],
            ['name' => 'هبة الشريف', 'age' => 23, 'education' => 'بكالوريوس صيدلة', 'occupation' => 'صيدلانية', 'religiosity' => 'committed', 'guardian' => 'محمود الشريف', 'guardian_phone' => '+201111333402', 'relation' => 'الأب'],
            ['name' => 'دعاء المنصوري', 'age' => 25, 'education' => 'ماجستير علم نفس', 'occupation' => 'أخصائية نفسية', 'religiosity' => 'committed', 'guardian' => 'سامي المنصوري', 'guardian_phone' => '+201111333403', 'relation' => 'الأب'],
            ['name' => 'إسراء الفقي', 'age' => 22, 'education' => 'بكالوريوس تربية', 'occupation' => 'معلمة رياض أطفال', 'religiosity' => 'committed', 'guardian' => 'أحمد الفقي', 'guardian_phone' => '+201111333404', 'relation' => 'الأب'],
            ['name' => 'نسرين الجبالي', 'age' => 26, 'education' => 'دكتوراه أحياء', 'occupation' => 'باحثة علمية', 'religiosity' => 'committed', 'guardian' => 'صبري الجبالي', 'guardian_phone' => '+201111333405', 'relation' => 'الأب'],
            ['name' => 'شيماء الصاوي', 'age' => 24, 'education' => 'بكالوريوس تجارة', 'occupation' => 'محاسبة', 'religiosity' => 'moderate', 'guardian' => 'علي الصاوي', 'guardian_phone' => '+201111333406', 'relation' => 'الأخ'],
            ['name' => 'هدى عبدالحميد', 'age' => 27, 'education' => 'بكالوريوس طب', 'occupation' => 'طبيبة', 'religiosity' => 'committed', 'guardian' => 'عبدالحميد فؤاد', 'guardian_phone' => '+201111333407', 'relation' => 'الأب'],
            ['name' => 'رضوى الشاذلي', 'age' => 23, 'education' => 'بكالوريوس آداب', 'occupation' => 'مترجمة', 'religiosity' => 'committed', 'guardian' => 'حسن الشاذلي', 'guardian_phone' => '+201111333408', 'relation' => 'الأب'],
            ['name' => 'إيمان الباز', 'age' => 25, 'education' => 'ماجستير شريعة', 'occupation' => 'معلمة قرآن', 'religiosity' => 'committed', 'guardian' => 'كمال الباز', 'guardian_phone' => '+201111333409', 'relation' => 'الأب'],
            ['name' => 'منى الجوهري', 'age' => 26, 'education' => 'بكالوريوس هندسة', 'occupation' => 'مهندسة برمجيات', 'religiosity' => 'committed', 'guardian' => 'محمد الجوهري', 'guardian_phone' => '+201111333410', 'relation' => 'الأب'],
            ['name' => 'العنود الشمري', 'age' => 22, 'education' => 'بكالوريوس تربية', 'occupation' => 'معلمة', 'religiosity' => 'committed', 'guardian' => 'محمد الشمري', 'guardian_phone' => '+966500555501', 'relation' => 'الأب'],
            ['name' => 'الجوهرة القحطاني', 'age' => 24, 'education' => 'بكالوريوس صيدلة', 'occupation' => 'صيدلانية', 'religiosity' => 'committed', 'guardian' => 'سعد القحطاني', 'guardian_phone' => '+966500555502', 'relation' => 'الأب'],
            ['name' => 'نوف الزهراني', 'age' => 25, 'education' => 'ماجستير لغة', 'occupation' => 'مدرّسة جامعية', 'religiosity' => 'committed', 'guardian' => 'علي الزهراني', 'guardian_phone' => '+966500555503', 'relation' => 'الأب'],
            ['name' => 'لطيفة الغامدي', 'age' => 23, 'education' => 'بكالوريوس طب أسنان', 'occupation' => 'طبيبة أسنان', 'religiosity' => 'committed', 'guardian' => 'فهد الغامدي', 'guardian_phone' => '+966500555504', 'relation' => 'الأب'],
            ['name' => 'دلال السبيعي', 'age' => 26, 'education' => 'دكتوراه إدارة', 'occupation' => 'استشارية إدارية', 'religiosity' => 'moderate', 'guardian' => 'عبدالعزيز السبيعي', 'guardian_phone' => '+966500555505', 'relation' => 'الأب'],
        ];

        $candidates = [];
        $allCandidatesData = array_merge(
            array_map(fn($c) => array_merge($c, ['gender' => 'male']), $maleCandidates),
            array_map(fn($c) => array_merge($c, ['gender' => 'female']), $femaleCandidates),
        );

        $approvedRecommenders = array_values(array_filter($recommenders, fn($r) => $r->is_approved));
        $cityList = $cities->values()->toArray();

        foreach ($allCandidatesData as $i => $data) {
            $recommender = $approvedRecommenders[$i % count($approvedRecommenders)];
            $city = $cityList[$i % count($cityList)];

            $candidate = Candidate::create([
                'recommender_id' => $recommender->id,
                'name' => $data['name'],
                'gender' => $data['gender'],
                'age' => $data['age'],
                'education' => $data['education'],
                'occupation' => $data['occupation'],
                'city_id' => $city['id'],
                'marital_status' => 'single',
                'religiosity_level' => $data['religiosity'],
                'guardian_name' => $data['guardian'],
                'guardian_phone' => $data['guardian_phone'],
                'guardian_relation' => $data['relation'],
                'recommender_notes' => 'مرشح/ة من أسرة كريمة، خلوق/ة وملتزم/ة',
                'status' => 'active',
            ]);

            $candidates[] = $candidate;

            // Update recommender count
            $recommender->increment('candidates_count');
        }

        return $candidates;
    }

    private function createRecommendations(array $candidates, array $recommenders): void
    {
        $males = array_values(array_filter($candidates, fn($c) => $c->gender === 'male'));
        $females = array_values(array_filter($candidates, fn($c) => $c->gender === 'female'));

        $reasons = [
            'تطابق في المستوى التعليمي والديني والاجتماعي بين الطرفين',
            'كلاهما من نفس المدينة وله نفس الاهتمامات الشخصية',
            'الفارق العمري مناسب والتوجه الديني متوافق',
            'كلاهما من أسرة محترمة ومستقرة، والمستوى المالي متقارب',
            'تطابق في الأهداف والطموحات المستقبلية للحياة الأسرية',
        ];

        $count = min(20, count($males), count($females));
        for ($i = 0; $i < $count; $i++) {
            $male = $males[$i];
            $female = $females[$i];

            $rec = Recommendation::create([
                'recommender_id' => $male->recommender_id,
                'male_candidate_id' => $male->id,
                'female_candidate_id' => $female->id,
                'reason' => $reasons[array_rand($reasons)],
                'compatibility_score' => fake()->numberBetween(70, 95),
                'status' => fake()->randomElement(['pending', 'pending', 'accepted', 'rejected']),
                'responded_at' => fake()->boolean(40) ? now()->subDays(rand(1, 30)) : null,
            ]);

            // Some get family requests
            if (fake()->boolean(50)) {
                FamilyRequest::create([
                    'recommendation_id' => $rec->id,
                    'initiated_by' => fake()->randomElement(['male_family', 'female_family']),
                    'status' => fake()->randomElement(['pending', 'accepted', 'meeting_scheduled']),
                    'meeting_date' => fake()->boolean(30) ? now()->addDays(rand(7, 30)) : null,
                    'meeting_location' => fake()->boolean(30) ? 'منزل العائلة بحضور ولي الأمر' : null,
                    'notes' => 'تم التواصل وحدد اللقاء',
                ]);
            }
        }
    }

    private function createFundCircles(array $users, $cities): void
    {
        // Get fresh users from DB to avoid stale data
        $regularUsers = User::where('role', 'user')->where('has_certificate', true)->get()->all();
        if (count($regularUsers) < 5) return;

        $circlesData = [
            ['name' => 'حلقة شباب القاهرة', 'city' => 'القاهرة', 'members' => 15, 'amount' => 1000, 'status' => 'active', 'round' => 5],
            ['name' => 'حلقة الإسكندرية الأولى', 'city' => 'الإسكندرية', 'members' => 12, 'amount' => 800, 'status' => 'active', 'round' => 3],
            ['name' => 'حلقة المنصورة الكبرى', 'city' => 'المنصورة', 'members' => 10, 'amount' => 1200, 'status' => 'active', 'round' => 7],
            ['name' => 'حلقة الرياض النخبة', 'city' => 'الرياض', 'members' => 20, 'amount' => 1500, 'status' => 'active', 'round' => 4, 'currency' => 'SAR'],
            ['name' => 'حلقة جدة الذهبية', 'city' => 'جدة', 'members' => 15, 'amount' => 2000, 'status' => 'active', 'round' => 2, 'currency' => 'SAR'],
            ['name' => 'حلقة شباب القاهرة الثانية', 'city' => 'القاهرة', 'members' => 15, 'amount' => 1500, 'status' => 'forming', 'round' => 0],
            ['name' => 'حلقة الإسكندرية الفتاة', 'city' => 'الإسكندرية', 'members' => 10, 'amount' => 600, 'status' => 'forming', 'round' => 0],
            ['name' => 'حلقة الرياض الجديدة', 'city' => 'الرياض', 'members' => 12, 'amount' => 1200, 'status' => 'forming', 'round' => 0, 'currency' => 'SAR'],
            ['name' => 'حلقة عمّان التعاونية', 'city' => 'عمّان', 'members' => 10, 'amount' => 100, 'status' => 'forming', 'round' => 0, 'currency' => 'JOD'],
            ['name' => 'حلقة شباب الجيزة', 'city' => 'القاهرة', 'members' => 20, 'amount' => 800, 'status' => 'forming', 'round' => 0],
        ];

        foreach ($circlesData as $i => $data) {
            $cityId = isset($cities[$data['city']]) ? $cities[$data['city']]->id : null;
            $creator = $regularUsers[$i % count($regularUsers)];

            $circle = FundCircle::create([
                'name' => $data['name'],
                'city_id' => $cityId,
                'created_by' => $creator->id,
                'max_members' => $data['members'],
                'monthly_amount' => $data['amount'],
                'currency' => $data['currency'] ?? 'EGP',
                'cycle_months' => $data['members'],
                'current_round' => $data['round'],
                'status' => $data['status'],
                'payout_method' => 'priority',
                'started_at' => $data['status'] === 'active' ? now()->subMonths($data['round']) : null,
                'guarantee_fee_percent' => 5.00,
                'service_fee_percent' => 3.00,
                'guarantee_balance' => $data['status'] === 'active' ? $data['amount'] * $data['members'] * 0.05 : 0,
            ]);

            // Add members
            $memberCount = $data['status'] === 'active'
                ? $data['members']
                : rand(2, intval($data['members'] / 2));

            $shuffledUsers = $regularUsers;
            shuffle($shuffledUsers);
            $selectedUsers = array_slice($shuffledUsers, 0, min($memberCount, count($shuffledUsers)));

            foreach ($selectedUsers as $idx => $user) {
                $member = CircleMember::create([
                    'circle_id' => $circle->id,
                    'user_id' => $user->id,
                    'payout_order' => $idx + 1,
                    'has_received_payout' => $data['status'] === 'active' && ($idx + 1) <= $data['round'],
                    'total_contributed' => $data['status'] === 'active' ? $data['amount'] * $data['round'] : 0,
                    'status' => 'active',
                    'joined_at' => now()->subMonths(rand(1, 6)),
                    'has_guarantor' => true,
                    'contract_signed' => true,
                    'guarantee_deposit' => $data['amount'] * 0.05,
                ]);

                // Create contributions for active circles
                if ($data['status'] === 'active') {
                    for ($r = 1; $r <= $data['round']; $r++) {
                        Contribution::create([
                            'circle_id' => $circle->id,
                            'member_id' => $member->id,
                            'amount' => $data['amount'],
                            'round_number' => $r,
                            'status' => 'paid',
                            'due_date' => now()->subMonths($data['round'] - $r + 1),
                            'paid_at' => now()->subMonths($data['round'] - $r + 1)->addDays(rand(1, 5)),
                            'payment_ref' => 'TXN-' . strtoupper(fake()->bothify('??##??##')),
                        ]);
                    }

                    // Create payout if it's their turn
                    if (($idx + 1) <= $data['round']) {
                        Payout::create([
                            'circle_id' => $circle->id,
                            'member_id' => $member->id,
                            'amount' => $data['amount'] * $data['members'],
                            'round_number' => $idx + 1,
                            'status' => 'completed',
                            'paid_at' => now()->subMonths($data['round'] - $idx),
                        ]);
                    }
                }
            }
        }
    }

    private function createWeddingRegistrations(array $users): void
    {
        $weddings = GroupWedding::where('status', 'upcoming')->get();
        if ($weddings->isEmpty()) return;

        $regularUsers = User::where('gender', 'male')->where('has_certificate', true)->get()->all();
        if (empty($regularUsers)) return;

        foreach ($weddings as $wedding) {
            $shuffled = array_values($regularUsers);
            shuffle($shuffled);
            $count = rand(3, min(10, $wedding->max_grooms - 2));
            $registrants = array_slice($shuffled, 0, $count);

            foreach ($registrants as $user) {
                $exists = WeddingRegistration::where('wedding_id', $wedding->id)
                    ->where('user_id', $user->id)
                    ->exists();
                if ($exists) continue;

                WeddingRegistration::create([
                    'wedding_id' => $wedding->id,
                    'user_id' => $user->id,
                    'payment_status' => fake()->randomElement(['pending', 'partial', 'paid']),
                    'payment_ref' => fake()->boolean(50) ? 'PAY-' . strtoupper(fake()->bothify('??##??##')) : null,
                    'notes' => fake()->boolean(30) ? 'سأحضر مع 30 ضيف من العائلة' : null,
                ]);
            }

            $wedding->update(['registered_count' => WeddingRegistration::where('wedding_id', $wedding->id)->count()]);
        }
    }

    private function createCourseProgress(array $users): void
    {
        $courses = Course::with('lessons')->get();
        if ($courses->isEmpty()) return;

        $regularUsers = User::where('role', 'user')->get()->all();

        foreach ($regularUsers as $user) {
            // Some users complete some lessons
            if (!fake()->boolean(60)) continue;

            foreach ($courses as $course) {
                $lessonsToComplete = $user->has_certificate
                    ? $course->lessons->count()
                    : rand(0, $course->lessons->count());

                $lessons = $course->lessons->take($lessonsToComplete);
                foreach ($lessons as $lesson) {
                    CourseProgress::firstOrCreate(
                        ['user_id' => $user->id, 'lesson_id' => $lesson->id],
                        [
                            'completed' => true,
                            'progress_percent' => 100,
                            'completed_at' => now()->subDays(rand(1, 60)),
                        ]
                    );
                }

                // Quiz attempt for completed courses
                if ($lessonsToComplete === $course->lessons->count() && $user->has_certificate) {
                    QuizAttempt::firstOrCreate(
                        ['user_id' => $user->id, 'course_id' => $course->id],
                        [
                            'score' => rand(7, 10),
                            'passed' => true,
                            'answers' => array_fill(0, 10, 1),
                            'attempt_number' => 1,
                        ]
                    );
                }
            }

            // Ensure a Certificate record exists for users flagged as certified.
            // Without this, has_certificate=true on the user row but the
            // /api/certificate endpoint returns 404 because the certificates
            // table has no matching row.
            if ($user->has_certificate) {
                Certificate::firstOrCreate(
                    ['user_id' => $user->id],
                    [
                        'certificate_number' => 'YSR-' . strtoupper(substr(md5($user->id . now()), 0, 8)),
                        'issued_at' => now()->subDays(rand(1, 90)),
                    ]
                );
            }
        }
    }

    private function createCounselingSessions(array $users): void
    {
        $regularUsers = User::where('role', 'user')->get()->all();

        $sampleNotes = [
            'استشارة عن إدارة الخلافات الزوجية في الأشهر الأولى',
            'مشاكل تواصل مع الأهل والاندماج مع العائلة الجديدة',
            'تخطيط مالي لتأسيس البيت بشكل صحيح',
            'كيف نوازن بين العمل والحياة الزوجية',
            'استشارة حول تربية الأطفال والاتفاق على المنهج',
            null,
        ];

        $createdCount = 0;
        foreach ($regularUsers as $user) {
            if (!fake()->boolean(35)) continue;

            $sessionsCount = rand(1, 3);
            for ($i = 0; $i < $sessionsCount; $i++) {
                $isPast = fake()->boolean(60);
                $date = $isPast
                    ? now()->subDays(rand(1, 90))->setHour(rand(9, 17))->setMinute(0)
                    : now()->addDays(rand(1, 30))->setHour(rand(9, 17))->setMinute(0);

                CounselingSession::create([
                    'user_id' => $user->id,
                    'type' => fake()->randomElement(['individual', 'individual', 'group']),
                    'scheduled_at' => $date,
                    'status' => $isPast
                        ? fake()->randomElement(['completed', 'completed', 'cancelled'])
                        : 'scheduled',
                    'notes' => $sampleNotes[array_rand($sampleNotes)],
                ]);
                $createdCount++;
            }
            if ($createdCount >= 30) break;
        }
    }

    private function createCommunityPosts(array $users): void
    {
        $users = User::where('role', 'user')->get()->all();
        $posts = [
            [
                'title' => 'تجربتي مع الدورة التأهيلية — كانت تغيير حقيقي',
                'content' => 'بعد إكمالي للدورة التأهيلية بمساراتها الأربعة، شعرت إن نظرتي للزواج تغيّرت تماماً. المسار النفسي خصوصاً أعطاني أدوات حقيقية للتعامل مع شريك حياتي المستقبلي. أنصح كل من يفكر في الزواج بإكمال الدورة قبل أي شيء.',
                'category' => 'experience',
            ],
            [
                'title' => 'كيف ساعدني صندوق التيسير في تجهيز شقتي',
                'content' => 'انضممت لحلقة صندوق فيها 15 عضو، دفعت 12 شهر بالتزام، وعند دوري قبضت 15 ألف جنيه. اشتريت بيها أساسيات الشقة وتزوجت بدون ديون. الحمد لله، تجربة محترمة وحلال 100%.',
                'category' => 'experience',
            ],
            [
                'title' => 'سؤال: كيف أختار المعرّف المناسب؟',
                'content' => 'السلام عليكم، أنا مسجل جديد في المنصة وأريد أن أبدأ في مرحلة التوفيق. كيف أختار المعرّف الأفضل لحالتي؟ هل هناك معايير معينة يجب البحث عنها؟',
                'category' => 'question',
            ],
            [
                'title' => 'نصيحة لمن يخطط لعرس جماعي',
                'content' => 'بعد ما حضرت 3 أعراس جماعية مع أصدقائي، أنصح كل من يفكر في الفكرة بأن يستفسر عن جميع التفاصيل قبل التسجيل. تأكد من القاعة، عدد الضيوف المسموح، البوفيه، والتصوير. كل التفاصيل مهمة.',
                'category' => 'tip',
            ],
            [
                'title' => 'أهمية الحوار قبل الزواج',
                'content' => 'من تجربتي ومن دراساتي، الحوار الصريح قبل الزواج يحل 80% من المشاكل المستقبلية. تكلموا في كل شيء: المال، الأطفال، العمل، السكن، العلاقة مع الأهل. لا تترك أي موضوع للصدفة.',
                'category' => 'advice',
            ],
            [
                'title' => 'تجربتي في التوفيق عبر معرّف موثوق',
                'content' => 'الحمد لله، تم زواجي بفضل توفيق إمام مسجدنا عبر المنصة. كانت تجربة محترمة جداً، الإمام تواصل مع والدي مباشرة، وحضرنا اللقاء الشرعي بحضور الأهل. لم يكن هناك أي تواصل مباشر بيننا قبل العقد.',
                'category' => 'experience',
            ],
            [
                'title' => 'كم مدة الدورة التأهيلية حقيقة؟',
                'content' => 'أنا مشغول جداً بالعمل، وأريد أن أعرف هل أستطيع إكمال الدورة في أسبوع واحد إذا خصصت ساعتين يومياً؟ أم تحتاج وقت أطول؟',
                'category' => 'question',
            ],
            [
                'title' => 'الحاسبة كانت صادمة لي بشكل إيجابي',
                'content' => 'لما حسبت تكاليف زواجي بالحاسبة، طلعت 180 ألف جنيه! وبعدها لما شفت كم سأوفّر مع يسّرو، الفرق كان مذهل. يعني ممكن أتجوّز بـ 60 ألف بدل 180. الفكرة عبقرية فعلاً.',
                'category' => 'experience',
            ],
        ];

        $regularUsers = $users;
        if (empty($regularUsers)) return;
        $regularUsers = array_values($regularUsers);

        foreach ($posts as $i => $post) {
            CommunityPost::create([
                'user_id' => $regularUsers[$i % count($regularUsers)]->id,
                'title' => $post['title'],
                'content' => $post['content'],
                'category' => $post['category'],
                'is_approved' => fake()->boolean(80),
            ]);
        }
    }

    private function createReports(array $users): void
    {
        $regularUsers = User::where('role', 'user')->get()->all();
        if (empty($regularUsers)) return;

        $reasons = [
            'محتوى غير لائق في رسالة المعرّف',
            'سلوك مشبوه من أحد الأعضاء',
            'محاولة احتيال في حلقة الصندوق',
            'معلومات غير صحيحة في ملف المرشح',
            'إساءة في التعامل من أحد المستخدمين',
        ];

        for ($i = 0; $i < 5; $i++) {
            Report::create([
                'reporter_id' => $regularUsers[$i % count($regularUsers)]->id,
                'reported_type' => fake()->randomElement(['user', 'recommender', 'candidate']),
                'reported_id' => rand(1, 10),
                'reason' => $reasons[$i],
                'status' => fake()->randomElement(['pending', 'investigating', 'resolved', 'dismissed']),
                'admin_notes' => fake()->boolean(50) ? 'تم التحقق من البلاغ وإجراء اللازم' : null,
            ]);
        }
    }
}
