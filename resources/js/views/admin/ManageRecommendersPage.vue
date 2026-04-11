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
            <span class="page-header__title__icon" style="--header-bg: rgba(21,101,192,0.1); --header-color: #1565c0">
              <PhHandshake :size="24" weight="duotone" />
            </span>
            إدارة المعرّفين
          </h1>
          <p class="page-header__subtitle">اعتماد ومراجعة المعرّفين على المنصة</p>
        </div>
      </div>

      <!-- Quick Stats -->
      <div class="stats-grid" style="grid-template-columns: repeat(3, 1fr)">
        <div class="stat-tile" style="--tile-color: #1565c0; --tile-bg: rgba(21,101,192,0.1)">
          <div class="stat-tile__icon"><PhUsers :size="22" weight="duotone" /></div>
          <div class="stat-tile__content">
            <div class="stat-tile__value">{{ recommenders.length }}</div>
            <div class="stat-tile__label">إجمالي المعرّفين</div>
          </div>
        </div>
        <div class="stat-tile" style="--tile-color: #1b7a4a; --tile-bg: rgba(27,122,74,0.1)">
          <div class="stat-tile__icon"><PhCheckCircle :size="22" weight="duotone" /></div>
          <div class="stat-tile__content">
            <div class="stat-tile__value">{{ approvedCount }}</div>
            <div class="stat-tile__label">المعتمدون</div>
          </div>
        </div>
        <div class="stat-tile" style="--tile-color: #e67e22; --tile-bg: rgba(230,126,34,0.1)">
          <div class="stat-tile__icon"><PhClock :size="22" weight="duotone" /></div>
          <div class="stat-tile__content">
            <div class="stat-tile__value">{{ pendingCount }}</div>
            <div class="stat-tile__label">قيد المراجعة</div>
          </div>
        </div>
      </div>

      <div v-if="loading" class="loading-state">
        <div class="spinner-border"></div>
        <p class="loading-state__text">جاري التحميل...</p>
      </div>

      <div v-else-if="recommenders.length" class="rec-grid">
        <div v-for="r in recommenders" :key="r.id" class="rec-admin-card">
          <div class="rec-admin-card__header">
            <div class="user-cell">
              <div class="user-cell__avatar">{{ r.user?.name?.charAt(0) }}</div>
              <div>
                <strong>{{ r.user?.name }}</strong>
                <div class="text-muted small">{{ r.user?.email }}</div>
              </div>
            </div>
            <span class="status-badge" :class="r.is_approved ? 'status-badge--success' : 'status-badge--warning'">
              {{ r.is_approved ? 'معتمد' : 'قيد المراجعة' }}
            </span>
          </div>

          <div class="rec-admin-card__body">
            <div class="rec-info-grid">
              <div class="rec-info">
                <PhIdentificationCard :size="16" />
                <div>
                  <div class="label">النوع</div>
                  <div class="value">{{ typeLabel(r.type) }}</div>
                </div>
              </div>
              <div class="rec-info">
                <PhBuildings :size="16" />
                <div>
                  <div class="label">المؤسسة</div>
                  <div class="value">{{ r.institution || '—' }}</div>
                </div>
              </div>
              <div class="rec-info">
                <PhUserList :size="16" />
                <div>
                  <div class="label">المرشحون</div>
                  <div class="value">{{ r.candidates_count }}</div>
                </div>
              </div>
              <div class="rec-info">
                <PhHeart :size="16" />
                <div>
                  <div class="label">زيجات ناجحة</div>
                  <div class="value">{{ r.successful_matches }}</div>
                </div>
              </div>
            </div>

            <p v-if="r.bio" class="rec-bio">
              <PhQuotes :size="14" />
              {{ r.bio }}
            </p>
          </div>

          <div class="rec-admin-card__actions">
            <button
              v-if="!r.is_approved"
              class="btn-action btn-action--primary btn-action--sm"
              :disabled="updating === r.id"
              @click="approve(r, true)"
            >
              <span v-if="updating === r.id" class="spinner-border spinner-border-sm"></span>
              <PhCheck :size="14" weight="bold" v-else />
              اعتماد
            </button>
            <button
              v-else
              class="btn-action btn-action--danger btn-action--sm"
              :disabled="updating === r.id"
              @click="approve(r, false)"
            >
              <span v-if="updating === r.id" class="spinner-border spinner-border-sm"></span>
              <PhX :size="14" weight="bold" v-else />
              إلغاء الاعتماد
            </button>
          </div>
        </div>
      </div>

      <div v-else class="empty-state">
        <PhHandshake :size="64" weight="duotone" class="empty-state__icon" />
        <h4 class="empty-state__title">لا يوجد معرّفون مسجّلون</h4>
        <p class="empty-state__desc">لم يسجّل أي شخص كمعرّف بعد</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useApi } from '@/composables/useApi'
