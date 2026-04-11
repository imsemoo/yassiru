# المهام المتبقية لإطلاق منصة يسّرو

> آخر تحديث: 2026-04-09
> حالة الكود: مكتمل ~98% — المتبقي مهام تشغيلية

---

## 1. فتح حساب فوري تاجر (أولوية: عالية جداً)

بوابة الدفع مبنية ومربوطة بالكود بالكامل، لكن محتاج credentials حقيقية عشان تشتغل.

### الخطوات:
1. ادخل على https://developer.fawry.io وسجّل حساب developer
2. أنشئ Merchant Account (حساب تاجر)
3. هتحتاج:
   - سجل تجاري أو بطاقة ضريبية (لو مؤسسة)
   - حساب بنكي لاستقبال المدفوعات
   - بيانات المنصة (اسم، وصف، URL)
4. بعد الموافقة هتاخد:
   - `FAWRY_MERCHANT_CODE` — كود التاجر
   - `FAWRY_SECURITY_KEY` — مفتاح الأمان
5. ضيفهم في ملف `.env`:
   ```
   FAWRY_MERCHANT_CODE=your_merchant_code_here
   FAWRY_SECURITY_KEY=your_security_key_here
   ```
6. للاختبار استخدم Staging:
   ```
   FAWRY_BASE_URL=https://atfawry.fawrystaging.com
   ```
7. للإنتاج غيّر لـ:
   ```
   FAWRY_BASE_URL=https://www.atfawry.com
   ```

### اختبار الدفع:
- فوري Staging بيوفر بيانات كروت تجريبية
- كود فوري التجريبي بيشتغل بدون دفع حقيقي
- اختبر الـ 3 أنواع: مساهمة صندوق + تسجيل عرس + رسوم ضمان
- اختبر الـ webhook: `curl -X POST http://localhost:8000/api/payments/webhook/fawry -d '...'`

---

## 2. محتوى الدورات التأهيلية (أولوية: عالية جداً)

الهيكل جاهز بالكامل (4 مسارات × 3-4 دروس = 14 درس + 4 اختبارات). المحتوى الفعلي محتاج يتضاف.

### المطلوب لكل درس:
- **عنوان** (موجود في الـ seeder)
- **محتوى نصي** (HTML) — حقل `content` في جدول `lessons`
- **فيديو** (اختياري) — حقل `video_url` — يقبل:
  - رابط YouTube (مثل: `https://youtube.com/watch?v=xxx`)
  - رابط Vimeo (مثل: `https://vimeo.com/xxx`)
  - رابط فيديو مباشر (MP4)
- **مدة الدرس** بالدقائق — حقل `duration_minutes`

### المسارات الأربعة:

**المسار الشرعي (4 دروس، ~3 ساعات):**
- أحكام الزواج في الإسلام
- حقوق الزوجين والواجبات
- آداب الخطبة والاختيار
- المحرمات والعلاقات المحرمة

**المسار النفسي (4 دروس، ~3 ساعات):**
- التوافق النفسي بين الزوجين
- إدارة الخلافات الزوجية
- التواصل الفعال
- الصحة النفسية في الحياة الزوجية

**المسار المالي (3 دروس، ~2.5 ساعة):**
- التخطيط المالي للزواج
- إدارة ميزانية الأسرة
- تجنب الديون والربا

**المسار العملي (3 دروس، ~2 ساعات):**
- المهارات المنزلية الأساسية
- التعامل مع الأهل والعائلة
- بناء بيت مستقر

### الاختبارات:
- كل مسار له 10 أسئلة (موجودة في `QuizQuestionSeeder`)
- النجاح يتطلب 70% (7 من 10)
- 3 محاولات كحد أقصى
- بعد نجاح الأربعة → شهادة تلقائية

### طريقة إضافة المحتوى:
الأسهل: عن طريق الـ Seeder مباشرة أو عن طريق Tinker:
```php
// مثال: تحديث محتوى درس
App\Models\Lesson::find(1)->update([
    'content' => '<h2>أحكام الزواج في الإسلام</h2><p>المحتوى هنا...</p>',
    'video_url' => 'https://youtube.com/watch?v=VIDEO_ID',
    'duration_minutes' => 45,
]);
```

---

## 3. شراء Domain + Server + النشر (أولوية: عالية)

### Domain:
- اشتري `yassiru.com` من Namecheap أو GoDaddy (~$10-15/سنة)
- أو `yassiru.org` لو الـ .com مش متاح
- وجّه الـ DNS لعنوان IP بتاع السيرفر

