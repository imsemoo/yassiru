<template>
  <div class="position-fixed bottom-0 start-0 p-3" style="z-index: 1070">
    <TransitionGroup name="toast">
      <div
        v-for="n in notifications"
        :key="n.id"
        class="toast show mb-2"
        :class="`border-${n.type}`"
        role="alert"
      >
        <div class="toast-body d-flex align-items-center justify-content-between">
          <span>{{ n.message }}</span>
          <button type="button" class="btn-close btn-close-sm ms-3" @click="remove(n.id)" />
        </div>
      </div>
    </TransitionGroup>
  </div>
</template>

<script setup>
import { useNotification } from '../../composables/useNotification'

const { notifications, remove } = useNotification()
</script>

<style scoped>
.toast-enter-active,
.toast-leave-active {
  transition: all 0.3s ease;
}
.toast-enter-from {
  opacity: 0;
  transform: translateX(-30px);
}
.toast-leave-to {
  opacity: 0;
  transform: translateX(-30px);
}
</style>
