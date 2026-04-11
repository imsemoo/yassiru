<template>
  <div v-if="message" class="alert" :class="`alert-${type}`" role="alert">
    <div class="d-flex justify-content-between align-items-center">
      <span>{{ message }}</span>
      <button v-if="dismissible" type="button" class="btn-close" @click="$emit('dismiss')"></button>
    </div>
  </div>
</template>

<script setup>
defineProps({
  message: { type: String, default: '' },
  type: { type: String, default: 'info' },
  dismissible: { type: Boolean, default: true },
})
defineEmits(['dismiss'])
</script>
