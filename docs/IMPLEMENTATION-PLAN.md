# خطة تنفيذ منصة يسّرو — الخطة الشاملة

> هذه الخطة مكتوبة بمنظور Tech Lead مسؤول عن المشروع بالكامل.
> كل قسم يحتوي على قرارات عملية قابلة للتنفيذ فوراً.

---

## القسم الأول: تحليل المشروع وفهمه

### 1.1 الفكرة الأساسية والهدف

يسّرو ليست تطبيق زواج تقليدي. الفرق الجوهري:

| المنصات الحالية | يسّرو |
|---|---|
| تحل 15% من المشكلة (إيجاد شريك) | تحل 100% (كل العوائق) |
| تواصل مباشر بين الجنسين | صفر تواصل مباشر |
| لا حل مالي | صندوق تعاوني + أعراس جماعية |
| لا تأهيل | دورة إلزامية 10-15 ساعة |
| لا متابعة | رعاية ما بعد الزواج |

**الخلاصة:** المنصة هي منظومة متكاملة وليست مجرد موقع. كل ركيزة تعتمد على الأخرى:
- التأهيل → شرط دخول للتوفيق
- التوفيق → يفتح باب الصندوق والأعراس
- الصندوق → يموّل الزواج
- الأعراس الجماعية → تخفض التكلفة
- الرعاية → تحمي الاستثمار

### 1.2 المستخدمون الأساسيون (User Personas)

#### Persona 1: أحمد — الشاب الراغب بالزواج
- **العمر:** 24-32 سنة
- **المشكلة:** يريد الزواج لكن التكاليف تفوق قدرته (10 أضعاف دخله السنوي)
- **الدافع:** حل شرعي مالي + اجتماعي بدون ديون أو ربا
- **السلوك الرقمي:** يستخدم الموبايل 90% من الوقت، واتساب أساسي، فيسبوك/تويتر
- **نقطة الألم:** الإحباط من غلاء المهور + تجهيزات الشقة + تكاليف الفرح
- **ما يحتاجه من المنصة:** دورة تأهيلية → شهادة → انضمام لصندوق → تسجيل بعرس جماعي
- **المخاوف:** هل المنصة موثوقة؟ هل الصندوق آمن؟ هل سيجد شريكة مناسبة؟

#### Persona 2: فاطمة — الفتاة المقبلة على الزواج
- **العمر:** 20-28 سنة
- **المشكلة:** تريد زواجاً شرعياً محترماً بدون تنازل عن الخصوصية
- **الدافع:** منصة تحترم حياءها وتشرك عائلتها
- **السلوك الرقمي:** موبايل أولاً، إنستغرام، حذرة جداً من مواقع الزواج
- **نقطة الألم:** مواقع الزواج الحالية فيها تواصل مباشر مع رجال غرباء
- **ما يحتاجه من المنصة:** تسجيل عبر معرّف موثوق → ولي أمرها يتواصل → خصوصية كاملة
- **المخاوف:** خصوصية بياناتها، عدم عرض صورتها، جدية المتقدمين

#### Persona 3: الشيخ عبدالله — المعرّف (إمام مسجد)
- **العمر:** 40-60 سنة
- **المشكلة:** يأتيه شباب يطلبون المساعدة في الزواج ولا يملك أداة منظمة
- **الدافع:** خدمة مجتمعه + أداة تسهل عمله
- **السلوك الرقمي:** يستخدم الموبايل بشكل أساسي، واتساب، قد لا يجيد التقنية
- **نقطة الألم:** الطريقة التقليدية غير منظمة وتضيع الفرص
- **ما يحتاجه من المنصة:** واجهة بسيطة جداً، إضافة مرشحين بسهولة، اقتراحات ذكية
- **المخاوف:** هل المنصة شرعية؟ هل هي آمنة؟ هل ستحفظ سمعته؟

#### Persona 4: أم محمد — ولي الأمر
- **العمر:** 45-60 سنة
- **المشكلة:** تريد زوجاً مناسباً لابنتها لكن لا تثق بالإنترنت
- **الدافع:** ضمان الجدية والشرعية
- **السلوك الرقمي:** واتساب فقط غالباً
- **نقطة الألم:** الخوف من الغش والنصب
- **ما يحتاجه:** أن يتواصل المعرّف معها مباشرة عبر الهاتف، لا تحتاج حساب على المنصة

#### Persona 5: المشرف (Admin)
- **المهمة:** مراقبة المنصة، اعتماد المعرّفين، حل النزاعات، مراجعة التقارير
- **ما يحتاجه:** لوحة تحكم شاملة بإحصائيات واضحة وأدوات إدارة

### 1.3 رحلة المستخدم الكاملة (User Journey)

```
┌─────────────────────────────────────────────────────────────────┐
│                    رحلة المستخدم في يسّرو                        │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│  1. الاكتشاف                                                    │
│     ├── مقال على موقع إخباري (مملوك)                            │
│     ├── منشور على السوشال ميديا                                 │
│     └── بحث جوجل "تكاليف الزواج"                               │
│              │                                                    │
│              ▼                                                    │
│  2. الصفحة الرئيسية                                             │
│     ├── يرى الإحصائيات الصادمة → يشعر بالتعاطف                 │
│     ├── يرى الأركان الخمسة → يفهم الحل                         │
│     ├── يجرب الحاسبة → يرى الفرق بالأرقام                      │
│     └── يقرر التسجيل                                            │
│              │                                                    │
│              ▼                                                    │
│  3. التسجيل وتوثيق الهوية                                      │
│     ├── بيانات أساسية (اسم، بريد، هاتف، مدينة)                 │
│     ├── رفع بطاقة هوية (تُحذف بعد التوثيق)                     │
│     └── تأكيد البريد/الهاتف                                     │
│              │                                                    │
│              ▼                                                    │
│  4. الدورة التأهيلية (إلزامية)                                  │
│     ├── المسار الشرعي (4 دروس، 3 ساعات)                        │
│     ├── المسار النفسي (4 دروس، 3 ساعات)                        │
│     ├── المسار المالي (3 دروس، 2.5 ساعة)                       │
│     ├── المسار العملي (3 دروس، ساعتان)                         │
│     ├── اختبار لكل مسار (10 أسئلة، 70% للنجاح)                 │
│     └── شهادة جاهزية → تفتح باقي الخدمات                       │
│              │                                                    │
│              ▼                                                    │
│  5. التوفيق عبر المعرّف                                        │
│     ├── معرّف يسجل المرشح (بيانات + ملاحظات + ولي أمر)         │
│     ├── النظام يقترح توافقات                                    │
│     ├── المعرّف يقدم توصية                                      │
│     ├── العائلتان تتواصلان عبر المعرّف                          │
│     └── لقاء شرعي بحضور ولي الأمر                              │
│              │                                                    │
│              ▼                                                    │
│  6. التمويل                                                     │
│     ├── الانضمام لحلقة صندوق (15 عضو مثلاً)                    │
│     ├── مساهمة شهرية (1000 ج مثلاً)                            │
│     ├── الحصول على المبلغ الكامل (15,000 ج)                    │
│     └── أو التسجيل في عرس جماعي (توفير 60-70%)                 │
│              │                                                    │
│              ▼                                                    │
│  7. الزواج                                                      │
│     ├── عرس جماعي منظم عبر المنصة                              │
│     └── أو عرس فردي بخصومات الشركاء                            │
│              │                                                    │
│              ▼                                                    │
│  8. رعاية ما بعد الزواج                                        │
│     ├── جلسات استشارية شهرية                                    │
│     ├── مجتمع أزواج للإرشاد                                    │
│     ├── نصائح أسبوعية                                          │
│     └── خط طوارئ واتساب                                        │
│                                                                  │
└─────────────────────────────────────────────────────────────────┘
```

### 1.4 نقاط الألم والمخاطر المحتملة

| المخاطر | الاحتمال | الأثر | الحل |
|---|---|---|---|
| عدم ثقة المستخدمين بالصندوق المالي | عالي | حرج | عقد التزام رقمي + ضمانات + شفافية كاملة + بدء بمجموعات صغيرة (5-10) |
| تخلف عضو عن السداد في الصندوق | عالي | عالي | نظام تذكير 3 أيام → مهلة 5 أيام → تجميد → استبعاد بعد 15 يوم |
| قلة المعرّفين المسجلين في البداية | عالي | عالي | حملة مباشرة على أئمة ومعلمين عبر واتساب (هدف: 10 معرّفين أول شهر) |
| تسريب بيانات حساسة | متوسط | حرج | تشفير AES-256 + حذف الهوية بعد التوثيق + أقل صلاحيات ممكنة |
| اعتراض شرعي على الصندوق (شبهة ربا) | متوسط | حرج | استشارة هيئة شرعية مسبقاً + توثيق الفتوى + 2-3% رسوم خدمة وليست فائدة |
| انخفاض إكمال الدورة التأهيلية | متوسط | عالي | تقسيم لدروس قصيرة (40-50 دقيقة) + شريط تقدم + gamification خفيف |
| صعوبة الاستخدام للفئة العمرية الكبيرة (أولياء أمور) | عالي | متوسط | ولي الأمر لا يحتاج حساب — المعرّف يتواصل معه هاتفياً |
| المنافسة من منصات زواج قائمة | منخفض | متوسط | يسّرو ليست منافسة — هي فئة جديدة تماماً |

---

## القسم الثاني: UI/UX — تجربة المستخدم

### 2.1 مبادئ التصميم الأساسية

