<template>
  <div class="dashboard-page">
    <div class="container">
      <div class="page-header">
        <div>
          <h1 class="page-header__title">
            <span class="page-header__title__icon" style="--header-bg: rgba(184,134,11,0.1); --header-color: #b8860b">
              <PhCirclesThree :size="24" weight="duotone" />
            </span>
            حلقات الصندوق التعاوني
          </h1>
          <p class="page-header__subtitle">انضم لحلقة موجودة أو أنشئ حلقة جديدة في مدينتك</p>
        </div>
        <div class="page-header__actions">
          <router-link to="/circles/create" class="btn-action btn-action--gold">
            <PhPlusCircle :size="18" weight="bold" />
            إنشاء حلقة جديدة
          </router-link>
        </div>
      </div>

      <!-- Stats -->
      <div class="stats-grid">
        <div class="stat-tile" style="--tile-color: #b8860b; --tile-bg: rgba(184,134,11,0.1)">
          <div class="stat-tile__icon"><PhCirclesThree :size="24" weight="duotone" /></div>
          <div class="stat-tile__content">
            <div class="stat-tile__value">{{ circles.length }}</div>
            <div class="stat-tile__label">حلقة متاحة</div>
          </div>
        </div>
        <div class="stat-tile" style="--tile-color: #1b7a4a; --tile-bg: rgba(27,122,74,0.1)">
          <div class="stat-tile__icon"><PhPlayCircle :size="24" weight="duotone" /></div>
          <div class="stat-tile__content">
            <div class="stat-tile__value">{{ activeCircles }}</div>
            <div class="stat-tile__label">حلقة نشطة</div>
          </div>
        </div>
        <div class="stat-tile" style="--tile-color: #1565c0; --tile-bg: rgba(21,101,192,0.1)">
          <div class="stat-tile__icon"><PhClock :size="24" weight="duotone" /></div>
          <div class="stat-tile__content">
            <div class="stat-tile__value">{{ formingCircles }}</div>
            <div class="stat-tile__label">قيد التشكيل</div>
          </div>
        </div>
        <div class="stat-tile" style="--tile-color: #0d7377; --tile-bg: rgba(13,115,119,0.1)">
          <div class="stat-tile__icon"><PhUsers :size="24" weight="duotone" /></div>
          <div class="stat-tile__content">
            <div class="stat-tile__value">{{ totalMembers }}</div>
            <div class="stat-tile__label">إجمالي الأعضاء</div>
          </div>
        </div>
      </div>

      <!-- Filters -->
      <div class="filter-bar">
        <div class="filter-bar__search">
          <PhMagnifyingGlass :size="18" class="icon" />
          <input
            v-model="search"
            type="text"
            placeholder="ابحث عن حلقة بالاسم..."
          >
        </div>
        <div class="filter-bar__filters">
          <select v-model="cityFilter" @change="loadCircles">
            <option value="">جميع المدن</option>
            <option v-for="city in cities" :key="city.id" :value="city.id">{{ city.name }}</option>
          </select>
          <select v-model="statusFilter">
            <option value="">جميع الحالات</option>
            <option value="forming">قيد التشكيل</option>
            <option value="active">نشطة</option>
          </select>
        </div>
      </div>

      <!-- Flash messages -->
      <div v-if="successMsg" class="dash-alert dash-alert--success">
        <PhCheckCircle :size="20" weight="fill" class="dash-alert__icon" />
        <div class="dash-alert__content">{{ successMsg }}</div>
      </div>
      <div v-if="errorMsg" class="dash-alert dash-alert--danger">
        <PhWarningCircle :size="20" weight="fill" class="dash-alert__icon" />
        <div class="dash-alert__content">{{ errorMsg }}</div>
      </div>

      <div v-if="loading" class="loading-state">
        <div class="spinner-border"></div>
        <p class="loading-state__text">جاري تحميل الحلقات...</p>
      </div>

      <div v-else-if="filteredCircles.length" class="circles-grid">
        <div v-for="circle in filteredCircles" :key="circle.id" class="circle-card">
          <div class="circle-card__header">
            <div>
              <h3 class="circle-card__title">{{ circle.name }}</h3>
              <div class="circle-card__city">
                <PhMapPin :size="13" />
                {{ circle.city?.name || 'غير محدد' }}
              </div>
            </div>
            <span class="status-badge" :class="circle.status === 'active' ? 'status-badge--success' : 'status-badge--warning'">
              <PhCircle :size="8" weight="fill" />
              {{ circle.status === 'active' ? 'نشطة' : 'قيد التشكيل' }}
            </span>
          </div>

          <div class="circle-card__amount">
            <div class="circle-card__amount-label">المبلغ الذي ستحصل عليه</div>
            <div class="circle-card__amount-value">
              {{ formatNumber(circle.monthly_amount * circle.max_members) }}
              <span>{{ circle.currency }}</span>
            </div>
          </div>

          <div class="circle-card__details">
            <div class="circle-card__detail">
              <PhCurrencyCircleDollar :size="16" />
              <div>
                <div class="label">شهرياً</div>
                <div class="value">{{ formatNumber(circle.monthly_amount) }} {{ circle.currency }}</div>
              </div>
            </div>
            <div class="circle-card__detail">
              <PhUsers :size="16" />
              <div>
                <div class="label">الأعضاء</div>
                <div class="value">{{ circle.max_members }} عضو</div>
              </div>
            </div>
            <div class="circle-card__detail">
              <PhClock :size="16" />
              <div>
                <div class="label">المدة</div>
                <div class="value">{{ circle.cycle_months }} شهر</div>
              </div>
            </div>
          </div>

          <div v-if="circle.status === 'active' && circle.current_round" class="circle-card__progress">
            <div class="circle-card__progress-header">
              <span>الجولة الحالية</span>
              <strong>{{ circle.current_round }} / {{ circle.cycle_months }}</strong>
            </div>
            <div class="circle-card__progress-bar">
              <div class="fill" :style="{ width: (circle.current_round / circle.cycle_months * 100) + '%' }"></div>
            </div>
          </div>

          <div class="circle-card__actions">
            <router-link :to="`/circles/${circle.id}/dashboard`" class="btn-action btn-action--outline btn-action--sm">
              <PhEye :size="14" />
              التفاصيل
            </router-link>

            <!-- Already a member -->
            <span
              v-if="circle.is_member"
              class="btn-action btn-action--outline btn-action--sm member-badge"
            >
              <PhCheckCircle :size="14" weight="fill" />
              عضو بالفعل
            </span>

            <!-- Full circle (forming only) -->
            <span
              v-else-if="circle.status === 'forming' && circle.is_full"
              class="btn-action btn-action--outline btn-action--sm full-badge"
            >
              <PhUsers :size="14" />
              مكتملة
            </span>

            <!-- Can join -->
            <button
              v-else-if="circle.status === 'forming'"
              class="btn-action btn-action--primary btn-action--sm"
              :disabled="joining === circle.id"
              @click="joinCircle(circle)"
            >
              <span v-if="joining === circle.id" class="spinner-border spinner-border-sm"></span>
              <PhUserPlus :size="14" v-else />
              انضمام
            </button>
          </div>
        </div>
      </div>

      <div v-else class="empty-state">
        <PhCirclesThree :size="64" weight="duotone" class="empty-state__icon" />
        <h4 class="empty-state__title">لا توجد حلقات متاحة</h4>
        <p class="empty-state__desc">كن أول من ينشئ حلقة في مدينتك وادعو أصدقائك للانضمام</p>
        <router-link to="/circles/create" class="btn-action btn-action--primary">
          <PhPlusCircle :size="18" />
          إنشاء حلقة جديدة
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useApi } from '@/composables/useApi'
import axios from 'axios'
import {
  PhCirclesThree, PhPlusCircle, PhPlayCircle, PhClock, PhUsers,
  PhMagnifyingGlass, PhMapPin, PhCircle, PhCurrencyCircleDollar,
  PhEye, PhUserPlus, PhCheckCircle, PhWarningCircle,
} from '@phosphor-icons/vue'

