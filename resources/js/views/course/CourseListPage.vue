<template>
  <!-- ============================================ -->
  <!-- 1. HERO — Why You Need This Course           -->
  <!-- ============================================ -->
  <section class="courses-hero">
    <div class="container courses-hero__content">
      <div class="row align-items-center g-5">
        <!-- Left -->
        <div class="col-lg-7">
          <div class="courses-hero__eyebrow anim">
            <PhBookOpen :size="16" weight="fill" />
            الدورة التأهيلية الإلزامية
          </div>

          <h1 class="courses-hero__title anim anim-delay-1">
            استثمر <span class="accent">15 ساعة</span><br>
            في تأسيس بيت يدوم
          </h1>

          <p class="courses-hero__subtitle anim anim-delay-2">
            دورة مكثّفة تجمع 4 مسارات أساسية للزواج الناجح:
            الشرعي، النفسي، المالي، والعملي. ادرسها على راحتك واحصل على شهادة معتمدة
            تفتح لك كل خدمات يسّرو.
          </p>

          <div class="courses-hero__cta-group anim anim-delay-3">
            <a href="#tracks" class="courses-hero__cta-primary" @click.prevent="scrollTo('tracks')">
              <PhPlayCircle :size="22" weight="bold" />
              ابدأ المسار الأول الآن
            </a>
            <a href="#curriculum" class="fund-hero__cta-secondary" @click.prevent="scrollTo('curriculum')">
              <PhListBullets :size="20" weight="bold" />
              شوف المحتوى
            </a>
          </div>
        </div>

        <!-- Right: Value Ledger -->
        <div class="col-lg-5">
          <div class="value-ledger anim anim-delay-3">
            <h6 class="value-ledger__title">ماذا ستحصل عليه؟</h6>

            <div class="value-ledger__row">
              <div class="value-ledger__row__icon">
                <PhBookBookmark :size="22" weight="duotone" />
              </div>
              <div class="value-ledger__row__text">4 مسارات تعليمية متخصصة</div>
              <div class="value-ledger__row__value">14</div>
            </div>

            <div class="value-ledger__row">
              <div class="value-ledger__row__icon">
                <PhClock :size="22" weight="duotone" />
              </div>
              <div class="value-ledger__row__text">إجمالي ساعات التعلم</div>
              <div class="value-ledger__row__value">15 س</div>
            </div>

            <div class="value-ledger__row">
              <div class="value-ledger__row__icon">
                <PhExam :size="22" weight="duotone" />
              </div>
              <div class="value-ledger__row__text">اختبارات تقييم</div>
              <div class="value-ledger__row__value">4</div>
            </div>

            <div class="value-ledger__row">
              <div class="value-ledger__row__icon">
                <PhCertificate :size="22" weight="duotone" />
              </div>
              <div class="value-ledger__row__text">شهادة معتمدة</div>
              <div class="value-ledger__row__value">1</div>
            </div>

            <div class="value-ledger__total">
              <div class="label">المكافأة الإجمالية</div>
              <div class="reward">فتح كل خدمات يسّرو</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ============================================ -->
  <!-- 2. PROGRESS DASHBOARD (logged-in only)       -->
  <!-- ============================================ -->
  <section v-if="isAuthenticated" class="progress-section">
    <div class="container">
      <div class="progress-dashboard">
        <!-- Progress Ring -->
        <div class="progress-ring">
          <svg viewBox="0 0 220 220">
            <defs>
              <linearGradient id="progressGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" stop-color="#0d7377" />
                <stop offset="100%" stop-color="#b8860b" />
              </linearGradient>
            </defs>
            <circle class="progress-ring__bg" cx="110" cy="110" r="95" />
            <circle
              class="progress-ring__fill"
              cx="110" cy="110" r="95"
              :stroke-dasharray="circumference"
              :stroke-dashoffset="dashOffset"
            />
          </svg>
          <div class="progress-ring__center">
            <div class="percent">{{ overallProgress }}%</div>
            <div class="label">من الدورة</div>
          </div>
        </div>

        <!-- Info -->
        <div class="progress-info">
          <div class="progress-info__greeting">مرحباً بعودتك</div>
          <h2 class="progress-info__title">
            {{ overallProgress >= 100 ? 'أتممت الدورة بنجاح!' : 'استمر في رحلتك' }}
          </h2>

          <div class="progress-info__stats">
            <div class="progress-info__stat">
              <div class="label">الدروس المكتملة</div>
              <div class="value">{{ completedCount }} / {{ totalLessons }}</div>
            </div>
            <div class="progress-info__stat">
              <div class="label">المسارات المجتازة</div>
              <div class="value">{{ passedCount }} / 4</div>
            </div>
            <div class="progress-info__stat">
              <div class="label">الشهادة</div>
              <div class="value" :class="hasCertificate ? 'text-success' : 'text-muted'">
                {{ hasCertificate ? 'حاصل' : 'لم يصدر' }}
              </div>
            </div>
          </div>

          <router-link
            v-if="hasCertificate"
            to="/certificate"
            class="progress-info__action"
          >
            <PhCertificate :size="20" weight="bold" />
            عرض شهادتك
          </router-link>
          <router-link
            v-else-if="overallProgress >= 100"
            to="/certificate"
            class="progress-info__action"
          >
            <PhTrophy :size="20" weight="bold" />
            احصل على الشهادة الآن
          </router-link>
          <a v-else href="#tracks" class="progress-info__action" @click.prevent="scrollTo('tracks')">
            <PhPlayCircle :size="20" weight="bold" />
            استكمل التعلم
          </a>
        </div>
      </div>
    </div>
  </section>

  <!-- ============================================ -->
  <!-- 3. THE 4 TRACKS — Journey                    -->
  <!-- ============================================ -->
  <section class="tracks-section" id="tracks">
    <div class="container">
      <div class="text-center mb-3">
        <span class="section-eyebrow">المسارات الأربعة</span>
      </div>

      <h2 class="section-title-lg text-center">
        رحلة متكاملة بـ 4 مسارات
      </h2>
      <p class="section-sub-lg text-center">
        كل مسار مصمّم بعناية لتغطية جانب أساسي من نجاح الزواج
      </p>

      <div class="tracks-journey">
        <component
          :is="isAuthenticated ? 'router-link' : 'div'"
          v-for="(course, i) in displayCourses"
          :key="course.id"
          :to="isAuthenticated ? `/courses/${course.id}` : undefined"
          class="track-card"
          :class="{ 'is-passed': course.quiz_passed }"
          :style="{
            '--track-color': trackConfig(course.track).color,
            '--track-bg': trackConfig(course.track).bg,
          }"


        >
          <div class="track-card__order">{{ i + 1 }}</div>
          <div class="track-card__icon">
            <component :is="trackConfig(course.track).icon" :size="36" weight="duotone" />
          </div>
          <h3 class="track-card__title">{{ course.title }}</h3>
          <p class="track-card__desc">{{ course.description || trackConfig(course.track).defaultDesc }}</p>

          <div class="track-card__meta">
            <span><PhBookOpen :size="13" /> {{ course.lessons_count }} درس</span>
            <span><PhClock :size="13" /> {{ course.duration_hours }} س</span>
          </div>

          <div v-if="isAuthenticated" class="track-card__status">
            <span v-if="course.quiz_passed" class="badge-passed">
              <PhCheckCircle :size="14" weight="fill" />
              تم الاجتياز
            </span>
            <span v-else-if="course.progress > 0" class="badge-progress">
              {{ course.progress }}% مكتمل
            </span>
            <div v-if="course.progress > 0 && !course.quiz_passed" class="progress-mini">
              <div class="fill" :style="{ width: course.progress + '%' }"></div>
            </div>
          </div>
        </component>
      </div>
    </div>
  </section>

  <!-- ============================================ -->
  <!-- 4. WHAT YOU'LL LEARN                         -->
  <!-- ============================================ -->
  <section class="curriculum-section" id="curriculum">
    <div class="container">
      <div class="text-center mb-3">
        <span class="section-eyebrow">المحتوى التعليمي</span>
      </div>

      <h2 class="section-title-lg text-center">
        ماذا ستتعلم في الدورة؟
      </h2>
      <p class="section-sub-lg text-center">
        موضوعات مختارة بعناية تغطي كل ما تحتاج معرفته قبل الزواج وبعده
      </p>

      <div class="curriculum-grid">
        <div
          v-for="(item, i) in curriculum"
          :key="item.title"
          class="curriculum-card"
          :style="{
            '--curr-color': item.color,
            '--curr-bg': item.bg,
          }"


        >
          <div class="curriculum-card__icon">
            <component :is="item.icon" :size="26" weight="duotone" />
          </div>
          <div>
            <h4 class="curriculum-card__title">{{ item.title }}</h4>
            <p class="curriculum-card__desc">{{ item.desc }}</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ============================================ -->
  <!-- 5. QUIZ SYSTEM                               -->
  <!-- ============================================ -->
  <section class="quiz-section">
    <div class="container">
      <div class="text-center mb-3">
        <span class="section-eyebrow">نظام الاختبارات</span>
      </div>

      <h2 class="section-title-lg text-center">
        اختبار عادل وشفّاف
      </h2>
      <p class="section-sub-lg text-center">
        نظام بسيط: أكمل دروس المسار، ثم اجتز اختباراً مكوناً من 10 أسئلة
      </p>

      <div class="quiz-rules">
        <div
          v-for="(rule, i) in quizRules"
          :key="rule.label"
          class="quiz-rule"
          :style="{
            '--rule-color': rule.color,
            '--rule-bg-1': rule.bg1,
            '--rule-bg-2': rule.bg2,
          }"


        >
          <div class="quiz-rule__icon">
            <component :is="rule.icon" :size="26" weight="duotone" />
          </div>
          <div class="quiz-rule__number">{{ rule.value }}</div>
          <div class="quiz-rule__label">{{ rule.label }}</div>
        </div>
      </div>
    </div>
  </section>

  <!-- ============================================ -->
  <!-- 6. CERTIFICATE REWARD                        -->
  <!-- ============================================ -->
  <section class="cert-section">
    <div class="container">
      <div class="cert-section__inner">
        <!-- Content -->
        <div class="cert-content">
          <span class="cert-content__eyebrow">المكافأة النهائية</span>
          <h2 class="cert-content__title">
            شهادة الجاهزية الزواجية
          </h2>
          <p class="cert-content__desc">
            بعد إكمال المسارات الأربعة واجتياز اختباراتها،
            تحصل على شهادة معتمدة من يسّرو تثبت جاهزيتك للزواج —
            وتفتح لك كل خدمات المنصة.
          </p>

          <ul class="cert-content__benefits">
            <li>
              <PhCheckCircle :size="20" weight="fill" class="check" />
              فتح خدمة التوفيق عبر المعرّفين
            </li>
            <li>
              <PhCheckCircle :size="20" weight="fill" class="check" />
              الانضمام لحلقات صندوق التيسير
            </li>
            <li>
              <PhCheckCircle :size="20" weight="fill" class="check" />
              التسجيل في الأعراس الجماعية
            </li>
            <li>
              <PhCheckCircle :size="20" weight="fill" class="check" />
              رقم تحقّق فريد قابل للمشاركة
            </li>
          </ul>

          <router-link to="/register" class="courses-hero__cta-primary">
            <PhRocketLaunch :size="20" weight="bold" />
            ابدأ رحلتك الآن
          </router-link>
        </div>

        <!-- Certificate Mockup -->
        <div class="cert-mockup">
          <div class="cert-mockup__header">YASSIRU CERTIFICATE</div>
          <div class="cert-mockup__title">شهادة الجاهزية الزواجية</div>
          <div class="cert-mockup__divider"></div>
          <div class="cert-mockup__body">تشهد منصة يسّرو بأن</div>
          <div class="cert-mockup__name">{{ userName }}</div>
          <div class="cert-mockup__body">قد أتم بنجاح الدورة التأهيلية للزواج بمساراتها الأربعة</div>
          <div class="cert-mockup__seal">
            <PhSealCheck :size="16" weight="fill" />
            موثّقة ومعتمدة
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ============================================ -->
  <!-- 7. TESTIMONIALS                              -->
  <!-- ============================================ -->
  <section class="courses-stories-section">
    <div class="container">
      <div class="text-center mb-3">
        <span class="section-eyebrow">آراء الخريجين</span>
      </div>

      <h2 class="section-title-lg text-center">
        أراء من أتموا الدورة
      </h2>

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
    </div>
  </section>

  <!-- ============================================ -->
  <!-- 8. FINAL CTA                                 -->
  <!-- ============================================ -->
  <section class="courses-final-cta">
    <div class="container">
      <div class="courses-final-cta__inner">
        <h2 class="courses-final-cta__title">
          خطوة واحدة فقط<br>
          تفصلك عن الجاهزية الكاملة
        </h2>
        <p class="courses-final-cta__subtitle">
          15 ساعة من التعلم المنظّم تكفي لتؤسّس بيتاً يدوم. ابدأ الآن — كل دقيقة استثمار في مستقبلك.
        </p>
        <router-link
          :to="isAuthenticated ? (firstUnpassed ? `/courses/${firstUnpassed.id}` : '/courses') : '/register'"
          class="courses-final-cta__btn"
        >
          <PhPlayCircle :size="22" weight="bold" />
          {{ isAuthenticated ? 'استكمل التعلم' : 'سجّل وابدأ مجاناً' }}
        </router-link>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useApi } from '@/composables/useApi'
