<template>
  <!-- ============================================ -->
  <!-- 1. HERO + TOOL                               -->
  <!-- ============================================ -->
  <section class="calc-page-hero">
    <div class="container">
      <div class="calc-page-hero__content">
        <div class="calc-page-hero__content__eyebrow anim">
          <PhCalculator :size="16" weight="fill" />
          أداة حساب احترافية
        </div>
        <h1 class="calc-page-hero__content__title anim anim-delay-1">
          احسب تكلفة زواجك<br>
          واكتشف <span class="accent">كم ستوفّر</span>
        </h1>
        <p class="calc-page-hero__content__subtitle anim anim-delay-2">
          أداة دقيقة تحسب لك التكلفة الفعلية للزواج في مدينتك،
          وتعرض لك بشكل مفصّل كم يمكن لـ يسّرو أن يوفّر من جيبك مباشرةً.
        </p>
      </div>

      <div class="calc-tool-card anim anim-delay-3">
        <div class="calc-tool-card__inputs">
          <div class="calc-tool-card__field">
            <label>
              <PhMapPin :size="14" weight="bold" />
              المدينة
            </label>
            <select v-model="selectedCity" @change="calculate">
              <option value="" disabled>اختر مدينتك</option>
              <option v-for="city in cities" :key="city.id" :value="city">
                {{ city.name }} — {{ city.country }}
              </option>
            </select>
          </div>
          <div class="calc-tool-card__field">
            <label>
              <PhSliders :size="14" weight="bold" />
              مستوى التجهيزات
            </label>
            <select v-model="selectedLevel" @change="calculate">
              <option value="simple">بسيط — الأساسيات فقط</option>
              <option value="medium">متوسط — الخيار الأكثر شيوعاً</option>
              <option value="luxury">مميز — تجهيزات كاملة</option>
            </select>
          </div>
          <button class="calc-tool-card__btn" @click="calculate" :disabled="!selectedCity">
            <PhMagnifyingGlass :size="18" weight="bold" />
            احسب
          </button>
        </div>
      </div>
    </div>
  </section>

  <!-- ============================================ -->
  <!-- 2. BIG RESULT                                -->
  <!-- ============================================ -->
  <section v-if="result" class="big-result-section">
    <div class="container">
      <div class="big-result">
        <div class="result-cards">
          <div class="result-card" style="--card-color: #c0392b; --card-bg: rgba(192,57,43,0.1)">
            <div class="result-card__icon">
              <PhTrendUp :size="26" weight="duotone" />
            </div>
            <div class="result-card__label">التكلفة التقليدية</div>
            <div class="result-card__amount">{{ formatNumber(result.individual_cost) }}</div>
            <div class="result-card__currency">{{ result.currency }}</div>
          </div>

          <div class="result-card" style="--card-color: #0d7377; --card-bg: rgba(13,115,119,0.1)">
            <div class="result-card__icon">
              <PhTrendDown :size="26" weight="duotone" />
            </div>
            <div class="result-card__label">التكلفة مع يسّرو</div>
            <div class="result-card__amount">{{ formatNumber(result.yassiru_cost) }}</div>
            <div class="result-card__currency">{{ result.currency }}</div>
          </div>

          <div class="result-card" style="--card-color: #1b7a4a; --card-bg: rgba(27,122,74,0.1)">
            <div class="result-card__icon">
              <PhPiggyBank :size="26" weight="duotone" />
            </div>
            <div class="result-card__label">نسبة التوفير</div>
            <div class="result-card__amount">{{ result.savings_percent }}%</div>
            <div class="result-card__currency">{{ result.currency }}</div>
          </div>
        </div>

        <div class="result-summary-bar">
          <div class="result-summary-bar__inner">
            <div class="result-summary-bar__label">إجمالي توفيرك</div>
            <div class="result-summary-bar__amount">{{ formatNumber(result.savings) }} {{ result.currency }}</div>
            <div class="result-summary-bar__city">في {{ result.city }} — مستوى {{ levelLabel(selectedLevel) }}</div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ============================================ -->
  <!-- 3. VISUAL BREAKDOWN                          -->
  <!-- ============================================ -->
  <section v-if="result && result.breakdown && result.breakdown.length > 0" class="breakdown-section">
    <div class="container">
      <div class="text-center mb-3">
        <span class="section-eyebrow">التفاصيل الكاملة</span>
      </div>

      <h2 class="section-title-lg text-center">
        تكلفة كل بند مقارنة بالتقليدي
      </h2>
      <p class="section-sub-lg text-center">
        شوف بالضبط أين يوفّر يسّرو وكم — كل بند بشكل بصري واضح
      </p>

      <div class="breakdown-list">
        <div
          v-for="(item, i) in result.breakdown"
          :key="item.label || item.item"
          class="breakdown-row"


        >
          <div class="breakdown-row__header">
            <div class="breakdown-row__title">
              {{ item.label || item.item }}
            </div>
            <span v-if="item.is_required === false" class="breakdown-row__optional">اختياري</span>
          </div>

          <div class="breakdown-row__bars">
            <div class="breakdown-row__bar-row">
              <div class="breakdown-row__bar-row__label">التقليدي</div>
              <div class="breakdown-row__bar-row__bar breakdown-row__bar-row__bar--bad">
                <div class="fill" :style="{ width: '100%' }"></div>
              </div>
              <div class="breakdown-row__bar-row__value breakdown-row__bar-row__value--bad">
                {{ formatNumber(item.individual_cost || item.cost) }}
              </div>
            </div>
            <div class="breakdown-row__bar-row">
              <div class="breakdown-row__bar-row__label">مع يسّرو</div>
              <div class="breakdown-row__bar-row__bar breakdown-row__bar-row__bar--good">
                <div class="fill" :style="{ width: getYassiruWidth(item) + '%' }"></div>
              </div>
              <div class="breakdown-row__bar-row__value breakdown-row__bar-row__value--good">
                {{ formatNumber(item.yassiru_cost ?? item.individual_cost ?? item.cost) }}
              </div>
            </div>
          </div>

          <div v-if="item.yassiru_note" class="breakdown-row__note">
            <PhCheckCircle :size="14" weight="fill" />
            {{ item.yassiru_note }}
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ============================================ -->
  <!-- 4. LEVELS COMPARISON                         -->
  <!-- ============================================ -->
  <section v-if="result" class="levels-section">
    <div class="container">
      <div class="text-center mb-3">
        <span class="section-eyebrow">قارن المستويات</span>
      </div>

      <h2 class="section-title-lg text-center">
        جرّب 3 مستويات مختلفة
      </h2>
      <p class="section-sub-lg text-center">
        اختر المستوى الأنسب لميزانيتك واحتياجاتك
      </p>

      <div class="levels-grid">
        <div
          v-for="(level, i) in levelsComparison"
          :key="level.value"
          class="level-card"
          :class="{ 'is-selected': selectedLevel === level.value }"
          :style="{ '--lvl-color': level.color, '--lvl-bg': level.bg }"
          @click="selectLevel(level.value)"


        >
          <span v-if="level.value === 'medium'" class="level-card__badge">الأكثر شيوعاً</span>
          <div class="level-card__icon">
            <component :is="level.icon" :size="28" weight="duotone" />
          </div>
          <h3 class="level-card__title">{{ level.title }}</h3>
          <p class="level-card__desc">{{ level.desc }}</p>
          <div class="level-card__price">
            <div class="original" v-if="level.estimate">{{ formatNumber(level.estimate.individual) }}</div>
            <div class="yassiru" v-if="level.estimate">{{ formatNumber(level.estimate.yassiru) }}</div>
            <div class="currency">{{ result.currency }}</div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ============================================ -->
  <!-- 5. HOW YASSIRU SAVES                         -->
  <!-- ============================================ -->
  <section class="savings-section">
    <div class="container">
      <div class="text-center mb-3">
        <span class="section-eyebrow">طرق التوفير</span>
      </div>

      <h2 class="section-title-lg text-center">
        كيف يوفّر يسّرو هذا المبلغ؟
      </h2>
      <p class="section-sub-lg text-center">
        ثلاث طرق متكاملة تخفّض تكاليف زواجك بشكل حقيقي
      </p>

      <div class="savings-grid">
        <router-link
          v-for="(method, i) in savingMethods"
          :key="method.title"
          :to="method.route"
          class="savings-method"
          :style="{
            '--method-color': method.color,
            '--method-dark': method.dark,
            '--method-bg': method.bg,
          }"


        >
          <div class="savings-method__icon">
            <component :is="method.icon" :size="28" weight="duotone" />
          </div>
          <span class="savings-method__percentage">توفير {{ method.percent }}%</span>
          <h3 class="savings-method__title">{{ method.title }}</h3>
          <p class="savings-method__desc">{{ method.desc }}</p>
          <span class="savings-method__link">
            اعرف أكثر <PhArrowLeft :size="14" weight="bold" />
          </span>
        </router-link>
      </div>
    </div>
  </section>

  <!-- ============================================ -->
  <!-- 6. SHARE                                     -->
  <!-- ============================================ -->
  <section v-if="result" class="share-section">
    <div class="container">
      <h3 class="share-section__title">احفظ نتيجتك أو شاركها</h3>
      <p class="share-section__subtitle">شارك حساباتك مع أهلك أو احتفظ بها للرجوع إليها</p>

      <div class="share-buttons">
        <button class="share-btn" @click="printResult">
          <PhPrinter :size="18" weight="bold" />
          طباعة
        </button>
        <button class="share-btn" @click="shareWhatsApp">
          <PhWhatsappLogo :size="18" weight="bold" />
          مشاركة عبر واتساب
        </button>
        <button class="share-btn" @click="copyResult">
          <PhCopy :size="18" weight="bold" />
          {{ copied ? 'تم النسخ ✓' : 'نسخ النتيجة' }}
        </button>
      </div>
    </div>
  </section>

  <!-- ============================================ -->
  <!-- EMPTY STATE                                  -->
  <!-- ============================================ -->
  <section v-if="!result" class="big-result-section">
    <div class="container">
      <div class="calc-empty">
        <PhCalculator :size="80" weight="duotone" class="calc-empty__icon" />
        <h5>اختر مدينتك أعلاه لبدء الحساب</h5>
        <p>سنعرض لك تفاصيل التكلفة بند بند مع التوفير الحقيقي</p>
      </div>
    </div>
  </section>

  <!-- ============================================ -->
  <!-- 7. FINAL CTA                                 -->
  <!-- ============================================ -->
  <section class="calc-final-cta">
    <div class="container">
      <div class="calc-final-cta__inner">
        <h2 class="calc-final-cta__title">
          الأرقام كانت مقنعة؟<br>
          ابدأ توفيرك الحقيقي الآن
        </h2>
        <p class="calc-final-cta__subtitle">
          سجّل مجاناً وابدأ الدورة التأهيلية — وافتح كل أدوات التوفير في يسّرو
        </p>
        <router-link to="/register" class="calc-final-cta__btn">
          <PhRocketLaunch :size="22" weight="bold" />
          ابدأ رحلتك مجاناً
        </router-link>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'
