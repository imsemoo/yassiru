<template>
  <div class="text-center">
    <div class="stat-number fw-black" :class="valueClass">{{ displayValue }}</div>
    <small class="text-muted">{{ suffix }}</small>
  </div>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue'

const props = defineProps({
  value: { type: Number, required: true },
  suffix: { type: String, default: '' },
  duration: { type: Number, default: 1500 },
  valueClass: { type: String, default: '' },
})

const displayValue = ref(0)

function animateCount(target) {
  const start = displayValue.value
  const diff = target - start
  if (diff === 0) return

  const startTime = performance.now()

  function update(currentTime) {
    const elapsed = currentTime - startTime
    const progress = Math.min(elapsed / props.duration, 1)
    // Ease out cubic
    const ease = 1 - Math.pow(1 - progress, 3)
    displayValue.value = Math.round(start + diff * ease)

    if (progress < 1) {
      requestAnimationFrame(update)
    }
  }

  requestAnimationFrame(update)
}

onMounted(() => animateCount(props.value))
watch(() => props.value, (newVal) => animateCount(newVal))
</script>
