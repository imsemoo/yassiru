<template>
  <div class="dashboard-page">
    <div class="container" style="max-width: 760px">
      <router-link to="/fund" class="page-header__back">
        <PhArrowRight :size="14" weight="bold" />
        العودة للصندوق
      </router-link>

      <div class="page-header">
        <div>
          <h1 class="page-header__title">
            <span class="page-header__title__icon" style="--header-bg: rgba(184,134,11,0.1); --header-color: #b8860b">
              <PhPlusCircle :size="24" weight="duotone" />
            </span>
            إنشاء حلقة جديدة
          </h1>
          <p class="page-header__subtitle">صمّم حلقتك الخاصة وادعو من تثق بهم للانضمام</p>
        </div>
      </div>

      <!-- No Certificate Warning -->
      <div v-if="!hasCertificate" class="dash-alert dash-alert--warning">
        <PhLockKey :size="20" weight="fill" class="dash-alert__icon" />
        <div class="dash-alert__content">
          <div class="dash-alert__title">شهادة التأهيل مطلوبة</div>
          يجب إكمال الدورة التأهيلية والحصول على شهادتها قبل إنشاء حلقة صندوق
          <div class="mt-2">
            <router-link to="/courses" class="btn-action btn-action--gold btn-action--sm">
              <PhBookOpen :size="14" />
              ابدأ الدورة الآن
            </router-link>
          </div>
        </div>
      </div>

      <div v-else>
        <div v-if="error && typeof error !== 'object'" class="dash-alert dash-alert--danger">
          <PhWarningCircle :size="20" weight="fill" class="dash-alert__icon" />
          <div class="dash-alert__content">{{ error }}</div>
        </div>

        <form @submit.prevent="submit">
          <div class="dash-card">
            <div class="dash-card__header">
              <h3 class="dash-card__header__title">
                <PhInfo :size="20" />
                تفاصيل الحلقة
              </h3>
            </div>
            <div class="dash-card__body">
              <div class="dash-form">
                <div class="dash-form__group">
                  <label class="dash-form__label">
                    <PhTag :size="14" />
                    اسم الحلقة
                  </label>
                  <input
                    v-model="form.name"
                    type="text"
                    class="dash-form__input"
                    :class="{ 'is-invalid': fieldError('name') }"
                    placeholder="مثال: حلقة شباب القاهرة - يناير 2026"
                    required
                  >
                  <div v-if="fieldError('name')" class="dash-form__error">
                    <PhWarningCircle :size="13" weight="fill" />
                    {{ fieldError('name') }}
                  </div>
                </div>

                <div class="dash-form__group">
                  <label class="dash-form__label">
                    <PhMapPin :size="14" />
                    المدينة
                  </label>
                  <select v-model="form.city_id" class="dash-form__select" required>
                    <option value="">اختر المدينة</option>
                    <option v-for="city in cities" :key="city.id" :value="city.id">{{ city.name }}</option>
                  </select>
                </div>

                <div class="dash-form__row">
                  <div class="dash-form__group">
                    <label class="dash-form__label">
                      <PhUsers :size="14" />
                      عدد الأعضاء
                    </label>
                    <input v-model.number="form.max_members" type="number" class="dash-form__input" min="5" max="30" required>
                    <p class="dash-form__hint">من 5 إلى 30 عضو</p>
                  </div>
                  <div class="dash-form__group">
                    <label class="dash-form__label">
                      <PhCurrencyCircleDollar :size="14" />
                      المبلغ الشهري
                    </label>
                    <input v-model.number="form.monthly_amount" type="number" class="dash-form__input" min="100" max="50000" required>
                  </div>
                </div>

                <div class="dash-form__row">
                  <div class="dash-form__group">
                    <label class="dash-form__label">
                      <PhCoins :size="14" />
                      العملة
                    </label>
                    <select v-model="form.currency" class="dash-form__select" required>
                      <option value="EGP">جنيه مصري (EGP)</option>
                      <option value="SAR">ريال سعودي (SAR)</option>
                      <option value="AED">درهم إماراتي (AED)</option>
                      <option value="KWD">دينار كويتي (KWD)</option>
                      <option value="JOD">دينار أردني (JOD)</option>
                      <option value="MAD">درهم مغربي (MAD)</option>
                    </select>
                  </div>
                  <div class="dash-form__group">
                    <label class="dash-form__label">
                      <PhListNumbers :size="14" />
                      طريقة الصرف
                    </label>
                    <select v-model="form.payout_method" class="dash-form__select" required>
                      <option value="priority">حسب الأولوية (Trust Score)</option>
                      <option value="lottery">القرعة العشوائية</option>
                      <option value="schedule">جدول زمني محدد</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Live Preview -->
          <div class="dash-card preview-card">
            <div class="dash-card__header">
              <h3 class="dash-card__header__title">
                <PhEye :size="20" />
                معاينة الحلقة
              </h3>
            </div>
            <div class="dash-card__body">
              <div class="preview-row">
                <div class="preview-item">
                  <PhUsers :size="20" weight="duotone" />
                  <div>
                    <div class="label">الأعضاء</div>
                    <div class="value">{{ form.max_members }}</div>
                  </div>
                </div>
                <div class="preview-item">
                  <PhCurrencyCircleDollar :size="20" weight="duotone" />
                  <div>
                    <div class="label">المساهمة الشهرية</div>
                    <div class="value">{{ formatNumber(form.monthly_amount) }} {{ form.currency }}</div>
                  </div>
                </div>
                <div class="preview-item">
                  <PhCalendar :size="20" weight="duotone" />
                  <div>
                    <div class="label">مدة الدورة</div>
                    <div class="value">{{ form.max_members }} شهر</div>
                  </div>
                </div>
              </div>

              <div class="preview-payout">
                <div class="preview-payout__label">المبلغ الذي ستحصل عليه عند دورك</div>
                <div class="preview-payout__amount">
                  {{ formatNumber(totalPayout) }}
                  <span>{{ form.currency }}</span>
                </div>
                <div class="preview-payout__hint">دفعة واحدة لتجهيز زواجك</div>
              </div>
            </div>
          </div>

          <div class="d-flex gap-2 justify-content-end">
            <router-link to="/fund" class="btn-action btn-action--outline">
              إلغاء
            </router-link>
            <button type="submit" class="btn-action btn-action--gold" :disabled="submitting">
              <span v-if="submitting" class="spinner-border spinner-border-sm"></span>
              <PhCheckCircle :size="18" weight="bold" v-else />
              إنشاء الحلقة
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useApi } from '@/composables/useApi'
import axios from 'axios'
import {
  PhArrowRight, PhPlusCircle, PhLockKey, PhBookOpen, PhWarningCircle,
  PhInfo, PhTag, PhMapPin, PhUsers, PhCurrencyCircleDollar, PhCoins,
  PhListNumbers, PhEye, PhCalendar, PhCheckCircle,
} from '@phosphor-icons/vue'