import { useAuthStore } from '@/stores/auth'
import { useSeo } from '@/composables/useSeo'
import {
  PhBookOpen, PhPlayCircle, PhListBullets, PhClock, PhExam,
  PhCertificate, PhBookBookmark, PhBrain, PhCurrencyCircleDollar, PhWrench,
  PhCheckCircle, PhTrophy, PhRocketLaunch, PhSealCheck, PhQuotes,
  PhMosque, PhHandshake, PhHeart, PhUsers, PhWarning, PhTarget, PhArrowsClockwise,
} from '@phosphor-icons/vue'

useSeo({
  title: 'الدورة التأهيلية',
  description: 'أكمل الدورة التأهيلية بمساراتها الأربعة: الشرعي، النفسي، المالي، والعملي. احصل على شهادة الجاهزية الزواجية.',
  path: '/courses',
})

const { get } = useApi()
const auth = useAuthStore()
const isAuthenticated = computed(() => auth.isAuthenticated)
const userName = computed(() => auth.user?.name || '............')

const courses = ref([])
const overallProgress = ref(0)
const hasCertificate = ref(false)

const completedCount = computed(() => courses.value.reduce((sum, c) => sum + (c.completed_lessons || 0), 0))
const totalLessons = computed(() => courses.value.reduce((sum, c) => sum + (c.lessons_count || 0), 0))
const passedCount = computed(() => courses.value.filter(c => c.quiz_passed).length)
const firstUnpassed = computed(() => courses.value.find(c => !c.quiz_passed))

