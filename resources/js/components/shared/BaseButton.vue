<template>
  <button
    :class="['btn', variantClass, sizeClass, { 'w-100': block }]"
    :disabled="disabled || loading"
    :type="type"
    @click="$emit('click', $event)"
  >
    <span v-if="loading" class="spinner-border spinner-border-sm ms-2" role="status" />
    <slot>{{ loading ? 'جاري...' : '' }}</slot>
  </button>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  variant: { type: String, default: 'primary' },
  size: { type: String, default: 'md' },
  block: { type: Boolean, default: false },
  loading: { type: Boolean, default: false },
  disabled: { type: Boolean, default: false },
  type: { type: String, default: 'button' },
})

defineEmits(['click'])

const variantClass = computed(() => {
  const map = {
    primary: 'btn-primary',
    outline: 'btn-outline-primary',
    gold: 'btn-gold',
    danger: 'btn-danger',
    success: 'btn-success',
    light: 'btn-light',
  }
  return map[props.variant] || `btn-${props.variant}`
})

const sizeClass = computed(() => {
  if (props.size === 'sm') return 'btn-sm'
  if (props.size === 'lg') return 'btn-lg'
  return ''
})
</script>
