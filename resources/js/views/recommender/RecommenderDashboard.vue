<template>
  <div class="dashboard-page">
    <div class="container">
      <div v-if="loading" class="loading-state">
        <div class="spinner-border"></div>
        <p class="loading-state__text">جاري تحميل لوحة المعرّف...</p>
      </div>

      <!-- Registration Form -->
      <div v-else-if="!data && !isRecommender" class="row justify-content-center">
        <div class="col-lg-7">
          <div class="page-header">
            <div>
              <h1 class="page-header__title">
                <span class="page-header__title__icon" style="--header-bg: rgba(21,101,192,0.1); --header-color: #1565c0">
                  <PhHandshake :size="24" weight="duotone" />
                </span>
                التسجيل كمعرّف
              </h1>
              <p class="page-header__subtitle">المعرّف هو شخص موثوق يقوم بترشيح طرفي الزواج والتوفيق بينهم</p>
            </div>
          </div>

          <div class="dash-card">
            <div class="dash-card__body">
              <div v-if="error" class="dash-alert dash-alert--danger">
                <PhWarningCircle :size="20" weight="fill" class="dash-alert__icon" />
                <div class="dash-alert__content">{{ typeof error === 'string' ? error : 'تحقق من البيانات' }}</div>
              </div>

              <form class="dash-form" @submit.prevent="registerRecommender">
                <div class="dash-form__group">
                  <label class="dash-form__label">
                    <PhUserCircle :size="14" />
                    نوع المعرّف
                  </label>
                  <select v-model="form.type" class="dash-form__select" required>
                    <option value="">اختر النوع</option>
                    <option value="imam">إمام مسجد</option>
                    <option value="teacher">معلم / أستاذ</option>
                    <option value="relative">قريب / من العائلة</option>
                    <option value="community_leader">وجيه مجتمعي</option>
                  </select>
                </div>

                <div class="dash-form__group">
                  <label class="dash-form__label">
                    <PhBuildings :size="14" />
                    المؤسسة (اختياري)
                  </label>
                  <input v-model="form.institution" type="text" class="dash-form__input" placeholder="مثال: مسجد الرحمة">
                </div>

                <div class="dash-form__group">
                  <label class="dash-form__label">
                    <PhNotePencil :size="14" />
                    نبذة تعريفية (اختياري)
                  </label>
                  <textarea v-model="form.bio" class="dash-form__textarea" placeholder="عرّف عن نفسك وخبرتك في التوفيق"></textarea>
                </div>

                <button type="submit" class="btn-action btn-action--primary" :disabled="registering">
                  <span v-if="registering" class="spinner-border spinner-border-sm"></span>
                  <PhCheckCircle :size="18" weight="bold" v-else />
                  تسجيل كمعرّف
                </button>
              </form>
            </div>
          </div>
        </div>
      </div>

      <!-- Dashboard -->
      <div v-else-if="data">
        <div class="page-header">
          <div>
            <h1 class="page-header__title">
              <span class="page-header__title__icon" style="--header-bg: rgba(21,101,192,0.1); --header-color: #1565c0">
                <PhHandshake :size="24" weight="duotone" />
              </span>
              لوحة المعرّف
            </h1>
            <p class="page-header__subtitle">أدر مرشحيك واقترح التوفيقات</p>
          </div>
          <div class="page-header__actions">
            <router-link to="/recommender/add-candidate" class="btn-action btn-action--primary">
              <PhUserPlus :size="18" weight="bold" />
              إضافة مرشح
            </router-link>
          </div>
        </div>

        <!-- Approval Alert -->
        <div v-if="!data.recommender.is_approved" class="dash-alert dash-alert--warning">
          <PhClock :size="20" weight="fill" class="dash-alert__icon" />
          <div class="dash-alert__content">
            <div class="dash-alert__title">حسابك قيد المراجعة</div>
            ستتمكن من إضافة مرشحين بعد اعتماد الإدارة. عادة لا يستغرق ذلك أكثر من 24 ساعة.
          </div>
        </div>

        <!-- Stats -->
        <div class="stats-grid">
          <div class="stat-tile" style="--tile-color: #1565c0; --tile-bg: rgba(21,101,192,0.1)">
            <div class="stat-tile__icon"><PhUsers :size="24" weight="duotone" /></div>
            <div class="stat-tile__content">
              <div class="stat-tile__value">{{ data.recommender.candidates_count }}</div>
              <div class="stat-tile__label">المرشحون</div>
            </div>
          </div>
          <div class="stat-tile" style="--tile-color: #1b7a4a; --tile-bg: rgba(27,122,74,0.1)">
            <div class="stat-tile__icon"><PhHeart :size="24" weight="duotone" /></div>
            <div class="stat-tile__content">
              <div class="stat-tile__value">{{ data.recommender.successful_matches }}</div>
              <div class="stat-tile__label">زيجات ناجحة</div>
            </div>
          </div>
          <div class="stat-tile" style="--tile-color: #b8860b; --tile-bg: rgba(184,134,11,0.1)">
            <div class="stat-tile__icon"><PhListChecks :size="24" weight="duotone" /></div>
            <div class="stat-tile__content">
              <div class="stat-tile__value">{{ data.recommendations?.length || 0 }}</div>
              <div class="stat-tile__label">التوصيات</div>
            </div>
          </div>
          <div class="stat-tile" style="--tile-color: #0d7377; --tile-bg: rgba(13,115,119,0.1)">
            <div class="stat-tile__icon"><PhShieldCheck :size="24" weight="duotone" /></div>
            <div class="stat-tile__content">
              <div class="stat-tile__value">
                <span class="status-badge" :class="data.recommender.is_approved ? 'status-badge--success' : 'status-badge--warning'" style="font-size: 0.75rem">
                  {{ data.recommender.is_approved ? 'معتمد' : 'قيد المراجعة' }}
                </span>
              </div>
              <div class="stat-tile__label">الحالة</div>
            </div>
          </div>
        </div>

        <!-- Quick Nav -->
        <div class="dash-card">
          <div class="dash-card__body">
            <div class="d-flex flex-wrap gap-2">
              <router-link to="/recommender/suggestions" class="btn-action btn-action--outline">
                <PhMagnifyingGlass :size="16" />
                اقتراحات التوفيق
              </router-link>
              <router-link to="/recommender/family-requests" class="btn-action btn-action--outline">
                <PhUsersFour :size="16" />
                طلبات الأهل
              </router-link>
            </div>
          </div>
        </div>

        <div class="row g-4">
          <!-- Candidates -->
          <div class="col-lg-7">
            <div class="dash-card">
              <div class="dash-card__header">
                <h3 class="dash-card__header__title">
                  <PhUsers :size="20" />
                  المرشحون
                </h3>
                <span class="dash-card__header__meta">{{ data.candidates?.length || 0 }} مرشح</span>
              </div>
              <div class="dash-card__body--flush">
                <div v-if="data.candidates?.length" class="candidates-grid">
                  <div v-for="c in data.candidates" :key="c.id" class="candidate-row">
                    <div class="candidate-row__avatar" :class="c.gender === 'male' ? 'male' : 'female'">
                      {{ c.name?.charAt(0) }}
                    </div>
                    <div class="candidate-row__content">
                      <div class="candidate-row__name">{{ c.name }}</div>
                      <div class="candidate-row__meta">
                        <span><PhCake :size="13" /> {{ c.age }} سنة</span>
                        <span v-if="c.occupation"><PhBriefcase :size="13" /> {{ c.occupation }}</span>
                        <span v-if="c.city"><PhMapPin :size="13" /> {{ c.city.name }}</span>
                      </div>
                    </div>
                    <span class="status-badge" :class="c.status === 'active' ? 'status-badge--success' : 'status-badge--muted'">
                      {{ c.status === 'active' ? 'نشط' : c.status === 'matched' ? 'متطابق' : 'منسحب' }}
                    </span>
                  </div>
                </div>

                <div v-else class="empty-state">
                  <PhUserCircle :size="48" weight="duotone" class="empty-state__icon" />
                  <h4 class="empty-state__title">لم تضف مرشحين بعد</h4>
                  <p class="empty-state__desc">ابدأ بإضافة المرشحين الذين تعرفهم</p>
                  <router-link to="/recommender/add-candidate" class="btn-action btn-action--primary">
                    <PhUserPlus :size="18" />
                    إضافة مرشح
                  </router-link>
                </div>
              </div>
            </div>
          </div>

          <!-- Recent Recommendations -->
          <div class="col-lg-5">
            <div class="dash-card">
              <div class="dash-card__header">
                <h3 class="dash-card__header__title">
                  <PhListChecks :size="20" />
                  آخر التوصيات
                </h3>
              </div>
              <div class="dash-card__body--flush">
                <div v-if="data.recommendations?.length">
                  <div v-for="r in data.recommendations" :key="r.id" class="rec-row">
                    <div class="rec-row__pair">
                      <span class="name">{{ r.male_candidate?.name }}</span>
                      <PhArrowsLeftRight :size="14" weight="bold" />
                      <span class="name">{{ r.female_candidate?.name }}</span>
                    </div>
                    <span class="rec-row__score" :class="scoreClass(r.compatibility_score)">
                      {{ r.compatibility_score }}%
                    </span>
                  </div>
                </div>
                <div v-else class="empty-state" style="padding: 2.5rem 1.5rem">
                  <PhListChecks :size="42" weight="duotone" class="empty-state__icon" />
                  <p class="empty-state__desc mb-0">لا توجد توصيات بعد</p>
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
import { ref, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useApi } from '@/composables/useApi'
import {
  PhHandshake, PhWarningCircle, PhUserCircle, PhBuildings, PhNotePencil,
  PhCheckCircle, PhClock, PhUsers, PhHeart, PhListChecks, PhShieldCheck,
  PhUserPlus, PhMagnifyingGlass, PhUsersFour, PhCake, PhBriefcase, PhMapPin,
  PhArrowsLeftRight,
} from '@phosphor-icons/vue'

