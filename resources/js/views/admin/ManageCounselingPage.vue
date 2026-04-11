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
            <span class="page-header__title__icon" style="--header-bg: rgba(27,122,74,0.1); --header-color: #1b7a4a">
              <PhChatsCircle :size="24" weight="duotone" />
            </span>
            إدارة الجلسات الاستشارية
          </h1>
          <p class="page-header__subtitle">إدارة جميع جلسات الاستشارة على المنصة</p>
        </div>
      </div>

      <!-- Filters -->
      <div class="filter-bar">
        <div class="filter-bar__search">
          <PhMagnifyingGlass :size="18" class="icon" />
          <input type="text" placeholder="البحث ضمن الجلسات..." disabled>
        </div>
        <div class="filter-bar__filters">
          <select v-model="statusFilter" @change="load()">
            <option value="">جميع الحالات</option>
            <option value="scheduled">محجوز</option>
            <option value="completed">مكتمل</option>
            <option value="cancelled">ملغي</option>
          </select>
        </div>
      </div>

      <div v-if="loading" class="loading-state">
        <div class="spinner-border"></div>
        <p class="loading-state__text">جاري تحميل الجلسات...</p>
      </div>

      <div v-else-if="sessions.length">
        <div class="dash-card">
          <div class="dash-card__body--flush">
            <div class="table-wrapper">
              <table class="dash-table">
                <thead>
                  <tr>
                    <th>المستخدم</th>
                    <th>النوع</th>
                    <th>الموعد</th>
                    <th>الحالة</th>
                    <th>إجراء</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="s in sessions" :key="s.id">
                    <td>
                      <div class="user-cell">
                        <div class="user-cell__avatar">{{ s.user?.name?.charAt(0) }}</div>
                        <div>
                          <strong>{{ s.user?.name }}</strong>
                          <div class="text-muted small">{{ s.user?.email }}</div>
                        </div>
                      </div>
                    </td>
                    <td>
                      <span class="status-badge" :class="s.type === 'individual' ? 'status-badge--primary' : 'status-badge--info'">
                        <component :is="s.type === 'individual' ? PhUser : PhUsersThree" :size="12" />
                        {{ s.type === 'individual' ? 'فردية' : 'جماعية' }}
                      </span>
                    </td>
                    <td class="small">{{ formatDateTime(s.scheduled_at) }}</td>
                    <td>
                      <span class="status-badge" :class="statusBadgeClass(s.status)">
                        {{ statusLabel(s.status) }}
                      </span>
                    </td>
                    <td>
                      <button
                        v-if="s.status === 'scheduled'"
                        class="btn-action btn-action--primary btn-action--sm"
                        :disabled="completing === s.id"
                        @click="complete(s)"
                      >
                        <span v-if="completing === s.id" class="spinner-border spinner-border-sm"></span>
                        <PhCheckCircle :size="14" v-else />
                        تم الإتمام
                      </button>
                      <span v-else class="text-muted small">—</span>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Pagination -->
          <div v-if="pagination.last_page > 1" class="dash-card__footer">
            <span class="text-muted small">صفحة {{ pagination.current_page }} من {{ pagination.last_page }}</span>
            <div class="d-flex gap-2">
              <button
                class="btn-action btn-action--outline btn-action--sm"
                :disabled="pagination.current_page <= 1"
                @click="load(pagination.current_page - 1)"
              >
                السابق
              </button>
              <button
                class="btn-action btn-action--outline btn-action--sm"
                :disabled="pagination.current_page >= pagination.last_page"
                @click="load(pagination.current_page + 1)"
              >
                التالي
              </button>
            </div>
          </div>
        </div>
      </div>

      <div v-else class="empty-state">
        <PhCalendarBlank :size="64" weight="duotone" class="empty-state__icon" />
        <h4 class="empty-state__title">لا توجد جلسات</h4>
        <p class="empty-state__desc">لا توجد جلسات استشارية مسجّلة حتى الآن</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useApi } from '@/composables/useApi'
import {
  PhArrowRight, PhChatsCircle, PhMagnifyingGlass, PhUser, PhUsersThree,
  PhCheckCircle, PhCalendarBlank,
} from '@phosphor-icons/vue'

const { get, put, loading } = useApi()
const sessions = ref([])
const statusFilter = ref('')
const pagination = ref({ current_page: 1, last_page: 1 })
const completing = ref(null)

const statusLabel = (s) => ({ scheduled: 'محجوز', completed: 'مكتمل', cancelled: 'ملغي' })[s] || s
const statusBadgeClass = (s) => ({
  scheduled: 'status-badge--info',
  completed: 'status-badge--success',
  cancelled: 'status-badge--muted',
})[s] || 'status-badge--muted'

function formatDateTime(dt) {
  return new Date(dt).toLocaleDateString('ar-EG', { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' })
}

async function load(page = 1) {
  try {
    let url = `/api/admin/counseling?page=${page}`
    if (statusFilter.value) url += `&status=${statusFilter.value}`
    const result = await get(url)
    sessions.value = result.data
    pagination.value = { current_page: result.current_page, last_page: result.last_page }
  } catch {}
}

async function complete(s) {
  completing.value = s.id
  try {
    await put(`/api/admin/counseling/${s.id}/complete`)
    s.status = 'completed'
  } catch {}
  completing.value = null
}

onMounted(() => load())
</script>

<style lang="scss" scoped>
.user-cell {
  display: flex;
  align-items: center;
  gap: 0.6rem;

  &__avatar {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: linear-gradient(135deg, #1b7a4a 0%, #14613b 100%);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    font-size: 0.85rem;
    flex-shrink: 0;
  }
}
</style>
