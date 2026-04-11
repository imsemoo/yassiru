<template>
  <div class="dashboard-page">
    <div class="container">
      <div class="page-header">
        <div>
          <h1 class="page-header__title">
            <span class="page-header__title__icon" style="--header-bg: rgba(192,57,43,0.1); --header-color: #c0392b">
              <PhHeart :size="24" weight="duotone" />
            </span>
            تسجيلاتي في الأعراس
          </h1>
          <p class="page-header__subtitle">إدارة جميع تسجيلاتك في الأعراس الجماعية</p>
        </div>
        <div class="page-header__actions">
          <router-link to="/weddings" class="btn-action btn-action--outline">
            <PhMagnifyingGlass :size="16" />
            تصفّح الأعراس
          </router-link>
        </div>
      </div>

      <div v-if="loading" class="loading-state">
        <div class="spinner-border"></div>
        <p class="loading-state__text">جاري تحميل تسجيلاتك...</p>
      </div>

      <div v-else-if="registrations.length" class="registrations-grid">
        <div v-for="r in registrations" :key="r.id" class="reg-card">
          <div class="reg-card__header">
            <div>
              <h3 class="reg-card__title">{{ r.wedding?.title }}</h3>
              <div class="reg-card__location">
                <PhBuildings :size="13" />
                {{ r.wedding?.venue_name }}
              </div>
            </div>
            <span class="status-badge" :class="paymentBadgeClass(r.payment_status)">
              {{ paymentLabel(r.payment_status) }}
            </span>
          </div>

          <div class="reg-card__details">
            <div class="reg-card__detail">
              <PhCalendar :size="16" />
              <div>
                <div class="label">تاريخ العرس</div>
                <div class="value">{{ formatDate(r.wedding?.wedding_date) }}</div>
              </div>
            </div>
            <div class="reg-card__detail">
              <PhCurrencyCircleDollar :size="16" />
              <div>
                <div class="label">التكلفة</div>
                <div class="value">{{ formatNumber(r.wedding?.price_per_groom) }} ج.م</div>
              </div>
            </div>
          </div>

          <!-- Pending Payment -->
          <div v-if="r.payment_status === 'pending'" class="reg-card__payment">
            <button
              class="btn-action btn-action--primary"
              :disabled="paymentLoading"
              @click="payWedding(r)"
            >
              <span v-if="paymentLoading" class="spinner-border spinner-border-sm"></span>
              <PhCreditCard :size="16" weight="bold" v-else />
              ادفع الآن عبر فوري
            </button>
            <p v-if="paymentError" class="text-danger mt-2" style="font-size: 0.85rem">{{ paymentError }}</p>
          </div>

          <div class="reg-card__actions">
            <button
              v-if="r.payment_status !== 'paid'"
              class="btn-action btn-action--danger btn-action--sm"
              :disabled="cancelling === r.id"
              @click="cancel(r)"
            >
              <PhX :size="14" weight="bold" />
              إلغاء التسجيل
            </button>
            <router-link
              :to="`/weddings/${r.wedding?.id || r.wedding_id}`"
              class="btn-action btn-action--outline btn-action--sm"
            >
              <PhEye :size="14" />
              عرض التفاصيل
            </router-link>
          </div>
        </div>
      </div>

      <div v-else class="empty-state">
        <PhHeart :size="64" weight="duotone" class="empty-state__icon" />
        <h4 class="empty-state__title">لم تسجّل في أي عرس بعد</h4>
        <p class="empty-state__desc">تصفّح الأعراس الجماعية المتاحة وسجّل في المناسب لك</p>
        <router-link to="/weddings" class="btn-action btn-action--primary">
          <PhMagnifyingGlass :size="18" />
          تصفّح الأعراس
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useApi } from '@/composables/useApi'
import { usePayment } from '@/composables/usePayment'
import {
  PhHeart, PhMagnifyingGlass, PhBuildings, PhCalendar, PhCurrencyCircleDollar,
  PhCreditCard, PhCheckCircle, PhX, PhEye,
} from '@phosphor-icons/vue'

const { get, post, del, loading } = useApi()
const { initiatePayment, paymentLoading, paymentError } = usePayment()
const registrations = ref([])
const cancelling = ref(null)

const paymentLabel = (s) => ({
  pending: 'في انتظار الدفع',
  partial: 'دفع جزئي',
  paid: 'مدفوع',
  refunded: 'مسترد',
})[s] || s

const paymentBadgeClass = (s) => ({
  pending: 'status-badge--warning',
  partial: 'status-badge--info',
  paid: 'status-badge--success',
  refunded: 'status-badge--muted',
})[s] || 'status-badge--muted'

function formatDate(d) {
  if (!d) return '—'
  return new Date(d).toLocaleDateString('ar-EG', { year: 'numeric', month: 'long', day: 'numeric' })
}

function formatNumber(n) {
  return n ? new Intl.NumberFormat('ar-EG').format(n) : '—'
}

async function payWedding(r) {
  await initiatePayment('wedding', r.id)
}

async function cancel(r) {
  if (!confirm('هل أنت متأكد من إلغاء التسجيل؟')) return
  cancelling.value = r.id
  try {
    await del(`/api/wedding-registrations/${r.id}`)
    registrations.value = registrations.value.filter(reg => reg.id !== r.id)
  } catch { /* handled */ }
  cancelling.value = null
}

onMounted(async () => {
  try {
    const data = await get('/api/my-weddings')
    registrations.value = (Array.isArray(data) ? data : []).map(r => ({ ...r, _payRef: '' }))
  } catch { /* handled */ }
})
</script>

<style lang="scss" scoped>
.registrations-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
  gap: 1.25rem;

  @media (max-width: 576px) { grid-template-columns: 1fr; }
}

.reg-card {
  background: #fff;
  border: 1px solid #f0ece4;
  border-radius: 16px;
  padding: 1.5rem;
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
    margin-bottom: 1rem;
  }

  &__title {
    font-size: 1.05rem;
    font-weight: 800;
    color: #1a1a2a;
    margin: 0 0 0.25rem;
  }

  &__location {
    font-size: 0.78rem;
    color: #8888a0;
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
  }

  &__details {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.75rem;
    padding: 1rem 0;
    border-top: 1px solid #f0ece4;
    border-bottom: 1px solid #f0ece4;
    margin-bottom: 1rem;
  }

  &__detail {
    display: flex;
    align-items: center;
    gap: 0.5rem;

    svg { color: #c0392b; flex-shrink: 0; }

    .label { font-size: 0.7rem; color: #8888a0; font-weight: 600; }
    .value { font-size: 0.85rem; font-weight: 800; color: #1a1a2a; }
  }

  &__payment {
    background: #faf9f6;
    border-radius: 12px;
    padding: 0.85rem;
    margin-bottom: 1rem;
  }

  &__actions {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;

    .btn-action { flex: 1; justify-content: center; }
  }
}
</style>
