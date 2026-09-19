<template>
  <div class="auth-page">
    <!-- ============================================ -->
    <!-- FORM SIDE                                    -->
    <!-- ============================================ -->
    <div class="auth-form-side">
      <div class="auth-form-wrap">
        <router-link to="/" class="auth-form-wrap__back">
          <PhArrowRight :size="14" weight="bold" />
          العودة للرئيسية
        </router-link>

        <div class="auth-form-wrap__logo-mobile">
          <img src="/logo.svg" alt="يسّرو">
        </div>

        <!-- Says why the guard sent them here, and where they are going next -->
        <p v-if="destination" class="auth-form-wrap__dest">
          للمتابعة إلى <strong>{{ destination }}</strong>
        </p>

        <h1 class="auth-form-wrap__title">مرحباً بعودتك</h1>
        <p class="auth-form-wrap__subtitle">سجّل دخولك للمتابعة في رحلتك مع يسّرو</p>

        <Transition name="auth-alert">
          <div v-if="error" class="auth-form-wrap__alert" role="alert">
            <PhWarningCircle :size="18" weight="fill" />
            <span>{{ error }}</span>
          </div>
        </Transition>

        <form class="auth-form" @submit.prevent="handleLogin">
          <div class="auth-form__group">
            <label for="email" class="auth-form__label">البريد الإلكتروني</label>
            <div class="auth-form__input-wrap">
              <input
                id="email"
                ref="emailField"
                v-model="form.email"
                type="email"
                class="auth-form__input has-icon"
                placeholder="example@email.com"
                required
                autocomplete="email"
              >
              <PhEnvelope :size="18" class="auth-form__icon" />
            </div>
          </div>

          <div class="auth-form__group">
            <label for="password" class="auth-form__label">
              كلمة المرور
              <router-link to="/forgot-password" class="label-action">
                نسيت كلمة المرور؟
              </router-link>
            </label>
            <div class="auth-form__input-wrap">
              <input
                id="password"
                v-model="form.password"
                :type="showPass ? 'text' : 'password'"
                class="auth-form__input has-icon has-toggle"
                dir="ltr"
                required
                autocomplete="current-password"
              >
              <PhLock :size="18" class="auth-form__icon" />
              <button
                type="button"
                class="auth-form__toggle"
                :aria-label="showPass ? 'إخفاء كلمة المرور' : 'إظهار كلمة المرور'"
                :aria-pressed="showPass"
                @click="showPass = !showPass"
              >
                <PhEye :size="18" v-if="!showPass" />
                <PhEyeSlash :size="18" v-else />
              </button>
            </div>
          </div>

          <button type="submit" class="auth-form__submit" :disabled="loading">
            <span v-if="loading" class="spinner-border spinner-border-sm"></span>
            {{ loading ? 'جاري الدخول…' : 'تسجيل الدخول' }}
            <PhArrowLeft :size="18" weight="bold" v-if="!loading" />
          </button>

          <p class="auth-form__footer">
            ليس لديك حساب؟
            <router-link :to="registerLink">سجّل مجاناً</router-link>
          </p>
        </form>
      </div>
    </div>

    <!-- ============================================ -->
    <!-- BRAND SIDE                                   -->
    <!-- ============================================ -->
    <div class="auth-brand-side">
      <div class="auth-brand">
        <img src="/logo.svg" alt="" class="auth-brand__logo">

        <p class="auth-brand__hadith">يسِّروا ولا تُعسِّروا</p>
        <p class="auth-brand__hadith-src">ومن الحديث جاء الاسم</p>

        <h2 class="auth-brand__title">
          منصتك المتكاملة<br>
          <span class="accent">لتيسير الزواج</span>
        </h2>
        <p class="auth-brand__subtitle">
          من التأهيل إلى الاحتفال — في رحلة واحدة.
        </p>

        <div class="auth-brand__benefits">
          <div class="auth-brand__benefit">
            <div class="auth-brand__benefit__icon">01</div>
            <div class="auth-brand__benefit__text">
              <div class="title">دورة تأهيلية مجانية</div>
              <div class="desc">15 ساعة تعليم منظّم في 4 مسارات</div>
            </div>
          </div>

          <div class="auth-brand__benefit">
            <div class="auth-brand__benefit__icon">02</div>
            <div class="auth-brand__benefit__text">
              <div class="title">صندوق تعاوني بدون فوائد</div>
              <div class="desc">احصل على مبلغ تجهيز زواجك كاملاً</div>
            </div>
          </div>

          <div class="auth-brand__benefit">
            <div class="auth-brand__benefit__icon">03</div>
            <div class="auth-brand__benefit__text">
              <div class="title">أعراس جماعية مهيبة</div>
              <div class="desc">وفّر حتى 70% من تكاليف الفرح</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useRouter, useRoute } from 'vue-router'
import {
  PhEnvelope, PhLock, PhEye, PhEyeSlash, PhWarningCircle,
  PhArrowRight, PhArrowLeft,
} from '@phosphor-icons/vue'

const auth = useAuthStore()
const router = useRouter()
const route = useRoute()

const form = reactive({ email: '', password: '' })
const loading = ref(false)
const error = ref('')
const showPass = ref(false)
const emailField = ref(null)

// A guard that bounces someone mid-journey should say where they were going.
const DESTINATIONS = {
  '/courses': 'الدورة التأهيلية',
  '/fund': 'الصندوق التعاوني',
  '/weddings': 'الأعراس الجماعية',
  '/counseling': 'الاستشارات',
  '/community': 'المجتمع',
  '/profile': 'ملفك الشخصي',
  '/calculator': 'حاسبة التكاليف',
}

const redirectPath = computed(() => {
  const r = route.query.redirect
  return typeof r === 'string' && r.startsWith('/') ? r : ''
})

const destination = computed(() => {
  if (!redirectPath.value) return ''
  const match = Object.keys(DESTINATIONS).find(
    (k) => redirectPath.value === k || redirectPath.value.startsWith(k + '/')
  )
  return match ? DESTINATIONS[match] : ''
})

// Carry the destination across to registration, so a new account lands there too.
const registerLink = computed(() =>
  redirectPath.value ? { path: '/register', query: { redirect: redirectPath.value } } : '/register'
)

onMounted(() => emailField.value?.focus())

async function handleLogin() {
  loading.value = true
  error.value = ''
  try {
    await auth.login(form)
    router.push(redirectPath.value || '/')
  } catch (err) {
    error.value = err.response?.data?.message || 'تعذّر تسجيل الدخول. تحقّق من البريد وكلمة المرور.'
  } finally {
    loading.value = false
  }
}
</script>