// Progress ring math
const radius = 95
const circumference = 2 * Math.PI * radius
const dashOffset = computed(() => circumference - (overallProgress.value / 100) * circumference)

// Display courses (use real data if available, else fallback)
const fallbackTracks = [
  { id: 'shariah', track: 'shariah', title: 'المسار الشرعي', description: 'أحكام الزواج والمسؤوليات والحقوق في الإسلام', lessons_count: 4, duration_hours: 4, progress: 0 },
  { id: 'psychology', track: 'psychology', title: 'المسار النفسي', description: 'التواصل، حل النزاعات، وفهم الشريك', lessons_count: 4, duration_hours: 4, progress: 0 },
  { id: 'financial', track: 'financial', title: 'المسار المالي', description: 'التخطيط المالي وإدارة ميزانية الأسرة', lessons_count: 3, duration_hours: 3, progress: 0 },
  { id: 'practical', track: 'practical', title: 'المسار العملي', description: 'مهارات إدارة البيت والحياة اليومية', lessons_count: 3, duration_hours: 4, progress: 0 },
]

const displayCourses = computed(() => courses.value.length > 0 ? courses.value : fallbackTracks)

function trackConfig(track) {
  const config = {
    shariah: {
      icon: PhBookBookmark, bg: 'rgba(27,122,74,0.12)', color: '#1b7a4a',
      defaultDesc: 'أحكام الزواج والمسؤوليات والحقوق',
    },
    psychology: {
      icon: PhBrain, bg: 'rgba(21,101,192,0.12)', color: '#1565c0',
      defaultDesc: 'التواصل وحل النزاعات وفهم الشريك',
    },
    financial: {
      icon: PhCurrencyCircleDollar, bg: 'rgba(184,134,11,0.12)', color: '#b8860b',
      defaultDesc: 'التخطيط المالي وإدارة ميزانية الأسرة',
    },
    practical: {
      icon: PhWrench, bg: 'rgba(107,114,128,0.12)', color: '#6b7280',
      defaultDesc: 'مهارات إدارة البيت والحياة اليومية',
    },
  }
  return config[track] || config.shariah
}

