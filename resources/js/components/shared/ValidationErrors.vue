<template>
  <div v-if="errors" class="alert alert-danger">
    <template v-if="typeof errors === 'string'">{{ errors }}</template>
    <template v-else-if="typeof errors === 'object'">
      <ul class="mb-0 pe-3">
        <li v-for="(messages, field) in errors" :key="field">
          <span v-for="msg in (Array.isArray(messages) ? messages : [messages])" :key="msg">{{ msg }}</span>
        </li>
      </ul>
    </template>
  </div>
</template>

<script setup>
defineProps({
  errors: { type: [String, Object], default: null },
})
</script>
