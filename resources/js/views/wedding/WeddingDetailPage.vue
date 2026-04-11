<template>
  <div class="dashboard-page">
    <div class="container" style="max-width: 880px">
      <div v-if="loading" class="loading-state">
        <div class="spinner-border"></div>
        <p class="loading-state__text">جاري تحميل تفاصيل العرس...</p>
      </div>

      <div v-else-if="wedding">
        <router-link to="/weddings" class="page-header__back">
          <PhArrowRight :size="14" weight="bold" />
          العودة للأعراس
        </router-link>

        <div class="page-header">
          <div>
            <h1 class="page-header__title">
              <span class="page-header__title__icon" style="--header-bg: rgba(192,57,43,0.1); --header-color: #c0392b">
                <PhHeart :size="24" weight="duotone" />
              </span>
              {{ wedding.title }}
            </h1>
            <p class="page-header__subtitle">
              <PhMapPin :size="13" class="ms-1" />
              {{ wedding.city?.name }} — {{ wedding.venue_name }}
            </p>
          </div>
          <div class="page-header__actions">
            <span class="status-badge" :class="wedding.status === 'upcoming' ? 'status-badge--success' : 'status-badge--warning'">
              <PhCircle :size="8" weight="fill" />
              {{ wedding.status === 'upcoming' ? 'مفتوح للتسجيل' : 'مغلق' }}
            </span>
          </div>
        </div>

        <!-- Info Stats -->
        <div class="stats-grid" style="grid-template-columns: repeat(3, 1fr)">
          <div class="stat-tile" style="--tile-color: #c0392b; --tile-bg: rgba(192,57,43,0.1)">
            <div class="stat-tile__icon"><PhCalendar :size="22" weight="duotone" /></div>
            <div class="stat-tile__content">
              <div class="stat-tile__value" style="font-size: 1rem">{{ formatDate(wedding.wedding_date) }}</div>
              <div class="stat-tile__label">تاريخ العرس</div>
            </div>
          </div>
          <div class="stat-tile" style="--tile-color: #e67e22; --tile-bg: rgba(230,126,34,0.1)">
            <div class="stat-tile__icon"><PhHourglass :size="22" weight="duotone" /></div>
            <div class="stat-tile__content">
              <div class="stat-tile__value" style="font-size: 1rem">{{ formatDate(wedding.registration_deadline) }}</div>
              <div class="stat-tile__label">آخر موعد للتسجيل</div>
            </div>
          </div>
          <div class="stat-tile" style="--tile-color: #1565c0; --tile-bg: rgba(21,101,192,0.1)">
            <div class="stat-tile__icon"><PhUsersThree :size="22" weight="duotone" /></div>
            <div class="stat-tile__content">
              <div class="stat-tile__value">{{ wedding.registered_count }} / {{ wedding.max_grooms }}</div>
              <div class="stat-tile__label">العرسان المسجّلون</div>
            </div>
          </div>
        </div>

        <!-- Description + Capacity -->
        <div class="dash-card">
          <div class="dash-card__body">
            <p v-if="wedding.description" class="wedding-desc">{{ wedding.description }}</p>

            <div class="capacity-progress">
              <div class="capacity-progress__header">
                <span>نسبة الامتلاء</span>
                <strong>{{ Math.round(wedding.registered_count / wedding.max_grooms * 100) }}%</strong>
              </div>
              <div class="capacity-progress__bar">
                <div class="fill" :style="{ width: (wedding.registered_count / wedding.max_grooms * 100) + '%' }"></div>
              </div>
              <div class="capacity-progress__hint">
                {{ wedding.max_grooms - wedding.registered_count }} مقاعد متبقية
              </div>
            </div>
          </div>
        </div>

        <!-- Price Card -->
        <div class="dash-card price-card">
          <div class="dash-card__body">
            <div class="price-card__inner">
              <div>
                <div class="price-card__label">سعر التسجيل في العرس</div>
                <div class="price-card__amount-row">
                  <span class="price-card__original">{{ formatNumber(wedding.original_price) }}</span>
                  <span class="price-card__final">{{ formatNumber(wedding.price_per_groom) }}</span>
                  <span class="price-card__currency">ج.م</span>
                </div>
              </div>
              <div class="price-card__savings">
                <PhTrendDown :size="22" weight="bold" />
                وفّر {{ Math.round((1 - wedding.price_per_groom / wedding.original_price) * 100) }}%
              </div>
            </div>
          </div>
        </div>

        <!-- Registration Action -->
        <div v-if="!auth.isAuthenticated" class="dash-alert dash-alert--info">
          <PhInfo :size="20" weight="fill" class="dash-alert__icon" />
          <div class="dash-alert__content">
            <div class="dash-alert__title">تسجيل الدخول مطلوب</div>
            سجّل دخولك أولاً للتسجيل في هذا العرس
            <div class="mt-2">
              <router-link :to="{ path: '/login', query: { redirect: $route.fullPath } }" class="btn-action btn-action--primary btn-action--sm">
                <PhSignIn :size="14" />
                تسجيل الدخول
              </router-link>
            </div>
          </div>
        </div>

        <div v-else-if="registered" class="dash-alert dash-alert--success">
          <PhCheckCircle :size="20" weight="fill" class="dash-alert__icon" />
          <div class="dash-alert__content">
            <div class="dash-alert__title">أنت مسجّل في هذا العرس</div>
            تابع تسجيلاتك من صفحة "أعراسي"
          </div>
        </div>

        <div v-else-if="wedding.status === 'upcoming'" class="dash-card">
          <div class="dash-card__header">
            <h3 class="dash-card__header__title">
              <PhUserPlus :size="20" />
              التسجيل في العرس
            </h3>
          </div>
          <div class="dash-card__body">
            <div v-if="actionMessage" class="dash-alert" :class="messageType === 'success' ? 'dash-alert--success' : 'dash-alert--danger'">
              <PhCheckCircle :size="20" weight="fill" class="dash-alert__icon" v-if="messageType === 'success'" />
              <PhWarningCircle :size="20" weight="fill" class="dash-alert__icon" v-else />
              <div class="dash-alert__content">{{ actionMessage }}</div>
            </div>

            <div class="dash-form">
              <div class="dash-form__group">
                <label class="dash-form__label">
                  <PhNotePencil :size="14" />
                  ملاحظات (اختياري)
                </label>
                <textarea
                  v-model="notes"
                  class="dash-form__textarea"
                  placeholder="أي ملاحظات تود إضافتها — مثل عدد الضيوف المتوقع..."
                ></textarea>
              </div>

              <button class="btn-action btn-action--primary" :disabled="registering" @click="registerWedding">
                <span v-if="registering" class="spinner-border spinner-border-sm"></span>
                <PhHeart :size="18" weight="bold" v-else />
                سجّل في هذا العرس
              </button>
            </div>
          </div>
        </div>

        <div v-else class="dash-alert dash-alert--warning">
          <PhWarningCircle :size="20" weight="fill" class="dash-alert__icon" />
          <div class="dash-alert__content">
            <div class="dash-alert__title">التسجيل مغلق</div>
            هذا العرس مكتمل أو انتهت مهلة التسجيل
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import { useRoute } from 'vue-router'
import { useApi } from '@/composables/useApi'
import { useAuthStore } from '@/stores/auth'
import { useSeo } from '@/composables/useSeo'
import {
  PhArrowRight, PhMapPin, PhCalendar, PhHourglass,
  PhUsersThree, PhTrendDown, PhCheckCircle, PhHeart, PhWarningCircle,
  PhCircle, PhInfo, PhSignIn, PhUserPlus, PhNotePencil,
} from '@phosphor-icons/vue'

