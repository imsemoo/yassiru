<template>
  <div class="dashboard-page">
    <div class="container" style="max-width: 880px">
      <div v-if="!auth.user" class="loading-state">
        <div class="spinner-border"></div>
      </div>

      <div v-else>
        <!-- Profile Hero -->
        <div class="profile-hero">
          <div class="profile-hero__avatar">{{ userInitial }}</div>
          <div class="profile-hero__info">
            <h1 class="profile-hero__name">{{ auth.user.name }}</h1>
            <div class="profile-hero__email">{{ auth.user.email }}</div>
            <div class="profile-hero__badges">
              <span class="status-badge" :class="roleBadgeClass">
                {{ roleLabel(auth.user.role) }}
              </span>
              <span v-if="auth.hasCertificate" class="status-badge status-badge--success">
                <PhCheckCircle :size="12" weight="fill" />
                حاصل على الشهادة
              </span>
              <span v-if="auth.user.is_verified" class="status-badge status-badge--info">
                <PhShieldCheck :size="12" weight="fill" />
                موثّق
              </span>
            </div>
          </div>
        </div>

        <!-- Quick Links Grid -->
        <div class="dash-card">
          <div class="dash-card__header">
            <h3 class="dash-card__header__title">
              <PhSquaresFour :size="20" />
              الوصول السريع
            </h3>
          </div>
          <div class="dash-card__body">
            <div class="quick-links-grid">
              <router-link
                v-for="link in quickLinks"
                :key="link.to"
                :to="link.to"
                class="quick-link"
                :style="{ '--ql-color': link.color, '--ql-bg': link.bg }"
              >
                <component :is="link.icon" :size="22" weight="duotone" />
                <span>{{ link.label }}</span>
              </router-link>
            </div>
          </div>
        </div>

        <!-- Profile Info -->
        <div class="dash-card">
          <div class="dash-card__header">
            <h3 class="dash-card__header__title">
              <PhIdentificationCard :size="20" />
              البيانات الشخصية
            </h3>
            <button v-if="!editing" class="btn-action btn-action--outline btn-action--sm" @click="editing = true">
              <PhPencilSimple :size="14" />
              تعديل
            </button>
          </div>
          <div class="dash-card__body">
            <div v-if="message" class="dash-alert" :class="messageType === 'success' ? 'dash-alert--success' : 'dash-alert--danger'">
              <PhCheckCircle :size="20" weight="fill" class="dash-alert__icon" v-if="messageType === 'success'" />
              <PhWarningCircle :size="20" weight="fill" class="dash-alert__icon" v-else />
              <div class="dash-alert__content">{{ message }}</div>
            </div>

            <div class="dash-form">
              <div class="dash-form__row">
                <div class="dash-form__group">
                  <label class="dash-form__label">
                    <PhUser :size="14" />
                    الاسم الكامل
                  </label>
                  <input v-model="form.name" type="text" class="dash-form__input" :disabled="!editing">
                </div>
                <div class="dash-form__group">
                  <label class="dash-form__label">
                    <PhEnvelope :size="14" />
                    البريد الإلكتروني
                  </label>
                  <input :value="auth.user.email" type="email" class="dash-form__input" disabled>
                </div>
              </div>
              <div class="dash-form__row">
                <div class="dash-form__group">
                  <label class="dash-form__label">
                    <PhPhone :size="14" />
                    الهاتف
                  </label>
                  <input v-model="form.phone" type="tel" class="dash-form__input" dir="ltr" :disabled="!editing">
                </div>
                <div class="dash-form__group">
                  <label class="dash-form__label">
                    <PhMapPin :size="14" />
                    المدينة
                  </label>
                  <select v-model="form.city_id" class="dash-form__select" :disabled="!editing">
                    <option v-for="c in cities" :key="c.id" :value="c.id">{{ c.name }}</option>
                  </select>
                </div>
              </div>
            </div>

            <div v-if="editing" class="d-flex gap-2 mt-3">
              <button class="btn-action btn-action--primary" :disabled="saving" @click="save">
                <span v-if="saving" class="spinner-border spinner-border-sm"></span>
                <PhCheckCircle :size="16" weight="bold" v-else />
                حفظ التعديلات
              </button>
              <button class="btn-action btn-action--outline" @click="cancelEdit">إلغاء</button>
            </div>
          </div>
        </div>

        <!-- Change Password -->
        <div class="dash-card">
          <div class="dash-card__header">
            <h3 class="dash-card__header__title">
              <PhLock :size="20" />
              تغيير كلمة المرور
            </h3>
          </div>
          <div class="dash-card__body">
            <div v-if="passwordMessage" class="dash-alert" :class="passwordSuccess ? 'dash-alert--success' : 'dash-alert--danger'">
              <PhCheckCircle :size="20" weight="fill" class="dash-alert__icon" v-if="passwordSuccess" />
              <PhWarningCircle :size="20" weight="fill" class="dash-alert__icon" v-else />
              <div class="dash-alert__content">{{ passwordMessage }}</div>
            </div>

            <div class="dash-form">
              <div class="dash-form__group">
                <label class="dash-form__label">
                  <PhLock :size="14" />
                  كلمة المرور الحالية
                </label>
                <input v-model="passwordForm.current" type="password" class="dash-form__input" placeholder="••••••••" dir="ltr">
              </div>
              <div class="dash-form__row">
                <div class="dash-form__group">
                  <label class="dash-form__label">
                    <PhLockKey :size="14" />
                    الجديدة
                  </label>
                  <input v-model="passwordForm.password" type="password" class="dash-form__input" placeholder="8 أحرف على الأقل" dir="ltr">
                </div>
                <div class="dash-form__group">
                  <label class="dash-form__label">
                    <PhLockKey :size="14" />
                    تأكيد الجديدة
                  </label>
                  <input v-model="passwordForm.password_confirmation" type="password" class="dash-form__input" placeholder="أعد الكتابة" dir="ltr">
                </div>
              </div>
              <button class="btn-action btn-action--danger" :disabled="changingPassword" @click="changePassword">
                <span v-if="changingPassword" class="spinner-border spinner-border-sm"></span>
                <PhLock :size="16" weight="bold" v-else />
                تغيير كلمة المرور
              </button>
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
import axios from 'axios'
import {
  PhCheckCircle, PhShieldCheck, PhIdentificationCard,
  PhPencilSimple, PhSquaresFour, PhLock, PhLockKey,
  PhUser, PhEnvelope, PhPhone, PhMapPin, PhWarningCircle,
  PhBookOpen, PhCertificate, PhHeart, PhHandCoins, PhChats, PhUsers,
} from '@phosphor-icons/vue'

