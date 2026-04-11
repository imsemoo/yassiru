<template>
  <div class="ycard p-3 mb-3">
    <div class="d-flex justify-content-between align-items-start mb-2">
      <div class="d-flex align-items-center gap-3">
        <div class="text-center">
          <div class="fw-bold small">{{ suggestion.male.name }}</div>
          <small class="text-muted">{{ suggestion.male.age }} سنة &bull; {{ suggestion.male.occupation }}</small>
        </div>
        <div class="match-arrow">&#x2194;</div>
        <div class="text-center">
          <div class="fw-bold small">{{ suggestion.female.name }}</div>
          <small class="text-muted">{{ suggestion.female.age }} سنة &bull; {{ suggestion.female.occupation }}</small>
        </div>
      </div>
      <div class="score-badge" :class="scoreClass">{{ suggestion.score }}%</div>
    </div>

    <div v-if="suggestion.reasons?.length" class="d-flex flex-wrap gap-1 mb-2">
      <span v-for="reason in suggestion.reasons" :key="reason" class="badge bg-light text-dark small">
        {{ reason }}
      </span>
    </div>

    <slot name="actions" />
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  suggestion: { type: Object, required: true },
})

const scoreClass = computed(() => {
  if (props.suggestion.score >= 80) return 'score--high'
  if (props.suggestion.score >= 60) return 'score--medium'
  return 'score--low'
})
</script>

<style scoped>
.match-arrow {
  font-size: 1.25rem;
  color: var(--primary, #0d7377);
}
.score-badge {
  padding: 4px 12px;
  border-radius: 20px;
  font-weight: bold;
  font-size: 0.85rem;
}
.score--high { background: #e8f5e9; color: #1b7a4a; }
.score--medium { background: #fff3e0; color: #e67e22; }
.score--low { background: #fdecea; color: #c0392b; }
</style>
