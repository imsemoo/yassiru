# يسّرو — قائمة المهام للإطلاق ✅

> اطبع الملف ده أو افتحه على الموبايل وعلّم على كل خطوة لما تخلصها.
> رتّب بالترتيب — كل خطوة بتعتمد على اللي قبلها.

---

## المرحلة 1: الحسابات (يوم 1)

### Domain
- [ ] ادخل namecheap.com أو godaddy.com
- [ ] ابحث عن `yassiru.com` — لو مش متاح جرب `yassiru.org`
- [ ] اشتريه (~$12/سنة)
- [ ] فعّل Privacy Protection (مجاني في Namecheap)
- [ ] **سجّل الـ Domain هنا:** ________________

### Hetzner (السيرفر)
- [ ] سجّل في hetzner.com/cloud
- [ ] أنشئ SSH Key من جهازك:
  ```
  ssh-keygen -t ed25519 -C "islam@yassiru.com"
  ```
- [ ] أنشئ Cloud Server:
  - النوع: CX22 (2 vCPU, 4GB RAM)
  - النظام: Ubuntu 24.04
  - الموقع: Falkenstein
  - ارفع الـ SSH public key
- [ ] **سجّل الـ IP هنا:** ________________

### DNS
- [ ] في إعدادات الـ Domain:
  ```
  A Record → @ → [IP السيرفر]
  A Record → www → [IP السيرفر]
  ```
- [ ] استنى 5-30 دقيقة
- [ ] تأكد: `ping yassiru.com` → يرجع الـ IP

### فوري (بوابة الدفع)
- [ ] سجّل في developer.fawry.io
- [ ] قدّم على Merchant Account (محتاج: هوية + حساب بنكي)
- [ ] **سجّل البيانات لما توصلك:**
  - FAWRY_MERCHANT_CODE: ________________
  - FAWRY_SECURITY_KEY: ________________
- [ ] ⚠️ الموافقة ممكن تاخد 3-7 أيام — كمّل باقي المهام في الانتظار

### Mailgun (الإيميلات)
- [ ] سجّل في mailgun.com
- [ ] أضف domain: mg.yassiru.com
- [ ] أضف DNS records المطلوبة (هيعرضهملك Mailgun):
  - [ ] TXT record
  - [ ] MX record
  - [ ] CNAME record
- [ ] استنى التفعيل (دقائق لساعات)
- [ ] **سجّل البيانات:**
  - MAIL_USERNAME: ________________
  - MAIL_PASSWORD: ________________

### Google Analytics
- [ ] ادخل analytics.google.com
- [ ] أنشئ Property جديد لـ yassiru.com
- [ ] اختار Web Stream
- [ ] **سجّل الـ ID:** G-________________

### Google Search Console
- [ ] ادخل search.google.com/search-console
- [ ] أضف yassiru.com
- [ ] تحقق بـ DNS record (هيديك TXT record تضيفه)
- [ ] بعد التحقق: أرسل الـ Sitemap → `https://yassiru.com/sitemap.xml`

---

## المرحلة 2: إعداد السيرفر (يوم 2-3)

### الاتصال والتثبيت
- [ ] اتصل بالسيرفر:
  ```
  ssh root@[IP]
  ```
- [ ] حدّث النظام:
  ```
  apt update && apt upgrade -y
  ```
- [ ] ثبّت Docker:
  ```
  curl -fsSL https://get.docker.com | sh
  apt install docker-compose-plugin git -y
  ```

### رفع المشروع
- [ ] لو عندك GitHub repo:
  ```
  cd /opt
  git clone https://github.com/YOUR_USERNAME/yassiru.git
  cd yassiru
  ```
- [ ] لو مفيش GitHub — ارفع من جهازك:
  ```
  scp -r "D:/MY work/me/yassiru" root@[IP]:/opt/yassiru
  ```

### ملف البيئة
- [ ] انسخ القالب:
  ```
  cp .env.production.example .env.production
  ```
- [ ] ولّد APP_KEY من جهازك المحلي:
  ```
  cd "D:/MY work/me/yassiru"
  php artisan key:generate --show
  ```
- [ ] **سجّل الـ KEY:** base64:________________
- [ ] عدّل الملف:
  ```
  nano .env.production
  ```