```
┌──────────────────────────────────────────────────┐
│            مبادئ تصميم يسّرو                      │
├──────────────────────────────────────────────────┤
│                                                    │
│  1. الثقة أولاً                                   │
│     كل عنصر يجب أن يبني ثقة:                     │
│     - ألوان هادئة (teal + ذهبي)                   │
│     - مساحات بيضاء كبيرة                         │
│     - خطوط واضحة ومقروءة                         │
│     - أيقونات بسيطة بدون مبالغة                   │
│                                                    │
│  2. البساطة في الظاهر، القوة في الباطن           │
│     واجهات نظيفة لكن خلفها نظام معقد              │
│                                                    │
│  3. الموبايل أولاً وأخيراً                        │
│     70%+ من المستخدمين على الموبايل               │
│     كل شاشة تُصمم للموبايل أولاً                 │
│                                                    │
│  4. إحترام الخصوصية في التصميم                    │
│     لا صور، لا بروفايلات للتصفح                   │
│     كل شيء يمر عبر المعرّف                       │
│                                                    │
│  5. التوجيه الواضح                                │
│     المستخدم يعرف دائماً:                        │
│     - أين هو الآن                                │
│     - ماذا يفعل بعد ذلك                          │
│     - كم تبقى له                                 │
│                                                    │
└──────────────────────────────────────────────────┘
```

### 2.2 نظام التصميم الكامل (Design System)

#### الألوان

```scss
// الألوان الأساسية
$primary: #0d7377;        // Teal — اللون الرئيسي (ثقة + هدوء)
$primary-light: #e8f5f5;  // خلفية خفيفة للبطاقات المحددة
$primary-dark: #095456;   // hover states

$secondary: #b8860b;      // ذهبي — التميز والقيمة
$secondary-light: #faf3e0;
$secondary-dark: #8a6508;

// ألوان الحالات
$success: #1b7a4a;        // أخضر — إتمام، نجاح، تحقق
$danger: #c0392b;         // أحمر — خطأ، تحذير، بلاغ
$warning: #e67e22;        // برتقالي — انتباه، تأخر
$info: #1565c0;           // أزرق — معلومة، إمام

// ألوان الخلفيات
$bg-primary: #faf9f6;     // خلفية الصفحة — أبيض دافئ
$bg-surface: #ffffff;     // خلفية البطاقات
$bg-surface-2: #f3efe8;   // خلفية ثانوية
$bg-dark: #1a1a2a;        // Footer والأقسام الداكنة

// ألوان النصوص
$text-primary: #1a1a2a;   // العناوين والنص الأساسي
$text-secondary: #4a4a5e; // النص الثانوي
$text-muted: #8888a0;     // النص الباهت
$text-on-dark: #ffffff;   // نص على خلفية داكنة

// الحدود
$border-color: #e0d8cc;   // حدود البطاقات
$border-light: #f0ece4;   // حدود خفيفة

// ألوان الشارات (Badges)
$badge-verified: $success;     // معتمد/حاصل على شهادة
$badge-pending: $secondary;    // قيد المراجعة
$badge-rejected: $danger;      // مرفوض
$badge-imam: $info;            // إمام
$badge-fund: #6a1b4d;          // حلقة صندوق
```

#### الخطوط

```scss
// الخط الأساسي — Cairo
$font-family-base: 'Cairo', sans-serif;
$font-family-heading: 'Cairo', sans-serif;
$font-family-verse: 'Amiri', serif; // فقط للآيات والأحاديث

// الأحجام
$font-size-h1: 2.5rem;    // 40px — العناوين الرئيسية
$font-size-h2: 2rem;      // 32px
$font-size-h3: 1.5rem;    // 24px
$font-size-h4: 1.25rem;   // 20px
$font-size-body: 1rem;     // 16px — النص الأساسي
$font-size-small: 0.875rem; // 14px
$font-size-xs: 0.75rem;    // 12px

// الأوزان
$font-weight-regular: 400;
$font-weight-medium: 600;
$font-weight-bold: 700;
$font-weight-black: 900;   // للأرقام الكبيرة والإحصائيات

// للموبايل — تقليل الأحجام
@media (max-width: 768px) {
  $font-size-h1: 1.75rem;  // 28px
  $font-size-h2: 1.5rem;   // 24px
  $font-size-h3: 1.25rem;  // 20px
}
```

#### المكونات الأساسية

```scss
// البطاقات
$card-border-radius: 16px;
$card-padding: 24px;
$card-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
$card-shadow-hover: 0 8px 24px rgba(0, 0, 0, 0.12);
$card-transition: transform 0.2s ease, box-shadow 0.2s ease;

// الأزرار
$btn-border-radius: 12px;
$btn-padding-y: 12px;
$btn-padding-x: 24px;
$btn-min-height: 48px;     // للموبايل — سهولة اللمس
$btn-font-weight: 700;

// الحقول (Inputs)
$input-border-radius: 12px;
$input-padding: 12px 16px;
$input-border-color: $border-color;
$input-focus-border: $primary;
$input-focus-shadow: 0 0 0 3px rgba($primary, 0.15);

// المسافات
$spacing-xs: 4px;
$spacing-sm: 8px;
$spacing-md: 16px;
$spacing-lg: 24px;
$spacing-xl: 32px;
$spacing-2xl: 48px;
$spacing-3xl: 64px;
$section-padding: 80px 0; // مسافة بين الأقسام
```

#### حالات المكونات

```
┌──────────────────────────────────────────────────────────────┐
│  الأزرار                                                      │
├──────────────────────────────────────────────────────────────┤
│  Primary:   bg #0d7377, text white, hover → darken 10%       │
│  Outline:   border #0d7377, text #0d7377, hover → fill       │
│  Gold:      bg #b8860b, text white, hover → darken 10%       │
│  Danger:    bg #c0392b, text white (للبلاغات فقط)            │
│  Disabled:  opacity 0.5, cursor not-allowed                   │
│  Loading:   spinner + text "جاري..."                          │
├──────────────────────────────────────────────────────────────┤
│  البطاقات                                                     │
├──────────────────────────────────────────────────────────────┤
│  Default:   bg white, border light, shadow subtle             │
│  Hover:     translateY(-4px), shadow stronger                 │
│  Active:    border primary, bg primary-light                  │
│  Disabled:  opacity 0.6                                       │
├──────────────────────────────────────────────────────────────┤
│  الشارات (Badges)                                             │
├──────────────────────────────────────────────────────────────┤
│  Verified:  bg green-light, text green, border green          │
│  Pending:   bg gold-light, text gold, border gold             │
│  Rejected:  bg red-light, text red, border red                │
│  Info:      bg blue-light, text blue, border blue             │
├──────────────────────────────────────────────────────────────┤
│  حقول الإدخال                                                 │
├──────────────────────────────────────────────────────────────┤
│  Default:   border gray, bg white                             │
│  Focus:     border primary, shadow primary-light              │
│  Error:     border red, text red below, shake animation       │
│  Success:   border green, checkmark icon                      │
│  Disabled:  bg gray-light, cursor not-allowed                 │
└──────────────────────────────────────────────────────────────┘
```

### 2.3 هرمية المعلومات لكل شاشة

#### الصفحة الرئيسية — هدفها: إقناع الزائر بالتسجيل

```
[1] Hero: حديث شريف + عنوان المنصة + وصف مختصر + زرّان (سجل الآن / جرب الحاسبة)
[2] إحصائيات الأزمة: 4 أرقام صادمة (بدون تفاصيل — فقط أرقام كبيرة)
[3] الأركان الخمسة: 5 بطاقات قابلة للنقر (تأهيل، توفيق، صندوق، أعراس، رعاية)
[4] الحاسبة المصغرة: اختر مدينتك → شاهد الفرق مع يسّرو
[5] إحصائيات المنصة: عدد المستخدمين، الشهادات، الحلقات، الزيجات
[6] CTA نهائي: "ابدأ رحلتك" + زر تسجيل
[7] Footer: روابط + تواصل + حقوق
```

#### شاشة الدورة — هدفها: إبقاء المستخدم متحمساً لإكمال المسارات

```
[1] شريط التقدم الكلي: XX% مكتمل (بارز ومحفّز)
[2] بطاقات المسارات الأربعة: لكل مسار (اسم، تقدم، عدد الدروس، مدة)
[3] المسار المفتوح حالياً: قائمة الدروس مع حالة كل درس (مكتمل/حالي/مقفل)
[4] شريط تحفيزي: "باقي لك 3 دروس فقط للشهادة!"
```

#### لوحة المعرّف — هدفها: تسهيل إدارة المرشحين والتوصيات

```
[1] 3 بطاقات إحصائية: عدد المرشحين، التوصيات، النجاحات
[2] عمود يسار: قائمة المرشحين (اسم، عمر، مهنة، ولي أمر، حالة)
[3] عمود يمين: اقتراحات التوافق (مرشح أ + مرشحة ب + سبب التوافق)
[4] زر إضافة مرشح جديد (بارز)
```

### 2.4 تحسينات الموبايل

```
القرارات الحاسمة لتجربة الموبايل:

1. الأزرار: min-height 48px (ليست 44px — أفضل للإبهام)
2. النافبار: hamburger menu مع slide-in من اليمين (RTL)
3. الجداول: تتحول لبطاقات عمودية على الشاشات الصغيرة
4. النماذج: حقل واحد في كل سطر، لوحة مفاتيح مناسبة (tel للهاتف، email للبريد)
5. الحاسبة: sliders بحجم كبير وقيم واضحة
6. شريط التقدم: ثابت في أعلى الشاشة أثناء الدورة
7. الشهادة: قابلة للتحميل كـ PDF + مشاركة مباشرة
8. التنقل السفلي: شريط تنقل سفلي ثابت للمستخدم المسجل
   (الرئيسية | الدورات | الصندوق | الأعراس | حسابي)
```

---

## القسم الثالث: Frontend Architecture

### 3.1 التقنيات المختارة ولماذا

