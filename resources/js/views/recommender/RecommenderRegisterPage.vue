<template>
  <div class="dashboard-page">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-7">
          <div class="page-header">
            <div>
              <h1 class="page-header__title">
                <span class="page-header__title__icon" style="--header-bg: rgba(21,101,192,0.1); --header-color: #1565c0">
                  <PhHandshake :size="24" weight="duotone" />
                </span>
                التسجيل كمعرّف
              </h1>
              <p class="page-header__subtitle">
                المعرّف هو شخص موثوق يقوم بترشيح طرفي الزواج والتوفيق بينهم
              </p>
            </div>
          </div>

          <!-- Info Card -->
          <div class="dash-card info-card">
            <div class="dash-card__body">
              <h3 class="info-card__title">
                <PhInfo :size="20" weight="fill" />
                ما هو دور المعرّف؟
              </h3>
              <ul class="info-list">
                <li>ترشيح الشباب والشابات المؤهلين للزواج</li>
                <li>اقتراح التوفيقات المناسبة بناءً على المعرفة والثقة</li>
                <li>التواصل بين الأهل والعائلات</li>
                <li>الحفاظ على خصوصية وكرامة المرشحين</li>
              </ul>
              <div class="info-card__warning">
                <PhWarning :size="18" weight="fill" />
                <div>
                  <strong>تنبيه:</strong> بعد التسجيل كمعرّف، لن تتمكن من الوصول لميزات
                  المستخدم العادي (الكورسات، الصندوق، الأعراس). تأكد من إلغاء أي
                  التزامات نشطة قبل المتابعة.
                </div>
              </div>
            </div>
          </div>

          <!-- Registration Form -->
          <div class="dash-card">
            <div class="dash-card__body">
              <div v-if="errorMsg" class="dash-alert dash-alert--danger">
                <PhWarningCircle :size="20" weight="fill" class="dash-alert__icon" />
                <div class="dash-alert__content">{{ errorMsg }}</div>
              </div>

              <div v-if="successMsg" class="dash-alert dash-alert--success">
                <PhCheckCircle :size="20" weight="fill" class="dash-alert__icon" />
                <div class="dash-alert__content">{{ successMsg }}</div>
              </div>

              <form class="dash-form" @submit.prevent="submit">
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
                  <input
                    v-model="form.institution"
                    type="text"
                    class="dash-form__input"
                    placeholder="مثال: مسجد الرحمة"
                  />
                </div>

                <div class="dash-form__group">
                  <label class="dash-form__label">
                    <PhNotePencil :size="14" />
                    نبذة تعريفية (اختياري)
                  </label>
                  <textarea
                    v-model="form.bio"
                    class="dash-form__textarea"
                    rows="4"
                    placeholder="عرّف عن نفسك وخبرتك في التوفيق"
                  ></textarea>
                </div>

                <button
                  type="submit"
                  class="btn-action btn-action--primary"
                  :disabled="submitting"
                >
                  <span v-if="submitting" class="spinner-border spinner-border-sm"></span>
                  <PhCheckCircle :size="18" weight="bold" v-else />
                  {{ submitting ? 'جاري التسجيل...' : 'تسجيل كمعرّف' }}
                </button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import { useAuthStore } from '@/stores/auth'
import {
  PhHandshake, PhInfo, PhWarning, PhWarningCircle, PhCheckCircle,
  PhUserCircle, PhBuildings, PhNotePencil,
} from '@phosphor-icons/vue'

const router = useRouter()
const auth = useAuthStore()
const submitting = ref(false)
const errorMsg = ref('')
const successMsg = ref('')
const form = ref({ type: '', institution: '', bio: '' })

onMounted(() => {
  // If already a recommender, go straight to dashboard
  if (auth.isRecommender) {
    router.replace('/recommender')
  }
})

async function submit() {
  submitting.value = true
  errorMsg.value = ''
  successMsg.value = ''

  try {
    const { data } = await axios.post('/api/recommender/register', form.value)

    // Update auth store with fresh user + token
    if (data.token && data.user) {
      const userData = data.user.data || data.user
      auth.setAuth(userData, data.token)
    }

    successMsg.value = data.message || 'تم التسجيل بنجاح'

    // Redirect to dashboard after a short delay
    setTimeout(() => {
      router.push('/recommender')
    }, 1500)
  } catch (e) {
    errorMsg.value = e.response?.data?.message || 'حدث خطأ أثناء التسجيل'
    submitting.value = false
  }
}
</script>

<style lang="scss" scoped>
.info-card {
  background: linear-gradient(135deg, #fff 0%, rgba(21, 101, 192, 0.03) 100%);

  &__title {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #1565c0;
    font-size: 1.1rem;
    font-weight: 800;
    margin-bottom: 1rem;
  }

  &__warning {
    display: flex;
    gap: 0.75rem;
    margin-top: 1.25rem;
    padding: 1rem;
    background: rgba(184, 134, 11, 0.08);
    border-right: 3px solid #b8860b;
    border-radius: 10px;
    font-size: 0.85rem;
    color: #4a4a5e;
    line-height: 1.8;

    > svg {
      color: #b8860b;
      flex-shrink: 0;
      margin-top: 2px;
    }

    strong {
      color: #b8860b;
    }
  }
}

.info-list {
  list-style: none;
  padding: 0;
  margin: 0;

  li {
    position: relative;
    padding-right: 1.5rem;
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
    color: #4a4a5e;

    &::before {
      content: '✓';
      position: absolute;
      right: 0;
      color: #1b7a4a;
      font-weight: 900;
    }
  }
}
</style>
