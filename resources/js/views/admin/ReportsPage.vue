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
              <PhFlag :size="24" weight="duotone" />
            </span>
            البلاغات
          </h1>
          <p class="page-header__subtitle">إدارة ومراجعة جميع البلاغات الواردة من المستخدمين</p>
        </div>
      </div>

      <div v-if="loading" class="loading-state">
        <div class="spinner-border"></div>
        <p class="loading-state__text">جاري التحميل...</p>
      </div>

      <div v-else-if="reports.length" class="reports-grid">
        <div v-for="report in reports" :key="report.id" class="report-card" :class="`is-${report.status}`">
          <div class="report-card__header">
            <div class="d-flex align-items-center gap-2">
              <span class="status-badge" :class="typeBadgeClass(report.reported_type)">
                <PhFlag :size="11" />
                {{ typeLabel(report.reported_type) }}
              </span>
              <span class="text-muted small">#{{ report.id }}</span>
            </div>
            <span class="status-badge" :class="statusBadgeClass(report.status)">
              {{ statusLabel(report.status) }}
            </span>
          </div>

          <div class="report-card__body">
            <div class="reporter-row">
              <PhUser :size="14" />
              <span>المُبلِّغ: <strong>{{ report.reporter?.name }}</strong></span>
              <span class="text-muted">— {{ formatDate(report.created_at) }}</span>
            </div>

            <div class="report-card__reason">
              <PhQuotes :size="14" />
              <p>{{ report.reason }}</p>
            </div>

            <div v-if="report.admin_notes" class="report-card__admin-notes">
              <strong><PhNotePencil :size="13" /> ملاحظات الإدارة:</strong>
              {{ report.admin_notes }}
            </div>
          </div>

          <div v-if="report.status === 'pending'" class="report-card__actions">
            <input
              v-model="report._notes"
              type="text"
              class="dash-form__input"
              placeholder="أضف ملاحظات (اختياري)..."
              style="flex: 1; min-width: 200px"
            >
            <button class="btn-action btn-action--outline btn-action--sm" @click="resolve(report, 'investigating')">
              <PhMagnifyingGlass :size="14" />
              تحقيق
            </button>
            <button class="btn-action btn-action--primary btn-action--sm" @click="resolve(report, 'resolved')">
              <PhCheck :size="14" weight="bold" />
              حل
            </button>
            <button class="btn-action btn-action--danger btn-action--sm" @click="resolve(report, 'dismissed')">
              <PhX :size="14" weight="bold" />
              رفض
            </button>
          </div>
        </div>
      </div>

      <div v-else class="empty-state">
        <PhFlag :size="64" weight="duotone" class="empty-state__icon" />
        <h4 class="empty-state__title">لا توجد بلاغات</h4>
        <p class="empty-state__desc">لا يوجد بلاغات معلقة حالياً</p>
      </div>

      <!-- Pagination -->
      <div v-if="lastPage > 1" class="dash-card">
        <div class="dash-card__footer">
          <span class="text-muted small">صفحة {{ currentPage }} من {{ lastPage }}</span>
          <div class="d-flex gap-2">
            <button class="btn-action btn-action--outline btn-action--sm" :disabled="currentPage <= 1" @click="loadPage(currentPage - 1)">
              السابق
            </button>
            <button class="btn-action btn-action--outline btn-action--sm" :disabled="currentPage >= lastPage" @click="loadPage(currentPage + 1)">
              التالي
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useApi } from '@/composables/useApi'
import {
  PhArrowRight, PhFlag, PhUser, PhQuotes, PhNotePencil,
  PhMagnifyingGlass, PhCheck, PhX,
} from '@phosphor-icons/vue'

const { get, put, loading } = useApi()
const reports = ref([])
const currentPage = ref(1)
const lastPage = ref(1)

const typeLabel = (t) => ({ user: 'مستخدم', recommender: 'معرّف', candidate: 'مرشح', other: 'أخرى' })[t] || t
const typeBadgeClass = (t) => ({
  user: 'status-badge--danger',
  recommender: 'status-badge--warning',
  candidate: 'status-badge--info',
})[t] || 'status-badge--muted'

const statusLabel = (s) => ({ pending: 'معلق', investigating: 'قيد التحقيق', resolved: 'تم الحل', dismissed: 'مرفوض' })[s] || s
const statusBadgeClass = (s) => ({
  pending: 'status-badge--warning',
  investigating: 'status-badge--info',
  resolved: 'status-badge--success',
  dismissed: 'status-badge--muted',
})[s] || 'status-badge--muted'

function formatDate(d) {
  if (!d) return ''
  return new Date(d).toLocaleDateString('ar-EG', { year: 'numeric', month: 'short', day: 'numeric' })
}

async function resolve(report, status) {
  try {
    await put(`/api/admin/reports/${report.id}`, { status, admin_notes: report._notes || null })
    report.status = status
    report.admin_notes = report._notes
  } catch { /* handled */ }
}

async function loadPage(page) {
  try {
    const result = await get(`/api/admin/reports?page=${page}`)
    reports.value = (result.data || []).map(r => ({ ...r, _notes: '' }))
    currentPage.value = result.current_page || 1
    lastPage.value = result.last_page || 1
  } catch { /* handled */ }
}

onMounted(() => loadPage(1))
</script>

<style lang="scss" scoped>
.reports-grid {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.report-card {
  background: #fff;
  border: 1px solid #f0ece4;
  border-radius: 16px;
  overflow: hidden;
  transition: all 0.25s;

  &.is-pending {
    border-right: 4px solid #e67e22;
  }

  &.is-investigating {
    border-right: 4px solid #1565c0;
  }

  &__header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 1.5rem;
    background: #faf9f6;
    border-bottom: 1px solid #f0ece4;
  }

  &__body {
    padding: 1.25rem 1.5rem;
  }

  &__actions {
    padding: 1rem 1.5rem;
    background: #faf9f6;
    border-top: 1px solid #f0ece4;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 0.5rem;
  }
}

.reporter-row {
  display: flex;
  align-items: center;
  gap: 0.4rem;
  font-size: 0.85rem;
  color: #4a4a5e;
  margin-bottom: 0.85rem;

  svg { color: #8888a0; flex-shrink: 0; }
}

.report-card__reason {
  display: flex;
  gap: 0.5rem;
  padding: 0.85rem 1rem;
  background: #faf9f6;
  border-right: 3px solid #c0392b;
  border-radius: 8px;

  svg { color: #c0392b; flex-shrink: 0; margin-top: 4px; }

  p {
    margin: 0;
    font-size: 0.9rem;
    color: #1a1a2a;
    line-height: 1.75;
  }
}

.report-card__admin-notes {
  margin-top: 0.85rem;
  padding: 0.75rem 1rem;
  background: rgba(13,115,119,0.05);
  border-right: 3px solid #0d7377;
  border-radius: 8px;
  font-size: 0.85rem;
  color: #4a4a5e;
  line-height: 1.6;

  strong {
    color: #0d7377;
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    margin-left: 0.4rem;
  }
}
</style>