```
Vue.js 3 (Composition API + SFC)
├── لماذا Vue وليس React:
│   ├── learning curve أقل (مطور واحد)
│   ├── ecosystem أبسط (Vue Router + Pinia vs React Router + Redux/Zustand)
│   ├── SFC (Single File Components) = تنظيم طبيعي
│   └── أداء ممتاز مع Vite
│
Bootstrap 5.3 RTL
├── لماذا وليس Tailwind:
│   ├── RTL مدمج ومستقر
│   ├── مكونات جاهزة (modals, dropdowns, tooltips)
│   ├── أسرع للمطور الواحد
│   └── يمكن تخصيصه بالكامل عبر SCSS variables
│
Vite (Build Tool)
├── سريع جداً في التطوير (HMR فوري)
└── code splitting تلقائي مع Vue Router

الحزم الأساسية:
├── vue@3           — الإطار
├── vue-router@4    — التوجيه
├── pinia           — إدارة الحالة
├── axios           — HTTP requests
├── vee-validate    — التحقق من النماذج
├── yup             — مخططات التحقق
├── bootstrap@5.3.3 — واجهة المستخدم
├── sass            — تخصيص Bootstrap
└── @vueuse/core    — composables مفيدة (useLocalStorage, useIntersectionObserver)
```

### 3.2 هيكل المشروع

```
resources/js/
├── App.vue                          # المكون الجذري
├── router/
│   └── index.js                     # كل المسارات + guards
├── stores/
│   ├── auth.js                      # حالة المصادقة + المستخدم
│   ├── course.js                    # تقدم الدورة
│   ├── fund.js                      # بيانات الصندوق
│   └── notification.js              # الإشعارات الفورية
├── composables/
│   ├── useApi.js                    # axios wrapper مع error handling
│   ├── useAuth.js                   # login, logout, user state
│   ├── useCurrency.js               # تنسيق العملات حسب المدينة
│   └── useValidation.js             # قواعد تحقق مشتركة
├── views/
│   ├── public/
│   │   ├── HomePage.vue
│   │   ├── AboutPage.vue
│   │   ├── LoginPage.vue
│   │   ├── RegisterPage.vue
│   │   └── CostCalculatorPage.vue
│   ├── course/
│   │   ├── CourseListPage.vue
│   │   ├── CoursePage.vue
│   │   ├── LessonPage.vue
│   │   ├── QuizPage.vue
│   │   └── CertificatePage.vue
│   ├── matching/
│   │   ├── RecommenderDashboard.vue
│   │   ├── AddCandidatePage.vue
│   │   ├── RecommendationsPage.vue
│   │   └── FamilyRequestsPage.vue
│   ├── fund/
│   │   ├── FundOverviewPage.vue
│   │   ├── CreateCirclePage.vue
│   │   ├── CircleDashboard.vue
│   │   └── ContributionPage.vue
│   ├── wedding/
│   │   ├── GroupWeddingsPage.vue
│   │   ├── WeddingDetailPage.vue
│   │   └── VendorsPage.vue
│   ├── aftercare/
│   │   ├── CounselingPage.vue
│   │   └── CommunityPage.vue
│   └── admin/
│       ├── AdminDashboard.vue
│       ├── ManageUsers.vue
│       ├── ManageRecommenders.vue
│       └── ManageCircles.vue
├── components/
│   ├── layout/
│   │   ├── AppNavbar.vue            # نافبار + hamburger للموبايل
│   │   ├── AppFooter.vue
│   │   ├── AppSidebar.vue           # sidebar للوحات التحكم
│   │   ├── MobileBottomNav.vue      # تنقل سفلي للموبايل
│   │   └── DashboardLayout.vue      # غلاف لوحات التحكم
│   ├── course/
│   │   ├── CourseCard.vue
│   │   ├── VideoPlayer.vue          # HLS.js player
│   │   ├── ProgressBar.vue
│   │   └── QuizQuestion.vue
│   ├── matching/
│   │   ├── CandidateForm.vue
│   │   ├── RecommendationCard.vue
│   │   └── FamilyRequestCard.vue
│   ├── fund/
│   │   ├── CircleCard.vue
│   │   ├── ContributionTimeline.vue
│   │   ├── PayoutSchedule.vue
│   │   └── FundStats.vue
│   └── ui/
│       ├── BaseButton.vue           # زر قابل لإعادة الاستخدام
│       ├── BaseCard.vue             # بطاقة أساسية
│       ├── BaseBadge.vue            # شارة
│       ├── BaseModal.vue            # نافذة منبثقة
│       ├── LoadingSpinner.vue
│       ├── AlertMessage.vue
│       ├── ConfirmDialog.vue
│       ├── StatsCounter.vue         # عداد متحرك للأرقام
│       ├── CitySelector.vue
│       └── EmptyState.vue           # حالة فارغة مع رسالة وCTA
└── assets/
    └── scss/
        ├── _variables.scss          # تجاوز متغيرات Bootstrap
        ├── _islamic-theme.scss      # أنماط إسلامية (آيات، أحاديث)
        ├── _rtl-fixes.scss          # إصلاحات RTL
        ├── _components.scss         # أنماط المكونات المخصصة
        ├── _animations.scss         # حركات مخصصة
        ├── _utilities.scss          # أدوات مساعدة
        └── app.scss                 # الملف الرئيسي
```

### 3.3 مكونات قابلة لإعادة الاستخدام (Reusable Components)

**القاعدة:** كل مكون يُستخدم في أكثر من مكانين = يصبح مكون مشترك في `components/ui/`.

```vue
<!-- BaseButton.vue — مثال على واجهة المكون -->
<template>
  <button
    :class="['btn', variantClass, sizeClass, { 'w-100': block }]"
    :disabled="disabled || loading"
    @click="$emit('click', $event)"
  >
    <span v-if="loading" class="spinner-border spinner-border-sm me-2" />
    <slot />
  </button>
</template>

<!-- Props: variant (primary|outline|gold|danger), size (sm|md|lg), block, loading, disabled -->
```

```vue
<!-- BaseCard.vue -->
<!-- Props: hoverable (Boolean), padding (sm|md|lg) -->
<!-- Slots: default, header, footer -->
```

```vue
<!-- StatsCounter.vue — عداد أرقام متحرك -->
<!-- Props: value (Number), suffix (String مثل "مستخدم"), duration (ms) -->
<!-- يستخدم: requestAnimationFrame لعد سلس -->
```

### 3.4 إدارة الحالة (State Management)

```javascript
// stores/auth.js — المتجر الأهم
export const useAuthStore = defineStore('auth', () => {
  const user = ref(null)
  const token = ref(localStorage.getItem('token'))
  const isAuthenticated = computed(() => !!token.value)
  const isRecommender = computed(() => user.value?.role === 'recommender')
  const isAdmin = computed(() => user.value?.role === 'admin')
  const hasCertificate = computed(() => user.value?.has_certificate)

  // كل الـ API calls المتعلقة بالمصادقة هنا
  async function login(credentials) { ... }
  async function register(data) { ... }
  async function logout() { ... }
  async function fetchUser() { ... }

  return { user, token, isAuthenticated, isRecommender, isAdmin, hasCertificate, login, register, logout, fetchUser }
})
```

### 3.5 حماية المسارات (Route Guards)

```javascript
// router/index.js
router.beforeEach((to, from, next) => {
  const auth = useAuthStore()

  // صفحات تتطلب تسجيل دخول
  if (to.meta.requiresAuth && !auth.isAuthenticated) {
    return next({ name: 'login', query: { redirect: to.fullPath } })
  }

  // صفحات تتطلب شهادة (مثل إنشاء حلقة صندوق)
  if (to.meta.requiresCertificate && !auth.hasCertificate) {
    return next({ name: 'courses' }) // وجّهه للدورة أولاً
  }

  // صفحات المعرّف فقط
  if (to.meta.requiresRecommender && !auth.isRecommender) {
    return next({ name: 'home' })
  }

  // صفحات الأدمن فقط
  if (to.meta.requiresAdmin && !auth.isAdmin) {
    return next({ name: 'home' })
  }

  next()
})
```

### 3.6 التعامل مع الـ API

```javascript
// composables/useApi.js
export function useApi() {
  const api = axios.create({
    baseURL: '/api',
    headers: { 'Accept': 'application/json' }
  })

  // إرفاق التوكن تلقائياً
  api.interceptors.request.use(config => {
    const token = localStorage.getItem('token')
    if (token) config.headers.Authorization = `Bearer ${token}`
    return config
  })

  // معالجة الأخطاء مركزياً
  api.interceptors.response.use(
    response => response,
    error => {
      if (error.response?.status === 401) {
        // token منتهي → تسجيل خروج
        localStorage.removeItem('token')
        window.location.href = '/login'
      }
      if (error.response?.status === 422) {
        // أخطاء تحقق → إرجاعها للنموذج
        return Promise.reject(error.response.data.errors)
      }
      if (error.response?.status === 429) {
        // rate limit → رسالة انتظار
      }
      return Promise.reject(error)
    }
  )

  return { api }
}
```

---

## القسم الرابع: Backend Architecture

### 4.1 المعمارية

```
Laravel 11 — API-First Architecture
│
├── Pattern: Service Layer + Repository (عند الحاجة)
│   Controller → Service → Model
│   Controller: يستقبل الطلب، يتحقق، يرجع الاستجابة
│   Service: منطق الأعمال (business logic)
│   Model: العلاقات والـ scopes
│
├── لماذا هذا النمط:
│   ├── Controller نظيف وقصير
│   ├── Service قابل للاختبار بشكل مستقل
│   ├── يمكن إعادة استخدام الـ Service في Jobs و Commands
│   └── بدون تعقيد زائد (لا Repository pattern لمشروع بمطور واحد)
│
└── مجلد Services فقط للمنطق المعقد:
    ├── IdentityVerificationService
    ├── CertificateGeneratorService
    ├── MatchSuggestionService
    ├── FundCalculationService
    ├── PaymentService (Stripe + Fawry)
    ├── NotificationService
    └── CostCalculatorService
```