const route = useRoute()
const auth = useAuthStore()
const { get, post, loading } = useApi()
const wedding = ref(null)
const registered = ref(false)
const notes = ref('')
const registering = ref(false)
const actionMessage = ref('')
const messageType = ref('success')

watch(wedding, (w) => {
  if (w) {
    useSeo({
      title: w.title,
      description: `عرس جماعي في ${w.venue_name} — سجّل الآن بتكلفة ${w.price_per_groom} فقط.`,
      path: `/weddings/${w.id}`,
      schema: {
        '@context': 'https://schema.org',
        '@type': 'Event',
        name: w.title,
        startDate: w.wedding_date,
        location: { '@type': 'Place', name: w.venue_name },
        organizer: { '@type': 'Organization', name: 'يسّرو' },
        inLanguage: 'ar',
      },
    })
  }
})

function formatDate(d) {
  return new Date(d).toLocaleDateString('ar-EG', { year: 'numeric', month: 'short', day: 'numeric' })
}
function formatNumber(n) {
  return new Intl.NumberFormat('ar-EG').format(n)
}

async function registerWedding() {
  registering.value = true
  actionMessage.value = ''
  try {
    const result = await post(`/api/weddings/${route.params.id}/register`, { notes: notes.value })
    actionMessage.value = result.message
    messageType.value = 'success'
    registered.value = true
  } catch (err) {
    actionMessage.value = err.response?.data?.message || 'حدث خطأ'
    messageType.value = 'danger'
  }
  registering.value = false
}

