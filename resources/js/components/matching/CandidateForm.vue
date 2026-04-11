<template>
  <form @submit.prevent="$emit('submit', form)">
    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label">الاسم الكامل</label>
        <input v-model="form.name" type="text" class="form-control" :class="{ 'is-invalid': getError('name') }" required />
        <div v-if="getError('name')" class="invalid-feedback">{{ getError('name') }}</div>
      </div>

      <div class="col-md-6">
        <label class="form-label">الجنس</label>
        <select v-model="form.gender" class="form-select" required>
          <option value="">اختر</option>
          <option value="male">ذكر</option>
          <option value="female">أنثى</option>
        </select>
      </div>

      <div class="col-md-4">
        <label class="form-label">العمر</label>
        <input v-model.number="form.age" type="number" class="form-control" min="18" max="70" required />
      </div>

      <div class="col-md-4">
        <label class="form-label">التعليم</label>
        <input v-model="form.education" type="text" class="form-control" />
      </div>

      <div class="col-md-4">
        <label class="form-label">المهنة</label>
        <input v-model="form.occupation" type="text" class="form-control" />
      </div>

      <div class="col-md-4">
        <label class="form-label">المدينة</label>
        <select v-model="form.city_id" class="form-select">
          <option value="">اختر</option>
          <option v-for="city in cities" :key="city.id" :value="city.id">{{ city.name }}</option>
        </select>
      </div>

      <div class="col-md-4">
        <label class="form-label">الحالة الاجتماعية</label>
        <select v-model="form.marital_status" class="form-select">
          <option value="single">أعزب/عزباء</option>
          <option value="divorced">مطلق/ة</option>
          <option value="widowed">أرمل/ة</option>
        </select>
      </div>

      <div class="col-md-4">
        <label class="form-label">مستوى التدين</label>
        <select v-model="form.religiosity_level" class="form-select">
          <option value="committed">ملتزم</option>
          <option value="moderate">معتدل</option>
          <option value="learning">متعلم</option>
        </select>
      </div>

      <div class="col-12"><hr /></div>

      <div class="col-md-4">
        <label class="form-label">اسم ولي الأمر</label>
        <input v-model="form.guardian_name" type="text" class="form-control" required />
      </div>

      <div class="col-md-4">
        <label class="form-label">هاتف ولي الأمر</label>
        <input v-model="form.guardian_phone" type="tel" class="form-control" required />
      </div>

      <div class="col-md-4">
        <label class="form-label">صلة القرابة</label>
        <input v-model="form.guardian_relation" type="text" class="form-control" required />
      </div>

      <div class="col-12">
        <label class="form-label">ملاحظات المعرّف</label>
        <textarea v-model="form.recommender_notes" class="form-control" rows="3" />
      </div>

      <div class="col-12">
        <button type="submit" class="btn btn-primary" :disabled="loading">
          <span v-if="loading" class="spinner-border spinner-border-sm ms-1" />
          {{ loading ? 'جاري الحفظ...' : 'إضافة المرشح' }}
        </button>
      </div>
    </div>
  </form>
</template>

<script setup>
import { reactive } from 'vue'

const props = defineProps({
  cities: { type: Array, default: () => [] },
  loading: { type: Boolean, default: false },
  getError: { type: Function, default: () => null },
})

defineEmits(['submit'])

const form = reactive({
  name: '',
  gender: '',
  age: null,
  education: '',
  occupation: '',
  city_id: '',
  marital_status: 'single',
  religiosity_level: 'committed',
  guardian_name: '',
  guardian_phone: '',
  guardian_relation: '',
  recommender_notes: '',
})
</script>
