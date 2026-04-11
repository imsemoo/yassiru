<template>
  <div class="ycard ycard--static p-3 text-center h-100">
    <div class="stat-number" :class="valueClass">{{ value }}</div>
    <small class="text-muted">{{ label }}</small>
  </div>
</template>

<script setup>
defineProps({
  value: { type: [String, Number], required: true },
  label: { type: String, required: true },
  valueClass: { type: String, default: '' },
})
</script>
