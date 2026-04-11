<template>
  <div
    class="ycard"
    :class="{ 'ycard--hoverable': hoverable, [`p-${paddingSize}`]: true }"
  >
    <div v-if="$slots.header" class="ycard-header mb-3">
      <slot name="header" />
    </div>
    <slot />
    <div v-if="$slots.footer" class="ycard-footer mt-3 pt-3 border-top">
      <slot name="footer" />
    </div>
  </div>
</template>

<script setup>
defineProps({
  hoverable: { type: Boolean, default: false },
  paddingSize: { type: [String, Number], default: '3' },
})
</script>
