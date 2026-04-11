# خطة إطلاق منصة يسّرو — من المحلي للإنتاج

> خطة عملية خطوة بخطوة لنشر المنصة وإطلاقها رسمياً
> آخر تحديث: 2026-04-09

---

## الأسبوع 1: التجهيزات الأساسية

### يوم 1-2: الحسابات والخدمات

#### 1. شراء Domain
- ادخل https://www.namecheap.com أو https://www.godaddy.com
- اشتري `yassiru.com` (~$10-15/سنة)
- لو مش متاح جرب: `yassiru.org` أو `yassiru.app`
- **مهم:** فعّل Privacy Protection عشان بياناتك متظهرش في WHOIS

#### 2. إنشاء سيرفر Hetzner
- سجّل في https://www.hetzner.com/cloud
- أنشئ Cloud Server:
  - **النوع:** CX22 (2 vCPU, 4GB RAM) — €4.5/شهر
  - **النظام:** Ubuntu 24.04
  - **الموقع:** Falkenstein (أقرب لمصر وأوروبا)
  - **SSH Key:** أنشئ مفتاح SSH من جهازك:
    ```bash
    ssh-keygen -t ed25519 -C "islam@yassiru.com"
    ```
    وارفع الـ public key في Hetzner
- **بعد الإنشاء** سجّل الـ IP address

#### 3. توجيه الـ DNS
- في Namecheap/GoDaddy → DNS Settings:
  ```
  Type: A    | Host: @    | Value: [IP السيرفر]  | TTL: 300
  Type: A    | Host: www  | Value: [IP السيرفر]  | TTL: 300
  ```
- استنى 5-30 دقيقة للتفعيل
- تأكد: `ping yassiru.com` → يرجع IP السيرفر

#### 4. فتح حساب فوري
- سجّل في https://developer.fawry.io
- أنشئ Merchant Account
- المطلوب:
  - بطاقة رقم قومي
  - حساب بنكي (لاستقبال المدفوعات)
  - بيانات المنصة
- بعد الموافقة هتاخد:
  - `FAWRY_MERCHANT_CODE`
  - `FAWRY_SECURITY_KEY`
- **ملاحظة:** الموافقة ممكن تاخد 3-7 أيام عمل

#### 5. فتح حساب Mailgun
- سجّل في https://www.mailgun.com (مجاني أول 3 أشهر)
- أضف domain: `mg.yassiru.com`
- أضف DNS records المطلوبة:
  ```
  Type: TXT   | Host: mg        | Value: [من Mailgun]
  Type: MX    | Host: mg        | Value: [من Mailgun]
  Type: CNAME | Host: email.mg  | Value: mailgun.org
  ```
- بعد التفعيل هتاخد:
  - `MAIL_USERNAME` (postmaster@mg.yassiru.com)
  - `MAIL_PASSWORD`

#### 6. Google Analytics + Search Console
- https://analytics.google.com → أنشئ Property لـ `yassiru.com`
- انسخ الـ Measurement ID (`G-XXXXXXXXXX`)
- https://search.google.com/search-console → أضف الموقع → تحقق بـ DNS

---

### يوم 3-4: إعداد السيرفر

#### 1. الاتصال بالسيرفر
```bash
ssh root@[IP]
```

#### 2. تثبيت Docker
```bash
apt update && apt upgrade -y
curl -fsSL https://get.docker.com | sh
apt install docker-compose-plugin -y
```

#### 3. تثبيت Git
```bash
apt install git -y
```

#### 4. رفع المشروع
```bash
mkdir -p /opt
cd /opt
git clone https://github.com/YOUR_USERNAME/yassiru.git
cd yassiru
```

**أو** لو مش على GitHub — ارفع بالـ SCP:
```bash
# من جهازك المحلي:
scp -r /path/to/yassiru root@[IP]:/opt/yassiru
```

#### 5. إنشاء ملف البيئة
```bash
cp .env.production.example .env.production
nano .env.production
```

عدّل القيم التالية:
```env
APP_KEY=     # ولّده: php artisan key:generate --show (على جهازك)
APP_URL=https://yassiru.com

DB_DATABASE=yassiru
DB_USERNAME=yassiru
DB_PASSWORD=[كلمة_مرور_قوية_جداً_هنا]
DB_ROOT_PASSWORD=[كلمة_مرور_مختلفة_قوية]

FAWRY_MERCHANT_CODE=[من فوري]
FAWRY_SECURITY_KEY=[من فوري]
FAWRY_BASE_URL=https://atfawry.fawrystaging.com
FAWRY_RETURN_URL=https://yassiru.com/api/payment/callback

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailgun.org
MAIL_PORT=587
MAIL_USERNAME=[من Mailgun]
MAIL_PASSWORD=[من Mailgun]
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yassiru.com
MAIL_FROM_NAME=يسّرو

GOOGLE_ANALYTICS_ID=[من Google Analytics]

SANCTUM_STATEFUL_DOMAINS=yassiru.com,www.yassiru.com
SESSION_DOMAIN=.yassiru.com

CORS_ALLOWED_ORIGINS=https://yassiru.com,https://www.yassiru.com
```

