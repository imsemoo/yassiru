<template>
  <!-- ============================================ -->
  <!-- 1. HERO                                       -->
  <!-- ============================================ -->
  <section class="weddings-hero">
    <div class="container weddings-hero__content">
      <div class="row align-items-center g-5">
        <div class="col-lg-7">
          <div class="weddings-hero__eyebrow anim">
            <PhHeart :size="16" weight="fill" />
            أعراس جماعية مهيبة
          </div>

          <h1 class="weddings-hero__title anim anim-delay-1">
            عرس مهيب بـ<br>
            <span class="accent">ربع التكلفة</span>
          </h1>

          <p class="weddings-hero__subtitle anim anim-delay-2">
            انضم لعرس جماعي منظّم بالكامل في قاعة فاخرة:
            بوفيه، تصوير، ضيوف، وتجهيزات كاملة. كل عريس يحتفل بزواجه
            في يوم واحد بأقل تكلفة ممكنة.
          </p>

          <div class="weddings-hero__cta-group anim anim-delay-3">
            <a href="#weddings-list" class="weddings-hero__cta-primary" @click.prevent="scrollTo('weddings-list')">
              <PhListMagnifyingGlass :size="22" weight="bold" />
              تصفّح الأعراس المتاحة
            </a>
            <a href="#concept" class="weddings-hero__cta-secondary" @click.prevent="scrollTo('concept')">
              <PhInfo :size="20" weight="bold" />
              إيه هو العرس الجماعي؟
            </a>
          </div>
        </div>

        <!-- Hero Visual -->
        <div class="col-lg-5">
          <div class="wedding-hero-visual anim anim-delay-3">
            <div class="savings-card">
              <div class="savings-card__badge">
                <PhSparkle :size="14" weight="fill" />
                مثال حقيقي
              </div>

              <h6 class="savings-card__title">عرس متوسط في القاهرة</h6>

              <div class="savings-card__row savings-card__row--bad">
                <div class="savings-card__row__label">
                  <PhTrendUp :size="20" weight="fill" />
                  عرس فردي
                </div>
                <div class="savings-card__row__amount savings-card__row__amount--bad">
                  20,000
                </div>
              </div>

              <div class="savings-card__row savings-card__row--good">
                <div class="savings-card__row__label">
                  <PhTrendDown :size="20" weight="fill" />
                  عرس جماعي
                </div>
                <div class="savings-card__row__amount savings-card__row__amount--good">
                  3,000
                </div>
              </div>

              <div class="savings-card__savings">
                <div class="label">توفيرك</div>
                <div class="amount">17,000 ج.م</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ============================================ -->
  <!-- 2. CONCEPT EXPLAINED                         -->
  <!-- ============================================ -->
  <section class="concept-section" id="concept">
    <div class="container">
      <div class="text-center mb-3">
        <span class="section-eyebrow">الفكرة بسيطة</span>
      </div>

      <h2 class="section-title-lg text-center">
        ما هو العرس الجماعي؟
      </h2>
      <p class="section-sub-lg text-center">
        قاعة واحدة، عدة عرسان، ليلة لا تُنسى — والتكاليف تنخفض للجميع
      </p>

      <div class="concept-visual">
        <h6 class="concept-visual__title">قارن بنفسك</h6>

        <div class="concept-grid">
          <div class="concept-side concept-side--bad">
            <div class="concept-side__icon">
              <PhUser :size="36" weight="duotone" />
            </div>
            <h4 class="concept-side__title">عرس فردي تقليدي</h4>
            <p class="concept-side__desc">
              عريس واحد يتحمّل كل التكاليف:
              قاعة + بوفيه + تصوير + خدمات
            </p>
          </div>

          <div class="concept-divider">
            <PhArrowsLeftRight :size="28" weight="bold" />
          </div>

          <div class="concept-side concept-side--good">
            <div class="concept-side__icon">
              <PhUsersThree :size="36" weight="duotone" />
            </div>
            <h4 class="concept-side__title">عرس جماعي يسّرو</h4>
            <p class="concept-side__desc">
              20 عريس يتشاركون نفس القاعة في نفس اليوم —
              التكاليف تنقسم على الجميع
            </p>
          </div>
        </div>

        <div class="concept-features">
          <div class="concept-feature">
            <PhCrown :size="28" weight="duotone" class="icon" />
            <div class="text">قاعة فاخرة لكل عريس</div>
          </div>
          <div class="concept-feature">
            <PhSparkle :size="28" weight="duotone" class="icon" />
            <div class="text">احتفال خاص ومميّز</div>
          </div>
          <div class="concept-feature">
            <PhTrendDown :size="28" weight="duotone" class="icon" />
            <div class="text">توفير حقيقي 70%</div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ============================================ -->
  <!-- 3. WEDDINGS LIST                             -->
  <!-- ============================================ -->
  <section class="weddings-list-section" id="weddings-list">
    <div class="container">
      <div class="text-center mb-3">
        <span class="section-eyebrow">الأعراس المتاحة</span>
      </div>

      <h2 class="section-title-lg text-center">
        اختر عرسك القادم
      </h2>
      <p class="section-sub-lg text-center">
        فلتر حسب مدينتك أو ميزانيتك واحجز مكانك
      </p>

      <div class="weddings-filters">
        <div class="weddings-filters__field">
          <label><PhMapPin :size="14" /> المدينة</label>
          <select v-model="cityFilter" @change="load">
            <option value="">جميع المدن</option>
            <option v-for="city in cities" :key="city.id" :value="city.id">{{ city.name }}</option>
          </select>
        </div>
        <div class="weddings-filters__field">
          <label><PhCalendar :size="14" /> الترتيب</label>
          <select v-model="sortBy">
            <option value="date">أقرب موعد</option>
            <option value="price-asc">السعر: من الأقل</option>
            <option value="price-desc">السعر: من الأعلى</option>
            <option value="capacity">المتاح</option>
          </select>
        </div>
        <div class="weddings-filters__field">
          <label><PhFunnel :size="14" /> الحالة</label>
          <select v-model="statusFilter">
            <option value="">جميع الحالات</option>
            <option value="upcoming">مفتوح للتسجيل</option>
            <option value="full">مكتمل</option>
          </select>
        </div>
        <div class="weddings-filters__count">
          {{ filteredWeddings.length }} عرس
        </div>
      </div>

      <div v-if="loading" class="text-center py-5">
        <div class="spinner-border text-danger"></div>
      </div>

      <div v-else-if="filteredWeddings.length" class="weddings-grid">
        <div v-for="(w, i) in filteredWeddings" :key="w.id">
          <div class="wedding-card-pro">
            <div class="wedding-card-pro__header">
              <span class="wedding-card-pro__status" :class="w.status === 'upcoming' ? 'wedding-card-pro__status--open' : 'wedding-card-pro__status--full'">
                <PhCircle :size="8" weight="fill" v-if="w.status === 'upcoming'" />
                <PhCheckCircle :size="12" weight="fill" v-else />
                {{ w.status === 'upcoming' ? 'مفتوح' : 'مكتمل' }}
              </span>
              <h3 class="wedding-card-pro__title">{{ w.title }}</h3>
              <div class="wedding-card-pro__location">
                <span><PhMapPin :size="14" /> {{ w.city?.name }}</span>
                <span><PhBuildings :size="14" /> {{ w.venue_name }}</span>
              </div>
            </div>

            <div class="wedding-card-pro__body">
              <p v-if="w.description" class="wedding-card-pro__desc">{{ w.description }}</p>

              <div class="wedding-card-pro__info">
                <div class="wedding-card-pro__info-item">
                  <div class="label"><PhCalendar :size="12" /> التاريخ</div>
                  <div class="value">{{ formatDate(w.wedding_date) }}</div>
                </div>
                <div class="wedding-card-pro__info-item">
                  <div class="label"><PhHourglass :size="12" /> آخر موعد</div>
                  <div class="value">{{ formatDate(w.registration_deadline) }}</div>
                </div>
              </div>

              <div class="wedding-card-pro__capacity">
                <div class="header">
                  <span>{{ w.registered_count }} / {{ w.max_grooms }} عريس مسجّل</span>
                  <span>{{ Math.round(w.registered_count / w.max_grooms * 100) }}%</span>
                </div>
                <div class="bar">
                  <div class="fill" :style="{ width: (w.registered_count / w.max_grooms * 100) + '%' }"></div>
                </div>
              </div>
            </div>

            <div class="wedding-card-pro__footer">
              <div class="wedding-card-pro__price">
                <div class="original">{{ formatNumber(w.original_price) }}</div>
                <div class="final">{{ formatNumber(w.price_per_groom) }}</div>
                <div class="currency">{{ w.currency || 'ج.م' }}</div>
              </div>
              <router-link :to="`/weddings/${w.id}`" class="wedding-card-pro__cta">
                التفاصيل
                <PhArrowLeft :size="16" weight="bold" />
              </router-link>
            </div>
          </div>
        </div>
      </div>

      <div v-else class="weddings-empty">
        <PhHeart :size="64" weight="duotone" class="weddings-empty__icon" />
        <h5>لا توجد أعراس متاحة حالياً</h5>
        <p>سيتم الإعلان عن أعراس جديدة قريباً</p>
      </div>
    </div>
  </section>

  <!-- ============================================ -->
  <!-- 4. WHAT'S INCLUDED                           -->
  <!-- ============================================ -->
  <section class="included-section">
    <div class="container">
      <div class="text-center mb-3">
        <span class="section-eyebrow">كل شيء شامل</span>
      </div>

      <h2 class="section-title-lg text-center">
        كل ما تحتاجه في عرسك
      </h2>
      <p class="section-sub-lg text-center">
        السعر شامل كل الخدمات الأساسية — ما عليك سوى الحضور والاستمتاع
      </p>

      <div class="included-grid">
        <div
          v-for="(item, i) in included"
          :key="item.title"
          class="included-card"
          :style="{
            '--inc-color': item.color,
            '--inc-bg-1': item.bg1,
            '--inc-bg-2': item.bg2,
          }"


        >
          <div class="included-card__icon">
            <component :is="item.icon" :size="26" weight="duotone" />
          </div>
          <h4 class="included-card__title">{{ item.title }}</h4>
          <p class="included-card__desc">{{ item.desc }}</p>
        </div>
      </div>
    </div>
  </section>

  <!-- ============================================ -->
  <!-- 5. VENDORS                                   -->
  <!-- ============================================ -->
  <section v-if="vendors.length" class="vendors-section">
    <div class="container">
      <div class="text-center mb-3">
        <span class="section-eyebrow">شركاء يسّرو</span>
      </div>

      <h2 class="section-title-lg text-center">
        موردون بخصومات حصرية
      </h2>
      <p class="section-sub-lg text-center">
        حتى لو فضّلت عرساً فردياً، استفد من خصومات شركائنا الموثوقين
      </p>

      <div class="vendors-grid">
        <div
          v-for="(v, i) in vendors"
          :key="v.id"
          class="vendor-card"


        >
          <div class="vendor-card__discount">
            <PhTag :size="14" weight="fill" />
            خصم {{ v.discount_percent }}%
          </div>
          <span class="vendor-card__category">{{ categoryLabel(v.category) }}</span>
          <h4 class="vendor-card__title">{{ v.name }}</h4>
          <p v-if="v.description" class="vendor-card__desc">{{ v.description }}</p>
          <div class="vendor-card__city">
            <PhMapPin :size="12" />
            {{ v.city?.name }}
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ============================================ -->
  <!-- 6. FINAL CTA                                 -->
  <!-- ============================================ -->
  <section class="weddings-final-cta">
    <div class="container">
      <div class="weddings-final-cta__inner">
        <h2 class="weddings-final-cta__title">
          ليلة عمرك في انتظارك
        </h2>
        <p class="weddings-final-cta__subtitle">
          سجّل في عرس جماعي اليوم وابدأ تجهيزاتك بأقل تكلفة ممكنة — كل ما تحتاجه شامل
        </p>
        <router-link to="/register" class="weddings-final-cta__btn">
          <PhRocketLaunch :size="22" weight="bold" />
          ابدأ رحلتك الآن
        </router-link>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useApi } from '@/composables/useApi'
