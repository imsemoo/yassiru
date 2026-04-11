<template>
  <div class="dashboard-page">
    <div class="container" style="max-width: 820px">
      <router-link to="/recommender" class="page-header__back">
        <PhArrowRight :size="14" weight="bold" />
        العودة للوحة المعرّف
      </router-link>

      <div class="page-header">
        <div>
          <h1 class="page-header__title">
            <span class="page-header__title__icon" style="--header-bg: rgba(21,101,192,0.1); --header-color: #1565c0">
              <PhUserPlus :size="24" weight="duotone" />
            </span>
            إضافة مرشح جديد
          </h1>
          <p class="page-header__subtitle">أضف بيانات المرشح بدقة لتسهيل عملية التوفيق</p>
        </div>
      </div>

      <div v-if="success" class="dash-alert dash-alert--success">
        <PhCheckCircle :size="20" weight="fill" class="dash-alert__icon" />
        <div class="dash-alert__content">
          <div class="dash-alert__title">تم بنجاح</div>
          تم إضافة المرشح إلى قائمتك
        </div>
      </div>

      <div v-if="error && typeof error !== 'object'" class="dash-alert dash-alert--danger">
        <PhWarningCircle :size="20" weight="fill" class="dash-alert__icon" />
        <div class="dash-alert__content">{{ error }}</div>
      </div>

      <form @submit.prevent="submit">
        <!-- Basic Info -->
        <div class="dash-card">
          <div class="dash-card__header">
            <h3 class="dash-card__header__title">
              <PhIdentificationCard :size="20" />
              المعلومات الأساسية
            </h3>
          </div>
          <div class="dash-card__body">
            <div class="dash-form">
              <div class="dash-form__row">
                <div class="dash-form__group">
                  <label class="dash-form__label">
                    <PhUser :size="14" />
                    الاسم الكامل
                  </label>
                  <input v-model="form.name" type="text" class="dash-form__input" :class="{ 'is-invalid': fieldError('name') }" required>
                  <div v-if="fieldError('name')" class="dash-form__error">
                    <PhWarningCircle :size="13" weight="fill" />
                    {{ fieldError('name') }}
                  </div>
                </div>
                <div class="dash-form__group">
                  <label class="dash-form__label">
                    <PhCake :size="14" />
                    العمر
                  </label>
                  <input v-model.number="form.age" type="number" class="dash-form__input" min="18" max="60" required>
                </div>
              </div>

              <div class="dash-form__row">
                <div class="dash-form__group">
                  <label class="dash-form__label">
                    <PhGenderIntersex :size="14" />
                    الجنس
                  </label>
                  <select v-model="form.gender" class="dash-form__select" required>
                    <option value="male">ذكر</option>
                    <option value="female">أنثى</option>
                  </select>
                </div>
                <div class="dash-form__group">
                  <label class="dash-form__label">
                    <PhMapPin :size="14" />
                    المدينة
                  </label>
                  <select v-model="form.city_id" class="dash-form__select">
                    <option value="">اختر المدينة</option>
                    <option v-for="city in cities" :key="city.id" :value="city.id">{{ city.name }}</option>
                  </select>
                </div>
              </div>

              <div class="dash-form__row">
                <div class="dash-form__group">
                  <label class="dash-form__label">
                    <PhGraduationCap :size="14" />
                    التعليم
                  </label>
                  <input v-model="form.education" type="text" class="dash-form__input" placeholder="مثال: بكالوريوس هندسة">
                </div>
                <div class="dash-form__group">
                  <label class="dash-form__label">
                    <PhBriefcase :size="14" />
                    المهنة
                  </label>
                  <input v-model="form.occupation" type="text" class="dash-form__input" placeholder="مثال: مهندس برمجيات">
                </div>
              </div>

              <div class="dash-form__row">
                <div class="dash-form__group">
                  <label class="dash-form__label">
                    <PhHeart :size="14" />
                    الحالة الاجتماعية
                  </label>
                  <select v-model="form.marital_status" class="dash-form__select" required>
                    <option value="single">أعزب/عزباء</option>
                    <option value="divorced">مطلق/ة</option>
                    <option value="widowed">أرمل/ة</option>
                  </select>
                </div>
                <div class="dash-form__group">
                  <label class="dash-form__label">
                    <PhMosque :size="14" />
                    مستوى التدين
                  </label>
                  <select v-model="form.religiosity_level" class="dash-form__select" required>
                    <option value="committed">ملتزم</option>
                    <option value="moderate">متوسط</option>
                    <option value="learning">في طور التعلم</option>
                  </select>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Guardian Info -->
        <div class="dash-card">
          <div class="dash-card__header">
            <h3 class="dash-card__header__title">
              <PhUsersFour :size="20" />
              بيانات ولي الأمر
            </h3>
          </div>
          <div class="dash-card__body">
            <div class="dash-form">
              <div class="dash-form__row">
                <div class="dash-form__group">
                  <label class="dash-form__label">
                    <PhUser :size="14" />
                    اسم الولي
                  </label>
                  <input v-model="form.guardian_name" type="text" class="dash-form__input" required>
                </div>
                <div class="dash-form__group">
                  <label class="dash-form__label">
                    <PhPhone :size="14" />
                    هاتف الولي
                  </label>
                  <input v-model="form.guardian_phone" type="tel" class="dash-form__input" dir="ltr" placeholder="+201001234567" required>
                </div>
              </div>
              <div class="dash-form__group">
                <label class="dash-form__label">
                  <PhUsersThree :size="14" />
                  صلة القرابة
                </label>
                <input v-model="form.guardian_relation" type="text" class="dash-form__input" placeholder="مثال: الأب، الأخ، العم" required>
              </div>
            </div>
          </div>
        </div>

        <!-- Notes -->
        <div class="dash-card">
          <div class="dash-card__header">
            <h3 class="dash-card__header__title">
              <PhNotePencil :size="20" />
              ملاحظات إضافية (اختياري)
            </h3>
          </div>
          <div class="dash-card__body">
            <div class="dash-form">
              <div class="dash-form__group">
                <textarea
                  v-model="form.recommender_notes"
                  class="dash-form__textarea"
                  placeholder="أي ملاحظات تساعد في عملية التوفيق (الشخصية، التطلعات، التفضيلات...)"
                ></textarea>
                <p class="dash-form__hint">هذه الملاحظات تظهر فقط لك ولا تُعرض على المرشحين الآخرين</p>
              </div>
            </div>
          </div>
        </div>

        <div class="d-flex gap-2 justify-content-end">
          <router-link to="/recommender" class="btn-action btn-action--outline">
            إلغاء
          </router-link>
          <button type="submit" class="btn-action btn-action--primary" :disabled="submitting">
            <span v-if="submitting" class="spinner-border spinner-border-sm"></span>
            <PhUserPlus :size="18" weight="bold" v-else />
            إضافة المرشح
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { useApi } from '@/composables/useApi'
import axios from 'axios'
import {
  PhArrowRight, PhUserPlus, PhCheckCircle, PhWarningCircle,
  PhIdentificationCard, PhUser, PhCake, PhGenderIntersex, PhMapPin,
  PhGraduationCap, PhBriefcase, PhHeart, PhMosque,
  PhUsersFour, PhPhone, PhUsersThree, PhNotePencil,
} from '@phosphor-icons/vue'

const { post, error } = useApi()
const submitting = ref(false)
const success = ref(false)
const cities = ref([])

const form = reactive({
  name: '', gender: 'male', age: 25,
  education: '', occupation: '', city_id: '',
  marital_status: 'single', religiosity_level: 'committed',
  guardian_name: '', guardian_phone: '', guardian_relation: '',
  recommender_notes: '',
})

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
  success.value = false
  try {
    await post('/api/recommender/candidates', form)
    success.value = true
    Object.assign(form, {
      name: '', age: 25, education: '', occupation: '',
      guardian_name: '', guardian_phone: '', guardian_relation: '',
      recommender_notes: '',
    })
    window.scrollTo({ top: 0, behavior: 'smooth' })
  } catch { /* handled */ }
  submitting.value = false
}
</script>
