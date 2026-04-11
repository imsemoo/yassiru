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

        <h1 class="auth-form-wrap__title">مرحباً بعودتك 👋</h1>
        <p class="auth-form-wrap__subtitle">سجّل دخولك للمتابعة في رحلتك مع يسّرو</p>

        <div v-if="error" class="auth-form-wrap__alert">
          <PhWarningCircle :size="18" weight="fill" />
          <span>{{ error }}</span>
        </div>

        <form class="auth-form" @submit.prevent="handleLogin">
          <div class="auth-form__group">
            <label for="email" class="auth-form__label">
              <PhEnvelope :size="16" weight="bold" />
              البريد الإلكتروني
            </label>
            <div class="auth-form__input-wrap">
              <input
                id="email"
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
            <label for="password" class="auth-form__label">
              <PhLock :size="16" weight="bold" />
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
                placeholder="••••••••"
                dir="ltr"
                required
                autocomplete="current-password"
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

          <button type="submit" class="auth-form__submit" :disabled="loading">
            <span v-if="loading" class="spinner-border spinner-border-sm"></span>
            <PhSignIn :size="20" weight="bold" v-else />
            {{ loading ? 'جاري الدخول...' : 'تسجيل الدخول' }}
          </button>

          <div class="auth-form__divider">أو</div>

          <p class="auth-form__footer">
            ليس لديك حساب؟
            <router-link to="/register">سجّل مجاناً</router-link>
          </p>
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
          أهلاً بك في<br>
          <span class="accent">يسّرو</span>
        </h2>
        <p class="auth-brand__subtitle">
          منصتك المتكاملة لتيسير الزواج —
          من التأهيل إلى الاحتفال.
        </p>

        <div class="auth-brand__benefits">
          <div class="auth-brand__benefit">
            <div class="auth-brand__benefit__icon">
              <PhBookOpen :size="20" weight="duotone" />
            </div>
            <div class="auth-brand__benefit__text">
              <div class="title">دورة تأهيلية مجانية</div>
              <div class="desc">15 ساعة تعليم منظّم بـ 4 مسارات</div>
            </div>
          </div>

          <div class="auth-brand__benefit">
            <div class="auth-brand__benefit__icon">
              <PhHandCoins :size="20" weight="duotone" />
            </div>
            <div class="auth-brand__benefit__text">
              <div class="title">صندوق تعاوني بدون فوائد</div>
              <div class="desc">احصل على مبلغ تجهيز زواجك كاملاً</div>
            </div>
          </div>

          <div class="auth-brand__benefit">
            <div class="auth-brand__benefit__icon">
              <PhHeart :size="20" weight="duotone" />
            </div>
            <div class="auth-brand__benefit__text">
              <div class="title">أعراس جماعية مهيبة</div>
              <div class="desc">وفّر حتى 70% من تكاليف الفرح</div>
            </div>
          </div>
        </div>

        <div class="auth-brand__quote">
          <p class="auth-brand__quote__text">
            يسّرو غيّرت حياتي. كنت يائساً من الزواج بسبب التكاليف،
            وعبر الصندوق التعاوني والعرس الجماعي قدرت أتزوّج بدون ديون.
          </p>
          <div class="auth-brand__quote__author">
            <div class="auth-brand__quote__author-avatar">م</div>
            <div class="auth-brand__quote__author-info">
              <p class="name">محمد عبدالله</p>
              <span class="role">عضو منذ 2024 — القاهرة</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useRouter, useRoute } from 'vue-router'
import {
  PhEnvelope, PhLock, PhEye, PhEyeSlash, PhSignIn, PhWarningCircle,
  PhArrowRight, PhBookOpen, PhHandCoins, PhHeart,
} from '@phosphor-icons/vue'

const auth = useAuthStore()
const router = useRouter()
const route = useRoute()

const form = reactive({ email: '', password: '' })
const loading = ref(false)
const error = ref('')
const showPass = ref(false)

async function handleLogin() {
  loading.value = true
  error.value = ''
  try {
    await auth.login(form)
    router.push(route.query.redirect || '/')
  } catch (err) {
    error.value = err.response?.data?.message || 'حدث خطأ أثناء تسجيل الدخول'
  } finally {
    loading.value = false
  }
}
</script>
