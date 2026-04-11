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
            <span class="page-header__title__icon" style="--header-bg: rgba(184,134,11,0.1); --header-color: #b8860b">
              <PhMagnifyingGlass :size="24" weight="duotone" />
            </span>
            اقتراحات التوفيق
          </h1>
          <p class="page-header__subtitle">اقتراحات مبنية على التوافق الذكي بين مرشحيك</p>
        </div>
      </div>

      <div v-if="loading" class="loading-state">
        <div class="spinner-border"></div>
        <p class="loading-state__text">جاري حساب الاقتراحات...</p>
      </div>

      <div v-else-if="suggestions.length" class="suggestions-grid">
        <div v-for="(s, i) in suggestions" :key="i" class="suggestion-card">
          <div class="suggestion-card__header">
            <span class="suggestion-card__rank">#{{ i + 1 }}</span>
            <span class="suggestion-card__score" :class="scoreClass(s.score)">
              <PhStar :size="14" weight="fill" />
              {{ s.score }}%
            </span>
          </div>

          <div class="suggestion-card__pair">
            <div class="suggestion-card__person">
              <div class="suggestion-card__avatar male">{{ s.male.name?.charAt(0) }}</div>
              <div class="name">{{ s.male.name }}</div>
              <div class="meta">{{ s.male.age }} سنة</div>
              <div class="meta">{{ s.male.occupation || '—' }}</div>
            </div>

            <div class="suggestion-card__divider">
              <div class="line"></div>
              <div class="icon">
                <PhHeart :size="20" weight="fill" />
              </div>
              <div class="line"></div>
            </div>

            <div class="suggestion-card__person">
              <div class="suggestion-card__avatar female">{{ s.female.name?.charAt(0) }}</div>
              <div class="name">{{ s.female.name }}</div>
              <div class="meta">{{ s.female.age }} سنة</div>
              <div class="meta">{{ s.female.occupation || '—' }}</div>
            </div>
          </div>

          <div class="suggestion-card__reasons">
            <span v-for="reason in s.reasons" :key="reason" class="reason-chip">
              <PhCheck :size="11" weight="bold" />
              {{ reason }}
            </span>
          </div>

          <button class="btn-action btn-action--primary w-100" @click="openRecommend(s)">
            <PhCheckCircle :size="16" weight="bold" />
            إنشاء توصية
          </button>
        </div>
      </div>

      <div v-else class="empty-state">
        <PhMagnifyingGlass :size="64" weight="duotone" class="empty-state__icon" />
        <h4 class="empty-state__title">لا توجد اقتراحات حالياً</h4>
        <p class="empty-state__desc">أضف مرشحين أولاً ليتمكن النظام من اقتراح التوفيقات بناءً على التوافق</p>
        <router-link to="/recommender/add-candidate" class="btn-action btn-action--primary">
          <PhUserPlus :size="18" />
          إضافة مرشح
        </router-link>
      </div>

      <!-- Modal -->
      <div v-if="selected" class="recommend-modal" @click.self="selected = null">
        <div class="recommend-modal__inner">
          <div class="recommend-modal__header">
            <h3>إنشاء توصية</h3>
            <button class="btn-close-x" @click="selected = null">
              <PhX :size="20" />
            </button>
          </div>
          <div class="recommend-modal__body">
            <div class="modal-pair">
              <div class="person">
                <div class="suggestion-card__avatar male">{{ selected.male.name?.charAt(0) }}</div>
                <strong>{{ selected.male.name }}</strong>
              </div>
              <PhHeart :size="24" weight="fill" class="text-danger" />
              <div class="person">
                <div class="suggestion-card__avatar female">{{ selected.female.name?.charAt(0) }}</div>
                <strong>{{ selected.female.name }}</strong>
              </div>
            </div>

            <div class="dash-form">
              <div class="dash-form__group">
                <label class="dash-form__label">
                  <PhNotePencil :size="14" />
                  سبب التوصية
                </label>
                <textarea
                  v-model="reason"
                  class="dash-form__textarea"
                  placeholder="لماذا تعتقد أن هذا التوفيق مناسب؟"
                  required
                ></textarea>
              </div>
            </div>
          </div>
          <div class="recommend-modal__footer">
            <button class="btn-action btn-action--outline" @click="selected = null">إلغاء</button>
            <button class="btn-action btn-action--primary" :disabled="!reason || recommending" @click="submitRecommend">
              <span v-if="recommending" class="spinner-border spinner-border-sm"></span>
              <PhCheckCircle :size="16" weight="bold" v-else />
              تأكيد التوصية
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
  PhArrowRight, PhMagnifyingGlass, PhStar, PhHeart, PhCheck,
  PhCheckCircle, PhUserPlus, PhX, PhNotePencil,
} from '@phosphor-icons/vue'

const { get, post, loading } = useApi()
const suggestions = ref([])
const selected = ref(null)
const reason = ref('')
const recommending = ref(false)

