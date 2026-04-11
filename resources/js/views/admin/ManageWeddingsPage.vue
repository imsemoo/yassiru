<template>
  <div class="dashboard-page">
    <div class="container">
      <router-link to="/admin" class="page-header__back">
        <PhArrowRight :size="14" weight="bold" />
        العودة للوحة الإدارة
      </router-link>

      <div class="page-header">
        <div>
          <h1 class="page-header__title">
            <span class="page-header__title__icon" style="--header-bg: rgba(192,57,43,0.1); --header-color: #c0392b">
              <PhHeart :size="24" weight="duotone" />
            </span>
            إدارة الأعراس الجماعية
          </h1>
          <p class="page-header__subtitle">إنشاء وإدارة الأعراس الجماعية على المنصة</p>
        </div>
        <div class="page-header__actions">
          <button class="btn-action btn-action--primary" @click="showModal = true">
            <PhPlusCircle :size="18" weight="bold" />
            إنشاء عرس جديد
          </button>
        </div>
      </div>

      <div v-if="loading" class="loading-state">
        <div class="spinner-border"></div>
        <p class="loading-state__text">جاري التحميل...</p>
      </div>

      <div v-else-if="weddings.length" class="weddings-admin-grid">
        <div v-for="w in weddings" :key="w.id" class="wedding-admin-card">
          <div class="wedding-admin-card__header">
            <div>
              <h3 class="wedding-admin-card__title">{{ w.title }}</h3>
              <div class="wedding-admin-card__meta">
                <span><PhMapPin :size="13" /> {{ w.city?.name }}</span>
                <span><PhBuildings :size="13" /> {{ w.venue_name }}</span>
              </div>
            </div>
            <span class="status-badge" :class="statusBadgeClass(w.status)">
              {{ statusLabel(w.status) }}
            </span>
          </div>

          <div class="wedding-admin-card__body">
            <div class="wedding-info-row">
              <div class="info-item">
                <PhCalendar :size="14" />
                <div>
                  <div class="label">التاريخ</div>
                  <div class="value">{{ formatDate(w.wedding_date) }}</div>
                </div>
              </div>
              <div class="info-item">
                <PhUsers :size="14" />
                <div>
                  <div class="label">المسجلون</div>
                  <div class="value">{{ w.registrations_count || 0 }} / {{ w.max_grooms }}</div>
                </div>
              </div>
              <div class="info-item">
                <PhCurrencyCircleDollar :size="14" />
                <div>
                  <div class="label">السعر</div>
                  <div class="value">{{ formatNumber(w.price_per_groom) }}</div>
                </div>
              </div>
            </div>

            <div class="capacity-bar">
              <div class="fill" :style="{ width: ((w.registrations_count || 0) / w.max_grooms * 100) + '%' }"></div>
            </div>
          </div>
        </div>
      </div>

      <div v-else class="empty-state">
        <PhHeart :size="64" weight="duotone" class="empty-state__icon" />
        <h4 class="empty-state__title">لا توجد أعراس مسجّلة</h4>
        <p class="empty-state__desc">ابدأ بإنشاء أول عرس جماعي على المنصة</p>
        <button class="btn-action btn-action--primary" @click="showModal = true">
          <PhPlusCircle :size="18" />
          إنشاء عرس جديد
        </button>
      </div>

      <!-- Create Modal -->
      <div v-if="showModal" class="admin-modal" @click.self="showModal = false">
        <div class="admin-modal__inner">
          <div class="admin-modal__header">
            <h3>إنشاء عرس جماعي جديد</h3>
            <button class="btn-close-x" @click="showModal = false">
              <PhX :size="20" />
            </button>
          </div>
          <div class="admin-modal__body">
            <div v-if="formError" class="dash-alert dash-alert--danger">
              <PhWarningCircle :size="20" weight="fill" class="dash-alert__icon" />
              <div class="dash-alert__content">{{ formError }}</div>
            </div>

            <div class="dash-form">
              <div class="dash-form__row">
                <div class="dash-form__group">
                  <label class="dash-form__label">عنوان العرس</label>
                  <input v-model="form.title" type="text" class="dash-form__input" placeholder="مثال: عرس القاهرة الجماعي - مايو 2026" required>
                </div>
                <div class="dash-form__group">
                  <label class="dash-form__label">المدينة</label>
                  <select v-model="form.city_id" class="dash-form__select" required>
                    <option value="">اختر</option>
                    <option v-for="c in cities" :key="c.id" :value="c.id">{{ c.name }}</option>
                  </select>
                </div>
              </div>
              <div class="dash-form__group">
                <label class="dash-form__label">اسم القاعة / المكان</label>
                <input v-model="form.venue_name" type="text" class="dash-form__input" required>
              </div>
              <div class="dash-form__row">
                <div class="dash-form__group">
                  <label class="dash-form__label">تاريخ العرس</label>
                  <input v-model="form.wedding_date" type="date" class="dash-form__input" required>
                </div>
                <div class="dash-form__group">
                  <label class="dash-form__label">آخر موعد للتسجيل</label>
                  <input v-model="form.registration_deadline" type="date" class="dash-form__input" required>
                </div>
              </div>
              <div class="dash-form__row">
                <div class="dash-form__group">
                  <label class="dash-form__label">الحد الأقصى للعرسان</label>
                  <input v-model.number="form.max_grooms" type="number" class="dash-form__input" min="5" required>
                </div>
                <div class="dash-form__group">
                  <label class="dash-form__label">السعر للعريس</label>
                  <input v-model.number="form.price_per_groom" type="number" class="dash-form__input" required>
                </div>
              </div>
              <div class="dash-form__group">
                <label class="dash-form__label">السعر الأصلي (للمقارنة)</label>
                <input v-model.number="form.original_price" type="number" class="dash-form__input" required>
              </div>
              <div class="dash-form__group">
                <label class="dash-form__label">الوصف</label>
                <textarea v-model="form.description" class="dash-form__textarea" placeholder="وصف العرس والخدمات المشمولة..."></textarea>
              </div>
            </div>
          </div>
          <div class="admin-modal__footer">
            <button class="btn-action btn-action--outline" @click="showModal = false">إلغاء</button>
            <button class="btn-action btn-action--primary" :disabled="creating" @click="createWedding">
              <span v-if="creating" class="spinner-border spinner-border-sm"></span>
              <PhCheckCircle :size="16" weight="bold" v-else />
              إنشاء العرس
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { useApi } from '@/composables/useApi'
import axios from 'axios'
import {
  PhArrowRight, PhHeart, PhPlusCircle, PhMapPin, PhBuildings,
  PhCalendar, PhUsers, PhCurrencyCircleDollar, PhX, PhWarningCircle,
  PhCheckCircle,
} from '@phosphor-icons/vue'

