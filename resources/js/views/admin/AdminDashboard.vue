<template>
  <div class="dashboard-page">
    <div class="container">
      <div class="page-header">
        <div>
          <h1 class="page-header__title">
            <span class="page-header__title__icon" style="--header-bg: rgba(192,57,43,0.1); --header-color: #c0392b">
              <PhShieldCheck :size="24" weight="duotone" />
            </span>
            لوحة الإدارة
          </h1>
          <p class="page-header__subtitle">نظرة عامة على حالة المنصة</p>
        </div>
      </div>

      <div v-if="loading" class="loading-state">
        <div class="spinner-border"></div>
        <p class="loading-state__text">جاري تحميل البيانات...</p>
      </div>

      <div v-else-if="data">
        <!-- Stats Grid -->
        <div class="stats-grid">
          <div
            v-for="stat in stats"
            :key="stat.label"
            class="stat-tile"
            :style="{ '--tile-color': stat.color, '--tile-bg': stat.bg }"
          >
            <div class="stat-tile__icon">
              <component :is="stat.icon" :size="22" weight="duotone" />
            </div>
            <div class="stat-tile__content">
              <div class="stat-tile__value">{{ stat.value }}</div>
              <div class="stat-tile__label">{{ stat.label }}</div>
            </div>
          </div>
        </div>

        <!-- Quick Actions -->
        <div class="dash-card">
          <div class="dash-card__header">
            <h3 class="dash-card__header__title">
              <PhSquaresFour :size="20" />
              الإجراءات السريعة
            </h3>
          </div>
          <div class="dash-card__body">
            <div class="quick-actions">
              <router-link to="/admin/users" class="quick-action" style="--qa-color: #0d7377">
                <PhUsers :size="22" weight="duotone" />
                <div>
                  <div class="title">إدارة المستخدمين</div>
                  <div class="desc">{{ data.users_count }} مستخدم</div>
                </div>
              </router-link>
              <router-link to="/admin/recommenders" class="quick-action" style="--qa-color: #1565c0">
                <PhHandshake :size="22" weight="duotone" />
                <div>
                  <div class="title">إدارة المعرّفين</div>
                  <div class="desc">{{ data.pending_recommenders }} بانتظار الاعتماد</div>
                </div>
              </router-link>
              <router-link to="/admin/weddings" class="quick-action" style="--qa-color: #c0392b">
                <PhHeart :size="22" weight="duotone" />
                <div>
                  <div class="title">إدارة الأعراس</div>
                  <div class="desc">إنشاء وتعديل الأعراس</div>
                </div>
              </router-link>
              <router-link to="/admin/counseling" class="quick-action" style="--qa-color: #1b7a4a">
                <PhChats :size="22" weight="duotone" />
                <div>
                  <div class="title">الاستشارات</div>
                  <div class="desc">إدارة الجلسات</div>
                </div>
              </router-link>
              <router-link to="/admin/reports" class="quick-action quick-action--danger" style="--qa-color: #c0392b">
                <PhFlag :size="22" weight="duotone" />
                <div>
                  <div class="title">البلاغات</div>
                  <div class="desc">{{ data.reports_count }} بلاغ معلق</div>
                </div>
              </router-link>
            </div>
          </div>
        </div>

        <!-- Recent Users -->
        <div class="dash-card">
          <div class="dash-card__header">
            <h3 class="dash-card__header__title">
              <PhUserPlus :size="20" />
              آخر المسجلين
            </h3>
            <span class="dash-card__header__meta">{{ data.recent_users?.length || 0 }} مستخدم</span>
          </div>
          <div class="dash-card__body--flush">
            <div class="table-wrapper">
              <table class="dash-table">
                <thead>
                  <tr>
                    <th>المستخدم</th>
                    <th>البريد</th>
                    <th>الدور</th>
                    <th>التاريخ</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="u in data.recent_users" :key="u.id">
                    <td>
                      <div class="user-cell">
                        <div class="user-cell__avatar">{{ u.name?.charAt(0) }}</div>
                        <strong>{{ u.name }}</strong>
                      </div>
                    </td>
                    <td class="text-muted small">{{ u.email }}</td>
                    <td>
                      <span class="status-badge" :class="roleBadgeClass(u.role)">
                        {{ roleLabel(u.role) }}
                      </span>
                    </td>
                    <td class="text-muted small">{{ formatDate(u.created_at) }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useApi } from '@/composables/useApi'
import {
  PhShieldCheck, PhSquaresFour, PhUsers, PhHandshake, PhHeart, PhChats,
  PhUserPlus, PhUsersThree, PhCertificate, PhCirclesThree, PhFlag,
} from '@phosphor-icons/vue'

const { get, loading } = useApi()
const data = ref(null)

const roleLabel = (r) => ({ admin: 'مدير', recommender: 'معرّف', user: 'عضو' })[r] || r
const roleBadgeClass = (r) => ({
  admin: 'status-badge--danger',
  recommender: 'status-badge--info',
  user: 'status-badge--primary',
})[r] || 'status-badge--muted'

function formatDate(d) {
  return new Date(d).toLocaleDateString('ar-EG', { year: 'numeric', month: 'short', day: 'numeric' })
}

const stats = computed(() => {
  if (!data.value) return []
  return [
    { icon: PhUsersThree, label: 'المستخدمون', value: data.value.users_count, bg: 'rgba(13,115,119,0.1)', color: '#0d7377' },
    { icon: PhHandshake, label: 'المعرّفون', value: data.value.recommenders_count, bg: 'rgba(21,101,192,0.1)', color: '#1565c0' },
    { icon: PhUserPlus, label: 'بانتظار الاعتماد', value: data.value.pending_recommenders, bg: 'rgba(230,126,34,0.1)', color: '#e67e22' },
    { icon: PhCertificate, label: 'الشهادات', value: data.value.certificates_count, bg: 'rgba(27,122,74,0.1)', color: '#1b7a4a' },
    { icon: PhCirclesThree, label: 'الحلقات النشطة', value: data.value.circles_count, bg: 'rgba(184,134,11,0.1)', color: '#b8860b' },
    { icon: PhFlag, label: 'بلاغات معلقة', value: data.value.reports_count, bg: 'rgba(192,57,43,0.1)', color: '#c0392b' },
  ]
})

onMounted(async () => {
  try { data.value = await get('/api/admin/dashboard') } catch { /* handled */ }
})
</script>

<style lang="scss" scoped>
.quick-actions {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
  gap: 0.85rem;
}

.quick-action {
  display: flex;
  align-items: center;
  gap: 0.85rem;
  padding: 1rem 1.25rem;
  background: #faf9f6;
  border: 1.5px solid #e0d8cc;
  border-radius: 12px;
  text-decoration: none;
  color: inherit;
  transition: all 0.25s;

  > svg {
    color: var(--qa-color);
    flex-shrink: 0;
  }

  .title {
    font-size: 0.95rem;
    font-weight: 800;
    color: #1a1a2a;
    margin-bottom: 0.15rem;
  }

  .desc {
    font-size: 0.78rem;
    color: #8888a0;
  }

  &:hover {
    background: #fff;
    border-color: var(--qa-color);
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(13, 26, 42, 0.05);
    color: inherit;
  }

  &--danger {
    background: rgba(192,57,43,0.05);
    border-color: rgba(192,57,43,0.2);

    .title { color: #c0392b; }
  }
}

.user-cell {
  display: flex;
  align-items: center;
  gap: 0.6rem;

  &__avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: linear-gradient(135deg, #0d7377 0%, #095456 100%);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    font-size: 0.8rem;
    flex-shrink: 0;
  }
}
</style>