- [ ] املأ كل القيم:
  - [ ] APP_KEY ← اللي ولّدته
  - [ ] DB_PASSWORD ← كلمة مرور قوية (حروف + أرقام + رموز)
  - [ ] DB_ROOT_PASSWORD ← كلمة مرور مختلفة
  - [ ] FAWRY_MERCHANT_CODE ← من فوري
  - [ ] FAWRY_SECURITY_KEY ← من فوري
  - [ ] MAIL_USERNAME ← من Mailgun
  - [ ] MAIL_PASSWORD ← من Mailgun
  - [ ] GOOGLE_ANALYTICS_ID ← من Google
  - [ ] SANCTUM_STATEFUL_DOMAINS=yassiru.com,www.yassiru.com
  - [ ] CORS_ALLOWED_ORIGINS=https://yassiru.com,https://www.yassiru.com

### أول تشغيل
- [ ] شغّل Docker:
  ```
  docker compose -f docker-compose.prod.yml up -d
  ```
- [ ] شغّل الترحيلات:
  ```
  docker exec yassiru-app php artisan migrate --force
  ```
- [ ] شغّل الـ Seeders:
  ```
  docker exec yassiru-app php artisan db:seed
  ```
- [ ] فعّل الكاش:
  ```
  docker exec yassiru-app php artisan config:cache
  docker exec yassiru-app php artisan route:cache
  docker exec yassiru-app php artisan view:cache
  ```

### SSL (شهادة HTTPS)
- [ ] شغّل سكريبت الـ SSL:
  ```
  bash scripts/init-ssl.sh
  ```
- [ ] أعد تشغيل nginx:
  ```
  docker restart yassiru-nginx
  ```
- [ ] تأكد: افتح https://yassiru.com في المتصفح ← قفل أخضر؟

### التحقق
- [ ] الموقع يفتح: https://yassiru.com ✓
- [ ] SSL شغال (قفل أخضر) ✓
- [ ] الصفحة الرئيسية بتحمل بالعربي ✓
- [ ] API شغال: https://yassiru.com/api/stats يرجع JSON ✓
- [ ] Queue Worker شغال:
  ```
  docker logs yassiru-queue --tail 3
  ```
- [ ] Scheduler شغال:
  ```
  docker logs yassiru-scheduler --tail 3
  ```

---

## المرحلة 3: المحتوى (يوم 4-5)

### محتوى الدورات
- [ ] ادخل Tinker:
  ```
  docker exec -it yassiru-app php artisan tinker
  ```

#### المسار الشرعي (4 دروس)
- [ ] الدرس 1: أحكام الزواج في الإسلام
  ```php
  App\Models\Lesson::find(1)->update([
    'content' => '<h2>...</h2><p>...</p>',
    'video_url' => 'https://youtube.com/watch?v=...',
  ]);
  ```
- [ ] الدرس 2: حقوق الزوجين
- [ ] الدرس 3: آداب الخطبة والاختيار
- [ ] الدرس 4: المحرمات والعلاقات المحرمة

#### المسار النفسي (4 دروس)
- [ ] الدرس 5: التوافق النفسي
- [ ] الدرس 6: إدارة الخلافات
- [ ] الدرس 7: التواصل الفعال
- [ ] الدرس 8: الصحة النفسية الزوجية

#### المسار المالي (3 دروس)
- [ ] الدرس 9: التخطيط المالي للزواج
- [ ] الدرس 10: ميزانية الأسرة
- [ ] الدرس 11: تجنب الديون والربا

#### المسار العملي (3 دروس)
- [ ] الدرس 12: المهارات المنزلية
- [ ] الدرس 13: التعامل مع الأهل
- [ ] الدرس 14: بناء بيت مستقر

### أسئلة الاختبارات
- [ ] راجع أسئلة كل مسار:
  ```php
  App\Models\QuizQuestion::where('track', 'shariah')->get(['question']);
  ```
- [ ] عدّل أو أضف أسئلة لو محتاج

### إنشاء عرس جماعي تجريبي
- [ ] من لوحة الأدمن أو Tinker:
  ```php
  App\Models\GroupWedding::create([
    'city_id' => 1,
    'title' => 'العرس الجماعي الأول — القاهرة',
    'venue_name' => 'قاعة النيل الكبرى',
    'wedding_date' => now()->addMonths(3),
    'max_grooms' => 20,
    'price_per_groom' => 15000,
    'original_price' => 45000,
    'registration_deadline' => now()->addMonths(2),
  ]);
  ```

---

## المرحلة 4: الاختبار الشامل (يوم 6-7)

