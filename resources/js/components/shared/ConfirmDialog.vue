<template>
  <BaseModal v-model="show" :title="title" size="sm">
    <p class="mb-0">{{ message }}</p>

    <template #footer>
      <button class="btn btn-light" @click="cancel">إلغاء</button>
      <button
        class="btn"
        :class="confirmVariant === 'danger' ? 'btn-danger' : 'btn-primary'"
        :disabled="loading"
        @click="confirm"
      >
        <span v-if="loading" class="spinner-border spinner-border-sm ms-1" />
        {{ confirmText }}
      </button>
    </template>
  </BaseModal>
</template>

<script setup>
import { ref } from 'vue'
import BaseModal from './BaseModal.vue'

defineProps({
  title: { type: String, default: 'تأكيد' },
  message: { type: String, required: true },
  confirmText: { type: String, default: 'تأكيد' },
  confirmVariant: { type: String, default: 'primary' },
})

const show = defineModel({ type: Boolean, default: false })
const loading = ref(false)

const emit = defineEmits(['confirm', 'cancel'])

function confirm() {
  emit('confirm')
}

function cancel() {
  show.value = false
  emit('cancel')
}
</script>
