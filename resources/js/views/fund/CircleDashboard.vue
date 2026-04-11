<template>
  <div class="dashboard-page">
    <div class="container">
      <div v-if="loading" class="loading-state">
        <div class="spinner-border"></div>
        <p class="loading-state__text">جاري تحميل تفاصيل الحلقة...</p>
      </div>

      <div v-else-if="data">
        <router-link to="/circles" class="page-header__back">
          <PhArrowRight :size="14" weight="bold" />
          العودة للحلقات
        </router-link>

        <div class="page-header">
          <div>
            <h1 class="page-header__title">
              <span class="page-header__title__icon" style="--header-bg: rgba(184,134,11,0.1); --header-color: #b8860b">
                <PhCirclesThree :size="24" weight="duotone" />
              </span>
              {{ data.circle.name }}
            </h1>
            <p class="page-header__subtitle">
              <PhMapPin :size="13" class="ms-1" />
              {{ data.circle.city?.name }}
            </p>
          </div>
          <div class="page-header__actions">
            <span class="status-badge" :class="statusBadgeClass(data.circle.status)">
              <PhCircle :size="8" weight="fill" />
              {{ statusLabel(data.circle.status) }}
            </span>
          </div>
        </div>

        <!-- Stats -->
        <div class="stats-grid">
          <div class="stat-tile" style="--tile-color: #b8860b; --tile-bg: rgba(184,134,11,0.1)">
            <div class="stat-tile__icon"><PhCurrencyCircleDollar :size="24" weight="duotone" /></div>
            <div class="stat-tile__content">
              <div class="stat-tile__value">{{ formatNumber(data.circle.monthly_amount) }}</div>
              <div class="stat-tile__label">{{ data.circle.currency }} شهرياً</div>
            </div>
          </div>
          <div class="stat-tile" style="--tile-color: #1b7a4a; --tile-bg: rgba(27,122,74,0.1)">
            <div class="stat-tile__icon"><PhTrophy :size="24" weight="duotone" /></div>
            <div class="stat-tile__content">
              <div class="stat-tile__value">{{ formatNumber(data.total_payout) }}</div>
              <div class="stat-tile__label">إجمالي القبض</div>
            </div>
          </div>
          <div class="stat-tile" style="--tile-color: #1565c0; --tile-bg: rgba(21,101,192,0.1)">
            <div class="stat-tile__icon"><PhUsers :size="24" weight="duotone" /></div>
            <div class="stat-tile__content">
              <div class="stat-tile__value">{{ data.members?.length || 0 }} / {{ data.circle.max_members }}</div>
              <div class="stat-tile__label">الأعضاء</div>
            </div>
          </div>
          <div class="stat-tile" style="--tile-color: #0d7377; --tile-bg: rgba(13,115,119,0.1)">
            <div class="stat-tile__icon"><PhClock :size="24" weight="duotone" /></div>
            <div class="stat-tile__content">
              <div class="stat-tile__value">{{ data.circle.current_round || '-' }} / {{ data.circle.cycle_months }}</div>
              <div class="stat-tile__label">الجولة الحالية</div>
            </div>
          </div>
        </div>

        <!-- Pending Contribution -->
        <div v-if="pendingContribution && data.circle.status === 'active'" class="dash-alert dash-alert--warning">
          <PhBellRinging :size="20" weight="fill" class="dash-alert__icon" />
          <div class="dash-alert__content w-100">
            <div class="dash-alert__title">مساهمة مستحقة — الجولة {{ data.circle.current_round }}</div>
            <p class="mb-3">يجب دفع {{ formatNumber(data.circle.monthly_amount) }} {{ data.circle.currency }} قبل نهاية الموعد</p>
            <div class="d-flex gap-2 align-items-end flex-wrap">
              <button class="btn-action btn-action--primary" :disabled="paymentLoading" @click="payContribution">
                <span v-if="paymentLoading" class="spinner-border spinner-border-sm"></span>
                <PhCreditCard :size="16" weight="bold" v-else />
                ادفع الآن عبر فوري
              </button>
            </div>
            <p v-if="paymentError" class="text-danger mt-2" style="font-size: 0.85rem">{{ paymentError }}</p>
          </div>
        </div>

        <div v-if="successMsg" class="dash-alert dash-alert--success">
          <PhCheckCircle :size="20" weight="fill" class="dash-alert__icon" />
          <div class="dash-alert__content">{{ successMsg }}</div>
        </div>

        <!-- My Membership -->
        <div class="dash-card">
          <div class="dash-card__header">
            <h3 class="dash-card__header__title">
              <PhUserCircle :size="20" />
              عضويتي في الحلقة
            </h3>
          </div>
          <div class="dash-card__body">
            <div class="my-membership">
              <div class="my-membership__item">
                <PhListNumbers :size="20" weight="duotone" />
                <div>
                  <div class="label">ترتيب الصرف</div>
                  <div class="value">#{{ data.my_membership.payout_order }}</div>
                </div>
              </div>
              <div class="my-membership__item">
                <PhCurrencyCircleDollar :size="20" weight="duotone" />
                <div>
                  <div class="label">إجمالي مساهماتي</div>
                  <div class="value">{{ formatNumber(data.my_membership.total_contributed) }} {{ data.circle.currency }}</div>
                </div>
              </div>
              <div class="my-membership__item">
                <PhCheckCircle :size="20" weight="duotone" />
                <div>
                  <div class="label">حالة الصرف</div>
                  <div class="value">
                    <span class="status-badge" :class="data.my_membership.has_received_payout ? 'status-badge--success' : 'status-badge--warning'">
                      {{ data.my_membership.has_received_payout ? 'تم الصرف' : 'لم يتم بعد' }}
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="row g-4">
          <!-- Timeline -->
          <div class="col-lg-7">
            <div class="dash-card">
              <div class="dash-card__header">
                <h3 class="dash-card__header__title">
                  <PhCalendar :size="20" />
                  الجدول الزمني للحلقة
                </h3>
              </div>
              <div class="dash-card__body--flush">
                <div class="timeline-list">
                  <div v-for="t in data.timeline" :key="t.round" class="timeline-row" :class="`is-${t.status}`">
                    <div class="timeline-row__circle">
                      <PhCheckCircle :size="18" weight="fill" v-if="t.status === 'done'" />
                      <PhCircle :size="18" weight="bold" v-else-if="t.status === 'current'" />
                      <span v-else>{{ t.round }}</span>
                    </div>
                    <div class="timeline-row__content">
                      <div class="timeline-row__name">{{ t.name }}</div>
                      <div class="timeline-row__round">الجولة {{ t.round }}</div>
                    </div>
                    <span class="status-badge" :class="timelineBadgeClass(t.status)">
                      {{ timelineLabel(t.status) }}
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- My Contributions -->
          <div class="col-lg-5">
            <div class="dash-card">
              <div class="dash-card__header">
                <h3 class="dash-card__header__title">
                  <PhReceipt :size="20" />
                  سجل مساهماتي
                </h3>
                <span class="dash-card__header__meta">{{ data.contributions?.length || 0 }} مساهمة</span>
              </div>
              <div class="dash-card__body--flush">
                <div v-if="data.contributions?.length" class="table-wrapper">
                  <table class="dash-table">
                    <thead>
                      <tr>
                        <th>الجولة</th>
                        <th>المبلغ</th>
                        <th>الحالة</th>
                        <th>التاريخ</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="c in data.contributions" :key="c.id">
                        <td><strong>#{{ c.round_number }}</strong></td>
                        <td>{{ formatNumber(c.amount) }}</td>
                        <td>
                          <span class="status-badge" :class="c.status === 'paid' ? 'status-badge--success' : 'status-badge--warning'">
                            {{ c.status === 'paid' ? 'مدفوع' : 'مستحق' }}
                          </span>
                        </td>
                        <td class="text-muted small">{{ c.paid_at ? formatDate(c.paid_at) : '—' }}</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
                <div v-else class="empty-state" style="padding: 2rem 1rem">
                  <p class="empty-state__desc mb-0">لا توجد مساهمات بعد</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { useApi } from '@/composables/useApi'