### Flow 1: المستخدم الجديد
- [ ] افتح https://yassiru.com على الموبايل
- [ ] الصفحة الرئيسية بتحمل كويس؟
- [ ] اضغط "ابدأ مجاناً" ← صفحة التسجيل؟
- [ ] سجّل بإيميل وهاتف حقيقي
- [ ] وصلك إيميل تأكيد؟ (لو Mailgun مفعّل)
- [ ] سجّل دخول
- [ ] ادخل الدورات ← ابدأ أول درس
- [ ] الفيديو يشتغل؟
- [ ] أكمل الدرس ← التقدم يتحدث؟
- [ ] أكمل كل المسارات واجتاز الاختبارات (70%+)
- [ ] ظهرت الشهادة؟
- [ ] حمّل PDF ← يتحمل؟

### Flow 2: الحاسبة
- [ ] ادخل الحاسبة
- [ ] اختار مدينة ← النتيجة تظهر؟
- [ ] غيّر المستوى (بسيط/متوسط/مميز)
- [ ] المقارنة بين التقليدي ويسّرو واضحة؟

### Flow 3: الصندوق التعاوني
- [ ] بحساب فيه شهادة: أنشئ حلقة جديدة
- [ ] الحلقة ظهرت في القائمة؟
- [ ] بحساب تاني: انضم للحلقة
- [ ] العقد الرقمي ظهر؟
- [ ] لوحة الحلقة: الأعضاء + الجدول واضح؟
- [ ] اضغط "ادفع الآن" ← يتوجه لفوري؟

### Flow 4: الأعراس
- [ ] شوف قائمة الأعراس
- [ ] ادخل تفاصيل عرس ← كل المعلومات واضحة؟
- [ ] سجّل في عرس
- [ ] ادخل "تسجيلاتي" ← التسجيل موجود؟
- [ ] اضغط "ادفع الآن" ← يتوجه لفوري؟

### Flow 5: المعرّف
- [ ] سجّل دخول بحساب المعرّف
- [ ] لوحة المعرّف ← الإحصائيات واضحة؟
- [ ] أضف مرشح (ذكر) ← اتضاف؟
- [ ] أضف مرشحة (أنثى) ← اتضافت؟
- [ ] شوف الاقتراحات ← نسب التوافق ظاهرة؟

### Flow 6: الأدمن
- [ ] سجّل دخول بحساب الأدمن
- [ ] لوحة التحكم ← الإحصائيات واضحة؟
- [ ] إدارة المستخدمين ← القائمة تظهر؟
- [ ] إدارة المعرّفين ← ممكن تعتمد/ترفض؟
- [ ] البلاغات ← ممكن تحل؟
- [ ] سجل المراجعة ← العمليات متسجلة؟

### Flow 7: الموبايل
- [ ] افتح على iPhone أو Android
- [ ] الـ Bottom Nav شغال؟
- [ ] النصوص مقروءة؟ مفيش نص متقطع؟
- [ ] الأزرار سهل تضغطها بالإصبع؟
- [ ] جرب "أضف للشاشة الرئيسية" ← يشتغل كتطبيق (PWA)؟

### Flow 8: صفحة "عن المنصة"
- [ ] ادخل /about
- [ ] الأركان الخمسة واضحة؟
- [ ] الآية القرآنية بتظهر؟
- [ ] CTA "أنشئ حساب مجاني" شغال؟

---

## المرحلة 5: الإطلاق التجريبي Beta (يوم 8-14)

### دعوة المعرّفين
- [ ] جهّز قائمة بـ 10 أئمة/معلمين تعرفهم
  1. ________________ (واتساب: ____________)
  2. ________________ (واتساب: ____________)
  3. ________________ (واتساب: ____________)
  4. ________________ (واتساب: ____________)
  5. ________________ (واتساب: ____________)
  6. ________________ (واتساب: ____________)
  7. ________________ (واتساب: ____________)
  8. ________________ (واتساب: ____________)
  9. ________________ (واتساب: ____________)
  10. ________________ (واتساب: ____________)
- [ ] أرسل رسالة واتساب لكل واحد (النص موجود في LAUNCH-PLAN.md)
- [ ] لما يسجلوا → اعتمدهم من لوحة الأدمن
- [ ] ساعدهم يضيفوا أول مرشح

### دعوة المستخدمين
- [ ] انشر على Facebook
- [ ] انشر على Twitter
- [ ] أرسل في جروبات واتساب
- [ ] انشر مقال على موقع إخباري
- [ ] الهدف: 30-50 مستخدم أول أسبوع