const { get, post, loading } = useApi()
const weddings = ref([])
const cities = ref([])
const creating = ref(false)
const formError = ref('')
const showModal = ref(false)

const form = reactive({
  title: '', city_id: '', venue_name: '', wedding_date: '', registration_deadline: '',
  max_grooms: 20, price_per_groom: 0, original_price: 0, description: '',
})

const statusLabel = (s) => ({ upcoming: 'قادم', full: 'مكتمل', completed: 'انتهى', cancelled: 'ملغي' })[s] || s
const statusBadgeClass = (s) => ({
  upcoming: 'status-badge--success',
  full: 'status-badge--warning',
  completed: 'status-badge--muted',
  cancelled: 'status-badge--danger',
})[s] || 'status-badge--muted'

function formatDate(d) {
  return new Date(d).toLocaleDateString('ar-EG', { year: 'numeric', month: 'short', day: 'numeric' })
}

function formatNumber(n) {
  return n ? new Intl.NumberFormat('ar-EG').format(n) : '—'
}

async function createWedding() {
  creating.value = true
  formError.value = ''
  try {
    await post('/api/admin/weddings', form)
    weddings.value = await get('/api/admin/weddings')
    showModal.value = false
  } catch (err) {
    formError.value = err.response?.data?.message || 'حدث خطأ'
  }
  creating.value = false
}

onMounted(async () => {
  try {
    const { data } = await axios.get('/api/calculator/cities')
    cities.value = data
  } catch {}
  try { weddings.value = await get('/api/admin/weddings') } catch {}
})
</script>

<style lang="scss" scoped>
.weddings-admin-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
  gap: 1.25rem;

  @media (max-width: 576px) { grid-template-columns: 1fr; }
}

.wedding-admin-card {
  background: #fff;
  border: 1px solid #f0ece4;
  border-radius: 16px;
  overflow: hidden;
  transition: all 0.25s;

  &:hover {
    border-color: rgba(192,57,43,0.25);
    box-shadow: 0 12px 32px rgba(13, 26, 42, 0.06);
  }

  &__header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 0.75rem;
    padding: 1.25rem 1.5rem;
    background: #faf9f6;
    border-bottom: 1px solid #f0ece4;
  }

  &__title {
    font-size: 1.05rem;
    font-weight: 800;
    color: #1a1a2a;
    margin: 0 0 0.4rem;
  }

  &__meta {
    display: flex;
    gap: 0.85rem;
    font-size: 0.78rem;
    color: #8888a0;
    flex-wrap: wrap;

    span {
      display: inline-flex;
      align-items: center;
      gap: 0.25rem;
    }
  }

  &__body { padding: 1.25rem 1.5rem; }
}

.wedding-info-row {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 0.5rem;
  margin-bottom: 0.85rem;
}

.info-item {
  display: flex;
  align-items: center;
  gap: 0.5rem;

  svg { color: #c0392b; flex-shrink: 0; }

  .label { font-size: 0.65rem; color: #8888a0; font-weight: 600; }
  .value { font-size: 0.85rem; font-weight: 800; color: #1a1a2a; }
}

.capacity-bar {
  height: 6px;
  background: #f0ece4;
  border-radius: 100px;
  overflow: hidden;

  .fill {
    height: 100%;
    background: linear-gradient(90deg, #c0392b 0%, #b8860b 100%);
    border-radius: 100px;
  }
}

// Modal
.admin-modal {
  position: fixed;
  inset: 0;
  background: rgba(13, 26, 42, 0.6);
  backdrop-filter: blur(4px);
  z-index: 1100;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 1rem;

  &__inner {
    background: #fff;
    border-radius: 20px;
    max-width: 720px;
    width: 100%;
    max-height: 90vh;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    box-shadow: 0 40px 80px rgba(0, 0, 0, 0.3);
  }

  &__header {
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid #f0ece4;
    display: flex;
    justify-content: space-between;
    align-items: center;

    h3 { margin: 0; font-weight: 800; }
  }

  &__body { padding: 1.5rem; overflow-y: auto; }
  &__footer {
    padding: 1.25rem 1.5rem;
    border-top: 1px solid #f0ece4;
    background: #faf9f6;
    display: flex;
    justify-content: flex-end;
    gap: 0.5rem;
  }
}

.btn-close-x {
  background: #faf9f6;
  border: 1.5px solid #e0d8cc;
  width: 36px;
  height: 36px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  color: #4a4a5e;

  &:hover { background: #f0ece4; color: #c0392b; }
}
</style>
