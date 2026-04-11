<template>
  <div class="text-center py-5">
    <div class="spinner-border text-primary" role="status">
      <span class="visually-hidden">جاري التحميل...</span>
    </div>
    <p v-if="text" class="text-muted mt-2">{{ text }}</p>
  </div>
</template>

<script setup>
defineProps({
  text: { type: String, default: '' },
})
</script>