### 4.2 قاعدة البيانات — المخطط الكامل

```sql
-- ═══════════════════════════════════════════════════════
-- الجداول الأساسية
-- ═══════════════════════════════════════════════════════

-- المستخدمون
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    phone VARCHAR(20) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'recommender', 'admin') DEFAULT 'user',
    gender ENUM('male', 'female') NOT NULL,
    national_id VARCHAR(20) NULL,          -- يُحذف بعد التوثيق
    is_verified BOOLEAN DEFAULT FALSE,
    has_certificate BOOLEAN DEFAULT FALSE,
    city_id BIGINT UNSIGNED NULL,
    date_of_birth DATE NULL,
    email_verified_at TIMESTAMP NULL,
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (city_id) REFERENCES cities(id)
);

-- المدن
CREATE TABLE cities (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    country VARCHAR(100) NOT NULL,
    country_code VARCHAR(3) NOT NULL,
    avg_marriage_cost DECIMAL(12, 2) NOT NULL,
    currency VARCHAR(10) DEFAULT 'EGP',
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- ═══════════════════════════════════════════════════════
-- نظام التأهيل (الدورات)
-- ═══════════════════════════════════════════════════════

CREATE TABLE courses (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    track ENUM('shariah', 'psychology', 'financial', 'practical') NOT NULL,
    description TEXT,
    duration_hours DECIMAL(4, 1) NOT NULL,
    lessons_count INT UNSIGNED DEFAULT 0,
    order_index INT UNSIGNED DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

CREATE TABLE lessons (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    course_id BIGINT UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    type ENUM('video', 'article', 'interactive') DEFAULT 'video',
    content TEXT,
    video_url VARCHAR(500) NULL,
    duration_minutes INT UNSIGNED NOT NULL,
    order_index INT UNSIGNED DEFAULT 0,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
);

CREATE TABLE course_progress (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    lesson_id BIGINT UNSIGNED NOT NULL,
    completed BOOLEAN DEFAULT FALSE,
    progress_percent DECIMAL(5, 2) DEFAULT 0,
    completed_at TIMESTAMP NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    UNIQUE KEY (user_id, lesson_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (lesson_id) REFERENCES lessons(id) ON DELETE CASCADE
);

CREATE TABLE quiz_attempts (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    course_id BIGINT UNSIGNED NOT NULL,
    score DECIMAL(5, 2) NOT NULL,           -- مثل 7.00 من 10
    passed BOOLEAN DEFAULT FALSE,
    answers JSON NOT NULL,                   -- الإجابات المفصلة
    attempt_number INT UNSIGNED DEFAULT 1,
    created_at TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
);

CREATE TABLE certificates (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED UNIQUE NOT NULL,
    certificate_number VARCHAR(50) UNIQUE NOT NULL,
    issued_at TIMESTAMP NOT NULL,
    pdf_path VARCHAR(500) NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ═══════════════════════════════════════════════════════
-- نظام التوفيق (المعرّفون)
-- ═══════════════════════════════════════════════════════

CREATE TABLE recommenders (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED UNIQUE NOT NULL,
    type ENUM('imam', 'teacher', 'relative', 'community_leader') NOT NULL,
    institution VARCHAR(255) NULL,          -- اسم المسجد/المدرسة
    bio TEXT NULL,
    candidates_count INT UNSIGNED DEFAULT 0,
    successful_matches INT UNSIGNED DEFAULT 0,
    is_approved BOOLEAN DEFAULT FALSE,
    honor_pledge_signed BOOLEAN DEFAULT FALSE,
    approved_at TIMESTAMP NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE candidates (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NULL,           -- ربط بحساب إن وجد
    recommender_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    gender ENUM('male', 'female') NOT NULL,
    age INT UNSIGNED NOT NULL,
    education VARCHAR(255) NULL,
    occupation VARCHAR(255) NULL,
    city_id BIGINT UNSIGNED NULL,
    marital_status ENUM('single', 'divorced', 'widowed') DEFAULT 'single',
    religiosity_level ENUM('committed', 'moderate', 'learning') DEFAULT 'committed',
    guardian_name VARCHAR(255) NOT NULL,
    guardian_phone VARCHAR(255) NOT NULL,    -- مشفّر AES-256
    guardian_relation VARCHAR(100) NOT NULL,
    preferences JSON NULL,                   -- تفضيلات (عمر، تعليم، مدينة)
    recommender_notes TEXT NULL,
    status ENUM('active', 'matched', 'withdrawn') DEFAULT 'active',
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (recommender_id) REFERENCES recommenders(id) ON DELETE CASCADE,
    FOREIGN KEY (city_id) REFERENCES cities(id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE recommendations (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    recommender_id BIGINT UNSIGNED NOT NULL,
    male_candidate_id BIGINT UNSIGNED NOT NULL,
    female_candidate_id BIGINT UNSIGNED NOT NULL,
    reason TEXT NOT NULL,
    compatibility_score DECIMAL(5, 2) NULL, -- نسبة التوافق المقترحة
    status ENUM('pending', 'accepted', 'rejected') DEFAULT 'pending',
    responded_at TIMESTAMP NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (recommender_id) REFERENCES recommenders(id),
    FOREIGN KEY (male_candidate_id) REFERENCES candidates(id),
    FOREIGN KEY (female_candidate_id) REFERENCES candidates(id)
);

CREATE TABLE family_requests (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    recommendation_id BIGINT UNSIGNED NOT NULL,
    initiated_by ENUM('male_family', 'female_family') NOT NULL,
    status ENUM('pending', 'accepted', 'rejected', 'meeting_scheduled') DEFAULT 'pending',
    meeting_date DATETIME NULL,
    meeting_location VARCHAR(500) NULL,
    notes TEXT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (recommendation_id) REFERENCES recommendations(id) ON DELETE CASCADE
);

-- ═══════════════════════════════════════════════════════
-- نظام الصندوق التعاوني
-- ═══════════════════════════════════════════════════════

CREATE TABLE fund_circles (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    city_id BIGINT UNSIGNED NULL,
    created_by BIGINT UNSIGNED NOT NULL,
    max_members INT UNSIGNED DEFAULT 15,
    monthly_amount DECIMAL(12, 2) NOT NULL,
    currency VARCHAR(10) DEFAULT 'EGP',
    cycle_months INT UNSIGNED NOT NULL,      -- = max_members
    current_round INT UNSIGNED DEFAULT 0,
    status ENUM('forming', 'active', 'completed', 'cancelled') DEFAULT 'forming',
    payout_method ENUM('lottery', 'priority', 'schedule') DEFAULT 'schedule',
    started_at TIMESTAMP NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (city_id) REFERENCES cities(id),
    FOREIGN KEY (created_by) REFERENCES users(id)
);

CREATE TABLE circle_members (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    circle_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    payout_order INT UNSIGNED NULL,
    has_received_payout BOOLEAN DEFAULT FALSE,
    total_contributed DECIMAL(12, 2) DEFAULT 0,
    status ENUM('active', 'frozen', 'removed') DEFAULT 'active',
    joined_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    UNIQUE KEY (circle_id, user_id),
    FOREIGN KEY (circle_id) REFERENCES fund_circles(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE contributions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    circle_id BIGINT UNSIGNED NOT NULL,
    member_id BIGINT UNSIGNED NOT NULL,
    amount DECIMAL(12, 2) NOT NULL,
    round_number INT UNSIGNED NOT NULL,
    payment_ref VARCHAR(255) NULL,           -- مرجع الدفع (Fawry/Stripe)
    status ENUM('pending', 'paid', 'late', 'failed') DEFAULT 'pending',
    due_date DATE NOT NULL,
    paid_at TIMESTAMP NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (circle_id) REFERENCES fund_circles(id),
    FOREIGN KEY (member_id) REFERENCES circle_members(id)
);

CREATE TABLE payouts (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    circle_id BIGINT UNSIGNED NOT NULL,
    member_id BIGINT UNSIGNED NOT NULL,
    amount DECIMAL(12, 2) NOT NULL,
    round_number INT UNSIGNED NOT NULL,
    status ENUM('pending', 'processing', 'completed', 'failed') DEFAULT 'pending',
    paid_at TIMESTAMP NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (circle_id) REFERENCES fund_circles(id),
    FOREIGN KEY (member_id) REFERENCES circle_members(id)
);

-- ═══════════════════════════════════════════════════════
-- نظام الأعراس الجماعية
-- ═══════════════════════════════════════════════════════

CREATE TABLE group_weddings (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    city_id BIGINT UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    venue_name VARCHAR(255) NOT NULL,
    wedding_date DATE NOT NULL,
    max_grooms INT UNSIGNED DEFAULT 20,
    registered_count INT UNSIGNED DEFAULT 0,
    price_per_groom DECIMAL(12, 2) NOT NULL,
    original_price DECIMAL(12, 2) NOT NULL,  -- السعر بدون خصم
    description TEXT NULL,
    status ENUM('upcoming', 'full', 'completed', 'cancelled') DEFAULT 'upcoming',
    registration_deadline DATE NOT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (city_id) REFERENCES cities(id)
);

CREATE TABLE wedding_registrations (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    wedding_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    payment_status ENUM('pending', 'partial', 'paid', 'refunded') DEFAULT 'pending',
    payment_ref VARCHAR(255) NULL,
    notes TEXT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    UNIQUE KEY (wedding_id, user_id),
    FOREIGN KEY (wedding_id) REFERENCES group_weddings(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE vendors (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    category ENUM('venue', 'photography', 'catering', 'furniture', 'clothing', 'other') NOT NULL,
    city_id BIGINT UNSIGNED NOT NULL,
    description TEXT NULL,
    discount_percent DECIMAL(5, 2) NULL,
    contact_phone VARCHAR(20) NULL,
    contact_email VARCHAR(255) NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (city_id) REFERENCES cities(id)
);

-- ═══════════════════════════════════════════════════════
-- نظام الرعاية والبلاغات
-- ═══════════════════════════════════════════════════════

CREATE TABLE counseling_sessions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    type ENUM('individual', 'group') DEFAULT 'individual',
    scheduled_at DATETIME NOT NULL,
    status ENUM('scheduled', 'completed', 'cancelled') DEFAULT 'scheduled',
    notes TEXT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE reports (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    reporter_id BIGINT UNSIGNED NOT NULL,
    reported_type VARCHAR(50) NOT NULL,      -- 'user', 'recommender', 'circle'
    reported_id BIGINT UNSIGNED NOT NULL,
    reason TEXT NOT NULL,
    status ENUM('pending', 'investigating', 'resolved', 'dismissed') DEFAULT 'pending',
    admin_notes TEXT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (reporter_id) REFERENCES users(id)
);

-- ═══════════════════════════════════════════════════════
-- الفهارس (Indexes) للأداء
-- ═══════════════════════════════════════════════════════

CREATE INDEX idx_users_city ON users(city_id);
CREATE INDEX idx_users_role ON users(role);
CREATE INDEX idx_candidates_recommender ON candidates(recommender_id);
CREATE INDEX idx_candidates_gender_status ON candidates(gender, status);
CREATE INDEX idx_candidates_city ON candidates(city_id);
CREATE INDEX idx_recommendations_status ON recommendations(status);
CREATE INDEX idx_contributions_status ON contributions(status);
CREATE INDEX idx_contributions_circle_round ON contributions(circle_id, round_number);
CREATE INDEX idx_fund_circles_city_status ON fund_circles(city_id, status);
CREATE INDEX idx_group_weddings_city_status ON group_weddings(city_id, status);
CREATE INDEX idx_course_progress_user ON course_progress(user_id);
```