#### 6. أول تشغيل (بدون SSL)
```bash
# عدّل nginx config مؤقتاً لـ HTTP فقط
# في docker/nginx/production.conf — علّق قسم SSL مؤقتاً

docker compose -f docker-compose.prod.yml up -d
docker exec yassiru-app php artisan migrate --force
docker exec yassiru-app php artisan db:seed
docker exec yassiru-app php artisan config:cache
docker exec yassiru-app php artisan route:cache
```

#### 7. تفعيل SSL
```bash
bash scripts/init-ssl.sh
```
- هيطلب إيميلك: `islam@yassiru.com`
- بعد النجاح، شغّل الـ production nginx config الكامل:
```bash
docker compose -f docker-compose.prod.yml down
docker compose -f docker-compose.prod.yml up -d
```

#### 8. التحقق
```bash
# SSL
curl -I https://yassiru.com
# → يرجع 200 OK مع headers الأمان

# API
curl https://yassiru.com/api/stats
# → يرجع JSON بإحصائيات المنصة

# Queue Worker
docker logs yassiru-queue --tail 5
# → يعرض "Processing" أو "No jobs"

# Scheduler
docker logs yassiru-scheduler --tail 5
# → يعرض "No scheduled commands are ready to run"
```

---

### يوم 5: إضافة المحتوى

#### 1. محتوى الدورات التأهيلية
المطلوب لكل درس:
- **محتوى HTML** — يتحط في حقل `content` عبر Tinker أو Seeder
- **فيديو** (اختياري) — رابط YouTube أو Vimeo في حقل `video_url`

```bash
docker exec -it yassiru-app php artisan tinker
```

```php
// مثال: تحديث الدرس الأول
$lesson = App\Models\Lesson::find(1);
$lesson->content = '<h2>أحكام الزواج في الإسلام</h2><p>الزواج سنة مؤكدة...</p>';
$lesson->video_url = 'https://www.youtube.com/watch?v=VIDEO_ID';
$lesson->save();
```

**المسارات الأربعة:**

| المسار | عدد الدروس | المطلوب |
|--------|-----------|---------|
| الشرعي | 4 دروس | محتوى عن أحكام الزواج، الحقوق، الآداب، المحرمات |
| النفسي | 4 دروس | التوافق، إدارة الخلافات، التواصل، الصحة النفسية |
| المالي | 3 دروس | التخطيط المالي، الميزانية، تجنب الديون |
| العملي | 3 دروس | مهارات منزلية، التعامل مع الأهل، بناء البيت |

**نصيحة:** ابدأ بمحتوى نصي بسيط + روابط فيديوهات YouTube موجودة عن الموضوع. ممكن تحسّن المحتوى لاحقاً.

#### 2. أسئلة الاختبارات
الأسئلة موجودة في `QuizQuestionSeeder` — راجعها وعدّل لو محتاج:
```bash
docker exec -it yassiru-app php artisan tinker
```
```php
App\Models\QuizQuestion::where('track', 'shariah')->get(['id','question','options']);
```

---

### يوم 6-7: الاختبار الشامل

#### 1. اختبار كل Flow بنفسك

**Flow 1: مستخدم جديد**
- [ ] ادخل https://yassiru.com
- [ ] الصفحة الرئيسية بتحمل؟ الحاسبة شغالة؟
- [ ] سجّل حساب جديد (بريد + هاتف حقيقي)
- [ ] سجّل دخول
- [ ] ادخل الدورات → ابدأ درس → شاهد فيديو → أكمل الدرس
- [ ] أكمل الـ 4 مسارات واجتاز الاختبارات
- [ ] شوف الشهادة → حمّل PDF
- [ ] جرب الحاسبة بمدن مختلفة

**Flow 2: الصندوق**
- [ ] بحساب حاصل على شهادة: أنشئ حلقة صندوق
- [ ] بحساب تاني: انضم للحلقة
- [ ] اضغط "ادفع الآن" → يتوجه لفوري؟
- [ ] شوف لوحة الحلقة: الأعضاء، الجدول، المساهمات

