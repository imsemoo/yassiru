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

        <h1 class="auth-form-wrap__title">نسيت كلمة المرور؟</h1>
        <p class="auth-form-wrap__subtitle">أدخل بريدك الإلكتروني وسنرسل لك رابط إعادة التعيين</p>

        <!-- Success State -->
        <div v-if="sent" class="success-card">
          <div class="success-card__icon">
            <PhEnvelopeOpen :size="48" weight="duotone" />
          </div>
          <h3 class="success-card__title">تم إرسال الرابط!</h3>
          <p class="success-card__text">{{ message || 'افتح بريدك الإلكتروني واتبع التعليمات لإعادة تعيين كلمة المرور.' }}</p>
          <div class="success-card__hint">
            <PhInfo :size="14" />
            لم تجد الرسالة؟ تحقق من مجلد البريد المزعج (Spam)
          </div>
          <router-link to="/login" class="auth-form__submit mt-3">
            <PhSignIn :size="18" weight="bold" />
            العودة لتسجيل الدخول
          </router-link>
        </div>

        <!-- Form State -->
        <form v-else class="auth-form" @submit.prevent="submit">
          <div v-if="error" class="auth-form-wrap__alert">
            <PhWarningCircle :size="18" weight="fill" />
            <span>{{ error }}</span>
          </div>

          <div class="auth-form__group">
            <label for="forgot-email" class="auth-form__label">
              <PhEnvelope :size="16" weight="bold" />
              البريد الإلكتروني
            </label>
            <div class="auth-form__input-wrap">
              <input
                id="forgot-email"
                v-model="email"
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

          <button type="submit" class="auth-form__submit" :disabled="submitting">
            <span v-if="submitting" class="spinner-border spinner-border-sm"></span>
            <PhPaperPlaneTilt :size="20" weight="bold" v-else />
            {{ submitting ? 'جاري الإرسال...' : 'إرسال رابط الاستعادة' }}
          </button>

          <div class="auth-form__divider">أو</div>

          <p class="auth-form__footer">
            تذكّرت كلمة المرور؟
            <router-link to="/login">سجّل دخولك</router-link>
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
          نحن<br>
          <span class="accent">معك</span>
        </h2>
        <p class="auth-brand__subtitle">
          استعادة كلمة المرور بسيطة وآمنة. خلال دقائق ستعود إلى رحلتك مع يسّرو.
        </p>

        <div class="auth-brand__benefits">
          <div class="auth-brand__benefit">
            <div class="auth-brand__benefit__icon">
              <PhPaperPlaneTilt :size="20" weight="duotone" />
            </div>
            <div class="auth-brand__benefit__text">
              <div class="title">رابط فوري</div>
              <div class="desc">سيصلك رابط إعادة التعيين خلال دقيقة</div>
            </div>
          </div>

          <div class="auth-brand__benefit">
            <div class="auth-brand__benefit__icon">
              <PhClock :size="20" weight="duotone" />
            </div>
            <div class="auth-brand__benefit__text">
              <div class="title">صالح لمدة 60 دقيقة</div>
              <div class="desc">الرابط آمن وينتهي بعد ساعة لحماية حسابك</div>
            </div>
          </div>

          <div class="auth-brand__benefit">
            <div class="auth-brand__benefit__icon">
              <PhShieldCheck :size="20" weight="duotone" />
            </div>
            <div class="auth-brand__benefit__text">
              <div class="title">آمن 100%</div>
              <div class="desc">عملية الاستعادة مشفّرة وموثّقة</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useApi } from '@/composables/useApi'
import {
  PhArrowRight, PhEnvelope, PhEnvelopeOpen, PhInfo, PhSignIn,
  PhWarningCircle, PhPaperPlaneTilt, PhClock, PhShieldCheck,
} from '@phosphor-icons/vue'

const { post } = useApi()
const email = ref('')
const error = ref(null)
const message = ref('')
const sent = ref(false)
const submitting = ref(false)

async function submit() {
  error.value = null
  submitting.value = true
  try {
    const data = await post('/api/auth/forgot-password', { email: email.value })
    message.value = data.message
    sent.value = true
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

  &__hint {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    background: rgba(184,134,11,0.08);
    color: #b8860b;
    padding: 0.65rem 1rem;
    border-radius: 100px;
    font-size: 0.8rem;
    font-weight: 600;
  }
}
</style>
