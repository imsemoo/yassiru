import { ref } from 'vue'
import { useApi } from '@/composables/useApi'
import { useRouter } from 'vue-router'

export function usePayment() {
  const { post, get } = useApi()
  const router = useRouter()
  const paymentLoading = ref(false)
  const paymentError = ref(null)

  async function initiatePayment(type, payableId) {
    paymentLoading.value = true
    paymentError.value = null

    try {
      const result = await post('/api/payments/initiate', {
        type,
        payable_id: payableId,
      })

      // If Fawry returns a checkout URL, redirect to it
      if (result.checkout_url) {
        window.location.href = result.checkout_url
        return result
      }

      // Otherwise, navigate to our status page (for Fawry ref code display)
      if (result.payment_uuid) {
        router.push({ name: 'paymentStatus', params: { uuid: result.payment_uuid } })
        return result
      }

      paymentError.value = 'فشل في بدء عملية الدفع'
      return null
    } catch (e) {
      paymentError.value = e.response?.data?.message || 'حدث خطأ أثناء بدء الدفع'
      return null
    } finally {
      paymentLoading.value = false
    }
  }

  async function checkPaymentStatus(uuid) {
    try {
      return await get(`/api/payments/${uuid}/status`)
    } catch {
      return null
    }
  }

  return {
    initiatePayment,
    checkPaymentStatus,
    paymentLoading,
    paymentError,
  }
}