const router = useRouter()
const auth = useAuthStore()
const { post, error } = useApi()
const submitting = ref(false)
const cities = ref([])
const hasCertificate = computed(() => auth.hasCertificate)

const form = reactive({
  name: '', city_id: '', max_members: 15,
  monthly_amount: 1000, currency: 'EGP', payout_method: 'priority',
})

const totalPayout = computed(() => form.max_members * form.monthly_amount)

function formatNumber(num) {
  return new Intl.NumberFormat('ar-EG').format(num)
}

function fieldError(field) {
  if (typeof error.value === 'object' && error.value?.[field]) {
    return error.value[field][0]
  }
  return null
}

onMounted(async () => {
  try {
    const { data } = await axios.get('/api/calculator/cities')
    cities.value = data
  } catch { /* ignore */ }
})

async function submit() {
  submitting.value = true
  try {
    const result = await post('/api/circles', form)
    router.push(`/circles/${result.circle.id}/dashboard`)
  } catch { /* handled */ }
  submitting.value = false
}
</script>

<style lang="scss" scoped>
.preview-card {
  background: linear-gradient(135deg, rgba(184,134,11,0.04) 0%, rgba(13,115,119,0.03) 100%);
  border-color: rgba(184,134,11,0.2);
}

.preview-row {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1rem;
  margin-bottom: 1.5rem;

  @media (max-width: 576px) { grid-template-columns: 1fr; }
}

.preview-item {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.85rem 1rem;
  background: #fff;
  border-radius: 12px;
  border: 1px solid #f0ece4;

  svg { color: #b8860b; flex-shrink: 0; }

  .label { font-size: 0.7rem; color: #8888a0; font-weight: 600; }
  .value { font-size: 0.95rem; font-weight: 800; color: #1a1a2a; }
}

.preview-payout {
  background: linear-gradient(135deg, #b8860b 0%, #8a6508 100%);
  color: #fff;
  border-radius: 14px;
  padding: 1.5rem;
  text-align: center;

  &__label {
    font-size: 0.85rem;
    opacity: 0.9;
    margin-bottom: 0.5rem;
  }

  &__amount {
    font-size: 2.5rem;
    font-weight: 900;
    letter-spacing: -0.02em;
    line-height: 1;

    span {
      font-size: 1rem;
      opacity: 0.85;
    }
  }

  &__hint {
    font-size: 0.8rem;
    opacity: 0.85;
    margin-top: 0.5rem;
  }
}
</style>