const auth = useAuthStore()
const editing = ref(false)
const saving = ref(false)
const message = ref('')
const messageType = ref('success')
const cities = ref([])

const form = reactive({ name: '', phone: '', city_id: '' })
const passwordForm = reactive({ current: '', password: '', password_confirmation: '' })
const changingPassword = ref(false)
const passwordMessage = ref('')
const passwordSuccess = ref(false)

const userInitial = computed(() => auth.user?.name?.charAt(0)?.toUpperCase() || '؟')
const roleLabel = (r) => ({ admin: 'مدير', recommender: 'معرّف', user: 'عضو' })[r] || r
const roleBadgeClass = computed(() => ({
  admin: 'status-badge--danger',
  recommender: 'status-badge--info',
  counselor: 'status-badge--success',
  user: 'status-badge--primary',
})[auth.user?.role] || 'status-badge--muted')

const quickLinks = computed(() => {
  // Admin: oversight links
  if (auth.isAdmin) {
    return [
      { to: '/admin', label: 'لوحة الإدارة', icon: PhShieldCheck, color: '#c0392b', bg: 'rgba(192,57,43,0.1)' },
      { to: '/admin/users', label: 'إدارة المستخدمين', icon: PhUsers, color: '#1565c0', bg: 'rgba(21,101,192,0.1)' },
      { to: '/admin/recommenders', label: 'إدارة المعرّفين', icon: PhUsers, color: '#6a1b4d', bg: 'rgba(106,27,77,0.1)' },
      { to: '/admin/reports', label: 'البلاغات', icon: PhShieldCheck, color: '#e67e22', bg: 'rgba(230,126,34,0.1)' },
    ]
  }

  // Recommender: only recommender panel links
  if (auth.isRecommender) {
    return [
      { to: '/recommender', label: 'لوحة المعرّف', icon: PhUsers, color: '#0d7377', bg: 'rgba(13,115,119,0.1)' },
      { to: '/recommender/add-candidate', label: 'إضافة مرشح', icon: PhUsers, color: '#1565c0', bg: 'rgba(21,101,192,0.1)' },
      { to: '/recommender/suggestions', label: 'الاقتراحات', icon: PhHeart, color: '#c0392b', bg: 'rgba(192,57,43,0.1)' },
      { to: '/recommender/family-requests', label: 'طلبات العائلات', icon: PhChats, color: '#b8860b', bg: 'rgba(184,134,11,0.1)' },
    ]
  }

  // Counselor: only counselor dashboard
  if (auth.isCounselor) {
    return [
      { to: '/counselor', label: 'جلساتي', icon: PhChats, color: '#1b7a4a', bg: 'rgba(27,122,74,0.1)' },
    ]
  }

  // Regular user (default): marriage-seeking features
  return [
    { to: '/courses', label: 'الدورات', icon: PhBookOpen, color: '#0d7377', bg: 'rgba(13,115,119,0.1)' },
    { to: '/certificate', label: 'الشهادة', icon: PhCertificate, color: '#b8860b', bg: 'rgba(184,134,11,0.1)' },
    { to: '/my-weddings', label: 'تسجيلات الأعراس', icon: PhHeart, color: '#c0392b', bg: 'rgba(192,57,43,0.1)' },
    { to: '/circles', label: 'حلقات الصندوق', icon: PhHandCoins, color: '#1565c0', bg: 'rgba(21,101,192,0.1)' },
    { to: '/counseling', label: 'الاستشارات', icon: PhChats, color: '#1b7a4a', bg: 'rgba(27,122,74,0.1)' },
    { to: '/recommender/register', label: 'سجّل كمعرّف', icon: PhUsers, color: '#6b7280', bg: 'rgba(107,114,128,0.1)' },
  ]
})

