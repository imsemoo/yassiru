<template>
  <span :class="['badge', badgeClass]">
    <slot>{{ label }}</slot>
  </span>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  variant: { type: String, default: 'info' },
  label: { type: String, default: '' },
})

const badgeClass = computed(() => {
  const map = {
    verified: 'bg-success-subtle text-success',
    pending: 'bg-warning-subtle text-warning',
    rejected: 'bg-danger-subtle text-danger',
    info: 'bg-info-subtle text-info',
    imam: 'bg-primary-subtle text-primary',
    fund: 'bg-purple-subtle text-purple',
    active: 'bg-success-subtle text-success',
    forming: 'bg-warning-subtle text-warning',
    completed: 'bg-secondary-subtle text-secondary',
    cancelled: 'bg-danger-subtle text-danger',
    paid: 'bg-success-subtle text-success',
    scheduled: 'bg-info-subtle text-info',
  }
  return map[props.variant] || `bg-${props.variant}-subtle text-${props.variant}`
})
</script>
