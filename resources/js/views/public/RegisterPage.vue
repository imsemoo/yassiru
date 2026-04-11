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

        <h1 class="auth-form-wrap__title">أنشئ حسابك مجاناً</h1>
        <p class="auth-form-wrap__subtitle">3 دقائق فقط لتبدأ رحلتك نحو زواج ميسّر</p>

        <div v-if="generalError" class="auth-form-wrap__alert">
          <PhWarningCircle :size="18" weight="fill" />
          <span>{{ generalError }}</span>
        </div>

        <form class="auth-form" @submit.prevent="handleRegister">
          <!-- Name -->
          <div class="auth-form__group">
            <label for="name" class="auth-form__label">
              <PhUser :size="16" weight="bold" />
              الاسم الكامل
            </label>
            <div class="auth-form__input-wrap">
              <input
                id="name"
                v-model="form.name"
                type="text"
                class="auth-form__input has-icon"
                :class="{ 'is-invalid': errors.name }"
                placeholder="مثال: أحمد محمد"
                required
                autocomplete="name"
              >
              <PhUser :size="18" class="auth-form__icon" />
            </div>
            <div v-if="errors.name" class="auth-form__error">
              <PhWarningCircle :size="13" weight="fill" />
              {{ errors.name[0] }}
            </div>
          </div>

          <!-- Email -->
          <div class="auth-form__group">
            <label for="reg-email" class="auth-form__label">
              <PhEnvelope :size="16" weight="bold" />
              البريد الإلكتروني
            </label>
            <div class="auth-form__input-wrap">
              <input
                id="reg-email"
                v-model="form.email"
                type="email"
                class="auth-form__input has-icon"
                :class="{ 'is-invalid': errors.email }"
                placeholder="example@email.com"
                dir="ltr"
                required
                autocomplete="email"
              >
              <PhEnvelope :size="18" class="auth-form__icon" />
            </div>
            <div v-if="errors.email" class="auth-form__error">
              <PhWarningCircle :size="13" weight="fill" />
              {{ errors.email[0] }}
            </div>
          </div>

          <!-- Phone -->
          <div class="auth-form__group">
            <label for="phone" class="auth-form__label">
              <PhPhone :size="16" weight="bold" />
              رقم الهاتف
            </label>
            <div class="auth-form__input-wrap">
              <input
                id="phone"
                v-model="form.phone"
                type="tel"
                class="auth-form__input has-icon"
                :class="{ 'is-invalid': errors.phone }"
                placeholder="+201001234567"
                dir="ltr"
                required
                autocomplete="tel"
              >
              <PhPhone :size="18" class="auth-form__icon" />
            </div>
            <div v-if="errors.phone" class="auth-form__error">
              <PhWarningCircle :size="13" weight="fill" />
              {{ errors.phone[0] }}
            </div>
          </div>

          <!-- Gender + City -->
          <div class="auth-form__row">
            <div class="auth-form__group">
              <label for="gender" class="auth-form__label">
                <PhGenderIntersex :size="16" weight="bold" />
                الجنس
              </label>
              <select
                id="gender"
                v-model="form.gender"
                class="auth-form__input"
                :class="{ 'is-invalid': errors.gender }"
                required
              >
                <option value="" disabled>اختر</option>
                <option value="male">ذكر</option>
                <option value="female">أنثى</option>
              </select>
              <div v-if="errors.gender" class="auth-form__error">
                <PhWarningCircle :size="13" weight="fill" />
                {{ errors.gender[0] }}
              </div>
            </div>

            <div class="auth-form__group">
              <label for="city" class="auth-form__label">
                <PhMapPin :size="16" weight="bold" />
                المدينة
              </label>
              <select
                id="city"
                v-model="form.city_id"
                class="auth-form__input"
                :class="{ 'is-invalid': errors.city_id }"
                required
              >
                <option value="" disabled>اختر مدينتك</option>
                <option v-for="city in cities" :key="city.id" :value="city.id">
                  {{ city.name }}
                </option>
              </select>
              <div v-if="errors.city_id" class="auth-form__error">
                <PhWarningCircle :size="13" weight="fill" />
                {{ errors.city_id[0] }}
              </div>
            </div>
          </div>

          <!-- Password -->
          <div class="auth-form__group">
            <label for="reg-password" class="auth-form__label">
              <PhLock :size="16" weight="bold" />
              كلمة المرور
            </label>
            <div class="auth-form__input-wrap">
              <input
                id="reg-password"
                v-model="form.password"
                :type="showPass ? 'text' : 'password'"
                class="auth-form__input has-icon has-toggle"
                :class="{ 'is-invalid': errors.password }"
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
            <div v-if="errors.password" class="auth-form__error">
              <PhWarningCircle :size="13" weight="fill" />
              {{ errors.password[0] }}
            </div>
          </div>

          <!-- Confirm Password -->
          <div class="auth-form__group">
            <label for="password-confirm" class="auth-form__label">
              <PhLockKey :size="16" weight="bold" />
              تأكيد كلمة المرور
            </label>
            <div class="auth-form__input-wrap">
              <input
                id="password-confirm"
                v-model="form.password_confirmation"
                type="password"
                class="auth-form__input has-icon"
                placeholder="أعد كتابة كلمة المرور"
                dir="ltr"
                required
                autocomplete="new-password"
              >
              <PhLockKey :size="18" class="auth-form__icon" />
            </div>
          </div>

          <button type="submit" class="auth-form__submit" :disabled="loading">
            <span v-if="loading" class="spinner-border spinner-border-sm"></span>
            <PhUserPlus :size="20" weight="bold" v-else />
            {{ loading ? 'جاري التسجيل...' : 'أنشئ حسابي مجاناً' }}
          </button>

          <p class="text-center text-muted small mt-3 mb-0" style="font-size: 0.8rem">
            بالتسجيل، أنت توافق على شروط الاستخدام وسياسة الخصوصية الخاصة بمنصة يسّرو
          </p>

          <div class="auth-form__divider">أو</div>

          <p class="auth-form__footer">
            لديك حساب بالفعل؟
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
          ابدأ رحلتك<br>
          <span class="accent">من اليوم</span>
        </h2>
        <p class="auth-brand__subtitle">
          انضم لآلاف المسلمين الذين اختاروا تيسير الزواج —
          بدون فوائد، بدون تعقيد، بدون ديون.
        </p>

        <div class="auth-brand__benefits">
          <div class="auth-brand__benefit">
            <div class="auth-brand__benefit__icon">
              <PhCheckCircle :size="20" weight="duotone" />
            </div>
            <div class="auth-brand__benefit__text">
              <div class="title">التسجيل مجاني تماماً</div>
              <div class="desc">لا رسوم خفية، لا التزامات</div>
            </div>
          </div>

          <div class="auth-brand__benefit">
            <div class="auth-brand__benefit__icon">
              <PhBookOpen :size="20" weight="duotone" />
            </div>
            <div class="auth-brand__benefit__text">
              <div class="title">دورة تأهيلية مجانية</div>
              <div class="desc">15 ساعة تعلّم منظّم بشهادة معتمدة</div>
            </div>
          </div>

          <div class="auth-brand__benefit">
            <div class="auth-brand__benefit__icon">
              <PhShieldCheck :size="20" weight="duotone" />
            </div>
            <div class="auth-brand__benefit__text">
              <div class="title">خصوصيتك مقدّسة</div>
              <div class="desc">صفر تواصل مباشر، تشفير كامل للبيانات</div>
            </div>
          </div>
        </div>

        <div class="auth-brand__quote">
          <p class="auth-brand__quote__text">
            بدأت رحلتي في يسّرو من تسجيل بسيط، وفي 6 أشهر تزوّجت بدون ديون.
            النظام محترم ويراعي خصوصيتي تماماً.
          </p>
          <div class="auth-brand__quote__author">
            <div class="auth-brand__quote__author-avatar">س</div>
            <div class="auth-brand__quote__author-info">
              <p class="name">سارة أحمد</p>
              <span class="role">عضوة منذ 2024 — الإسكندرية</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useRouter } from 'vue-router'