import { useSeo } from '@/composables/useSeo'
import axios from 'axios'
import {
  PhHeart, PhMapPin, PhBuildings, PhCalendar, PhHourglass, PhArrowLeft,
  PhListMagnifyingGlass, PhInfo, PhSparkle, PhTrendUp, PhTrendDown,
  PhUser, PhUsersThree, PhArrowsLeftRight, PhCrown, PhFunnel,
  PhCheckCircle, PhCircle, PhRocketLaunch, PhTag,
  PhCookingPot, PhCamera, PhMusicNotes, PhFlower, PhArmchair, PhWine,
} from '@phosphor-icons/vue'

useSeo({
  title: 'الأعراس الجماعية',
  description: 'سجّل في أعراس جماعية منظمة بتوفير 60-70% من التكاليف. أعراس كريمة بأسعار ميسّرة.',
  path: '/weddings',
})

const { get, loading } = useApi()
const weddings = ref([])
const vendors = ref([])
const cities = ref([])
const cityFilter = ref('')
const statusFilter = ref('')
const sortBy = ref('date')

const categoryLabel = (c) => ({ venue: 'قاعات', photography: 'تصوير', catering: 'ضيافة', furniture: 'أثاث', clothing: 'أزياء', other: 'أخرى' })[c] || c

function formatDate(d) {
  return new Date(d).toLocaleDateString('ar-EG', { year: 'numeric', month: 'long', day: 'numeric' })
}
function formatNumber(n) {
  return new Intl.NumberFormat('ar-EG').format(n)
}