**Flow 3: الأعراس**
- [ ] شوف قائمة الأعراس
- [ ] ادخل تفاصيل عرس
- [ ] سجّل في عرس → ادفع عبر فوري

**Flow 4: المعرّف**
- [ ] بحساب معرّف: ادخل لوحة المعرّف
- [ ] أضف مرشح (ذكر + أنثى)
- [ ] شوف الاقتراحات
- [ ] أنشئ توصية

**Flow 5: الأدمن**
- [ ] بحساب أدمن: ادخل /admin
- [ ] شوف الإحصائيات
- [ ] اعتمد معرّف
- [ ] شوف البلاغات
- [ ] شوف سجل المراجعة

**Flow 6: موبايل**
- [ ] افتح الموقع على الموبايل
- [ ] التنقل سلس؟ Bottom nav شغال؟
- [ ] النصوص مقروءة؟ الأزرار كبيرة كفاية؟
- [ ] جرب "أضف للشاشة الرئيسية" (PWA)

#### 2. اختبار الأمان
```bash
# من جهازك المحلي:

# هل SSL شغال؟
curl -I https://yassiru.com | grep -i strict

# هل Security Headers موجودة؟
curl -I https://yassiru.com | grep -i "x-frame\|x-content\|referrer"

# هل API rate limiting شغال؟
for i in {1..70}; do curl -s -o /dev/null -w "%{http_code}\n" https://yassiru.com/api/stats; done
# → بعد 60 request يرجع 429

# هل Webhook endpoint مفتوح؟
curl -X POST https://yassiru.com/api/payments/webhook/fawry -d '{"test":1}'
# → يرجع 200 (بس مش هيعالج لأن الـ signature غلط)
```

#### 3. اختبار الأداء
```bash
# تأكد الصفحات سريعة
time curl -s https://yassiru.com > /dev/null
# → أقل من 1 ثانية

# تأكد API سريع
time curl -s https://yassiru.com/api/stats > /dev/null
# → أقل من 500ms
```

---

## الأسبوع 2: الإطلاق التجريبي (Beta)

### يوم 8-9: دعوة 10 معرّفين

#### الهدف: 10 أئمة/معلمين يسجلوا كمعرّفين

**رسالة واتساب للأئمة:**
```
السلام عليكم شيخنا الكريم،

أنا إسلام، مطور منصة "يسّرو" لتيسير الزواج.

المنصة بتساعد الشباب يتزوجوا عن طريق:
- دورة تأهيلية شرعية ونفسية
- توفيق عبر معرّفين موثوقين (زي حضرتك)
- صندوق تعاوني بدون فوائد
- أعراس جماعية بتوفير 70%

حابب أدعو حضرتك تكون من أوائل المعرّفين على المنصة.
مش هتحتاج تدفع أي حاجة — فقط تسجّل وتضيف المرشحين اللي تعرفهم.

الرابط: https://yassiru.com/register

لو عندك أي سؤال أنا تحت أمرك.
جزاكم الله خيراً.
```

**بعد التسجيل:**
- ادخل لوحة الأدمن → اعتمد المعرّف
- تواصل معاه وساعده يضيف أول مرشح

### يوم 10-11: دعوة 30-50 مستخدم

#### المصادر:
1. **المواقع الإخبارية** — انشر مقال "يسّرو: حل جديد لأزمة الزواج"
2. **واتساب** — أرسل لأصدقاء ومعارف
3. **السوشال ميديا** — بوست على Facebook/Twitter

**رسالة واتساب للمستخدمين:**
```
🕌 يسّرو — أول منصة تيسير زواج متكاملة

زواجك بنصف التكلفة. بدون فوائد. بدون ديون.

✅ دورة تأهيلية مجانية
✅ توفيق عبر معرّفين موثوقين
✅ صندوق تعاوني (جمعية رقمية)
✅ أعراس جماعية بتوفير 70%

سجّل مجاناً: https://yassiru.com

ابعت الرابط لأي حد تعرفه محتاج يتجوز 💍
```

### يوم 12-14: المراقبة والإصلاح

#### مراقبة يومية:
```bash
# مراقبة الأخطاء
docker exec yassiru-app tail -50 storage/logs/laravel.log

# مراقبة Queue
docker logs yassiru-queue --tail 20

# مراقبة الأداء
docker stats --no-stream

# النسخ الاحتياطي
bash scripts/backup-db.sh
```

#### تتبع الأرقام:
- [ ] كم واحد سجّل؟
- [ ] كم واحد أكمل الدورة؟
- [ ] كم معرّف اتسجل؟
- [ ] كم حلقة صندوق اتعملت؟
- [ ] كم واحد سجل في عرس؟
- [ ] فيه بلاغات؟
- [ ] فيه أخطاء في الـ logs؟