// Curriculum content
const curriculum = [
  {
    icon: PhMosque, title: 'الأحكام الشرعية',
    desc: 'الحقوق والواجبات، صيغ العقد، أركان النكاح، والمسؤوليات المتبادلة.',
    color: '#1b7a4a', bg: 'rgba(27,122,74,0.1)',
  },
  {
    icon: PhHandshake, title: 'مهارات التواصل',
    desc: 'كيف تتحدث مع شريكك، تحل الخلافات، وتبني قنوات حوار صحية.',
    color: '#1565c0', bg: 'rgba(21,101,192,0.1)',
  },
  {
    icon: PhCurrencyCircleDollar, title: 'الإدارة المالية',
    desc: 'بناء ميزانية الأسرة، التخطيط للمستقبل، وإدارة المدخرات والديون.',
    color: '#b8860b', bg: 'rgba(184,134,11,0.1)',
  },
  {
    icon: PhHeart, title: 'الذكاء العاطفي',
    desc: 'فهم احتياجات الشريك العاطفية، التعامل مع الضغوط، وبناء الحب.',
    color: '#c0392b', bg: 'rgba(192,57,43,0.1)',
  },
  {
    icon: PhUsers, title: 'العلاقات الأسرية',
    desc: 'إدارة العلاقة مع الأهل، حدود التدخل، ومسؤولية تربية الأطفال.',
    color: '#0d7377', bg: 'rgba(13,115,119,0.1)',
  },
  {
    icon: PhWrench, title: 'مهارات عملية',
    desc: 'إدارة البيت، التخطيط للوقت، والتعامل مع المتطلبات اليومية.',
    color: '#6b7280', bg: 'rgba(107,114,128,0.1)',
  },
]