import { useSeo } from '@/composables/useSeo'
import {
  PhCalculator, PhMapPin, PhSliders, PhMagnifyingGlass,
  PhTrendUp, PhTrendDown, PhPiggyBank, PhCheckCircle,
  PhRocketLaunch, PhHandCoins, PhHeart, PhUsersThree, PhArrowLeft,
  PhPrinter, PhWhatsappLogo, PhCopy,
  PhCircleDashed, PhCircle, PhStar,
} from '@phosphor-icons/vue'

useSeo({
  title: 'حاسبة تكاليف الزواج',
  description: 'احسب تكلفة زواجك بالتفصيل وقارن بين الزواج التقليدي والعرس الجماعي. وفّر حتى 70% من التكاليف.',
  path: '/calculator',
})

const cities = ref([])
const selectedCity = ref('')
const selectedLevel = ref('medium')
const result = ref(null)
const copied = ref(false)

const levelLabel = (l) => ({ simple: 'بسيط', medium: 'متوسط', luxury: 'مميز' })[l] || l

const levelsComparison = computed(() => {
  if (!result.value) return []

  const baseCost = result.value.individual_cost / getMultiplier(selectedLevel.value)

  return [
    {
      value: 'simple',
      title: 'بسيط',
      desc: 'الأساسيات فقط — للبدايات المتواضعة',
      icon: PhCircleDashed,
      color: '#1b7a4a', bg: 'rgba(27,122,74,0.1)',
      estimate: {
        individual: Math.round(baseCost * 0.6),
        yassiru: Math.round(baseCost * 0.6 * 0.3),
      },
    },
    {
      value: 'medium',
      title: 'متوسط',
      desc: 'الخيار الأكثر شيوعاً — توازن بين السعر والجودة',
      icon: PhCircle,
      color: '#0d7377', bg: 'rgba(13,115,119,0.1)',
      estimate: {
        individual: Math.round(baseCost * 1.0),
        yassiru: Math.round(baseCost * 1.0 * 0.3),
      },
    },
    {
      value: 'luxury',
      title: 'مميز',
      desc: 'تجهيزات كاملة وعناية بالتفاصيل',
      icon: PhStar,
      color: '#b8860b', bg: 'rgba(184,134,11,0.1)',
      estimate: {
        individual: Math.round(baseCost * 1.8),
        yassiru: Math.round(baseCost * 1.8 * 0.3),
      },
    },
  ]
})

