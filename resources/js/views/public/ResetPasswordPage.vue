<template>
  <div class="auth-page">
    <!-- ============================================ -->
    <!-- FORM SIDE                                    -->
    <!-- ============================================ -->
    <div class="auth-form-side">
      <div class="auth-form-wrap">
        <router-link to="/login" class="auth-form-wrap__back">
          <PhArrowRight :size="14" weight="bold" />
          العودة لتسجيل الدخول
        </router-link>

        <div class="auth-form-wrap__logo-mobile">
          <img src="/logo.svg" alt="يسّرو">
        </div>

        <h1 class="auth-form-wrap__title">إعادة تعيين كلمة المرور</h1>
        <p class="auth-form-wrap__subtitle">اختر كلمة مرور جديدة قوية لحسابك</p>

        <!-- Success State -->
        <div v-if="success" class="success-card">
          <div class="success-card__icon">
            <PhCheckCircle :size="48" weight="duotone" />
          </div>
          <h3 class="success-card__title">تم بنجاح!</h3>
          <p class="success-card__text">{{ message || 'تم إعادة تعيين كلمة المرور. يمكنك الآن تسجيل الدخول بكلمة المرور الجديدة.' }}</p>
          <router-link to="/login" class="auth-form__submit mt-3">
            <PhSignIn :size="18" weight="bold" />
            تسجيل الدخول
          </router-link>
        </div>

        <!-- Form -->
        <form v-else class="auth-form" @submit.prevent="submit">
          <div v-if="error" class="auth-form-wrap__alert">
            <PhWarningCircle :size="18" weight="fill" />
            <span>{{ error }}</span>
          </div>

          <div class="auth-form__group">
            <label for="reset-email" class="auth-form__label">
              <PhEnvelope :size="16" weight="bold" />
              البريد الإلكتروني
            </label>
            <div class="auth-form__input-wrap">
              <input
                id="reset-email"
                v-model="form.email"
                type="email"
                class="auth-form__input has-icon"
                placeholder="example@email.com"
                dir="ltr"
                required
                autocomplete="email"
              >
              <PhEnvelope :size="18" class="auth-form__icon" />
            </div>
          </div>

          <div class="auth-form__group">
            <label for="reset-password" class="auth-form__label">
              <PhLock :size="16" weight="bold" />
              كلمة المرور الجديدة
            </label>
            <div class="auth-form__input-wrap">
              <input
                id="reset-password"
                v-model="form.password"
                :type="showPass ? 'text' : 'password'"
                class="auth-form__input has-icon has-toggle"
                placeholder="8 أحرف على الأقل"
                dir="ltr"
                minlength="8"
                required
                autocomplete="new-password"
              >
              <PhLock :size="18" class="auth-form__icon" />
              <button
                type="button"
                class="auth-form__toggle"
                @click="showPass = !showPass"
                tabindex="-1"
                aria-label="إظهار/إخفاء كلمة المرور"
              >
                <PhEye :size="18" v-if="!showPass" />
                <PhEyeSlash :size="18" v-else />
              </button>
            </div>
          </div>

          <div class="auth-form__group">
            <label for="reset-password-confirm" class="auth-form__label">
              <PhLockKey :size="16" weight="bold" />
              تأكيد كلمة المرور
            </label>
            <div class="auth-form__input-wrap">
              <input
                id="reset-password-confirm"
                v-model="form.password_confirmation"
                type="password"
                class="auth-form__input has-icon"
                placeholder="أعد كتابة كلمة المرور"
                dir="ltr"
                minlength="8"
                required
                autocomplete="new-password"
              >
              <PhLockKey :size="18" class="auth-form__icon" />
            </div>
          </div>

          <button type="submit" class="auth-form__submit" :disabled="submitting">
            <span v-if="submitting" class="spinner-border spinner-border-sm"></span>
            <PhArrowsClockwise :size="20" weight="bold" v-else />
            {{ submitting ? 'جاري التغيير...' : 'إعادة تعيين كلمة المرور' }}
          </button>
        </form>
      </div>
    </div>

    <!-- ============================================ -->
    <!-- BRAND SIDE                                   -->
    <!-- ============================================ -->
    <div class="auth-brand-side">
      <div class="auth-brand">
        <img src="/logo.svg" alt="يسّرو" class="auth-brand__logo">

        <h2 class="auth-brand__title">
          أنشئ كلمة مرور<br>
          <span class="accent">قوية وآمنة</span>
        </h2>
        <p class="auth-brand__subtitle">
          اختر كلمة مرور صعبة التخمين لحماية حسابك ورحلتك مع يسّرو.
        </p>

        <div class="auth-brand__benefits">
          <div class="auth-brand__benefit">
            <div class="auth-brand__benefit__icon">
              <PhCheck :size="20" weight="duotone" />
            </div>
            <div class="auth-brand__benefit__text">
              <div class="title">8 أحرف على الأقل</div>
              <div class="desc">كلما زادت الأحرف، زاد الأمان</div>
            </div>
          </div>

          <div class="auth-brand__benefit">
            <div class="auth-brand__benefit__icon">
              <PhCheck :size="20" weight="duotone" />
            </div>
            <div class="auth-brand__benefit__text">
              <div class="title">امزج الحروف والأرقام</div>
              <div class="desc">حروف كبيرة وصغيرة + أرقام = أقوى</div>
            </div>
          </div>

          <div class="auth-brand__benefit">
            <div class="auth-brand__benefit__icon">
              <PhCheck :size="20" weight="duotone" />
            </div>
            <div class="auth-brand__benefit__text">
              <div class="title">تجنّب المعلومات الشخصية</div>
              <div class="desc">لا تستخدم اسمك أو تاريخ ميلادك</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { useApi } from '@/composables/useApi'
import {
  PhArrowRight, PhEnvelope, PhLock, PhLockKey, PhEye, PhEyeSlash,
  PhSignIn, PhCheckCircle, PhWarningCircle, PhArrowsClockwise, PhCheck,
} from '@phosphor-icons/vue'

const route = useRoute()
const { post } = useApi()

const form = ref({
  email: '',
  password: '',
  password_confirmation: '',
  token: '',
})
const error = ref(null)
const message = ref('')
const success = ref(false)
const submitting = ref(false)
const showPass = ref(false)

onMounted(() => {
  form.value.token = route.query.token || ''
  form.value.email = route.query.email || ''
})

async function submit() {
  error.value = null
  submitting.value = true
  try {
    const data = await post('/api/auth/reset-password', form.value)
    message.value = data.message
    success.value = true
  } catch (e) {
    error.value = e.response?.data?.message || 'حدث خطأ غير متوقع'
  }
  submitting.value = false
}
</script>

<style lang="scss" scoped>
.success-card {
  text-align: center;
  padding: 1rem 0;

  &__icon {
    width: 96px;
    height: 96px;
    border-radius: 50%;
    background: rgba(27,122,74,0.1);
    color: #1b7a4a;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1.25rem;
  }

  &__title {
    font-size: 1.5rem;
    font-weight: 900;
    color: #1b7a4a;
    margin: 0 0 0.5rem;
  }

  &__text {
    color: #4a4a5e;
    line-height: 1.95;
    margin-bottom: 1rem;
  }
}
</style>