const { get, post, loading } = useApi()
const circles = ref([])
const cities = ref([])
const cityFilter = ref('')
const statusFilter = ref('')
const search = ref('')
const joining = ref(null)
const successMsg = ref('')
const errorMsg = ref('')

function formatNumber(num) {
  return new Intl.NumberFormat('ar-EG').format(num)
}

const filteredCircles = computed(() => {
  return circles.value.filter(c => {
    if (statusFilter.value && c.status !== statusFilter.value) return false
    if (search.value && !c.name.toLowerCase().includes(search.value.toLowerCase())) return false
    return true
  })
})

const activeCircles = computed(() => circles.value.filter(c => c.status === 'active').length)
const formingCircles = computed(() => circles.value.filter(c => c.status === 'forming').length)
const totalMembers = computed(() => circles.value.reduce((sum, c) => sum + (c.max_members || 0), 0))

function clearMessages() {
  successMsg.value = ''
  errorMsg.value = ''
}

async function loadCircles() {
  clearMessages()
  try {
    const url = cityFilter.value ? `/api/circles?city_id=${cityFilter.value}` : '/api/circles'
    const result = await get(url)
    circles.value = result.data || result
  } catch (e) {
    errorMsg.value = e.response?.data?.message || 'تعذر تحميل الحلقات'
  }
}