### Hetzner Server:
1. سجّل في https://www.hetzner.com
2. أنشئ Cloud Server:
   - **CX22** (2 vCPU, 4GB RAM) — ~€4/شهر (كافي للبداية)
   - أو **CX32** (4 vCPU, 8GB RAM) — ~€7/شهر (أفضل)
   - نظام: Ubuntu 24.04
   - الموقع: Falkenstein أو Nuremberg (أقرب لمصر)
3. ثبّت Docker + Docker Compose:
   ```bash
   curl -fsSL https://get.docker.com | sh
   sudo apt install docker-compose-plugin
   ```
4. انسخ المشروع:
   ```bash
   cd /opt
   git clone https://github.com/YOUR_USERNAME/yassiru.git
   cd yassiru
   ```
5. أنشئ `.env.production` من القالب:
   ```bash
   cp .env.production.example .env.production
   nano .env.production  # عدّل كل القيم
   ```
6. شغّل SSL:
   ```bash
   bash scripts/init-ssl.sh
   ```
7. شغّل المشروع:
   ```bash
   bash deploy.sh
   ```

### أهم إعدادات `.env.production`:
```
APP_ENV=production
APP_DEBUG=false
APP_KEY=    # ولّد واحد: php artisan key:generate --show
APP_URL=https://yassiru.com

DB_PASSWORD=    # كلمة مرور قوية جداً

FAWRY_MERCHANT_CODE=    # من فوري
FAWRY_SECURITY_KEY=     # من فوري
FAWRY_BASE_URL=https://www.atfawry.com
FAWRY_RETURN_URL=https://yassiru.com/api/payment/callback

MAIL_MAILER=smtp
MAIL_HOST=    # من مزود الإيميل
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_FROM_ADDRESS=noreply@yassiru.com

GOOGLE_ANALYTICS_ID=    # من Google Analytics
```

### بعد النشر — تحقق من:
- [ ] الموقع يفتح على `https://yassiru.com`
- [ ] SSL شغال (قفل أخضر)
- [ ] التسجيل والدخول يعملوا
- [ ] الدورات تفتح
- [ ] الحاسبة تشتغل
- [ ] الدفع يتوجه لفوري
- [ ] الـ Queue Worker شغال: `docker logs yassiru-queue`
- [ ] الـ Scheduler شغال: `docker logs yassiru-scheduler`

---

## 4. ربط SMTP للإيميلات (أولوية: متوسطة)

حالياً الإيميلات بتتسجل في الـ log فقط (`MAIL_MAILER=log`). محتاج مزود إيميل حقيقي.

### الخيارات (من الأرخص للأغلى):

**Mailgun (موصى به):**
- مجاني أول 3 أشهر (1000 إيميل/شهر بعدها)
- سجّل في https://www.mailgun.com
- أضف الدومين وفعّل DNS records
- إعدادات `.env`:
  ```
  MAIL_MAILER=smtp
  MAIL_HOST=smtp.mailgun.org
  MAIL_PORT=587
  MAIL_USERNAME=postmaster@mg.yassiru.com
  MAIL_PASSWORD=your_mailgun_password
  MAIL_ENCRYPTION=tls
  MAIL_FROM_ADDRESS=noreply@yassiru.com
  MAIL_FROM_NAME=يسّرو
  ```

**Resend (بديل حديث):**
- مجاني حتى 3000 إيميل/شهر
- https://resend.com
- أبسط في الإعداد

**Gmail SMTP (للاختبار فقط):**
- مش موصى به للإنتاج (حدود يومية)
- محتاج App Password

### أنواع الإيميلات اللي بتتبعت:
- تأكيد التسجيل
- شهادة الجاهزية
- بدء حلقة صندوق
- تذكير بمساهمة
- مساهمة متأخرة
- تأكيد دفع
- توصية زواج جديدة
- تأكيد تسجيل عرس
- اعتماد معرّف
- تذكير جلسة استشارية
- صرف حصة من الصندوق

---

## 5. ربط SMS Provider للـ OTP (أولوية: متوسطة)

الـ OTP حالياً بيتسجل في الـ log (`\Log::info`). محتاج مزود SMS حقيقي لـ:
- تأكيد رقم الهاتف عند التسجيل
- تأكيد الضامن في الصندوق

### الخيارات لمصر:

**Vonage (Nexmo):**
- أشهر مزود SMS عالمياً
- يدعم مصر بسعر ~0.04 USD/رسالة
- https://www.vonage.com