function getMultiplier(level) {
  return { simple: 0.6, medium: 1.0, luxury: 1.8 }[level] || 1.0
}

const savingMethods = [
  {
    icon: PhHandCoins, title: 'الصندوق التعاوني', percent: '100',
    desc: 'تمويل بدون فوائد — كل عضو يستلم مبلغ تجهيز الزواج كاملاً عند دوره. لا فوائد، لا ضمانات بنكية.',
    bg: 'rgba(184,134,11,0.1)', color: '#b8860b', dark: '#8a6508',
    route: '/fund',
  },
  {
    icon: PhHeart, title: 'الأعراس الجماعية', percent: '70',
    desc: 'قاعة مشتركة + خدمات بالجملة = توفير 60-70% من تكاليف الفرح بدون التنازل عن الجودة.',
    bg: 'rgba(192,57,43,0.1)', color: '#c0392b', dark: '#8b2820',
    route: '/weddings',
  },
  {
    icon: PhUsersThree, title: 'شركاء وموردون', percent: '40',
    desc: 'خصومات حصرية من شركاء يسّرو الموثوقين على الأثاث والملابس والذهب وكل احتياجاتك.',
    bg: 'rgba(13,115,119,0.1)', color: '#0d7377', dark: '#095456',
    route: '/weddings',
  },
]