async function joinCircle(circle) {
  clearMessages()
  joining.value = circle.id
  try {
    const result = await post(`/api/circles/${circle.id}/join`)
    successMsg.value = result?.message || 'تم الانضمام للحلقة بنجاح'
    setTimeout(() => (successMsg.value = ''), 6000)
    await loadCircles()
  } catch (e) {
    errorMsg.value = e.response?.data?.message || 'تعذر الانضمام للحلقة'
    setTimeout(() => (errorMsg.value = ''), 6000)
  }
  joining.value = null
}

onMounted(async () => {
  try {
    const { data } = await axios.get('/api/calculator/cities')
    cities.value = data
  } catch { /* ignore */ }
  await loadCircles()
})
</script>

<style lang="scss" scoped>
.circles-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
  gap: 1.25rem;

  @media (max-width: 576px) { grid-template-columns: 1fr; }
}

.member-badge {
  background: rgba(27, 122, 74, 0.08);
  color: #1b7a4a;
  border-color: rgba(27, 122, 74, 0.3);
  cursor: default;
  pointer-events: none;
}

.full-badge {
  background: rgba(136, 136, 160, 0.08);
  color: #8888a0;
  border-color: rgba(136, 136, 160, 0.3);
  cursor: default;
  pointer-events: none;
}

.circle-card {
  background: #fff;
  border: 1px solid #f0ece4;
  border-radius: 18px;
  padding: 1.5rem;
  transition: all 0.25s;
  display: flex;
  flex-direction: column;

  &:hover {
    transform: translateY(-3px);
    border-color: rgba(184,134,11,0.3);
    box-shadow: 0 16px 36px rgba(13, 26, 42, 0.06);
  }

  &__header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 0.75rem;
    margin-bottom: 1.25rem;
  }

  &__title {
    font-size: 1.1rem;
    font-weight: 800;
    color: #1a1a2a;
    margin: 0 0 0.25rem;
  }

  &__city {
    font-size: 0.78rem;
    color: #8888a0;
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
  }

  &__amount {
    background: linear-gradient(135deg, rgba(184,134,11,0.08) 0%, rgba(13,115,119,0.05) 100%);
    border: 1px dashed rgba(184,134,11,0.25);
    border-radius: 12px;
    padding: 1rem;
    text-align: center;
    margin-bottom: 1rem;

    &-label {
      font-size: 0.75rem;
      color: #8888a0;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      margin-bottom: 0.25rem;
    }

    &-value {
      font-size: 1.65rem;
      font-weight: 900;
      color: #b8860b;
      letter-spacing: -0.02em;
      line-height: 1;

      span {
        font-size: 0.85rem;
        color: #8888a0;
        font-weight: 600;
        margin-right: 0.25rem;
      }
    }
  }

  &__details {
    display: flex;
    justify-content: space-between;
    gap: 0.5rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #f0ece4;
    margin-bottom: 1rem;
  }

  &__detail {
    display: flex;
    align-items: center;
    gap: 0.4rem;
    font-size: 0.75rem;

    svg { color: #8888a0; flex-shrink: 0; }

    .label {
      color: #8888a0;
      font-weight: 600;
      font-size: 0.65rem;
    }
    .value {
      color: #1a1a2a;
      font-weight: 700;
    }
  }

  &__progress {
    margin-bottom: 1rem;

    &-header {
      display: flex;
      justify-content: space-between;
      font-size: 0.78rem;
      color: #8888a0;
      margin-bottom: 0.4rem;

      strong { color: #1a1a2a; }
    }

    &-bar {
      height: 6px;
      background: #f0ece4;
      border-radius: 100px;
      overflow: hidden;

      .fill {
        height: 100%;
        background: linear-gradient(90deg, #0d7377 0%, #b8860b 100%);
        border-radius: 100px;
        transition: width 0.6s;
      }
    }
  }

  &__actions {
    display: flex;
    gap: 0.5rem;
    margin-top: auto;

    .btn-action { flex: 1; justify-content: center; }
  }
}
</style>
