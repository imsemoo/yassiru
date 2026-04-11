<template>
  <!-- ============================================ -->
  <!-- 1. HERO — Single Focus, Strong CTA          -->
  <!-- ============================================ -->
  <section class="home-hero">
    <div class="container home-hero__content">
      <div class="row align-items-center g-5">
        <!-- Left: Headline + CTA -->
        <div class="col-lg-7">
          <div class="home-hero__eyebrow anim">
            <span class="pulse-dot"></span>
            أول منظومة تيسير زواج في العالم الإسلامي
          </div>

          <h1 class="home-hero__title anim anim-delay-1">
            زواجك بـ<span class="highlight">نصف التكلفة</span>.<br>
            بدون فوائد. بدون ديون.
          </h1>

          <p class="home-hero__subtitle anim anim-delay-2">
            يسّرو تجمع الدورة التأهيلية، التوفيق عبر معرّفين موثوقين،
            الصندوق التعاوني، والأعراس الجماعية —
            في رحلة واحدة تنقلك من القرار للزواج.
          </p>

          <div class="home-hero__cta-group anim anim-delay-3">
            <a href="#calculator" class="home-hero__cta-primary" @click.prevent="scrollTo('calculator')">
              <PhCalculator :size="22" weight="bold" />
              احسب توفيرك في 30 ثانية
            </a>
            <a href="#how" class="home-hero__cta-secondary" @click.prevent="scrollTo('how')">
              شوف إزاي يشتغل
              <PhCaretDown :size="16" weight="bold" />
            </a>
          </div>

          <div class="home-hero__trust-bar anim anim-delay-4">
            <div class="home-hero__trust-bar__item">
              <PhShieldCheck :size="18" weight="fill" class="text-success" />
              معتمد شرعياً
            </div>
            <div class="home-hero__trust-bar__item">
              <PhLockKey :size="18" weight="fill" class="text-primary" />
              خصوصية كاملة
            </div>
            <div class="home-hero__trust-bar__item">
              <PhCheckCircle :size="18" weight="fill" style="color: #b8860b" />
              مجاني 100%
            </div>
          </div>
        </div>

        <!-- Right: Visual Comparison -->
        <div class="col-lg-5">
          <div class="home-hero__visual anim anim-delay-3">
            <div class="compare-card">
              <div class="compare-card__badge">
                <PhSparkle :size="14" weight="fill" class="ms-1" />
                مثال: زواج في القاهرة
              </div>

              <div class="compare-card__header">
                <h6>المقارنة بالأرقام</h6>
              </div>

              <div class="compare-card__row compare-card__row--bad">
                <div class="compare-card__row__label">
                  <PhTrendUp :size="20" weight="fill" />
                  بدون يسّرو
                </div>
                <div class="compare-card__row__amount compare-card__row__amount--bad">
                  200,000
                </div>
              </div>

              <div class="compare-card__row compare-card__row--good">
                <div class="compare-card__row__label">
                  <PhTrendDown :size="20" weight="fill" />
                  مع يسّرو
                </div>
                <div class="compare-card__row__amount compare-card__row__amount--good">
                  62,000
                </div>
              </div>

              <div class="compare-card__savings">
                <div class="text-muted small mb-1">توفيرك</div>
                <div class="savings-amount">138,000 ج.م</div>
                <div class="text-muted small mt-1">~ 70% من التكلفة</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ============================================ -->
  <!-- 2. PROBLEM — Emotional Hook                 -->
  <!-- ============================================ -->
  <section class="problem-section">
    <div class="container">
      <div class="text-center mb-3">
        <span class="section-eyebrow" style="color: var(--bs-danger); background: rgba(192,57,43,0.08);">
          المشكلة الحقيقية
        </span>
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
            <component :is="stat.icon" :size="28" weight="duotone" />
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
          :style="{
            '--pillar-color': pillar.color,
            '--pillar-bg': pillar.bg,
          }"


        >
          <div class="ecosystem-pillar__number">{{ i + 1 }}</div>
          <div class="ecosystem-pillar__icon">
            <component :is="pillar.icon" :size="32" weight="duotone" :style="{ color: pillar.color }" />
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
          :style="{
            '--step-color': step.color,
            '--step-bg': step.bg,
          }"


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
            <router-link to="/register" class="home-hero__cta-primary">
              <PhRocketLaunch :size="20" weight="bold" />
              ابدأ رحلتك مجاناً
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
          :style="{
            '--trust-color': t.color,
            '--trust-bg-1': t.bg1,
            '--trust-bg-2': t.bg2,
          }"


        >
          <div class="trust-card__icon">
            <component :is="t.icon" :size="28" weight="duotone" />
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
        <h2 class="final-cta__title">
          خطوة واحدة تفصلك عن<br>
          حياة جديدة ميسّرة
        </h2>
        <p class="final-cta__subtitle">
          سجّل مجاناً الآن — ابدأ بالدورة التأهيلية،
          واكتشف كيف يمكن لـ يسّرو أن يفتح لك باباً جديداً.
        </p>

        <router-link to="/register" class="final-cta__btn">
          <PhRocketLaunch :size="22" weight="bold" />
          أنشئ حسابك مجاناً
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
  PhCalculator, PhCaretDown, PhShieldCheck, PhLockKey, PhCheckCircle,
  PhSparkle, PhTrendUp, PhTrendDown, PhRocketLaunch,
  PhBookOpen, PhHandshake, PhHandCoins, PhHeart, PhHeartbeat,
  PhClock, PhMapPin, PhSliders, PhArrowLeft, PhArrowFatUp,
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

function formatNumber(num) {
  return new Intl.NumberFormat('ar-EG').format(num)
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

onMounted(async () => {
  try {
    const [citiesRes, statsRes] = await Promise.all([
      axios.get('/api/calculator/cities'),
      axios.get('/api/stats'),
    ])
    cities.value = citiesRes.data
    liveStats.value = [
      { value: statsRes.data.users.toLocaleString('ar-EG'), label: 'مستخدم مسجّل' },
      { value: statsRes.data.certified.toLocaleString('ar-EG'), label: 'شهادة تأهيلية' },
      { value: statsRes.data.circles.toLocaleString('ar-EG'), label: 'حلقة صندوق نشطة' },
      { value: statsRes.data.marriages.toLocaleString('ar-EG'), label: 'زواج تمّ تيسيره' },
    ]
  } catch { /* silent */ }
})
</script>
