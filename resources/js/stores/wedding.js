import { defineStore } from 'pinia'
import { ref } from 'vue'
import axios from 'axios'

export const useWeddingStore = defineStore('wedding', () => {
  const weddings = ref([])
  const currentWedding = ref(null)
  const myRegistrations = ref([])
  const vendors = ref([])
  const loading = ref(false)
  const error = ref(null)
  const pagination = ref(null)

  async function fetchWeddings(params = {}) {
    loading.value = true
    error.value = null
    try {
      const { data } = await axios.get('/api/weddings', { params })
      weddings.value = data.data
      pagination.value = {
        current_page: data.current_page,
        last_page: data.last_page,
        total: data.total,
      }
    } catch (err) {
      error.value = err.response?.data?.message || 'حدث خطأ في تحميل الأعراس'
    } finally {
      loading.value = false
    }
  }

  async function fetchWedding(weddingId) {
    loading.value = true
    error.value = null
    try {
      const { data } = await axios.get(`/api/weddings/${weddingId}`)
      currentWedding.value = data
      return data
    } catch (err) {
      error.value = err.response?.data?.message || 'حدث خطأ في تحميل العرس'
      throw err
    } finally {
      loading.value = false
    }
  }

  async function registerForWedding(weddingId, notes = null) {
    const { data } = await axios.post(`/api/weddings/${weddingId}/register`, { notes })
    return data
  }

  async function fetchMyRegistrations() {
    loading.value = true
    error.value = null
    try {
      const { data } = await axios.get('/api/weddings/my-registrations')
      myRegistrations.value = data
      return data
    } catch (err) {
      error.value = err.response?.data?.message || 'حدث خطأ في تحميل التسجيلات'
    } finally {
      loading.value = false
    }
  }

  async function cancelRegistration(registrationId) {
    const { data } = await axios.delete(`/api/weddings/registrations/${registrationId}`)
    await fetchMyRegistrations()
    return data
  }

  async function payRegistration(registrationId, paymentRef) {
    const { data } = await axios.post(`/api/weddings/registrations/${registrationId}/pay`, {
      payment_ref: paymentRef,
    })
    await fetchMyRegistrations()
    return data
  }

  async function fetchVendors(params = {}) {
    loading.value = true
    error.value = null
    try {
      const { data } = await axios.get('/api/weddings/vendors', { params })
      vendors.value = data
      return data
    } catch (err) {
      error.value = err.response?.data?.message || 'حدث خطأ في تحميل الموردين'
    } finally {
      loading.value = false
    }
  }

  function $reset() {
    weddings.value = []
    currentWedding.value = null
    myRegistrations.value = []
    vendors.value = []
    loading.value = false
    error.value = null
    pagination.value = null
  }

  return {
    weddings, currentWedding, myRegistrations, vendors,
    loading, error, pagination,
    fetchWeddings, fetchWedding, registerForWedding,
    fetchMyRegistrations, cancelRegistration, payRegistration,
    fetchVendors, $reset,
  }
})