import {
  PhArrowRight, PhCirclesThree, PhMapPin, PhCircle, PhCurrencyCircleDollar,
  PhTrophy, PhUsers, PhClock, PhBellRinging, PhCheckCircle, PhUserCircle,
  PhListNumbers, PhCalendar, PhReceipt,
  PhCreditCard,
} from '@phosphor-icons/vue'
import { usePayment } from '@/composables/usePayment'

const route = useRoute()
const { get, post, loading } = useApi()
const { initiatePayment, paymentLoading, paymentError } = usePayment()
const data = ref(null)
const successMsg = ref('')

const statusLabel = (s) => ({ forming: 'قيد التشكيل', active: 'نشطة', completed: 'مكتملة' })[s] || s
const statusBadgeClass = (s) => ({
  forming: 'status-badge--warning',
  active: 'status-badge--success',
  completed: 'status-badge--muted',
})[s] || 'status-badge--muted'

const timelineLabel = (s) => ({ done: 'تم الصرف', current: 'الجولة الحالية', pending: 'قادم' })[s] || s
const timelineBadgeClass = (s) => ({
  done: 'status-badge--success',
  current: 'status-badge--info',
  pending: 'status-badge--muted',
})[s] || 'status-badge--muted'

const pendingContribution = computed(() => {
  if (!data.value?.contributions) return null
  return data.value.contributions.find(c => c.status === 'pending' && c.round_number === data.value.circle.current_round)
})