### 4.3 العلاقات (Eloquent Relationships)

```php
// User Model — العلاقات الأساسية
class User extends Authenticatable
{
    public function city() { return $this->belongsTo(City::class); }
    public function recommender() { return $this->hasOne(Recommender::class); }
    public function candidate() { return $this->hasOne(Candidate::class); }
    public function certificate() { return $this->hasOne(Certificate::class); }
    public function courseProgress() { return $this->hasMany(CourseProgress::class); }
    public function quizAttempts() { return $this->hasMany(QuizAttempt::class); }
    public function circleMemberships() { return $this->hasMany(CircleMember::class); }
    public function weddingRegistrations() { return $this->hasMany(WeddingRegistration::class); }
    public function reports() { return $this->hasMany(Report::class, 'reporter_id'); }

    // Scopes
    public function scopeVerified($q) { return $q->where('is_verified', true); }
    public function scopeCertified($q) { return $q->where('has_certificate', true); }
}

// Recommender Model
class Recommender extends Model
{
    public function user() { return $this->belongsTo(User::class); }
    public function candidates() { return $this->hasMany(Candidate::class); }
    public function recommendations() { return $this->hasMany(Recommendation::class); }

    public function scopeApproved($q) { return $q->where('is_approved', true); }
}

// FundCircle Model
class FundCircle extends Model
{
    public function city() { return $this->belongsTo(City::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function members() { return $this->hasMany(CircleMember::class, 'circle_id'); }
    public function contributions() { return $this->hasMany(Contribution::class, 'circle_id'); }
    public function payouts() { return $this->hasMany(Payout::class, 'circle_id'); }

    public function scopeActive($q) { return $q->where('status', 'active'); }
    public function scopeForming($q) { return $q->where('status', 'forming'); }
    public function scopeInCity($q, $cityId) { return $q->where('city_id', $cityId); }
}
```

### 4.4 API Endpoints — كامل ومنظم

```
═══════════════════════════════════════════════════
  AUTH — المصادقة
═══════════════════════════════════════════════════
POST   /api/auth/register              تسجيل حساب جديد
POST   /api/auth/login                 تسجيل دخول
POST   /api/auth/logout                تسجيل خروج (يتطلب توكن)
GET    /api/auth/user                  بيانات المستخدم الحالي
POST   /api/auth/verify-identity       رفع بطاقة الهوية للتوثيق
POST   /api/auth/forgot-password       طلب إعادة تعيين كلمة المرور
POST   /api/auth/reset-password        إعادة تعيين كلمة المرور

═══════════════════════════════════════════════════
  COURSES — الدورة التأهيلية
═══════════════════════════════════════════════════
GET    /api/courses                    قائمة المسارات مع التقدم
GET    /api/courses/{id}               تفاصيل مسار + دروسه
GET    /api/courses/{id}/lessons/{lid} محتوى درس محدد
POST   /api/courses/{id}/progress      تسجيل تقدم في درس
POST   /api/courses/{id}/quiz          تقديم اختبار
GET    /api/certificates/mine          شهادتي (إن وجدت)
GET    /api/certificates/{number}      التحقق من شهادة (عام)

═══════════════════════════════════════════════════
  MATCHING — التوفيق
═══════════════════════════════════════════════════
POST   /api/recommender/register       التسجيل كمعرّف
GET    /api/recommender/dashboard      لوحة تحكم المعرّف
GET    /api/recommender/candidates     قائمة مرشحي المعرّف
POST   /api/recommender/candidates     إضافة مرشح جديد
PUT    /api/recommender/candidates/{id} تعديل بيانات مرشح
GET    /api/recommender/suggestions    اقتراحات التوافق
POST   /api/recommender/recommend      إنشاء توصية
GET    /api/recommendations            التوصيات (الحالية والسابقة)
PUT    /api/recommendations/{id}       قبول/رفض توصية
GET    /api/family-requests            طلبات العائلات
PUT    /api/family-requests/{id}       الرد على طلب + تحديد موعد

═══════════════════════════════════════════════════
  FUND — الصندوق التعاوني
═══════════════════════════════════════════════════
GET    /api/circles                    الحلقات المتاحة (بالمدينة)
POST   /api/circles                    إنشاء حلقة جديدة
GET    /api/circles/{id}               تفاصيل حلقة
POST   /api/circles/{id}/join          الانضمام لحلقة
GET    /api/circles/{id}/dashboard     لوحة تحكم الحلقة
POST   /api/circles/{id}/contribute    دفع المساهمة
GET    /api/circles/{id}/timeline      جدول الدورات والمدفوعات
GET    /api/circles/{id}/members       أعضاء الحلقة + حالاتهم

═══════════════════════════════════════════════════
  WEDDINGS — الأعراس الجماعية
═══════════════════════════════════════════════════
GET    /api/weddings                   الأعراس القادمة (بالمدينة)
GET    /api/weddings/{id}              تفاصيل عرس
POST   /api/weddings/{id}/register     التسجيل في عرس
GET    /api/vendors                    الموردون (بالمدينة والفئة)

═══════════════════════════════════════════════════
  CALCULATOR — الحاسبة
═══════════════════════════════════════════════════
GET    /api/calculator/cities          المدن مع متوسط التكاليف
POST   /api/calculator/calculate       حساب التكاليف (مدينة + مستوى)

═══════════════════════════════════════════════════
  AFTERCARE — رعاية ما بعد الزواج
═══════════════════════════════════════════════════
GET    /api/counseling/sessions        الجلسات المتاحة
POST   /api/counseling/book            حجز جلسة
GET    /api/community/posts            منشورات المجتمع

═══════════════════════════════════════════════════
  ADMIN — لوحة التحكم
═══════════════════════════════════════════════════
GET    /api/admin/dashboard            إحصائيات عامة
GET    /api/admin/users                إدارة المستخدمين (paginated)
GET    /api/admin/recommenders         المعرّفون المعلقون
PUT    /api/admin/recommenders/{id}    اعتماد/رفض معرّف
GET    /api/admin/circles              إدارة الحلقات
GET    /api/admin/reports              البلاغات
PUT    /api/admin/reports/{id}         معالجة بلاغ
POST   /api/admin/weddings             إنشاء عرس جماعي
POST   /api/admin/vendors              إضافة مورد

═══════════════════════════════════════════════════
  COMMON — عام
═══════════════════════════════════════════════════
POST   /api/reports                    تقديم بلاغ
GET    /api/stats                      إحصائيات المنصة (عام)
```

### 4.5 نظام الصلاحيات (Roles & Permissions)

```
┌────────────────────────────────────────────────────────────────┐
│  الأدوار (Roles)                                                │
├────────────────────────────────────────────────────────────────┤
│                                                                  │
│  admin                                                           │
│  ├── manage-users           إدارة كل المستخدمين                │
│  ├── manage-recommenders    اعتماد/رفض المعرّفين                │
│  ├── manage-circles         إدارة الحلقات المالية               │
│  ├── manage-weddings        إنشاء/تعديل الأعراس                │
│  ├── manage-courses         إدارة محتوى الدورات                 │
│  ├── manage-reports         معالجة البلاغات                     │
│  └── view-admin-dashboard   الوصول للوحة التحكم                 │
│                                                                  │
│  recommender                                                     │
│  ├── add-candidates         إضافة مرشحين                       │
│  ├── make-recommendations   إنشاء توصيات                       │
│  ├── view-recommender-dashboard  لوحة المعرّف                  │
│  ├── enroll-courses         التسجيل بالدورات                    │
│  └── join-circles           الانضمام لحلقات                     │
│                                                                  │
│  user                                                            │
│  ├── enroll-courses         التسجيل بالدورات                    │
│  ├── join-circles           الانضمام لحلقات                     │
│  ├── register-weddings      التسجيل بأعراس                     │
│  └── submit-reports         تقديم بلاغات                       │
│                                                                  │
└────────────────────────────────────────────────────────────────┘

Middleware Stack لكل مجموعة API:
  /api/auth/*              → لا middleware (عام)
  /api/courses/*           → auth:sanctum
  /api/recommender/*       → auth:sanctum + role:recommender
  /api/circles/*           → auth:sanctum + (بعضها يتطلب certificate)
  /api/weddings/* (GET)    → لا middleware (عام)
  /api/weddings/* (POST)   → auth:sanctum
  /api/admin/*             → auth:sanctum + role:admin
  /api/calculator/*        → لا middleware (عام)
  /api/stats               → لا middleware (عام)
```

