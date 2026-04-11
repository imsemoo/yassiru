<template>
  <div class="dashboard-page">
    <div class="container" style="max-width: 960px">
      <div class="page-header">
        <div>
          <h1 class="page-header__title">
            <span class="page-header__title__icon" style="--header-bg: rgba(27,122,74,0.1); --header-color: #1b7a4a">
              <PhChats :size="24" weight="duotone" />
            </span>
            لوحة المستشار
          </h1>
          <p class="page-header__subtitle">
            مرحباً {{ auth.user?.name }} — هذه قائمة الجلسات المحجوزة
          </p>
        </div>
      </div>

      <!-- Stats -->
      <div class="stats-grid">
        <div class="stat-tile stat-tile--success">
          <div class="stat-tile__icon"><PhCalendarCheck :size="24" weight="duotone" /></div>
          <div class="stat-tile__content">
            <div class="stat-tile__value">{{ stats.upcoming }}</div>
            <div class="stat-tile__label">جلسات قادمة</div>
          </div>
        </div>
        <div class="stat-tile stat-tile--info">
          <div class="stat-tile__icon"><PhClock :size="24" weight="duotone" /></div>
          <div class="stat-tile__content">
            <div class="stat-tile__value">{{ stats.today }}</div>
            <div class="stat-tile__label">جلسات اليوم</div>
          </div>
        </div>
        <div class="stat-tile stat-tile--muted">
          <div class="stat-tile__icon"><PhCheckCircle :size="24" weight="duotone" /></div>
          <div class="stat-tile__content">
            <div class="stat-tile__value">{{ stats.completed }}</div>
            <div class="stat-tile__label">مكتملة</div>
          </div>
        </div>
      </div>

      <!-- Filter Tabs -->
      <div class="filter-tabs">
        <button
          v-for="tab in filterTabs"
          :key="tab.value"
          class="filter-tab"
          :class="{ 'filter-tab--active': filter === tab.value }"
          @click="filter = tab.value"
        >
          {{ tab.label }}
        </button>
      </div>

      <!-- Sessions List -->
      <div v-if="loading" class="loading-state">
        <div class="spinner-border"></div>
        <p class="loading-state__text">جاري تحميل الجلسات...</p>
      </div>

      <div v-else-if="sessions.length === 0" class="empty-state">
        <PhChats :size="56" weight="duotone" class="empty-state__icon" />
        <h4 class="empty-state__title">لا توجد جلسات</h4>
        <p class="empty-state__desc">لم يتم حجز أي جلسات في هذا التصنيف بعد</p>
      </div>

      <div v-else class="sessions-list">
        <div
          v-for="session in sessions"
          :key="session.id"
          class="session-card"
        >
          <div class="session-card__header">
            <div class="session-card__user">
              <div class="session-card__avatar">
                {{ (session.user?.name || '؟').charAt(0) }}
              </div>
              <div>
                <h3 class="session-card__name">{{ session.user?.name || 'مستخدم' }}</h3>
                <p class="session-card__phone">
                  <PhPhone :size="12" />
                  {{ session.user?.phone || '—' }}
                </p>
              </div>
            </div>
            <span class="status-badge" :class="statusClass(session.status)">
              {{ statusLabel(session.status) }}
            </span>
          </div>

          <div class="session-card__meta">
            <div class="session-card__meta-item">
              <PhCalendar :size="14" />
              {{ formatDate(session.scheduled_at) }}
            </div>
            <div class="session-card__meta-item">
              <PhClock :size="14" />
              {{ formatTime(session.scheduled_at) }}
            </div>
            <div class="session-card__meta-item">
              <PhTag :size="14" />
              {{ typeLabel(session.type) }}
            </div>
          </div>

          <div v-if="session.notes" class="session-card__notes">
            <strong>ملاحظات:</strong> {{ session.notes }}
          </div>

          <div v-if="session.status === 'scheduled'" class="session-card__actions">
            <button
              class="btn-action btn-action--primary btn-action--sm"
              :disabled="completing === session.id"
              @click="completeSession(session)"
            >
              <span v-if="completing === session.id" class="spinner-border spinner-border-sm"></span>
              <PhCheckCircle :size="14" weight="bold" v-else />
              تسجيل إتمام الجلسة
            </button>
          </div>
        </div>
      </div>

      <!-- Pagination -->
      <div v-if="pagination.last_page > 1" class="pagination-wrap">
        <button
          class="btn-action btn-action--sm"
          :disabled="pagination.current_page === 1"
          @click="loadPage(pagination.current_page - 1)"
        >
          السابق
        </button>
        <span class="pagination-info">
          صفحة {{ pagination.current_page }} من {{ pagination.last_page }}
        </span>
        <button
          class="btn-action btn-action--sm"
          :disabled="pagination.current_page === pagination.last_page"
          @click="loadPage(pagination.current_page + 1)"
        >
          التالي
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useApi } from '@/composables/useApi'
import { useAuthStore } from '@/stores/auth'
import {
  PhChats, PhCalendarCheck, PhClock, PhCheckCircle,
  PhCalendar, PhTag, PhPhone,
} from '@phosphor-icons/vue'

const auth = useAuthStore()
const { get, put } = useApi()
const sessions = ref([])
const loading = ref(true)
const completing = ref(null)
const filter = ref('scheduled')
const pagination = ref({ current_page: 1, last_page: 1, total: 0 })

