<template>
  <div class="ycard ycard--hoverable p-3 h-100" @click="$emit('click', course)">
    <div class="d-flex align-items-center mb-3">
      <span class="track-icon ms-2">{{ trackIcon }}</span>
      <div>
        <h6 class="fw-bold mb-0">{{ course.title }}</h6>
        <small class="text-muted">{{ course.lessons_count }} درس &bull; {{ course.duration_hours }} ساعة</small>
      </div>
    </div>

    <p v-if="course.description" class="text-muted small mb-3 line-clamp-2">
      {{ course.description }}
    </p>

    <div class="progress mb-2" style="height: 8px">
      <div
        class="progress-bar"
        :class="course.quiz_passed ? 'bg-success' : 'bg-primary'"
        :style="{ width: course.progress + '%' }"
      />
    </div>

    <div class="d-flex justify-content-between align-items-center">
      <small class="text-muted">{{ course.progress }}% مكتمل</small>
      <BaseBadge v-if="course.quiz_passed" variant="verified">اجتاز الاختبار</BaseBadge>
      <small v-else class="text-muted">{{ course.completed_lessons }}/{{ course.lessons_count }}</small>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import BaseBadge from '../shared/BaseBadge.vue'

const props = defineProps({
  course: { type: Object, required: true },
})

defineEmits(['click'])

const trackIcon = computed(() => {
  const icons = {
    shariah: '📖',
    psychology: '🧠',
    financial: '💰',
    practical: '🔧',
  }
  return icons[props.course.track] || '📚'
})
</script>

<style scoped>
.track-icon {
  font-size: 2rem;
}
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