### 4.6 Validation — أمثلة على القواعد

```php
// RegisterRequest
'name'     => 'required|string|max:255',
'email'    => 'required|email|unique:users',
'phone'    => 'required|string|regex:/^(\+?[0-9]{10,15})$/|unique:users',
'password' => 'required|string|min:8|confirmed',
'gender'   => 'required|in:male,female',
'city_id'  => 'required|exists:cities,id',

// AddCandidateRequest
'name'              => 'required|string|max:255',
'gender'            => 'required|in:male,female',
'age'               => 'required|integer|min:18|max:60',
'guardian_name'     => 'required|string|max:255',
'guardian_phone'    => 'required|string|regex:/^(\+?[0-9]{10,15})$/',
'guardian_relation' => 'required|string|max:100',
'marital_status'    => 'required|in:single,divorced,widowed',
'religiosity_level' => 'required|in:committed,moderate,learning',

// CreateCircleRequest
'name'           => 'required|string|max:255',
'city_id'        => 'required|exists:cities,id',
'max_members'    => 'required|integer|min:5|max:30',
'monthly_amount' => 'required|numeric|min:100|max:10000',
'payout_method'  => 'required|in:lottery,priority,schedule',
```

---

## القسم الخامس: خطة الأمان الشاملة

### 5.1 الحماية من الثغرات الشائعة

```
┌────────────────────────────────────────────────────────────────┐
│  OWASP Top 10 — خطة الحماية                                    │
├────────────────────────────────────────────────────────────────┤
│                                                                  │
│  1. SQL Injection                                                │
│     ✓ Eloquent ORM يمنعها تلقائياً (parameterized queries)     │
│     ✓ عدم استخدام raw queries إلا عند الضرورة القصوى          │
│     ✓ عند الضرورة: DB::select('...', [$param]) فقط            │
│                                                                  │
│  2. XSS (Cross-Site Scripting)                                   │
│     ✓ Vue.js يهرب النصوص تلقائياً ({{ }})                      │
│     ✓ عدم استخدام v-html إلا للمحتوى الموثوق                  │
│     ✓ Content-Security-Policy header                            │
│     ✓ HttpOnly cookies                                          │
│                                                                  │
│  3. CSRF (Cross-Site Request Forgery)                            │
│     ✓ Laravel Sanctum يتعامل مع CSRF تلقائياً للـ SPA          │
│     ✓ SameSite cookie attribute                                  │
│                                                                  │
│  4. Broken Authentication                                        │
│     ✓ Sanctum tokens مع expiration                              │
│     ✓ Rate limiting على login (5 محاولات / دقيقة)              │
│     ✓ Password hashing (bcrypt, cost 12)                        │
│     ✓ Email/phone verification مطلوب                            │
│                                                                  │
│  5. Sensitive Data Exposure                                      │
│     ✓ HTTPS إلزامي (Let's Encrypt)                              │
│     ✓ بطاقة الهوية تُحذف فوراً بعد التوثيق                    │
│     ✓ هاتف ولي الأمر مشفّر AES-256 (يُكشف فقط عند القبول)   │
│     ✓ لا تخزين صور شخصية إطلاقاً                               │
│     ✓ API لا يرجع حقول حساسة (password, national_id)          │
│                                                                  │
│  6. Security Misconfiguration                                    │
│     ✓ APP_DEBUG=false في الإنتاج                                │
│     ✓ إخفاء headers الخادم (Server, X-Powered-By)              │
│     ✓ تقييد CORS للدومين الرسمي فقط                            │
│     ✓ Cloudflare WAF (طبقة حماية إضافية)                       │
│                                                                  │
│  7. Broken Access Control                                        │
│     ✓ Spatie Permission للأدوار                                  │
│     ✓ Laravel Policies لكل model                                │
│     ✓ المعرّف يرى مرشحيه فقط                                  │
│     ✓ العضو يرى حلقته فقط                                     │
│                                                                  │
│  8. Rate Limiting                                                │
│     ✓ API: 60 طلب/دقيقة للمستخدم العادي                       │
│     ✓ Auth endpoints: 5 محاولات/دقيقة                           │
│     ✓ Registration: 3 تسجيلات/ساعة من نفس IP                   │
│     ✓ File upload: 5 رفع/ساعة                                  │
│                                                                  │
│  9. Logging & Monitoring                                         │
│     ✓ تسجيل كل عملية حساسة (login, payout, recommendation)     │
│     ✓ Audit log للعمليات الإدارية                                │
│     ✓ تنبيه فوري عند محاولات اختراق متكررة                     │
│                                                                  │
│  10. Input Validation                                            │
│      ✓ Form Requests لكل endpoint                               │
│      ✓ تنظيف المدخلات (trim, strip_tags عند الحاجة)            │
│      ✓ حد أقصى لحجم الملفات (5MB للهوية)                       │
│      ✓ التحقق من نوع الملف (image only للهوية)                 │
│                                                                  │
└────────────────────────────────────────────────────────────────┘
```

### 5.2 تأمين الصندوق المالي (الأهم)

```
الصندوق هو أكثر جزء حساس في المنصة. خطة تأمينه:

1. المنصة لا تمسك أي أموال
   ├── المدفوعات تتم عبر بوابة دفع (Fawry/Stripe)
   ├── التحويل مباشر بين الأعضاء
   └── المنصة تنظم فقط وتأخذ 2-3% رسوم خدمة

2. عقد التزام رقمي
   ├── كل عضو يوافق على شروط واضحة عند الانضمام
   ├── يتضمن: المبلغ، المدة، عواقب التخلف
   └── يُسجل بتاريخ وIP ومعرف الجهاز

3. نظام حماية من التخلف
   ├── تذكير قبل 3 أيام من الموعد
   ├── مهلة 5 أيام بعد الموعد
   ├── تجميد العضوية بعد المهلة
   ├── استبعاد نهائي بعد 15 يوم
   └── إشعار لكل أعضاء الحلقة

4. الشفافية
   ├── كل عضو يرى كل المدفوعات
   ├── Dashboard واضح بالأرقام والتواريخ
   └── لا عمليات خفية
```

### 5.3 حماية البيانات الشخصية (GDPR)

```
1. جمع أقل بيانات ممكنة (data minimization)
2. بطاقة الهوية تُحذف خلال 24 ساعة من التوثيق
3. حذف الحساب الكامل خلال 72 ساعة من الطلب
4. لا cookies تتبع — فقط cookies تشغيلية
5. لا مشاركة بيانات مع أطراف ثالثة
6. نسخ احتياطية مشفرة يومياً
7. حق الوصول: المستخدم يمكنه تحميل كل بياناته
8. سجل تدقيق (audit log) لكل عملية وصول للبيانات
```

---

## القسم السادس: الأداء

### 6.1 خطة تحسين الأداء

```
═══════════════════════════════════════════════════
  التخزين المؤقت (Caching)
═══════════════════════════════════════════════════

Redis Cache Strategy:

  إحصائيات المنصة (الصفحة الرئيسية)
  ├── المفتاح: platform:stats
  ├── المدة: 1 ساعة
  └── يُحدّث عند: تسجيل جديد، شهادة جديدة، حلقة جديدة

  قائمة المدن + التكاليف
  ├── المفتاح: cities:all
  ├── المدة: 24 ساعة
  └── يُحدّث عند: تعديل مدينة (نادر)

  قائمة الدورات (بدون تقدم المستخدم)
  ├── المفتاح: courses:list
  ├── المدة: 24 ساعة
  └── يُحدّث عند: تعديل دورة

  الأعراس القادمة
  ├── المفتاح: weddings:upcoming:{city_id}
  ├── المدة: 30 دقيقة
  └── يُحدّث عند: تسجيل جديد أو إلغاء

  لا تخزين مؤقت لـ:
  ├── بيانات الصندوق (تتغير باستمرار)
  ├── التوصيات (حساسة ومتغيرة)
  └── بيانات المستخدم الشخصية


═══════════════════════════════════════════════════
  التحميل الكسول (Lazy Loading)
═══════════════════════════════════════════════════

Vue Router — كل صفحة تُحمّل عند الحاجة فقط:

  const routes = [
    {
      path: '/',
      component: () => import('./views/public/HomePage.vue')
    },
    {
      path: '/courses',
      component: () => import('./views/course/CourseListPage.vue')
    },
    // ... كل الصفحات بنفس الطريقة
  ]

  النتيجة: الحزمة الأولية (initial bundle) صغيرة جداً
  المستخدم يحمّل فقط ما يحتاجه


═══════════════════════════════════════════════════
  تقسيم الكود (Code Splitting)
═══════════════════════════════════════════════════

Vite يقسم الكود تلقائياً:
  ├── vendor.js     — المكتبات (Vue, Bootstrap, Axios)
  ├── app.js        — كود التطبيق المشترك
  ├── [page].js     — كود كل صفحة (lazy loaded)
  └── app.css       — الأنماط (واحد فقط — Bootstrap صغير مع purge)


═══════════════════════════════════════════════════
  تحسين الصور والأصول
═══════════════════════════════════════════════════

  1. الصور: WebP format (أصغر 30% من JPEG)
  2. الأيقونات: SVG inline أو sprite sheet (بدون icon fonts ثقيلة)
  3. الشعار: SVG (موجود مسبقاً)
  4. الفيديو (الدورات): Bunny.net CDN + HLS adaptive streaming
  5. الخطوط: Cairo من Google Fonts مع font-display: swap
     └── تحميل الأوزان المطلوبة فقط (400, 600, 700, 900)
  6. Gzip/Brotli compression عبر Cloudflare (مجاني)


═══════════════════════════════════════════════════
  أداء قاعدة البيانات
═══════════════════════════════════════════════════

  1. Eager Loading (منع N+1):
     // بدلاً من:
     $circles = FundCircle::all(); // ثم الوصول لـ members في loop
     // استخدم:
     $circles = FundCircle::with(['members', 'city'])->get();

  2. Pagination في كل القوائم:
     return FundCircle::with('city')->paginate(15);

  3. Select فقط الحقول المطلوبة:
     User::select('id', 'name', 'has_certificate')->get();

  4. الفهارس المركبة (composite indexes) على الأعمدة الأكثر استعلاماً
     (محددة في القسم 4.2 أعلاه)


═══════════════════════════════════════════════════
  أهداف الأداء (Core Web Vitals)
═══════════════════════════════════════════════════

  LCP (Largest Contentful Paint): < 2.5 ثانية
  FID (First Input Delay): < 100ms
  CLS (Cumulative Layout Shift): < 0.1

  كيف نحققها:
  ├── Hero section: عنوان نصي (ليس صورة) → LCP سريع
  ├── Fonts: preload + font-display: swap → لا تأخر
  ├── الحاسبة: JavaScript خفيف (حسابات محلية) → FID سريع
  └── أبعاد ثابتة للبطاقات والصور → CLS صفر
```