const filterTabs = [
  { value: 'scheduled', label: 'المحجوزة' },
  { value: 'completed', label: 'المكتملة' },
  { value: 'cancelled', label: 'الملغاة' },
  { value: '', label: 'الكل' },
]

const stats = computed(() => {
  const today = new Date().toDateString()
  return {
    upcoming: sessions.value.filter(s => s.status === 'scheduled' && new Date(s.scheduled_at) > new Date()).length,
    today: sessions.value.filter(s => new Date(s.scheduled_at).toDateString() === today && s.status === 'scheduled').length,
    completed: sessions.value.filter(s => s.status === 'completed').length,
  }
})

function statusLabel(status) {
  return {
    scheduled: 'محجوزة',
    in_progress: 'جارية',
    completed: 'مكتملة',
    cancelled: 'ملغاة',
  }[status] || status
}

function statusClass(status) {
  return {
    scheduled: 'status-badge--info',
    in_progress: 'status-badge--warning',
    completed: 'status-badge--success',
    cancelled: 'status-badge--muted',
  }[status] || 'status-badge--muted'
}

function typeLabel(type) {
  return { individual: 'فردية', group: 'جماعية' }[type] || type
}

function formatDate(d) {
  if (!d) return '—'
  return new Date(d).toLocaleDateString('ar-EG', { year: 'numeric', month: 'long', day: 'numeric' })
}

function formatTime(d) {
  if (!d) return '—'
  return new Date(d).toLocaleTimeString('ar-EG', { hour: '2-digit', minute: '2-digit' })
}

async function loadSessions(page = 1) {
  loading.value = true
  try {
    const params = new URLSearchParams()
    if (filter.value) params.append('status', filter.value)
    params.append('page', page)
    const data = await get(`/api/counselor/sessions?${params}`)
    sessions.value = data.data || []
    pagination.value = {
      current_page: data.current_page || 1,
      last_page: data.last_page || 1,
      total: data.total || 0,
    }
  } catch { /* handled */ }
  loading.value = false
}

function loadPage(page) {
  loadSessions(page)
}

async function completeSession(session) {
  if (!confirm('هل تم إتمام هذه الجلسة؟')) return
  completing.value = session.id
  try {
    await put(`/api/counselor/sessions/${session.id}/complete`)
    session.status = 'completed'
  } catch { /* handled */ }
  completing.value = null
}

onMounted(() => loadSessions())

// Reload when filter changes
import { watch } from 'vue'
watch(filter, () => loadSessions(1))
</script>

<style lang="scss" scoped>
.stats-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1rem;
  margin-bottom: 1.5rem;

  @media (max-width: 768px) { grid-template-columns: 1fr; }
}

.stat-tile {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1.25rem;
  background: #fff;
  border: 1px solid #e0d8cc;
  border-radius: 16px;

  &__icon {
    width: 52px;
    height: 52px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }

  &__value { font-size: 1.8rem; font-weight: 900; color: #1a1a2a; line-height: 1; }
  &__label { font-size: 0.8rem; color: #8888a0; margin-top: 4px; }

  &--success &__icon { background: rgba(27,122,74,0.1); color: #1b7a4a; }
  &--info &__icon { background: rgba(21,101,192,0.1); color: #1565c0; }
  &--muted &__icon { background: rgba(107,114,128,0.1); color: #6b7280; }
}

.filter-tabs {
  display: flex;
  gap: 0.5rem;
  margin-bottom: 1.5rem;
  flex-wrap: wrap;
}

.filter-tab {
  padding: 0.6rem 1.25rem;
  border: 1px solid #e0d8cc;
  background: #fff;
  border-radius: 100px;
  font-size: 0.85rem;
  font-weight: 700;
  color: #4a4a5e;
  cursor: pointer;
  transition: all 0.2s;

  &:hover { border-color: #0d7377; color: #0d7377; }

  &--active {
    background: #0d7377;
    color: #fff;
    border-color: #0d7377;
  }
}

.sessions-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.session-card {
  background: #fff;
  border: 1px solid #e0d8cc;
  border-radius: 16px;
  padding: 1.25rem;

  &__header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 1rem;
    margin-bottom: 1rem;

    @media (max-width: 576px) { flex-direction: column; align-items: stretch; }
  }

  &__user { display: flex; align-items: center; gap: 0.75rem; }

  &__avatar {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    background: rgba(13,115,119,0.1);
    color: #0d7377;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 900;
    font-size: 1.1rem;
  }

  &__name { font-size: 1rem; font-weight: 800; color: #1a1a2a; margin: 0; }

  &__phone {
    font-size: 0.78rem;
    color: #8888a0;
    margin: 2px 0 0;
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
  }

  &__meta {
    display: flex;
    gap: 1.25rem;
    padding: 0.75rem;
    background: #faf9f6;
    border-radius: 10px;
    margin-bottom: 0.75rem;
    flex-wrap: wrap;
  }

  &__meta-item {
    font-size: 0.8rem;
    color: #4a4a5e;
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
  }

  &__notes {
    font-size: 0.85rem;
    color: #4a4a5e;
    padding: 0.75rem;
    background: rgba(184,134,11,0.06);
    border-right: 3px solid #b8860b;
    border-radius: 8px;
    margin-bottom: 0.75rem;
    line-height: 1.7;
  }

  &__actions {
    display: flex;
    gap: 0.5rem;
    justify-content: flex-end;
  }
}

.pagination-wrap {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 1rem;
  margin-top: 1.5rem;
}

.pagination-info {
  font-size: 0.85rem;
  color: #8888a0;
  font-weight: 600;
}
</style>
