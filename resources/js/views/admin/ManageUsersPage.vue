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
            <span class="page-header__title__icon" style="--header-bg: rgba(13,115,119,0.1); --header-color: #0d7377">
              <PhUsers :size="24" weight="duotone" />
            </span>
            إدارة المستخدمين
          </h1>
          <p class="page-header__subtitle">عرض وإدارة جميع المستخدمين على المنصة</p>
        </div>
      </div>

      <!-- Filters -->
      <div class="filter-bar">
        <div class="filter-bar__search">
          <PhMagnifyingGlass :size="18" class="icon" />
          <input v-model="search" type="text" placeholder="بحث بالاسم أو البريد..." @input="debouncedLoad">
        </div>
        <div class="filter-bar__filters">
          <select v-model="roleFilter" @change="loadUsers()">
            <option value="">جميع الأدوار</option>
            <option value="user">مستخدم</option>
            <option value="recommender">معرّف</option>
            <option value="admin">مدير</option>
          </select>
        </div>
      </div>

      <div v-if="loading" class="loading-state">
        <div class="spinner-border"></div>
        <p class="loading-state__text">جاري تحميل المستخدمين...</p>
      </div>

      <div v-else>
        <div class="dash-card">
          <div class="dash-card__body--flush">
            <div class="table-wrapper">
              <table class="dash-table">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>المستخدم</th>
                    <th>المدينة</th>
                    <th>الدور</th>
                    <th>الشهادة</th>
                    <th>التسجيل</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="u in users" :key="u.id">
                    <td><strong>#{{ u.id }}</strong></td>
                    <td>
                      <div class="user-cell">
                        <div class="user-cell__avatar">{{ u.name?.charAt(0) }}</div>
                        <div>
                          <strong>{{ u.name }}</strong>
                          <div class="text-muted small">{{ u.email }}</div>
                        </div>
                      </div>
                    </td>
                    <td class="text-muted small">{{ u.city?.name || '—' }}</td>
                    <td>
                      <span class="status-badge" :class="roleBadgeClass(u.role)">
                        {{ roleLabel(u.role) }}
                      </span>
                    </td>
                    <td>
                      <span class="status-badge" :class="u.has_certificate ? 'status-badge--success' : 'status-badge--muted'">
                        <PhCheckCircle :size="12" weight="fill" v-if="u.has_certificate" />
                        {{ u.has_certificate ? 'حاصل' : 'لا' }}
                      </span>
                    </td>
                    <td class="text-muted small">{{ formatDate(u.created_at) }}</td>
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
                @click="goToPage(pagination.current_page - 1)"
              >
                <PhCaretRight :size="14" />
                السابق
              </button>
              <button
                class="btn-action btn-action--outline btn-action--sm"
                :disabled="pagination.current_page >= pagination.last_page"
                @click="goToPage(pagination.current_page + 1)"
              >
                التالي
                <PhCaretLeft :size="14" />
              </button>
            </div>
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
  PhArrowRight, PhUsers, PhMagnifyingGlass, PhCheckCircle,
  PhCaretRight, PhCaretLeft,
} from '@phosphor-icons/vue'

const { get, loading } = useApi()
const users = ref([])
const search = ref('')
const roleFilter = ref('')
const pagination = ref({ current_page: 1, last_page: 1 })
let debounceTimer = null

const roleLabel = (r) => ({ admin: 'مدير', recommender: 'معرّف', user: 'عضو' })[r] || r
const roleBadgeClass = (r) => ({
  admin: 'status-badge--danger',
  recommender: 'status-badge--info',
  user: 'status-badge--primary',
})[r] || 'status-badge--muted'

function formatDate(d) {
  return new Date(d).toLocaleDateString('ar-EG', { year: 'numeric', month: 'short', day: 'numeric' })
}

function debouncedLoad() {
  clearTimeout(debounceTimer)
  debounceTimer = setTimeout(() => loadUsers(), 400)
}

async function loadUsers(page = 1) {
  try {
    let url = `/api/admin/users?page=${page}`
    if (search.value) url += `&search=${encodeURIComponent(search.value)}`
    if (roleFilter.value) url += `&role=${roleFilter.value}`
    const result = await get(url)
    users.value = result.data
    pagination.value = { current_page: result.current_page, last_page: result.last_page }
  } catch { /* handled */ }
}

function goToPage(p) {
  if (p >= 1 && p <= pagination.value.last_page) loadUsers(p)
}

onMounted(() => loadUsers())
</script>

<style lang="scss" scoped>
.user-cell {
  display: flex;
  align-items: center;
  gap: 0.6rem;

  &__avatar {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    background: linear-gradient(135deg, #0d7377 0%, #095456 100%);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    font-size: 0.9rem;
    flex-shrink: 0;
  }
}
</style>