function scoreClass(score) {
  if (score >= 85) return 'score--excellent'
  if (score >= 70) return 'score--good'
  return 'score--avg'
}

function openRecommend(s) {
  selected.value = s
  reason.value = ''
}

async function submitRecommend() {
  if (!selected.value) return
  recommending.value = true
  try {
    await post('/api/recommender/recommend', {
      male_candidate_id: selected.value.male.id,
      female_candidate_id: selected.value.female.id,
      reason: reason.value,
    })
    suggestions.value = suggestions.value.filter(s => s !== selected.value)
    selected.value = null
  } catch { /* handled */ }
  recommending.value = false
}

onMounted(async () => {
  try {
    suggestions.value = await get('/api/recommender/suggestions')
  } catch { /* handled */ }
})
</script>

<style lang="scss" scoped>
.suggestions-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
  gap: 1.25rem;

  @media (max-width: 576px) { grid-template-columns: 1fr; }
}

.suggestion-card {
  background: #fff;
  border: 1px solid #f0ece4;
  border-radius: 18px;
  padding: 1.5rem;
  transition: all 0.25s;

  &:hover {
    transform: translateY(-3px);
    border-color: #e0d8cc;
    box-shadow: 0 16px 36px rgba(13, 26, 42, 0.06);
  }

  &__header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.25rem;
  }

  &__rank {
    font-size: 0.85rem;
    font-weight: 800;
    color: #8888a0;
  }

  &__score {
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    padding: 0.4rem 0.85rem;
    border-radius: 100px;
    font-weight: 800;
    font-size: 0.85rem;

    &.score--excellent { background: rgba(27,122,74,0.1); color: #1b7a4a; }
    &.score--good { background: rgba(13,115,119,0.1); color: #0d7377; }
    &.score--avg { background: rgba(184,134,11,0.1); color: #b8860b; }
  }

  &__pair {
    display: grid;
    grid-template-columns: 1fr auto 1fr;
    gap: 0.75rem;
    align-items: center;
    margin-bottom: 1rem;
  }

  &__person {
    text-align: center;

    .name {
      font-weight: 800;
      color: #1a1a2a;
      font-size: 0.95rem;
      margin-top: 0.5rem;
      margin-bottom: 0.2rem;
    }
    .meta {
      font-size: 0.75rem;
      color: #8888a0;
    }
  }

  &__avatar {
    width: 56px;
    height: 56px;
    border-radius: 50%;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    font-size: 1.4rem;
    margin: 0 auto;

    &.male { background: linear-gradient(135deg, #1565c0 0%, #0d4a8f 100%); }
    &.female { background: linear-gradient(135deg, #b8860b 0%, #8a6508 100%); }
  }

  &__divider {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.4rem;

    .line {
      width: 1px;
      height: 16px;
      background: #e0d8cc;
    }

    .icon {
      color: #c0392b;
    }
  }

  &__reasons {
    display: flex;
    flex-wrap: wrap;
    gap: 0.4rem;
    padding: 1rem 0;
    border-top: 1px dashed #f0ece4;
    margin-bottom: 1rem;
  }
}

.reason-chip {
  display: inline-flex;
  align-items: center;
  gap: 0.25rem;
  padding: 0.3rem 0.7rem;
  background: rgba(27,122,74,0.08);
  color: #1b7a4a;
  border-radius: 100px;
  font-size: 0.72rem;
  font-weight: 700;
}

// Modal
.recommend-modal {
  position: fixed;
  inset: 0;
  background: rgba(13, 26, 42, 0.6);
  backdrop-filter: blur(4px);
  z-index: 1100;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 1rem;

  &__inner {
    background: #fff;
    border-radius: 20px;
    max-width: 520px;
    width: 100%;
    max-height: 90vh;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    box-shadow: 0 40px 80px rgba(0, 0, 0, 0.3);
  }

  &__header {
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid #f0ece4;
    display: flex;
    align-items: center;
    justify-content: space-between;

    h3 { margin: 0; font-weight: 800; font-size: 1.15rem; }
  }

  &__body { padding: 1.5rem; overflow-y: auto; }
  &__footer {
    padding: 1.25rem 1.5rem;
    border-top: 1px solid #f0ece4;
    display: flex;
    justify-content: flex-end;
    gap: 0.5rem;
    background: #faf9f6;
  }
}

.btn-close-x {
  background: #faf9f6;
  border: 1.5px solid #e0d8cc;
  width: 36px;
  height: 36px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  color: #4a4a5e;

  &:hover { background: #f0ece4; color: #c0392b; }
}

.modal-pair {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 1.5rem;
  padding: 1.5rem;
  background: #faf9f6;
  border-radius: 14px;
  margin-bottom: 1.25rem;

  .person {
    text-align: center;
    strong { display: block; margin-top: 0.5rem; }
  }
}
</style>
