<template>
  <div>
    <div class="d-flex justify-content-between align-items-center mb-1">
      <span class="small fw-bold">{{ label }}</span>
      <span class="small text-muted">{{ percent }}%</span>
    </div>
    <div class="progress" :style="{ height: height + 'px' }">
      <div
        class="progress-bar progress-bar-striped"
        :class="barClass"
        role="progressbar"
        :style="{ width: percent + '%' }"
        :aria-valuenow="percent"
        aria-valuemin="0"
        aria-valuemax="100"
      />
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  percent: { type: Number, required: true },
  label: { type: String, default: '' },
  height: { type: Number, default: 10 },
})

const barClass = computed(() => {
  if (props.percent >= 100) return 'bg-success'
  if (props.percent >= 60) return 'bg-primary'
  if (props.percent >= 30) return 'bg-warning'
  return 'bg-danger'
})
</script>