---

## الأسبوع 3: التحسين والإطلاق الرسمي

### يوم 15-17: إصلاح مشاكل Beta

- اجمع feedback من المستخدمين عبر واتساب
- أصلح أي bugs ظهرت
- حسّن أي UX مش واضح
- انشر التحديثات:
  ```bash
  ssh root@[IP]
  cd /opt/yassiru
  bash deploy.sh
  ```

### يوم 18-19: تحويل فوري للإنتاج

بعد ما تختبر الدفع على Staging وتتأكد إنه شغال:
```bash
# عدّل .env.production
nano .env.production
```
```env
FAWRY_BASE_URL=https://www.atfawry.com
FAWRY_RETURN_URL=https://yassiru.com/api/payment/callback
```
```bash
docker exec yassiru-app php artisan config:cache
docker restart yassiru-queue
```

### يوم 20: SEO

- [ ] تأكد إن `https://yassiru.com/sitemap.xml` شغال
- [ ] أرسل الـ Sitemap في Google Search Console
- [ ] تأكد إن كل صفحة لها title ووصف مختلف
- [ ] انشر أول مقال SEO على مواقعك الإخبارية:
  - "كم تكلفة الزواج في مصر 2026؟ — وكيف توفّر 70%"
  - ضيف رابط ليسّرو في المقال

### يوم 21: الإطلاق الرسمي 🚀

#### قبل الإطلاق:
- [ ] كل الـ tests بتنجح
- [ ] SSL شغال
- [ ] فوري Production شغال
- [ ] الإيميلات بتوصل
- [ ] Queue Worker شغال
- [ ] Scheduler شغال
- [ ] النسخ الاحتياطي شغال
- [ ] Google Analytics بيتتبع
- [ ] الموقع سريع على الموبايل

#### يوم الإطلاق:
1. انشر مقال رسمي على كل مواقعك الإخبارية
2. انشر على Facebook + Twitter + Instagram
3. أرسل واتساب لكل المعارف
4. أرسل للمعرّفين: "المنصة إطلقت رسمياً — ابدأ أضف مرشحين"

---

## بعد الإطلاق: مهام أسبوعية

### يومياً:
- [ ] شوف `storage/logs/laravel.log` — أي errors؟
- [ ] شوف لوحة الأدمن — أي بلاغات؟
- [ ] شوف Queue — أي jobs فشلت؟

### أسبوعياً:
- [ ] نسخ احتياطي (أو فعّل cron):
  ```bash
  crontab -e
  # أضف:
  0 3 * * * /opt/yassiru/scripts/backup-db.sh
  ```
- [ ] راجع Google Analytics — كم زائر؟ من أين؟
- [ ] راجع Search Console — أي أخطاء زحف؟
- [ ] حدّث المحتوى لو محتاج

### شهرياً:
- [ ] حدّث الـ packages:
  ```bash
  docker exec yassiru-app composer update
  docker exec yassiru-app npm update
  ```
- [ ] راجع SSL — يتجدد تلقائياً بس تأكد
- [ ] راجع مساحة القرص:
  ```bash
  df -h
  ```
- [ ] نضّف backups قديمة (السكريبت بيعمل ده تلقائياً)

---

## التكاليف الشهرية

| البند | التكلفة |
|-------|--------|
| Hetzner CX22 (سيرفر) | €4.5/شهر (~$5) |
| Domain (سنوياً) | ~$12/سنة (~$1/شهر) |
| Mailgun (أول 3 أشهر مجاني) | $0-35/شهر |
| فوري (عمولة على كل عملية) | ~2.5% من كل دفعة |
| **الإجمالي** | **~$6-40/شهر** |

---

## ملخص الجدول الزمني

```
الأسبوع 1:
  يوم 1-2: شراء domain + سيرفر + حسابات (فوري، Mailgun، Analytics)
  يوم 3-4: إعداد السيرفر + Docker + SSL + أول deploy
  يوم 5:   إضافة محتوى الدورات
  يوم 6-7: اختبار شامل (كل الـ flows + أمان + أداء)

الأسبوع 2:
  يوم 8-9:   دعوة 10 معرّفين (أئمة/معلمين)
  يوم 10-11:  دعوة 30-50 مستخدم
  يوم 12-14:  مراقبة + إصلاح مشاكل

الأسبوع 3:
  يوم 15-17: إصلاح bugs + تحسين UX
  يوم 18-19: تحويل فوري لـ Production
  يوم 20:    SEO + أول مقال
  يوم 21:    🚀 الإطلاق الرسمي
```
