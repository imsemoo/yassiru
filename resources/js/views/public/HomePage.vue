<template>
  <!-- ============================================ -->
  <!-- 1. HERO — Single Focus, Strong CTA          -->
  <!-- ============================================ -->
  <section class="hero">
    <div class="hero__field" aria-hidden="true"></div>
    <div class="hero__arcade" aria-hidden="true"></div>

    <div class="container hero__inner">
      <div class="hero__grid">
        <!-- Lead -->
        <div class="hero__lead">
          <p class="hero__origin anim">
            <span class="hero__origin__text">يسِّروا ولا تُعسِّروا</span>
            <span class="hero__origin__rule" aria-hidden="true"></span>
            <span class="hero__origin__src">ومن الحديث جاء الاسم</span>
          </p>

          <h1 class="hero__title anim anim-delay-1">
            زواجك بـ<span class="hero__title__accent">نصف التكلفة</span>.<br>
            بدون فوائد. بدون ديون.
          </h1>

          <p class="hero__lede anim anim-delay-2">
            يسّرو تجمع الدورة التأهيلية، التوفيق عبر معرّفين موثوقين،
            الصندوق التعاوني، والأعراس الجماعية —
            في رحلة واحدة تنقلك من القرار للزواج.
          </p>

          <div class="hero__actions anim anim-delay-3">
            <a href="#calculator" class="hero__cta" @click.prevent="scrollTo('calculator')">
              احسب توفيرك
              <PhArrowLeft :size="18" weight="bold" />
            </a>
            <a href="#how" class="hero__link" @click.prevent="scrollTo('how')">
              شوف إزاي يشتغل
              <PhArrowDown :size="15" weight="bold" />
            </a>
          </div>

          <ul class="hero__marks anim anim-delay-4">
            <li><i class="sq" aria-hidden="true"></i>معتمد شرعياً</li>
            <li><i class="sq" aria-hidden="true"></i>خصوصية كاملة</li>
            <li><i class="sq sq--gold" aria-hidden="true"></i>مجاني 100%</li>
          </ul>
        </div>

        <!-- The arch gauge: the whole arch is the full cost, the fill is what
             you pay, and the arched void above it is what you keep. -->
        <aside class="hero__aside anim anim-delay-2">
          <figure
            class="arch"
            role="img"
            aria-label="مقياس على شكل قوس: تكلفة زواج كاملة 200,000 جنيه، تدفع منها 62,000 مع يسّرو، فتوفّر 138,000 جنيه."
          >
            <svg class="arch__svg" viewBox="0 0 300 500" aria-hidden="true" focusable="false">
              <defs>
                <clipPath id="archClip">
                  <path d="M 45 460 L 45 270 A 210 210 0 0 1 150 88 A 210 210 0 0 1 255 270 L 255 460 Z" />
                </clipPath>
              </defs>

              <g clip-path="url(#archClip)">
                <rect class="arch__fill" x="0" y="345" width="300" height="115" />
                <line class="arch__waterline" x1="0" y1="345" x2="300" y2="345" />
              </g>

              <!-- Keystone: the four-point star from the brand mark -->
              <path
                class="arch__star"
                d="M 150 72 Q 152.8 85.2 166 88 Q 152.8 90.8 150 104 Q 147.2 90.8 134 88 Q 147.2 85.2 150 72 Z"
              />

              <path
                class="arch__outline"
                d="M 45 460 L 45 270 A 210 210 0 0 1 150 88 A 210 210 0 0 1 255 270 L 255 460 Z"
              />

              <g class="arch__tick">
                <line x1="14" y1="88" x2="45" y2="88" />
                <line x1="30" y1="181" x2="45" y2="181" />
                <line x1="30" y1="274" x2="45" y2="274" />
                <line x1="30" y1="367" x2="45" y2="367" />
                <line x1="14" y1="460" x2="45" y2="460" />
              </g>

              <text class="arch__scaletxt" x="14" y="79">200,000</text>
              <text class="arch__scaletxt" x="14" y="478">0</text>
            </svg>

            <div class="arch__plate">
              <div class="arch__saving">
                <p class="arch__saving__label">توفيرك</p>
                <p class="arch__saving__value">{{ fmtNum(savingCount) }} <span>ج.م</span></p>
                <p class="arch__saving__note">نحو 70% من إجمالي التكلفة</p>
              </div>

              <div class="arch__paid">
                <p class="arch__paid__value">{{ fmtNum(paidCount) }}</p>
                <p class="arch__paid__label">اللي بتدفعه فعلاً</p>
              </div>
            </div>
          </figure>
        </aside>
      </div>
    </div>
  </section>

  <!-- ============================================ -->
  <!-- 2. PROBLEM — Emotional Hook                 -->
  <!-- ============================================ -->
  <section class="problem-section">
    <div class="container">
      <div class="text-center mb-3">
        <span class="section-eyebrow section-eyebrow--alert">المشكلة الحقيقية</span>
      </div>

      <h2 class="problem-headline">
        لو زواج 200 ألف وراتبك 4 آلاف...<br>
        محتاج <span class="number">50 شهر</span> لتبدأ حياتك
      </h2>

      <p class="problem-sub">
        أزمة الزواج في العالم العربي ليست مجرد أرقام —
        بل أحلام مؤجّلة، وشباب محرومون من حقهم الشرعي،
        وحلول ملتوية تفتح أبواب الربا والديون.
      </p>

      <div class="problem-grid">
        <div class="problem-stat" v-for="(stat, i) in problemStats" :key="stat.label">
          <div class="problem-stat__icon">
            <component :is="stat.icon" :size="22" weight="regular" />
          </div>
          <div class="problem-stat__number">{{ stat.value }}</div>
          <div class="problem-stat__label">{{ stat.label }}</div>
        </div>
      </div>
    </div>
  </section>

  <!-- ============================================ -->
  <!-- 3. THE BIG IDEA — Ecosystem                 -->
  <!-- ============================================ -->
  <section class="idea-section">
    <div class="container">
      <div class="text-center">
        <span class="section-eyebrow">الحل المتكامل</span>
      </div>

      <h2 class="idea-headline">
        مش مجرد موقع زواج —<br>
        بل <span class="accent">منظومة كاملة</span>
        تحل كل العوائق مع بعض
      </h2>

      <div class="ecosystem-grid">
        <router-link
          v-for="(pillar, i) in pillars"
          :key="pillar.title"
          :to="pillar.route"
          class="ecosystem-pillar"
        >
          <div class="ecosystem-pillar__number">{{ i + 1 }}</div>
          <div class="ecosystem-pillar__icon">
            <component :is="pillar.icon" :size="26" weight="regular" />
          </div>
          <h3 class="ecosystem-pillar__title">{{ pillar.title }}</h3>
          <p class="ecosystem-pillar__desc">{{ pillar.desc }}</p>
        </router-link>
      </div>
    </div>
  </section>

  <!-- ============================================ -->
  <!-- 4. HOW IT WORKS — Step by Step              -->
  <!-- ============================================ -->
  <section class="how-section" id="how">
    <div class="container">
      <div class="text-center mb-3">
        <span class="section-eyebrow">رحلتك معنا</span>
      </div>

      <h2 class="section-title-lg text-center">
        من القرار للزواج في 4 خطوات
      </h2>
      <p class="section-sub-lg text-center">
        كل خطوة مدروسة. كل مرحلة محمية.
        ما عليك إلا اتباع الرحلة حتى النهاية.
      </p>

      <div class="steps-timeline">
        <div
          v-for="(step, i) in steps"
          :key="step.title"
          class="step-card"
        >
          <div class="step-card__circle">{{ i + 1 }}</div>
          <h4 class="step-card__title">{{ step.title }}</h4>
          <p class="step-card__desc">{{ step.desc }}</p>
          <span class="step-card__duration">
            <PhClock :size="12" weight="bold" class="ms-1" />
            {{ step.duration }}
          </span>
        </div>
      </div>
    </div>
  </section>

  <!-- ============================================ -->
  <!-- 5. INTERACTIVE CALCULATOR (HERO TOOL)       -->
  <!-- ============================================ -->
  <section class="calc-section" id="calculator">
    <div class="container calc-section__inner">
      <div class="calc-header">
        <h2>احسب توفيرك الحقيقي الآن</h2>
        <p>اختر مدينتك ومستوى التجهيزات — وشوف كم سيوفّر لك يسّرو من جيبك مباشرةً</p>
      </div>

      <div class="calc-tool">
        <div class="calc-tool__inputs">
          <div>
            <label class="calc-tool__label">
              <PhMapPin :size="14" weight="bold" class="ms-1" />
              المدينة
            </label>
            <select v-model="selectedCity" class="calc-tool__select" @change="calculateCost">
              <option value="" disabled>اختر مدينتك</option>
              <option v-for="city in cities" :key="city.id" :value="city">
                {{ city.name }} — {{ city.country }}
              </option>
            </select>
          </div>
          <div>
            <label class="calc-tool__label">
              <PhSliders :size="14" weight="bold" class="ms-1" />
              مستوى التجهيزات
            </label>
            <select v-model="selectedLevel" class="calc-tool__select" @change="calculateCost">
              <option value="simple">بسيط — الأساسيات فقط</option>
              <option value="medium">متوسط — الخيار الأكثر شيوعاً</option>
              <option value="luxury">مميز — تجهيزات كاملة</option>
            </select>
          </div>
        </div>

        <div v-if="calcResult" class="calc-tool__result">
          <div class="calc-tool__compare">
            <div class="calc-tool__compare-side calc-tool__compare-side--bad">
              <div class="label">بدون يسّرو</div>
              <div class="amount">{{ formatNumber(calcResult.individual_cost) }}</div>
              <div class="currency">{{ calcResult.currency }}</div>
            </div>
            <div class="arrow">
              <PhArrowLeft :size="32" weight="bold" />
            </div>
            <div class="calc-tool__compare-side calc-tool__compare-side--good">
              <div class="label">مع يسّرو</div>
              <div class="amount">{{ formatNumber(calcResult.yassiru_cost) }}</div>
              <div class="currency">{{ calcResult.currency }}</div>
            </div>
          </div>

          <div class="calc-tool__savings">
            <div class="label">توفيرك الإجمالي</div>
            <div class="amount">{{ formatNumber(calcResult.savings) }} {{ calcResult.currency }}</div>
            <div class="small-text">يعادل {{ calcResult.savings_percent }}% من تكلفتك التقليدية</div>
          </div>

          <div class="calc-tool__cta">
            <router-link to="/register" class="hero__cta">
              ابدأ رحلتك مجاناً
              <PhArrowLeft :size="18" weight="bold" />
            </router-link>
          </div>
        </div>

        <div v-else class="text-center py-3 text-muted">
          <PhArrowFatUp :size="32" weight="duotone" class="mb-2" />
          <p class="mb-0">اختر مدينتك أعلاه لرؤية النتيجة</p>
        </div>
      </div>
    </div>
  </section>

  <!-- ============================================ -->
  <!-- 6. WHY YOU CAN TRUST US                     -->
  <!-- ============================================ -->
  <section class="trust-section">
    <div class="container">
      <div class="text-center mb-3">
        <span class="section-eyebrow">لماذا يسّرو؟</span>
      </div>

      <h2 class="section-title-lg text-center">
        ثقتك ليست مجرد شعار —<br>
        بل التزام مُوثّق
      </h2>

      <div class="trust-grid">
        <div
          v-for="(t, i) in trustPillars"
          :key="t.title"
          class="trust-card"
        >
          <div class="trust-card__icon">
            <component :is="t.icon" :size="24" weight="regular" />
          </div>
          <h4 class="trust-card__title">{{ t.title }}</h4>
          <p class="trust-card__desc">{{ t.desc }}</p>
        </div>
      </div>
    </div>
  </section>

  <!-- ============================================ -->
  <!-- 7. SOCIAL PROOF — Testimonials              -->
  <!-- ============================================ -->
  <section class="proof-section">
    <div class="container">
      <div class="text-center mb-3">
        <span class="section-eyebrow">آراء حقيقية</span>
      </div>

      <h2 class="section-title-lg text-center">
        قصص نجاح من أعضاء يسّرو
      </h2>
      <p class="section-sub-lg text-center">
        رحلات حقيقية لشباب وفتيات بدأوا حياتهم الزوجية بميسّر من الله ومنّا
      </p>

      <div class="testimonial-grid">
        <div
          v-for="(t, i) in testimonials"
          :key="t.name"
          class="testimonial-card"


        >
          <PhQuotes :size="48" weight="fill" class="testimonial-card__quote-mark" />
          <p class="testimonial-card__text">{{ t.quote }}</p>
          <div class="testimonial-card__author">
            <div class="testimonial-card__author-avatar">{{ t.initial }}</div>
            <div class="testimonial-card__author-info">
              <p class="name">{{ t.name }}</p>
              <span class="role">{{ t.role }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Live Numbers -->
      <div class="live-numbers">
        <div class="live-stat" v-for="s in liveStats" :key="s.label">
          <div class="live-stat__number">{{ s.value }}</div>
          <div class="live-stat__label">{{ s.label }}</div>
        </div>
      </div>
    </div>
  </section>

  <!-- ============================================ -->
  <!-- 8. FAQ — Friction Killer                    -->
  <!-- ============================================ -->
  <section class="faq-section">
    <div class="container">
      <div class="text-center mb-3">
        <span class="section-eyebrow">أسئلة شائعة</span>
      </div>

      <h2 class="section-title-lg text-center">
        كل ما يدور في بالك — أجبنا عنه
      </h2>

      <div class="faq-list">
        <div
          v-for="(item, i) in faqs"
          :key="i"
          class="faq-item"
          :class="{ 'is-open': openFaq === i }"


        >
          <button class="faq-item__question" @click="toggleFaq(i)">
            <span>{{ item.q }}</span>
            <PhCaretDown :size="20" weight="bold" class="faq-item__icon" />
          </button>
          <div class="faq-item__answer">
            <div class="faq-item__answer-inner">{{ item.a }}</div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ============================================ -->
  <!-- 9. FINAL CTA                                -->
  <!-- ============================================ -->
  <section class="final-cta">
    <div class="container">
      <div class="final-cta__inner">
        <svg class="final-cta__star" viewBox="0 0 32 32" aria-hidden="true" focusable="false">
          <path
            fill="currentColor"
            d="M 16 2 Q 17.4 13.6 30 16 Q 17.4 18.4 16 30 Q 14.6 18.4 2 16 Q 14.6 13.6 16 2 Z"
          />
        </svg>

        <h2 class="final-cta__title">
          خطوة واحدة تفصلك عن<br>
          حياة جديدة ميسّرة
        </h2>
        <p class="final-cta__subtitle">
          سجّل مجاناً الآن — ابدأ بالدورة التأهيلية،
          واكتشف كيف يمكن لـ يسّرو أن يفتح لك باباً جديداً.
        </p>

        <router-link to="/register" class="final-cta__btn">
          أنشئ حسابك مجاناً
          <PhArrowLeft :size="18" weight="bold" />
        </router-link>

        <div class="final-cta__reassure">
          <span><PhCheckCircle :size="14" weight="fill" /> بدون رسوم</span>
          <span><PhCheckCircle :size="14" weight="fill" /> بدون التزام</span>
          <span><PhCheckCircle :size="14" weight="fill" /> 30 ثانية فقط</span>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { useSeo } from '@/composables/useSeo'
import {
  PhCaretDown, PhCheckCircle,
  PhBookOpen, PhHandshake, PhHandCoins, PhHeart, PhHeartbeat,
  PhClock, PhMapPin, PhSliders, PhArrowLeft, PhArrowDown, PhArrowFatUp,
  PhScales, PhUserCheck, PhEyeSlash, PhGift,
  PhQuotes, PhCurrencyCircleDollar, PhHourglassMedium, PhUsersFour, PhWarningCircle,
} from '@phosphor-icons/vue'

useSeo({
  description: 'يسّرو تُيسّر زواجك من الألف للياء: دورة تأهيلية، توفيق شرعي، صندوق تعاوني بدون فوائد، أعراس جماعية بتوفير 60%.',
  path: '/',
})

// === DATA ===

const problemStats = [
  { value: '40%', label: 'من الشباب لا يستطيعون الزواج', icon: PhUsersFour },
  { value: '×10', label: 'تكلفة الزواج = 10 سنوات دخل', icon: PhCurrencyCircleDollar },
  { value: '+32', label: 'متوسط سن الزواج في ارتفاع', icon: PhHourglassMedium },
  { value: '85%', label: 'يعتبرون التكلفة العائق الأول', icon: PhWarningCircle },
]

const pillars = [
  {
    title: 'التأهيل', route: '/courses',
    desc: 'دورة شرعية ونفسية ومالية وعملية. شهادة معتمدة بعد الإتمام.',
    icon: PhBookOpen, color: '#0d7377', bg: 'rgba(13,115,119,0.1)',
  },
  {
    title: 'التوفيق', route: '/recommender',
    desc: 'توفيق عبر معرّفين موثوقين. صفر تواصل مباشر بين الجنسين.',
    icon: PhHandshake, color: '#1565c0', bg: 'rgba(21,101,192,0.1)',
  },
  {
    title: 'الصندوق', route: '/fund',
    desc: 'صندوق تعاوني بدون فوائد. كل عضو يتسلّم المبلغ كاملاً.',
    icon: PhHandCoins, color: '#b8860b', bg: 'rgba(184,134,11,0.1)',
  },
  {
    title: 'الأعراس', route: '/weddings',
    desc: 'أعراس جماعية مهيبة توفّر 60-70% من التكاليف.',
    icon: PhHeart, color: '#c0392b', bg: 'rgba(192,57,43,0.1)',
  },
  {
    title: 'الرعاية', route: '/counseling',
    desc: 'استشارات أسرية ومجتمع دعم لما بعد الزواج.',
    icon: PhHeartbeat, color: '#1b7a4a', bg: 'rgba(27,122,74,0.1)',
  },
]

const steps = [
  {
    title: 'تأهّل', desc: 'أكمل الدورة التأهيلية واحصل على شهادة الجاهزية.',
    duration: '10-15 ساعة', color: '#0d7377', bg: 'rgba(13,115,119,0.1)',
  },
  {
    title: 'اتعرّف', desc: 'معرّفك الموثوق يقدّم لك المرشحات المناسبة.',
    duration: '1-3 أشهر', color: '#1565c0', bg: 'rgba(21,101,192,0.1)',
  },
  {
    title: 'موّل', desc: 'انضم لحلقة صندوق أو سجّل في عرس جماعي.',
    duration: 'حسب الحلقة', color: '#b8860b', bg: 'rgba(184,134,11,0.1)',
  },
  {
    title: 'تزوّج', desc: 'احتفل بزواجك بتكلفة معقولة وبرعاية كاملة.',
    duration: 'يوم العمر', color: '#c0392b', bg: 'rgba(192,57,43,0.1)',
  },
]

const trustPillars = [
  {
    title: 'معتمد شرعياً', icon: PhScales, color: '#1b7a4a',
    bg1: 'rgba(27,122,74,0.12)', bg2: 'rgba(27,122,74,0.05)',
    desc: 'الصندوق التعاوني خالٍ من الربا. مُراجَع من هيئة شرعية معتمدة.',
  },
  {
    title: 'أموالك محمية', icon: PhUserCheck, color: '#0d7377',
    bg1: 'rgba(13,115,119,0.12)', bg2: 'rgba(13,115,119,0.05)',
    desc: 'صندوق ضمان + ضامن إلزامي + عقد رقمي ملزم لكل عضو.',
  },
  {
    title: 'خصوصيتك مقدسة', icon: PhEyeSlash, color: '#1565c0',
    bg1: 'rgba(21,101,192,0.12)', bg2: 'rgba(21,101,192,0.05)',
    desc: 'لا صور شخصية. لا تواصل مباشر. بياناتك محمية بتشفير AES-256.',
  },
  {
    title: 'مجاني فعلاً', icon: PhGift, color: '#b8860b',
    bg1: 'rgba(184,134,11,0.12)', bg2: 'rgba(184,134,11,0.05)',
    desc: 'التسجيل والدورة وكل الخدمات الأساسية بدون أي رسوم خفية.',
  },
]

const testimonials = [
  {
    name: 'أحمد م.', role: 'مهندس — القاهرة', initial: 'أ',
    quote: 'كنت يائساً من الزواج بسبب تكاليف 250 ألف. عبر يسّرو، انضممت لحلقة صندوق وتسجّلت في عرس جماعي — وتزوّجت بأقل من 80 ألف. والحمد لله.',
  },
  {
    name: 'فاطمة ع.', role: 'معلّمة — الإسكندرية', initial: 'ف',
    quote: 'الشيء اللي طمّنّي في يسّرو إن الموقع محترم. ولي أمري كان جزء من كل خطوة، ومحدش تواصل معايا مباشرة. تجربة محترمة جداً.',
  },
  {
    name: 'الشيخ عبدالله', role: 'إمام مسجد — الجيزة', initial: 'ع',
    quote: 'كمعرّف، يسّرو سهّلت عليّ خدمة شبابي بطريقة منظّمة. الأداة ساعدتني أوصل بين 12 شخص للزواج خلال شهور.',
  },
]

const liveStats = ref([
  { value: '—', label: 'مستخدم مسجّل' },
  { value: '—', label: 'شهادة تأهيلية' },
  { value: '—', label: 'حلقة صندوق نشطة' },
  { value: '—', label: 'زواج تمّ تيسيره' },
])

const faqs = [
  {
    q: 'هل الصندوق التعاوني فيه ربا؟',
    a: 'لا. الصندوق هو قرض حسن دوّار — كل عضو يدفع نفس المبلغ ويستلم نفس المبلغ. لا فوائد ولا زيادات. تم اعتماده من هيئة شرعية متخصصة، والفتوى متاحة للاطلاع.',
  },
  {
    q: 'إزاي تضمن إن أعضاء الحلقة هيدفعوا؟',
    a: 'كل عضو يقدّم: ضامن مع تأكيد OTP، عقد رقمي ملزم، ورسوم ضمان تُدفع لصندوق احتياطي يغطي أي تخلّف. عندنا 3 مراحل تصعيد قبل الاستبعاد النهائي.',
  },
  {
    q: 'هل بناتي / أخواتي آمنات على المنصة؟',
    a: 'صفر تواصل مباشر بين الجنسين. كل التواصل يمر عبر المعرّف وولي الأمر. لا صور شخصية تُعرض. لا تصفّح حر للملفات. خصوصية كاملة من اللحظة الأولى.',
  },
  {
    q: 'هل في رسوم مخفية؟',
    a: 'التسجيل مجاني تماماً. الدورة التأهيلية مجانية. الانضمام لحلقات الصندوق مجاني. الرسوم الوحيدة هي 2-3% رسوم خدمة على الصندوق فقط، تُغطّي تشغيل المنصة، وتُعلَن قبل الانضمام.',
  },
  {
    q: 'كم تستغرق الدورة التأهيلية؟',
    a: 'الدورة من 10 إلى 15 ساعة، مقسّمة على 4 مسارات: شرعي، نفسي، مالي، عملي. تقدر تكملها على راحتك خلال أسبوع أو شهر. الشهادة شرط لباقي الخدمات.',
  },
  {
    q: 'لو سجّلت في عرس جماعي والظروف اتغيّرت؟',
    a: 'سياسة استرداد واضحة: قبل 30 يوم → استرداد 100%، قبل 15 يوم → 50%، بعد ذلك → لا استرداد لتغطية تكاليف التحضير. كل التفاصيل في عقد التسجيل.',
  },
]

// === STATE ===

const cities = ref([])
const selectedCity = ref('')
const selectedLevel = ref('medium')
const calcResult = ref(null)
const openFaq = ref(0)

// === FUNCTIONS ===

// One numeral system across the page. The mono face carries no
// Arabic-Indic digits, so those break both the tabular alignment and the
// instrument reading the design depends on.
function formatNumber(num) {
  return new Intl.NumberFormat('en-US').format(num)
}

function scrollTo(id) {
  const el = document.getElementById(id)
  if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' })
}

function toggleFaq(i) {
  openFaq.value = openFaq.value === i ? -1 : i
}

async function calculateCost() {
  if (!selectedCity.value || !selectedLevel.value) return
  try {
    const { data } = await axios.post('/api/calculator/calculate', {
      city_id: selectedCity.value.id,
      level: selectedLevel.value,
    })
    calcResult.value = data
  } catch { /* silent */ }
}

// === Arch gauge counters ===
// The numbers settle as the fill rises, so the shape and the figure agree.
const savingCount = ref(0)
const paidCount = ref(0)

const fmtNum = (n) => Math.round(n).toLocaleString('en-US')

function countUp(target, to, duration, delay) {
  const start = performance.now() + delay
  const step = (now) => {
    const t = Math.min(Math.max((now - start) / duration, 0), 1)
    // easeOutQuart — fast settle, no bounce
    target.value = to * (1 - Math.pow(1 - t, 4))
    if (t < 1) requestAnimationFrame(step)
  }
  requestAnimationFrame(step)
}

onMounted(() => {
  const still = window.matchMedia('(prefers-reduced-motion: reduce)').matches
  if (still) {
    savingCount.value = 138000
    paidCount.value = 62000
    return
  }
  countUp(paidCount, 62000, 1200, 950)
  countUp(savingCount, 138000, 1400, 1150)
})

onMounted(async () => {
  try {
    const [citiesRes, statsRes] = await Promise.all([
      axios.get('/api/calculator/cities'),
      axios.get('/api/stats'),
    ])
    cities.value = citiesRes.data
    liveStats.value = [
      { value: statsRes.data.users.toLocaleString('en-US'), label: 'مستخدم مسجّل' },
      { value: statsRes.data.certified.toLocaleString('en-US'), label: 'شهادة تأهيلية' },
      { value: statsRes.data.circles.toLocaleString('en-US'), label: 'حلقة صندوق نشطة' },
      { value: statsRes.data.marriages.toLocaleString('en-US'), label: 'زواج تمّ تيسيره' },
    ]
  } catch { /* silent */ }
})
</script>