// Quiz rules
const quizRules = [
  { icon: PhListBullets, value: '10', label: 'أسئلة لكل اختبار', color: '#0d7377', bg1: 'rgba(13,115,119,0.15)', bg2: 'rgba(13,115,119,0.05)' },
  { icon: PhTarget, value: '7/10', label: 'الحد الأدنى للنجاح', color: '#1b7a4a', bg1: 'rgba(27,122,74,0.15)', bg2: 'rgba(27,122,74,0.05)' },
  { icon: PhArrowsClockwise, value: '3', label: 'محاولات متاحة', color: '#b8860b', bg1: 'rgba(184,134,11,0.15)', bg2: 'rgba(184,134,11,0.05)' },
  { icon: PhWarning, value: 'عشوائي', label: 'الأسئلة كل مرة', color: '#c0392b', bg1: 'rgba(192,57,43,0.15)', bg2: 'rgba(192,57,43,0.05)' },
]

// Testimonials
const testimonials = [
  {
    name: 'يوسف أ.', role: 'مهندس — القاهرة', initial: 'ي',
    quote: 'الدورة كانت أكتر بكتير مما توقعت. المسار النفسي بالذات غيّر طريقة تفكيري في كل حاجة. حسّيت إني جاهز فعلاً للزواج، مش بس عاوز أتجوّز.',
  },
  {
    name: 'سارة م.', role: 'صيدلانية — الإسكندرية', initial: 'س',
    quote: 'كنت بفكر إن الدورة هتكون مملة لكن العكس. الأمثلة عملية والمحتوى مفيد جداً. الشهادة كانت إنجاز حقيقي خلاني فخورة بنفسي.',
  },
  {
    name: 'عمر ح.', role: 'محاسب — طنطا', initial: 'ع',
    quote: 'المسار المالي وحده قيمة الدورة كلها. ساعدني أخطط لميزانية زواجي بشكل ذكي ومنظّم. أنصح بيها أي حد بيفكر يتجوّز.',
  },
]

function scrollTo(id) {
  const el = document.getElementById(id)
  if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' })
}

onMounted(async () => {
  if (isAuthenticated.value) {
    try {
      const data = await get('/api/courses')
      courses.value = data.courses || []
      overallProgress.value = data.overall_progress || 0
      hasCertificate.value = data.has_certificate || false
    } catch { /* handled */ }
  }
})
</script>