### مراقبة يومية (كل يوم في الأسبوع)
- [ ] يوم 8: شوف الـ logs + إحصائيات التسجيل
- [ ] يوم 9: شوف الـ logs + تواصل مع المعرّفين
- [ ] يوم 10: شوف الـ logs + اجمع feedback
- [ ] يوم 11: شوف الـ logs + أصلح أي bugs
- [ ] يوم 12: شوف الـ logs + حسّن UX
- [ ] يوم 13: شوف الـ logs + نسخ احتياطي يدوي
- [ ] يوم 14: ملخص أسبوع Beta — كم مستخدم؟ كم معرّف؟ إيه المشاكل؟

---

## المرحلة 6: الإطلاق الرسمي (يوم 15-21)

### إصلاحات
- [ ] أصلح كل bugs اللي ظهرت في Beta
- [ ] حسّن أي شاشة المستخدمين اشتكوا منها
- [ ] انشر التحديثات:
  ```
  ssh root@[IP]
  cd /opt/yassiru
  bash deploy.sh
  ```

### فوري Production
- [ ] لو فوري وافقوا على حسابك:
  - عدّل `.env.production`:
    ```
    FAWRY_BASE_URL=https://www.atfawry.com
    ```
  - أعد الكاش:
    ```
    docker exec yassiru-app php artisan config:cache
    docker restart yassiru-queue
    ```
  - اختبر دفعة حقيقية صغيرة (سجّل في عرس وادفع)
- [ ] لو فوري لسه مرد:
  - كمّل بالـ Staging — بس قول للمستخدمين "الدفع قريباً"

### SEO
- [ ] https://yassiru.com/sitemap.xml شغال؟
- [ ] Google Search Console: الـ Sitemap متقبل؟
- [ ] انشر مقال SEO: "تكاليف الزواج في مصر 2026"
- [ ] ضيف رابط ليسّرو في المقال
- [ ] شاركه على السوشال ميديا

### النسخ الاحتياطي التلقائي
- [ ] فعّل cron:
  ```
  ssh root@[IP]
  crontab -e
  ```
  أضف:
  ```
  0 3 * * * /opt/yassiru/scripts/backup-db.sh
  ```

### يوم الإطلاق 🚀
- [ ] تأكد من كل حاجة شغالة (SSL + API + Queue + Scheduler)
- [ ] انشر مقال رسمي: "يسّرو: أول منصة تيسير زواج متكاملة"
- [ ] انشر على كل المواقع الإخبارية (10+ مواقع = 6 مليون زيارة)
- [ ] انشر على Facebook + Twitter + Instagram
- [ ] أرسل واتساب لكل المعارف
- [ ] أرسل للمعرّفين: "المنصة إطلقت — ابدأ أضف مرشحين"

---

## بعد الإطلاق: مهام دورية

### يومياً (5 دقائق)
- [ ] شوف `docker logs yassiru-app --tail 20` — أي errors؟
- [ ] شوف لوحة الأدمن — بلاغات جديدة؟

### أسبوعياً (15 دقيقة)
- [ ] Google Analytics — كم زائر هذا الأسبوع؟
- [ ] Search Console — أي أخطاء؟
- [ ] راجع feedback المستخدمين
- [ ] تأكد الـ backups شغالة:
  ```
  ls -la /backups/yassiru/
  ```

### شهرياً (30 دقيقة)
- [ ] حدّث packages:
  ```
  docker exec yassiru-app composer update --no-dev
  ```
- [ ] شوف مساحة القرص:
  ```
  df -h
  ```
- [ ] راجع إحصائيات الشهر: مستخدمين جدد، حلقات، أعراس، دفعات

---

## أرقام مهمة (سجّلها هنا)

| البيان | القيمة |
|--------|--------|
| Domain | |
| IP السيرفر | |
| APP_KEY | |
| DB_PASSWORD | |
| FAWRY_MERCHANT_CODE | |
| FAWRY_SECURITY_KEY | |
| MAIL_USERNAME | |
| MAIL_PASSWORD | |
| GOOGLE_ANALYTICS_ID | |
| SSH Key location | |

---

> **قاعدة:** لو حصلت مشكلة مش عارف تحلها:
> 1. شوف الـ logs أول: `docker logs yassiru-app --tail 50`
> 2. لو مشكلة في الدفع: `docker logs yassiru-queue --tail 50`
> 3. لو الموقع واقع: `docker compose -f docker-compose.prod.yml ps`
> 4. أعد التشغيل: `bash deploy.sh`
