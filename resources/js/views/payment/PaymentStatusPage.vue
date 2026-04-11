<template>
  <div class="dashboard-page">
    <div class="container" style="max-width: 600px">
      <div class="page-header" style="text-align: center">
        <h1 class="page-header__title">
          <span class="page-header__title__icon" :style="iconStyle">
            <component :is="statusIcon" :size="24" weight="duotone" />
          </span>
          حالة الدفع
        </h1>
      </div>

      <!-- Loading -->
      <div v-if="loading && !payment" class="loading-state">
        <div class="spinner-border"></div>
        <p class="loading-state__text">جاري التحقق من حالة الدفع...</p>
      </div>

      <!-- Payment Status -->
      <div v-else-if="payment" class="dash-card">
        <div class="dash-card__body" style="text-align: center; padding: 2rem 1.5rem">

          <!-- Paid -->
          <div v-if="payment.status === 'paid'" class="status-result status-result--success">
            <div class="status-result__icon"><PhCheckCircle :size="64" weight="fill" /></div>
            <h2>تم الدفع بنجاح!</h2>
            <p>المبلغ: <strong>{{ formatNumber(payment.amount) }} {{ payment.currency }}</strong></p>
            <p class="status-result__ref">رقم المرجع: <code>{{ payment.merchant_ref }}</code></p>
          </div>

          <!-- Processing / Pending -->
          <div v-else-if="payment.status === 'pending' || payment.status === 'processing'" class="status-result status-result--pending">
            <div class="status-result__icon"><PhHourglass :size="64" weight="duotone" /></div>
            <h2>في انتظار الدفع</h2>
            <p>المبلغ: <strong>{{ formatNumber(payment.amount) }} {{ payment.currency }}</strong></p>

            <!-- Fawry Reference Code -->
            <div v-if="payment.fawry_ref_code" class="fawry-ref-box">
              <p class="fawry-ref-box__label">كود الدفع عبر فوري</p>
              <code class="fawry-ref-box__code">{{ payment.fawry_ref_code }}</code>
              <p class="fawry-ref-box__hint">
                ادفع في أي فرع فوري أو عبر تطبيق فوري
                <br>ينتهي الكود: {{ formatDate(payment.expires_at) }}
              </p>
            </div>

            <div v-if="polling" class="polling-indicator">
              <div class="spinner-border spinner-border-sm"></div>
              <span>يتم التحقق تلقائياً...</span>
            </div>
          </div>

          <!-- Failed -->
          <div v-else-if="payment.status === 'failed'" class="status-result status-result--failed">
            <div class="status-result__icon"><PhXCircle :size="64" weight="fill" /></div>
            <h2>فشلت عملية الدفع</h2>
            <p>حاول مرة أخرى أو اختر طريقة دفع مختلفة</p>
          </div>

          <!-- Expired -->
          <div v-else-if="payment.status === 'expired'" class="status-result status-result--failed">
            <div class="status-result__icon"><PhClockCountdown :size="64" weight="duotone" /></div>
            <h2>انتهت صلاحية عملية الدفع</h2>
            <p>يمكنك بدء عملية دفع جديدة</p>
          </div>

          <button class="btn-action btn-action--primary" style="margin-top: 1.5rem" @click="goBack">
            <PhArrowRight :size="16" weight="bold" />
            العودة
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { usePayment } from '@/composables/usePayment'
import {
  PhCheckCircle, PhHourglass, PhXCircle, PhClockCountdown, PhArrowRight,
  PhCreditCard, PhWarningCircle,
} from '@phosphor-icons/vue'

const route = useRoute()
const router = useRouter()
const { checkPaymentStatus } = usePayment()

const payment = ref(null)
const loading = ref(true)
const polling = ref(false)
let pollInterval = null

const statusIcon = computed(() => {
  if (!payment.value) return PhCreditCard
  return ({ paid: PhCheckCircle, pending: PhHourglass, processing: PhHourglass, failed: PhXCircle, expired: PhClockCountdown })[payment.value.status] || PhWarningCircle
})

const iconStyle = computed(() => {
  if (!payment.value) return '--header-bg: rgba(13,115,119,0.1); --header-color: #0d7377'
  const styles = {
    paid: '--header-bg: rgba(27,122,74,0.1); --header-color: #1b7a4a',
    pending: '--header-bg: rgba(184,134,11,0.1); --header-color: #b8860b',
    processing: '--header-bg: rgba(184,134,11,0.1); --header-color: #b8860b',
    failed: '--header-bg: rgba(192,57,43,0.1); --header-color: #c0392b',
    expired: '--header-bg: rgba(107,114,128,0.1); --header-color: #6b7280',
  }
  return styles[payment.value.status] || styles.pending
})

function formatNumber(n) {
  return new Intl.NumberFormat('ar-EG').format(n)
}

function formatDate(d) {
  if (!d) return ''
  return new Date(d).toLocaleDateString('ar-EG', { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' })
}

function goBack() {
  if (window.history.length > 2) {
    router.go(-2)
  } else {
    router.push('/')
  }
}

async function fetchStatus() {
  const data = await checkPaymentStatus(route.params.uuid)
  if (data) {
    payment.value = data
    if (data.status === 'paid' || data.status === 'failed' || data.status === 'expired') {
      stopPolling()
    }
  }
}

function startPolling() {
  polling.value = true
  pollInterval = setInterval(fetchStatus, 5000) // every 5 seconds
}

function stopPolling() {
  polling.value = false
  if (pollInterval) {
    clearInterval(pollInterval)
    pollInterval = null
  }
}

onMounted(async () => {
  await fetchStatus()
  loading.value = false
  if (payment.value && (payment.value.status === 'pending' || payment.value.status === 'processing')) {
    startPolling()
  }
})

onBeforeUnmount(() => {
  stopPolling()
})
</script>

<style lang="scss" scoped>
.status-result {
  &__icon { margin-bottom: 1rem; }
  &--success &__icon { color: #1b7a4a; }
  &--pending &__icon { color: #b8860b; }
  &--failed &__icon { color: #c0392b; }

  h2 { font-size: 1.4rem; font-weight: 900; color: #1a1a2a; margin-bottom: 0.5rem; }
  p { color: #4a4a5e; margin-bottom: 0.5rem; }
  &__ref { font-size: 0.85rem; color: #8888a0; }
  &__ref code { color: #0d7377; font-weight: 700; }
}

.fawry-ref-box {
  background: rgba(184,134,11,0.06);
  border: 2px dashed #b8860b;
  border-radius: 16px;
  padding: 1.5rem;
  margin: 1.5rem 0;

  &__label { font-size: 0.85rem; color: #b8860b; font-weight: 700; margin-bottom: 0.5rem; }
  &__code {
    display: block;
    font-size: 2rem;
    font-weight: 900;
    color: #1a1a2a;
    letter-spacing: 4px;
    margin-bottom: 0.75rem;
  }
  &__hint { font-size: 0.8rem; color: #8888a0; line-height: 1.6; margin: 0; }
}

.polling-indicator {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.85rem;
  color: #8888a0;
  margin-top: 1rem;
}
</style>