**Twilio:**
- https://www.twilio.com
- ~0.05 USD/رسالة لمصر

**Infobip:**
- يدعم مصر بشكل ممتاز
- https://www.infobip.com

### الملفات اللي محتاج تعديل:
1. `app/Services/GuaranteeService.php` — سطر 80 (OTP الضامن)
2. `app/Services/IdentityVerificationService.php` — OTP التحقق من الهاتف

### التعديل المطلوب:
إنشاء `SmsService` بسيط:
```php
// app/Services/SmsService.php
class SmsService {
    public function send(string $phone, string $message): bool {
        // Vonage/Twilio API call
    }
}
```

---

## 6. Google Analytics (أولوية: متوسطة)

الكود جاهز في `app.blade.php` + composable `useAnalytics.js`. محتاج بس الـ ID.

### الخطوات:
1. ادخل https://analytics.google.com
2. أنشئ Property جديد لـ `yassiru.com`
3. اختار Web Stream
4. انسخ الـ Measurement ID (شكله `G-XXXXXXXXXX`)
5. ضيفه في `.env`:
   ```
   GOOGLE_ANALYTICS_ID=G-XXXXXXXXXX
   ```

### Google Search Console:
1. ادخل https://search.google.com/search-console
2. أضف الموقع `https://yassiru.com`
3. تحقق عن طريق DNS record
4. أرسل الـ Sitemap: `https://yassiru.com/sitemap.xml`

---

## 7. صور OG مخصصة (أولوية: منخفضة)

حالياً كل الصفحات بتستخدم `logo.svg` كصورة OG. ممكن تعمل صور مخصصة عشان المشاركة على السوشال ميديا تبان أحسن.

### المطلوب:
- صورة 1200×630 بكسل لكل صفحة رئيسية
- الصور تتحط في `public/images/og/`
- تتعدل في `SeoService.php` ليرجع الصورة المناسبة لكل path

### الصفحات المحتاجة صور:
- الصفحة الرئيسية — `og-home.jpg`
- الحاسبة — `og-calculator.jpg`
- الدورات — `og-courses.jpg`
- الأعراس — `og-weddings.jpg`
- الصندوق — `og-fund.jpg`
- عن المنصة — `og-about.jpg`

---

## 8. محتوى تسويقي / SEO (أولوية: منخفضة — بعد الإطلاق)

### مقالات SEO:
المنصة محتاجة مقالات عشان تجذب زيارات من محركات البحث:
- "كم تكلفة الزواج في مصر 2026؟"
- "الأعراس الجماعية — وفّر 70% من تكاليف زواجك"
- "الصندوق التعاوني — بديل حلال للقروض"
- "كيف تستعد للزواج شرعياً ونفسياً ومالياً؟"

### الميزة التنافسية:
عندك 10+ مواقع إخبارية بـ 6 مليون+ زيارة شهرية — استخدمها لنشر مقالات تشير ليسّرو (backlinks مجانية).

---

## 9. تحسينات مستقبلية (بعد الإطلاق)

| الميزة | الوقت المقدر | الأولوية |
|--------|-------------|---------|
| تطبيق موبايل Flutter (الـ API جاهز) | 4-6 أسابيع | متوسطة |
| إشعارات Push (Firebase) | 2-3 أيام | متوسطة |
| دردشة محدودة (معرّف ↔ عائلات) | أسبوع | متوسطة |
| تكامل واتساب Business API | 3-5 أيام | متوسطة |
| نظام تقييم للمعرّفين | 2-3 أيام | منخفضة |
| Stripe (للخليج) | 2-3 أيام | عند التوسع |
| ترجمة إنجليزية (vue-i18n) | أسبوع | عند التوسع |
| محفظة رقمية داخلية | أسبوعين | منخفضة |

---

## ترتيب الأولويات للإطلاق

```
الأسبوع 1:
  ├── فتح حساب فوري + إدخال credentials
  ├── شراء Domain + Hetzner server
  ├── النشر الأولي + SSL
  └── ربط SMTP (Mailgun)

الأسبوع 2:
  ├── إضافة محتوى الدورات (فيديوهات + نصوص)
  ├── ربط Google Analytics + Search Console
  ├── اختبار الدفع من فوري Staging
  └── اختبار شامل على الموبايل

الأسبوع 3:
  ├── تحويل فوري لـ Production
  ├── ربط SMS Provider
  ├── دعوة 10 معرّفين (أئمة) للاختبار
  └── نشر أول مقال SEO

→ إطلاق Beta مع 30-50 مستخدم
```
