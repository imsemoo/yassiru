<template>
  <div class="dashboard-page">
    <div class="container">
      <router-link to="/recommender" class="page-header__back">
        <PhArrowRight :size="14" weight="bold" />
        العودة للوحة المعرّف
      </router-link>

      <div class="page-header">
        <div>
          <h1 class="page-header__title">
            <span class="page-header__title__icon" style="--header-bg: rgba(192,57,43,0.1); --header-color: #c0392b">
              <PhUsersFour :size="24" weight="duotone" />
            </span>
            طلبات الأهل
          </h1>
          <p class="page-header__subtitle">إدارة طلبات العائلات على التوصيات الخاصة بك</p>
        </div>
      </div>

      <div v-if="loading" class="loading-state">
        <div class="spinner-border"></div>
        <p class="loading-state__text">جاري التحميل...</p>
      </div>

      <div v-else-if="requests.length" class="requests-grid">
        <div v-for="req in requests" :key="req.id" class="request-card">
          <div class="request-card__header">
            <div class="request-card__pair">
              <span class="name">{{ req.recommendation?.male_candidate?.name }}</span>
              <PhHeart :size="14" weight="fill" class="text-danger" />
              <span class="name">{{ req.recommendation?.female_candidate?.name }}</span>
            </div>
            <span class="status-badge" :class="statusBadgeClass(req.status)">
              {{ statusLabel(req.status) }}
            </span>
          </div>

          <div class="request-card__body">
            <div class="request-card__info">
              <div class="info-item">
                <PhUser :size="14" />
                <span>المبادرة من: <strong>{{ initiatedByLabel(req.initiated_by) }}</strong></span>
              </div>
              <div v-if="req.meeting_date" class="info-item">
                <PhCalendar :size="14" />
                <span>موعد اللقاء: <strong>{{ formatDate(req.meeting_date) }}</strong></span>
              </div>
              <div v-if="req.meeting_location" class="info-item">
                <PhMapPin :size="14" />
                <span>{{ req.meeting_location }}</span>
              </div>
            </div>

            <p v-if="req.notes" class="request-card__notes">
              <PhNotePencil :size="14" />
              {{ req.notes }}
            </p>
          </div>

          <div v-if="req.status === 'pending'" class="request-card__actions">
            <button class="btn-action btn-action--primary btn-action--sm" @click="respond(req, 'accepted')">
              <PhCheck :size="14" weight="bold" />
              قبول
            </button>
            <button class="btn-action btn-action--outline btn-action--sm" @click="respond(req, 'meeting_scheduled')">
              <PhCalendarPlus :size="14" weight="bold" />
              جدولة لقاء
            </button>
            <button class="btn-action btn-action--danger btn-action--sm" @click="respond(req, 'rejected')">
              <PhX :size="14" weight="bold" />
              رفض
            </button>
          </div>
        </div>
      </div>

      <div v-else class="empty-state">
        <PhUsersFour :size="64" weight="duotone" class="empty-state__icon" />
        <h4 class="empty-state__title">لا توجد طلبات حالياً</h4>
        <p class="empty-state__desc">ستظهر هنا طلبات الأهل بعد إنشاء توصيات وموافقة العائلات على البدء</p>
        <router-link to="/recommender/suggestions" class="btn-action btn-action--primary">
          <PhMagnifyingGlass :size="18" />
          استعرض الاقتراحات
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useApi } from '@/composables/useApi'
import {
  PhArrowRight, PhUsersFour, PhHeart, PhUser, PhCalendar, PhMapPin,
  PhNotePencil, PhCheck, PhCalendarPlus, PhX, PhMagnifyingGlass,
} from '@phosphor-icons/vue'

const { get, put, loading } = useApi()
const requests = ref([])

const statusLabel = (s) => ({
  pending: 'قيد الانتظار',
  accepted: 'مقبول',
  rejected: 'مرفوض',
  meeting_scheduled: 'لقاء مجدول',
})[s] || s

const statusBadgeClass = (s) => ({
  pending: 'status-badge--warning',
  accepted: 'status-badge--success',
  rejected: 'status-badge--danger',
  meeting_scheduled: 'status-badge--info',
})[s] || 'status-badge--muted'

const initiatedByLabel = (s) => ({
  male_family: 'عائلة الشاب',
  female_family: 'عائلة الفتاة',
})[s] || s

function formatDate(d) {
  return new Date(d).toLocaleDateString('ar-EG', { year: 'numeric', month: 'long', day: 'numeric' })
}

async function respond(req, status) {
  try {
    await put(`/api/recommender/family-requests/${req.id}`, { status })
    req.status = status
  } catch { /* handled */ }
}

onMounted(async () => {
  try {
    requests.value = await get('/api/recommender/family-requests')
  } catch { /* handled */ }
})
</script>

<style lang="scss" scoped>
.requests-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(420px, 1fr));
  gap: 1.25rem;

  @media (max-width: 576px) { grid-template-columns: 1fr; }
}

.request-card {
  background: #fff;
  border: 1px solid #f0ece4;
  border-radius: 16px;
  overflow: hidden;
  transition: all 0.25s;

  &:hover {
    border-color: #e0d8cc;
    box-shadow: 0 12px 32px rgba(13, 26, 42, 0.06);
  }

  &__header {
    padding: 1.25rem 1.5rem;
    background: #faf9f6;
    border-bottom: 1px solid #f0ece4;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.75rem;
    flex-wrap: wrap;
  }

  &__pair {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.95rem;

    .name { font-weight: 800; color: #1a1a2a; }
  }

  &__body { padding: 1.25rem 1.5rem; }

  &__info {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;

    .info-item {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      font-size: 0.85rem;
      color: #4a4a5e;
      svg { color: #8888a0; flex-shrink: 0; }
    }
  }

  &__notes {
    margin-top: 1rem;
    padding: 0.75rem 1rem;
    background: #faf9f6;
    border-right: 3px solid #b8860b;
    border-radius: 8px;
    font-size: 0.85rem;
    color: #4a4a5e;
    display: flex;
    align-items: flex-start;
    gap: 0.5rem;

    svg { color: #b8860b; flex-shrink: 0; margin-top: 2px; }
  }

  &__actions {
    padding: 1rem 1.5rem;
    border-top: 1px solid #f0ece4;
    background: #faf9f6;
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
  }
}
</style>