onMounted(async () => {
  try {
    wedding.value = await get(`/api/weddings/${route.params.id}`)
    if (wedding.value.registrations && auth.user) {
      registered.value = wedding.value.registrations.some(r => r.user_id === auth.user.id)
    }
  } catch { /* handled */ }
})
</script>

<style lang="scss" scoped>
.wedding-desc {
  font-size: 0.95rem;
  color: #4a4a5e;
  line-height: 1.95;
  margin-bottom: 1.5rem;
}

.capacity-progress {
  &__header {
    display: flex;
    justify-content: space-between;
    font-size: 0.85rem;
    color: #8888a0;
    margin-bottom: 0.5rem;

    strong {
      color: #c0392b;
      font-size: 1rem;
    }
  }

  &__bar {
    height: 12px;
    background: #f0ece4;
    border-radius: 100px;
    overflow: hidden;

    .fill {
      height: 100%;
      background: linear-gradient(90deg, #c0392b 0%, #b8860b 100%);
      border-radius: 100px;
      transition: width 0.6s;
    }
  }

  &__hint {
    font-size: 0.78rem;
    color: #8888a0;
    text-align: center;
    margin-top: 0.5rem;
  }
}

.price-card {
  background: linear-gradient(135deg, rgba(192,57,43,0.05) 0%, rgba(184,134,11,0.05) 100%);
  border-color: rgba(192,57,43,0.2);
}

.price-card__inner {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  flex-wrap: wrap;
}

.price-card__label {
  font-size: 0.85rem;
  color: #8888a0;
  font-weight: 700;
  margin-bottom: 0.4rem;
}

.price-card__amount-row {
  display: flex;
  align-items: baseline;
  gap: 0.5rem;
  flex-wrap: wrap;
}

.price-card__original {
  font-size: 1.1rem;
  color: #8888a0;
  text-decoration: line-through;
}

.price-card__final {
  font-size: 2.5rem;
  font-weight: 900;
  color: #c0392b;
  letter-spacing: -0.02em;
  line-height: 1;

  @media (max-width: 576px) { font-size: 2rem; }
}

.price-card__currency {
  font-size: 0.9rem;
  color: #8888a0;
  font-weight: 600;
}

.price-card__savings {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  background: #1b7a4a;
  color: #fff;
  padding: 0.65rem 1.15rem;
  border-radius: 100px;
  font-weight: 800;
  font-size: 0.9rem;
  box-shadow: 0 6px 16px rgba(27,122,74,0.25);
}
</style>