---

## القسم السابع: الجودة

### 7.1 معايير الكود

```
═══════════════════════════════════════════════════
  Backend (Laravel/PHP)
═══════════════════════════════════════════════════

  1. PSR-12 coding style (مدمج في Laravel)
  2. كل endpoint يملك Form Request خاص
  3. كل model يملك Policy خاصة
  4. كل عملية معقدة في Service منفصل
  5. لا logic في Controllers — فقط request → service → response
  6. Type hints على كل method
  7. return types واضحة
  8. API Resources لتنسيق الاستجابات

═══════════════════════════════════════════════════
  Frontend (Vue.js)
═══════════════════════════════════════════════════

  1. Composition API حصرياً (لا Options API)
  2. SFC (Single File Component) لكل مكون
  3. <script setup> syntax
  4. Props مع defineProps + types
  5. Emits مع defineEmits
  6. Composables للمنطق المشترك
  7. لا business logic في المكونات — فقط عرض + تفاعل
```

### 7.2 نظام الاختبار

```
═══════════════════════════════════════════════════
  Unit Tests (PHPUnit)
═══════════════════════════════════════════════════

  الأولوية القصوى (يجب كتابتها أولاً):
  ├── FundCalculationService — حسابات الصندوق (دقة الأرقام حرجة)
  ├── CostCalculatorService — حسابات الحاسبة
  ├── MatchSuggestionService — خوارزمية التوفيق
  └── CertificateGeneratorService — إصدار الشهادات

  لكل service:
  ├── الحالة الطبيعية (happy path)
  ├── حالات الحدود (edge cases)
  └── حالات الخطأ (error cases)

═══════════════════════════════════════════════════
  Feature/Integration Tests (PHPUnit)
═══════════════════════════════════════════════════

  لكل API endpoint:
  ├── الاستجابة الناجحة (200/201)
  ├── التحقق من الصلاحيات (403)
  ├── بيانات خاطئة (422)
  ├── غير مصادق (401)
  └── مورد غير موجود (404)

  سيناريوهات كاملة:
  ├── تسجيل → إكمال دورة → الحصول على شهادة
  ├── إنشاء حلقة → انضمام أعضاء → مساهمات → صرف
  ├── تسجيل معرّف → إضافة مرشحين → توصية → طلب عائلي
  └── التسجيل في عرس → الدفع

═══════════════════════════════════════════════════
  Frontend Tests (Vitest)
═══════════════════════════════════════════════════

  المكونات الحرجة فقط:
  ├── CostCalculator — التأكد من دقة الحسابات
  ├── QuizQuestion — التأكد من تسجيل الإجابات بشكل صحيح
  ├── ContributionTimeline — عرض الحالات الصحيحة
  └── Auth guards — التأكد من حماية المسارات


═══════════════════════════════════════════════════
  الحد الأدنى المطلوب قبل الإطلاق
═══════════════════════════════════════════════════

  ✓ 100% unit tests للـ Services المالية (الصندوق + الحاسبة)
  ✓ Feature tests لكل auth endpoint
  ✓ Feature tests لدورة الصندوق الكاملة
  ✓ اختبار يدوي لكل صفحة على Chrome + Safari mobile
  ✓ اختبار RTL يدوي
```

### 7.3 مراجعة الكود

```
بما أن المشروع بمطور واحد:

1. Self-review checklist قبل كل commit:
   □ هل الكود يعمل محلياً بدون أخطاء؟
   □ هل كتبت tests للمنطق الحرج؟
   □ هل الـ validation موجود على كل input؟
   □ هل تحققت من الصلاحيات (authorization)؟
   □ هل الـ API يرجع فقط البيانات المطلوبة (لا تسريب)؟
   □ هل الصفحة تعمل على الموبايل؟
   □ هل الـ RTL صحيح؟

2. أدوات آلية:
   ├── Laravel Pint (PHP code style) — يعمل مع كل commit
   ├── ESLint + Prettier (Vue/JS) — يعمل مع كل commit
   ├── PHPStan level 5 (static analysis) — أسبوعياً
   └── npm audit (security vulnerabilities) — أسبوعياً

3. Git workflow:
   ├── main branch: كود الإنتاج فقط
   ├── develop branch: التطوير النشط
   ├── feature/* branches: لكل ميزة جديدة
   └── كل merge يتطلب tests passing
```

---

## القسم الثامن: خطة التنفيذ المرحلية

### 8.1 MVP — المرحلة الأولى (أسابيع 1-6)

> **الهدف:** موقع يعمل بالحد الأدنى — مستخدم يمكنه التسجيل وإكمال الدورة والحصول على شهادة + حاسبة التكاليف

```
═══════════════════════════════════════════════════
  الأسبوع 1-2: البنية التحتية
═══════════════════════════════════════════════════

  يوم 1-2:
  ├── إنشاء مشروع Laravel 11
  ├── تثبيت الحزم (Sanctum, Spatie, Vue, Bootstrap RTL)
  ├── إعداد Vite + Vue plugin
  ├── إعداد قاعدة البيانات + Redis
  └── تشغيل المشروع محلياً

  يوم 3-4:
  ├── كتابة كل Migrations (21 جدول)
  ├── كتابة Seeders (المدن، الدورات، الأدوار)
  ├── إعداد Models + العلاقات الأساسية
  └── إعداد نظام الصلاحيات (Spatie)

  يوم 5-6:
  ├── Auth API (register, login, logout, user)
  ├── Vue Router setup مع guards
  ├── Auth Store (Pinia)
  └── صفحات Login + Register

  يوم 7-8:
  ├── SCSS setup (_variables, _islamic-theme, _rtl-fixes)
  ├── Layout components (Navbar, Footer, MobileBottomNav)
  ├── Base UI components (BaseButton, BaseCard, BaseBadge)
  └── تأكد من RTL يعمل بشكل كامل

  يوم 9-10:
  ├── الصفحة الرئيسية (Hero, Stats, Pillars, Calculator widget)
  ├── صفحة "عن المنصة"
  └── Footer مع الروابط

  يوم 11-14:
  ├── اختبار كل ما سبق على الموبايل
  ├── إصلاح RTL issues
  ├── Unit tests للـ Auth
  └── Feature tests للـ Auth endpoints


═══════════════════════════════════════════════════
  الأسبوع 3-4: نظام الدورات + الحاسبة
═══════════════════════════════════════════════════

  يوم 15-17:
  ├── Course API (قائمة، تفاصيل، دروس، تقدم)
  ├── CourseListPage مع شريط التقدم الكلي
  ├── CoursePage مع قائمة الدروس
  └── ProgressBar component

  يوم 18-20:
  ├── LessonPage مع VideoPlayer (HLS.js)
  ├── تسجيل التقدم تلقائياً
  ├── QuizPage + QuizQuestion component
  └── Quiz API (تقديم، تصحيح، عدد المحاولات)

  يوم 21-23:
  ├── CertificateGeneratorService (Canvas/Puppeteer → PDF)
  ├── CertificatePage (عرض + تحميل + مشاركة)
  ├── تحديث has_certificate في User عند النجاح
  └── صفحة التحقق العامة من الشهادة

  يوم 24-26:
  ├── CostCalculatorPage (المدن + المستويات + المقارنة)
  ├── Calculator API (أو حسابات frontend فقط)
  ├── CostCalculatorService
  └── ربط الحاسبة المصغرة في الصفحة الرئيسية

  يوم 27-28:
  ├── Unit tests لـ CertificateGenerator + CostCalculator
  ├── Feature tests لكل Course endpoints
  ├── اختبار على الموبايل
  └── إصلاحات


═══════════════════════════════════════════════════
  الأسبوع 5-6: نظام المعرّفين + التوفيق
═══════════════════════════════════════════════════

  يوم 29-31:
  ├── Recommender API (تسجيل، اعتماد، لوحة تحكم)
  ├── RecommenderDashboard مع الإحصائيات
  ├── CandidateForm + AddCandidatePage
  └── تشفير هاتف ولي الأمر (AES-256)

  يوم 32-34:
  ├── MatchSuggestionService (خوارزمية التوافق)
  ├── RecommendationsPage + RecommendationCard
  ├── Recommendation API (إنشاء، قبول، رفض)
  └── FamilyRequestsPage + FamilyRequestCard

  يوم 35-37:
  ├── إشعارات (توصية جديدة، طلب عائلي)
  ├── تسجيل المعرّف (صفحة خاصة بخطوات واضحة)
  ├── ميثاق شرف رقمي (موافقة عند التسجيل)
  └── Admin: صفحة اعتماد المعرّفين

  يوم 38-42:
  ├── Admin Dashboard أساسي (إحصائيات + إدارة مستخدمين)
  ├── Feature tests لنظام التوفيق
  ├── اختبار السيناريو الكامل يدوياً
  ├── SEO أساسي (meta tags, sitemap)
  └── نشر على خادم Hetzner (Beta)

  *** عند هذه النقطة: إطلاق Beta مع 30-50 مستخدم ***


═══════════════════════════════════════════════════
  المرحلة الثانية (أسابيع 7-10): الصندوق + الأعراس
═══════════════════════════════════════════════════

  الأسبوع 7-8: نظام الصندوق التعاوني
  ├── Fund API الكامل
  ├── FundOverviewPage + CreateCirclePage
  ├── CircleDashboard + ContributionTimeline
  ├── ربط بوابة الدفع (Fawry أولاً)
  ├── FundCalculationService + PayoutSchedule
  ├── Jobs (ProcessContribution, SendPayoutReminder)
  ├── عقد الالتزام الرقمي
  └── Unit + Feature tests للصندوق (أولوية قصوى)

  الأسبوع 9-10: الأعراس الجماعية + الموردون
  ├── Wedding API + WeddingDetailPage
  ├── GroupWeddingsPage مع فلتر المدينة
  ├── Vendor API + VendorsPage
  ├── صفحة التسجيل في عرس مع الدفع
  ├── Admin: إنشاء أعراس + إضافة موردين
  └── Feature tests


═══════════════════════════════════════════════════
  المرحلة الثالثة (أسابيع 11-12): التلميع والإطلاق
═══════════════════════════════════════════════════

  الأسبوع 11:
  ├── رعاية ما بعد الزواج (أساسي)
  ├── صفحة الاستشارات
  ├── المجتمع (أساسي)
  ├── نظام البلاغات
  └── صفحة الدعم

  الأسبوع 12:
  ├── اختبار شامل على كل الأجهزة
  ├── تحسين الأداء (Core Web Vitals)
  ├── إصلاح كل الـ bugs
  ├── SEO كامل (schema.org, OG tags)
  ├── Google Analytics + Search Console
  ├── أول مقال SEO
  └── الإطلاق الرسمي
```