function resetForm() {
  form.name = auth.user?.name || ''
  form.phone = auth.user?.phone || ''
  form.city_id = auth.user?.city_id || ''
}

function cancelEdit() {
  editing.value = false
  resetForm()
}

async function save() {
  saving.value = true
  message.value = ''
  try {
    await axios.put('/api/auth/user', form)
    await auth.fetchUser()
    editing.value = false
    message.value = 'تم تحديث البيانات بنجاح'
    messageType.value = 'success'
  } catch (err) {
    message.value = err.response?.data?.message || 'حدث خطأ'
    messageType.value = 'danger'
  }
  saving.value = false
}

async function changePassword() {
  changingPassword.value = true
  passwordMessage.value = ''
  try {
    await axios.put('/api/auth/password', passwordForm)
    passwordMessage.value = 'تم تغيير كلمة المرور بنجاح'
    passwordSuccess.value = true
    Object.assign(passwordForm, { current: '', password: '', password_confirmation: '' })
  } catch (err) {
    passwordMessage.value = err.response?.data?.message || 'حدث خطأ'
    passwordSuccess.value = false
  }
  changingPassword.value = false
}

onMounted(async () => {
  resetForm()
  try {
    const { data } = await axios.get('/api/calculator/cities')
    cities.value = data
  } catch {}
})
</script>

<style lang="scss" scoped>
.profile-hero {
  background: linear-gradient(135deg, rgba(13,115,119,0.08) 0%, rgba(184,134,11,0.05) 100%);
  border: 1px solid rgba(13,115,119,0.15);
  border-radius: 20px;
  padding: 1.5rem 2rem;
  display: flex;
  align-items: center;
  gap: 1.5rem;
  margin-bottom: 2rem;

  @media (max-width: 576px) { flex-direction: column; text-align: center; padding: 1.5rem; }

  &__avatar {
    width: 88px;
    height: 88px;
    border-radius: 50%;
    background: linear-gradient(135deg, #0d7377 0%, #095456 100%);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 900;
    font-size: 2.25rem;
    flex-shrink: 0;
    box-shadow: 0 10px 24px rgba(13,115,119,0.3);
  }

  &__info { flex: 1; min-width: 0; }

  &__name {
    font-size: 1.5rem;
    font-weight: 900;
    color: #1a1a2a;
    margin: 0 0 0.25rem;
    letter-spacing: -0.01em;
  }

  &__email {
    font-size: 0.85rem;
    color: #8888a0;
    margin-bottom: 0.75rem;
  }

  &__badges {
    display: flex;
    flex-wrap: wrap;
    gap: 0.4rem;

    @media (max-width: 576px) { justify-content: center; }
  }
}

.quick-links-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(170px, 1fr));
  gap: 0.75rem;
}

.quick-link {
  display: flex;
  align-items: center;
  gap: 0.65rem;
  padding: 0.95rem 1rem;
  background: #faf9f6;
  border: 1.5px solid #e0d8cc;
  border-radius: 12px;
  text-decoration: none;
  color: #1a1a2a;
  font-weight: 700;
  font-size: 0.85rem;
  transition: all 0.2s;

  > svg {
    color: var(--ql-color);
    flex-shrink: 0;
  }

  &:hover {
    background: var(--ql-bg);
    border-color: var(--ql-color);
    color: var(--ql-color);
    transform: translateY(-2px);
  }
}
</style>