import axios from 'axios'
import {
  PhUser, PhEnvelope, PhPhone, PhGenderIntersex, PhMapPin,
  PhLock, PhLockKey, PhUserPlus, PhWarningCircle, PhEye, PhEyeSlash,
  PhArrowRight, PhCheckCircle, PhBookOpen, PhShieldCheck,
} from '@phosphor-icons/vue'

const auth = useAuthStore()
const router = useRouter()

const form = reactive({
  name: '',
  email: '',
  phone: '',
  gender: '',
  city_id: '',
  password: '',
  password_confirmation: '',
})

const cities = ref([])
const loading = ref(false)
const errors = ref({})
const generalError = ref('')
const showPass = ref(false)

onMounted(async () => {
  try {
    const { data } = await axios.get('/api/calculator/cities')
    cities.value = data
  } catch { /* silent */ }
})

async function handleRegister() {
  loading.value = true
  errors.value = {}
  generalError.value = ''

  try {
    await auth.register(form)
    router.push('/courses')
  } catch (err) {
    if (err.response?.status === 422 && err.response?.data?.errors) {
      errors.value = err.response.data.errors
    } else {
      generalError.value = err.response?.data?.message || 'حدث خطأ أثناء التسجيل'
    }
  } finally {
    loading.value = false
  }
}
</script>