### 8.2 ملخص الأولويات

```
┌──────────────────────────────────────────────────────────────┐
│  الأولوية    │  الميزة              │  لماذا أولاً؟          │
├──────────────────────────────────────────────────────────────┤
│  1 (حرج)    │  التسجيل + المصادقة  │  بدونه لا شيء يعمل    │
│  2 (حرج)    │  الدورة التأهيلية    │  شرط لكل الخدمات      │
│  3 (عالي)   │  الحاسبة             │  أسهل طريقة لجذب زوار │
│  4 (عالي)   │  نظام المعرّفين      │  الركيزة الأساسية      │
│  5 (عالي)   │  الصندوق التعاوني    │  أهم ميزة تنافسية     │
│  6 (متوسط)  │  الأعراس الجماعية    │  يحتاج شراكات أولاً   │
│  7 (متوسط)  │  لوحة الأدمن         │  يمكن إدارة من DB مبدئياً │
│  8 (منخفض)  │  رعاية ما بعد الزواج │  المستخدمون لم يتزوجوا بعد │
│  9 (منخفض)  │  المجتمع             │  يحتاج كتلة حرجة من المستخدمين │
└──────────────────────────────────────────────────────────────┘
```

### 8.3 الجدول الزمني

```
┌────────────┬──────────────────────────┬───────────────────────┐
│  الأسبوع   │  الميزة                  │  المخرج                │
├────────────┼──────────────────────────┼───────────────────────┤
│  1-2       │  البنية التحتية + Auth   │  موقع يعمل + تسجيل   │
│  3-4       │  الدورات + الحاسبة       │  تأهيل كامل + شهادة  │
│  5-6       │  المعرّفين + التوفيق     │  نظام توفيق + Beta    │
│  ─── MVP مكتمل ─── إطلاق Beta ───                            │
│  7-8       │  الصندوق                 │  حلقات تعاونية + دفع  │
│  9-10      │  الأعراس + الموردون      │  أعراس جماعية         │
│  11-12     │  التلميع + الإطلاق       │  إطلاق رسمي           │
└────────────┴──────────────────────────┴───────────────────────┘

  معدل العمل: 3-4 ساعات يومياً
  إجمالي المدة: 12 أسبوع (3 أشهر)
```

---

## القسم التاسع: قابلية التوسع

### 9.1 التوسع التقني

```
الخادم الحالي (Hetzner CPX31):
  4 vCPU, 8GB RAM, 160GB SSD = يتحمل حتى ~5,000 مستخدم نشط

عند الحاجة للتوسع:
  ├── المرحلة 1 (5K-20K مستخدم):
  │   ├── ترقية الخادم (8 vCPU, 16GB RAM)
  │   ├── فصل قاعدة البيانات على خادم منفصل
  │   └── Redis مخصص
  │
  ├── المرحلة 2 (20K-100K مستخدم):
  │   ├── Load balancer + خادمين تطبيق
  │   ├── MySQL replica للقراءة
  │   ├── Queue workers منفصلة
  │   └── Object storage للملفات (Hetzner Storage Box)
  │
  └── المرحلة 3 (100K+ مستخدم):
      ├── Kubernetes أو managed containers
      ├── CDN للمحتوى الثابت + الفيديو
      ├── Database sharding حسب الدولة
      └── Microservices (فصل الصندوق كخدمة مستقلة)
```

### 9.2 التوسع الجغرافي

```
  المرحلة 1: مصر (الآن)
  ├── عملة: EGP
  ├── بوابة دفع: Fawry
  ├── مدن: القاهرة، الإسكندرية، المنصورة
  └── لغة: العربية

  المرحلة 2: الخليج (بعد 6 أشهر)
  ├── عملات: SAR, AED, KWD
  ├── بوابة دفع: Stripe (يدعم الخليج)
  ├── مدن: الرياض، جدة، دبي، أبوظبي، الكويت
  └── تعديلات: تكاليف مختلفة، أعراس مختلفة

  المرحلة 3: عالمي (بعد سنة)
  ├── تركيا، إندونيسيا، ماليزيا
  ├── أوروبا وأمريكا (مسلمون في الغرب)
  ├── ترجمة: English, Turkish, Malay, Indonesian
  └── i18n framework (vue-i18n)

  قرارات معمارية تدعم التوسع من البداية:
  ├── جدول cities يحتوي country + currency (جاهز)
  ├── الأسعار بـ DECIMAL مع عملة (جاهز)
  ├── الـ seeders منظمة حسب الدولة (جاهز)
  └── Vue i18n يمكن إضافته لاحقاً بدون إعادة بناء
```

### 9.3 التوسع الوظيفي (ميزات مستقبلية)

```
  بعد الإطلاق مباشرة:
  ├── تطبيق موبايل (PWA أولاً — بدون تكلفة إضافية)
  ├── إشعارات Push
  └── إحصائيات متقدمة للأدمن

  بعد 3 أشهر:
  ├── تطبيق أصلي (Flutter — كود واحد لـ iOS + Android)
  ├── نظام دردشة محدود (بين المعرّف والعائلات فقط)
  ├── تكامل واتساب (إشعارات عبر WhatsApp Business API)
  └── نظام تقييم للمعرّفين

  بعد 6 أشهر:
  ├── AI matching suggestions (تحسين خوارزمية التوفيق)
  ├── محفظة رقمية داخلية
  ├── شراكات مع بنوك إسلامية
  └── فرع B2B (مؤسسات خيرية تستخدم المنصة)

  كل هذه الإضافات ممكنة بدون إعادة بناء لأن:
  ├── API-first architecture → أي واجهة يمكنها استهلاك الـ API
  ├── Service layer → المنطق منفصل عن الواجهة
  ├── Modular structure → كل ميزة في مجلدها
  └── Database مصمم بمرونة (JSON fields للتفضيلات)
```

---

## ملخص تنفيذي

```
┌────────────────────────────────────────────────────────────────┐
│                                                                  │
│  المشروع: يسّرو — منصة تيسير الزواج                           │
│  النوع: SPA (Single Page Application)                           │
│  التقنيات: Laravel 11 + Vue.js 3 + MySQL + Redis               │
│  المدة: 12 أسبوع (3 أشهر)                                      │
│  الفريق: مطور واحد (3-4 ساعات/يوم)                            │
│  التكلفة الشهرية: ~$20-30 (استضافة)                            │
│  MVP: أسبوع 6 (تسجيل + دورة + حاسبة + معرّفين)              │
│  الإطلاق: أسبوع 12                                              │
│                                                                  │
│  أول 3 أشياء يجب فعلها:                                        │
│  1. composer create-project laravel/laravel yassiru             │
│  2. كتابة كل الـ Migrations                                    │
│  3. Auth API + Vue Router setup                                  │
│                                                                  │
└────────────────────────────────────────────────────────────────┘
```
