<template>
  <div class="ycard ycard--hoverable p-3 h-100" @click="$emit('click', circle)">
    <div class="d-flex justify-content-between align-items-start mb-3">
      <div>
        <h6 class="fw-bold mb-1">{{ circle.name }}</h6>
        <small class="text-muted">{{ circle.city?.name }}</small>
      </div>
      <BaseBadge :variant="circle.status">{{ statusLabel }}</BaseBadge>
    </div>

    <div class="row g-2 mb-3">
      <div class="col-6">
        <div class="text-muted small">المساهمة الشهرية</div>
        <div class="fw-bold">{{ format(circle.monthly_amount, circle.currency) }}</div>
      </div>
      <div class="col-6">
        <div class="text-muted small">إجمالي الحصة</div>
        <div class="fw-bold text-success">{{ format(circle.monthly_amount * circle.max_members, circle.currency) }}</div>
      </div>
    </div>

    <div class="d-flex justify-content-between align-items-center small">
      <span class="text-muted">{{ membersText }}</span>
      <span v-if="circle.status === 'active'" class="text-muted">
        الجولة {{ circle.current_round }} / {{ circle.cycle_months }}
      </span>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useCurrency } from '../../composables/useCurrency'
import BaseBadge from '../shared/BaseBadge.vue'

const props = defineProps({
  circle: { type: Object, required: true },
  membersCount: { type: Number, default: null },
})

defineEmits(['click'])

const { format } = useCurrency()

const statusLabel = computed(() => {
  const labels = {
    forming: 'قيد التكوين',
    active: 'نشطة',
    completed: 'مكتملة',
    cancelled: 'ملغاة',
  }
  return labels[props.circle.status] || props.circle.status
})

const membersText = computed(() => {
  const count = props.membersCount ?? props.circle.members?.length ?? 0
  return `${count} / ${props.circle.max_members} عضو`
})
</script>
