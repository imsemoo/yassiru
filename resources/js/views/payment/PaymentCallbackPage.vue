<template>
  <div class="dashboard-page">
    <div class="container" style="max-width: 400px; text-align: center; padding-top: 4rem">
      <div class="spinner-border"></div>
      <p style="margin-top: 1rem; color: #8888a0">جاري التحقق من الدفع...</p>
    </div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useApi } from '@/composables/useApi'

const route = useRoute()
const router = useRouter()
const { get } = useApi()

onMounted(async () => {
  const ref = route.query.ref
  if (!ref) {
    router.push('/')
    return
  }

  try {
    // Find payment by merchant ref via status endpoint
    // The callback URL has the merchant_ref, we need the UUID
    // Use a dedicated lookup or just navigate with ref
    router.push({ name: 'paymentStatus', params: { uuid: ref } })
  } catch {
    router.push('/')
  }
})
</script>