function formatNumber(num) {
  return new Intl.NumberFormat('ar-EG').format(num)
}

function getYassiruWidth(item) {
  const original = item.individual_cost || item.cost
  const yassiru = item.yassiru_cost ?? original
  if (!original) return 0
  return Math.round((yassiru / original) * 100)
}

async function calculate() {
  if (!selectedCity.value) return
  try {
    const { data } = await axios.post('/api/calculator/calculate', {
      city_id: selectedCity.value.id,
      level: selectedLevel.value,
    })
    result.value = data
  } catch { /* silent */ }
}

function selectLevel(lvl) {
  selectedLevel.value = lvl
  calculate()
  setTimeout(() => {
    const el = document.querySelector('.big-result-section')
    if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' })
  }, 200)
}

function printResult() {
  window.print()
}

function shareWhatsApp() {
  if (!result.value) return
  const text = `حسبت تكلفة زواجي مع يسّرو — هتوفّر ${formatNumber(result.value.savings)} ${result.value.currency} (${result.value.savings_percent}%)! جرّب بنفسك: ${window.location.href}`
  window.open(`https://wa.me/?text=${encodeURIComponent(text)}`, '_blank')
}

async function copyResult() {
  if (!result.value) return
  const text = `حسبت تكلفة زواجي في ${result.value.city} — التكلفة التقليدية: ${formatNumber(result.value.individual_cost)} ${result.value.currency}، مع يسّرو: ${formatNumber(result.value.yassiru_cost)} ${result.value.currency}، توفير: ${formatNumber(result.value.savings)} (${result.value.savings_percent}%). جرّب بنفسك: ${window.location.href}`
  try {
    await navigator.clipboard.writeText(text)
    copied.value = true
    setTimeout(() => copied.value = false, 2000)
  } catch { /* silent */ }
}

onMounted(async () => {
  try {
    const { data } = await axios.get('/api/calculator/cities')
    cities.value = data
  } catch { /* silent */ }
})
</script>