const filteredWeddings = computed(() => {
  let result = [...weddings.value]
  if (statusFilter.value) {
    result = result.filter(w => w.status === statusFilter.value)
  }
  if (sortBy.value === 'price-asc') {
    result.sort((a, b) => a.price_per_groom - b.price_per_groom)
  } else if (sortBy.value === 'price-desc') {
    result.sort((a, b) => b.price_per_groom - a.price_per_groom)
  } else if (sortBy.value === 'capacity') {
    result.sort((a, b) => (b.max_grooms - b.registered_count) - (a.max_grooms - a.registered_count))
  } else {
    result.sort((a, b) => new Date(a.wedding_date) - new Date(b.wedding_date))
  }
  return result
})

const included = [
  {
    icon: PhBuildings, title: 'قاعة فاخرة',
    desc: 'قاعة مجهّزة بأحدث المعايير مع ديكور احتفالي كامل',
    color: '#c0392b', bg1: 'rgba(192,57,43,0.15)', bg2: 'rgba(192,57,43,0.05)',
  },
  {
    icon: PhCookingPot, title: 'بوفيه شامل',
    desc: 'وجبات متنوعة وعالية الجودة لجميع الضيوف',
    color: '#b8860b', bg1: 'rgba(184,134,11,0.15)', bg2: 'rgba(184,134,11,0.05)',
  },
  {
    icon: PhCamera, title: 'تصوير احترافي',
    desc: 'فريق تصوير محترف لتوثيق ذكرياتك بصور وفيديو',
    color: '#0d7377', bg1: 'rgba(13,115,119,0.15)', bg2: 'rgba(13,115,119,0.05)',
  },
  {
    icon: PhMusicNotes, title: 'إنشاد إسلامي',
    desc: 'فرقة إنشاد محترفة تليق بالأجواء الإسلامية',
    color: '#1565c0', bg1: 'rgba(21,101,192,0.15)', bg2: 'rgba(21,101,192,0.05)',
  },
  {
    icon: PhFlower, title: 'تنسيق وزينة',
    desc: 'تنسيق احترافي بالورود والزينة لكل أرجاء القاعة',
    color: '#1b7a4a', bg1: 'rgba(27,122,74,0.15)', bg2: 'rgba(27,122,74,0.05)',
  },
  {
    icon: PhArmchair, title: 'مقاعد للضيوف',
    desc: 'سعة كافية لجميع ضيوفك في مكان مريح',
    color: '#6b7280', bg1: 'rgba(107,114,128,0.15)', bg2: 'rgba(107,114,128,0.05)',
  },
  {
    icon: PhWine, title: 'مشروبات',
    desc: 'مشروبات ساخنة وباردة طوال الليلة',
    color: '#8b5cf6', bg1: 'rgba(139,92,246,0.15)', bg2: 'rgba(139,92,246,0.05)',
  },
  {
    icon: PhSparkle, title: 'تنظيم متكامل',
    desc: 'فريق تنظيم لإدارة كل التفاصيل بدلاً منك',
    color: '#e67e22', bg1: 'rgba(230,126,34,0.15)', bg2: 'rgba(230,126,34,0.05)',
  },
]

async function load() {
  try {
    const url = cityFilter.value ? `/api/weddings?city_id=${cityFilter.value}` : '/api/weddings'
    const result = await get(url)
    weddings.value = result.data || result
  } catch { /* handled */ }
}

function scrollTo(id) {
  const el = document.getElementById(id)
  if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' })
}

onMounted(async () => {
  try {
    const { data } = await axios.get('/api/calculator/cities')
    cities.value = data
  } catch { /* ignore */ }
  await load()
  try {
    vendors.value = await get('/api/vendors')
  } catch { /* ignore */ }
})
</script>