import {
  PhArrowRight, PhHandshake, PhUsers, PhCheckCircle, PhClock,
  PhIdentificationCard, PhBuildings, PhUserList, PhHeart, PhQuotes,
  PhCheck, PhX,
} from '@phosphor-icons/vue'

const { get, put, loading } = useApi()
const recommenders = ref([])
const updating = ref(null)

const approvedCount = computed(() => recommenders.value.filter(r => r.is_approved).length)
const pendingCount = computed(() => recommenders.value.filter(r => !r.is_approved).length)

const typeLabel = (t) => ({
  imam: 'إمام مسجد',
  teacher: 'معلم',
  relative: 'قريب',
  community_leader: 'وجيه مجتمعي',
})[t] || t

async function approve(r, approved) {
  updating.value = r.id
  try {
    await put(`/api/admin/recommenders/${r.id}`, { approved })
    r.is_approved = approved
  } catch { /* handled */ }
  updating.value = null
}

onMounted(async () => {
  try { recommenders.value = await get('/api/admin/recommenders') } catch { /* handled */ }
})
</script>

<style lang="scss" scoped>
.rec-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(420px, 1fr));
  gap: 1.25rem;

  @media (max-width: 576px) { grid-template-columns: 1fr; }
}

.rec-admin-card {
  background: #fff;
  border: 1px solid #f0ece4;
  border-radius: 16px;
  overflow: hidden;
  transition: all 0.25s;

  &:hover {
    border-color: rgba(21,101,192,0.25);
    box-shadow: 0 12px 32px rgba(13, 26, 42, 0.05);
  }

  &__header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 0.75rem;
    padding: 1.25rem 1.5rem;
    background: #faf9f6;
    border-bottom: 1px solid #f0ece4;
  }

  &__body {
    padding: 1.25rem 1.5rem;
  }

  &__actions {
    padding: 1rem 1.5rem;
    border-top: 1px solid #f0ece4;
    background: #faf9f6;
    display: flex;
    gap: 0.5rem;
  }
}

.user-cell {
  display: flex;
  align-items: center;
  gap: 0.6rem;

  &__avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #1565c0 0%, #0d4a8f 100%);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    font-size: 0.9rem;
    flex-shrink: 0;
  }
}

.rec-info-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 0.75rem;
}

.rec-info {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.65rem 0.85rem;
  background: #faf9f6;
  border-radius: 10px;

  svg { color: #1565c0; flex-shrink: 0; }

  .label { font-size: 0.7rem; color: #8888a0; font-weight: 600; }
  .value { font-size: 0.85rem; font-weight: 800; color: #1a1a2a; }
}

.rec-bio {
  margin-top: 1rem;
  padding: 0.85rem 1rem;
  background: #faf9f6;
  border-right: 3px solid #1565c0;
  border-radius: 8px;
  font-size: 0.85rem;
  color: #4a4a5e;
  display: flex;
  align-items: flex-start;
  gap: 0.5rem;
  line-height: 1.75;

  svg { color: #1565c0; flex-shrink: 0; margin-top: 3px; }
}
</style>
