<template>
  <Teleport to="body">
    <div v-if="modelValue" class="modal-backdrop fade show" @click="close" />
    <div
      v-if="modelValue"
      class="modal fade show d-block"
      tabindex="-1"
      @click.self="close"
    >
      <div class="modal-dialog modal-dialog-centered" :class="sizeClass">
        <div class="modal-content">
          <div v-if="title || $slots.header" class="modal-header">
            <slot name="header">
              <h5 class="modal-title">{{ title }}</h5>
            </slot>
            <button type="button" class="btn-close" @click="close" />
          </div>
          <div class="modal-body">
            <slot />
          </div>
          <div v-if="$slots.footer" class="modal-footer">
            <slot name="footer" />
          </div>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
import { computed, watch } from 'vue'

const props = defineProps({
  modelValue: { type: Boolean, default: false },
  title: { type: String, default: '' },
  size: { type: String, default: 'md' },
  closeable: { type: Boolean, default: true },
})

const emit = defineEmits(['update:modelValue'])

const sizeClass = computed(() => {
  if (props.size === 'sm') return 'modal-sm'
  if (props.size === 'lg') return 'modal-lg'
  if (props.size === 'xl') return 'modal-xl'
  return ''
})

function close() {
  if (props.closeable) {
    emit('update:modelValue', false)
  }
}

watch(() => props.modelValue, (val) => {
  document.body.style.overflow = val ? 'hidden' : ''
})
</script>