function formatNumber(num) {
  return new Intl.NumberFormat('ar-EG').format(num)
}

function formatDate(d) {
  return new Date(d).toLocaleDateString('ar-EG', { year: 'numeric', month: 'short', day: 'numeric' })
}

async function payContribution() {
  if (!pendingContribution.value) return
  await initiatePayment('contribution', pendingContribution.value.id)
}

onMounted(async () => {
  try {
    data.value = await get(`/api/circles/${route.params.id}/dashboard`)
  } catch { /* handled */ }
})
</script>

<style lang="scss" scoped>
.my-membership {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1rem;

  @media (max-width: 768px) { grid-template-columns: 1fr; }

  &__item {
    display: flex;
    align-items: center;
    gap: 0.85rem;
    padding: 1rem;
    background: #faf9f6;
    border: 1px solid #f0ece4;
    border-radius: 12px;

    svg { color: #b8860b; flex-shrink: 0; }

    .label {
      font-size: 0.78rem;
      color: #8888a0;
      font-weight: 600;
      margin-bottom: 0.2rem;
    }

    .value {
      font-size: 1.05rem;
      font-weight: 800;
      color: #1a1a2a;
    }
  }
}

.timeline-list {
  display: flex;
  flex-direction: column;
}

.timeline-row {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1rem 1.5rem;
  border-bottom: 1px solid #f0ece4;
  transition: background 0.15s;

  &:last-child { border-bottom: none; }
  &:hover { background: #faf9f6; }

  &__circle {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: #faf9f6;
    border: 2px solid #e0d8cc;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    color: #8888a0;
    flex-shrink: 0;
    transition: all 0.2s;
  }

  &.is-done &__circle {
    background: rgba(27,122,74,0.1);
    border-color: #1b7a4a;
    color: #1b7a4a;
  }

  &.is-current &__circle {
    background: rgba(13,115,119,0.1);
    border-color: #0d7377;
    color: #0d7377;
    animation: pulse 2s infinite;
  }

  @keyframes pulse {
    0%, 100% { box-shadow: 0 0 0 0 rgba(13,115,119,0.4); }
    50% { box-shadow: 0 0 0 6px rgba(13,115,119,0); }
  }

  &__content { flex: 1; }
  &__name {
    font-weight: 700;
    color: #1a1a2a;
  }
  &__round {
    font-size: 0.78rem;
    color: #8888a0;
  }

  &.is-current &__name { color: #0d7377; font-weight: 800; }
}
</style>