const auth = useAuthStore()
const { get, post, loading, error } = useApi()
const data = ref(null)
const isRecommender = ref(auth.isRecommender)
const registering = ref(false)
const form = ref({ type: '', institution: '', bio: '' })

function scoreClass(score) {
  if (score >= 85) return 'score--excellent'
  if (score >= 70) return 'score--good'
  return 'score--avg'
}

async function registerRecommender() {
  registering.value = true
  try {
    await post('/api/recommender/register', form.value)
    isRecommender.value = true
    await auth.fetchUser()
    await loadDashboard()
  } catch { /* handled */ }
  registering.value = false
}

async function loadDashboard() {
  try {
    data.value = await get('/api/recommender/dashboard')
  } catch { /* handled */ }
}

onMounted(async () => {
  if (auth.isRecommender || auth.user?.role === 'recommender') {
    isRecommender.value = true
    await loadDashboard()
  }
})
</script>

<style lang="scss" scoped>
.candidates-grid {
  display: flex;
  flex-direction: column;
}

.candidate-row {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1rem 1.5rem;
  border-bottom: 1px solid #f0ece4;
  transition: background 0.15s;

  &:last-child { border-bottom: none; }
  &:hover { background: #faf9f6; }

  &__avatar {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    font-size: 1.1rem;
    flex-shrink: 0;

    &.male { background: linear-gradient(135deg, #1565c0 0%, #0d4a8f 100%); }
    &.female { background: linear-gradient(135deg, #b8860b 0%, #8a6508 100%); }
  }

  &__content { flex: 1; min-width: 0; }
  &__name {
    font-weight: 800;
    color: #1a1a2a;
    margin-bottom: 0.25rem;
  }
  &__meta {
    font-size: 0.78rem;
    color: #8888a0;
    display: flex;
    gap: 0.85rem;
    flex-wrap: wrap;

    span {
      display: inline-flex;
      align-items: center;
      gap: 0.2rem;
    }
  }
}

.rec-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 0.75rem;
  padding: 1rem 1.5rem;
  border-bottom: 1px solid #f0ece4;

  &:last-child { border-bottom: none; }

  &__pair {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.85rem;
    color: #4a4a5e;
    flex-wrap: wrap;

    .name { font-weight: 700; color: #1a1a2a; }
    svg { color: #0d7377; }
  }

  &__score {
    font-weight: 900;
    font-size: 0.95rem;
    padding: 0.3rem 0.75rem;
    border-radius: 100px;
    flex-shrink: 0;

    &.score--excellent { background: rgba(27,122,74,0.1); color: #1b7a4a; }
    &.score--good { background: rgba(13,115,119,0.1); color: #0d7377; }
    &.score--avg { background: rgba(184,134,11,0.1); color: #b8860b; }
  }
}
</style>
