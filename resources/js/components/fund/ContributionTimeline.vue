<template>
  <div class="timeline">
    <div
      v-for="item in timeline"
      :key="item.round"
      class="timeline-item d-flex align-items-center mb-3"
    >
      <div
        class="timeline-dot"
        :class="{
          'bg-success': item.status === 'done',
          'bg-primary pulse': item.status === 'current',
          'bg-secondary': item.status === 'pending',
        }"
      />
      <div class="me-3 flex-grow-1">
        <div class="fw-bold small">الجولة {{ item.round }}</div>
        <div class="text-muted small">{{ item.name }}</div>
      </div>
      <BaseBadge :variant="item.status === 'done' ? 'completed' : item.status === 'current' ? 'active' : 'pending'">
        {{ item.status === 'done' ? 'تم' : item.status === 'current' ? 'جارية' : 'قادمة' }}
      </BaseBadge>
    </div>
  </div>
</template>

<script setup>
import BaseBadge from '../shared/BaseBadge.vue'

defineProps({
  timeline: { type: Array, required: true },
})
</script>

<style scoped>
.timeline-dot {
  width: 14px;
  height: 14px;
  border-radius: 50%;
  flex-shrink: 0;
  margin-left: 12px;
}
.pulse {
  animation: pulse 1.5s infinite;
}
@keyframes pulse {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.5; }
}
</style>
