<template>
  <div class="text-center py-5">
    <div v-if="icon" class="mb-3" style="font-size: 3rem; opacity: 0.5">{{ icon }}</div>
    <h5 class="fw-bold mb-2">{{ title }}</h5>
    <p v-if="description" class="text-muted mb-3">{{ description }}</p>
    <slot></slot>
  </div>
</template>

<script setup>
defineProps({
  icon: { type: String, default: '' },
  title: { type: String, required: true },
  description: { type: String, default: '' },
})
</script>
