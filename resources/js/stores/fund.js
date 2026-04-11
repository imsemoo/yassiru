import { defineStore } from 'pinia'
import { ref } from 'vue'
import axios from 'axios'

export const useFundStore = defineStore('fund', () => {
  const circles = ref([])
  const currentCircle = ref(null)
  const dashboard = ref(null)
  const loading = ref(false)
  const error = ref(null)
  const pagination = ref(null)

  async function fetchCircles(params = {}) {
    loading.value = true
    error.value = null
    try {
      const { data } = await axios.get('/api/fund/circles', { params })
      circles.value = data.data
      pagination.value = {
        current_page: data.current_page,
        last_page: data.last_page,
        total: data.total,
      }
    } catch (err) {
      error.value = err.response?.data?.message || 'حدث خطأ في تحميل الحلقات'
    } finally {
      loading.value = false
    }
  }

  async function fetchCircle(circleId) {
    loading.value = true
    error.value = null
    try {
      const { data } = await axios.get(`/api/fund/circles/${circleId}`)
      currentCircle.value = data
      return data
    } catch (err) {
      error.value = err.response?.data?.message || 'حدث خطأ في تحميل الحلقة'
      throw err
    } finally {
      loading.value = false
    }
  }

  async function createCircle(formData) {
    const { data } = await axios.post('/api/fund/circles', formData)
    return data
  }

  async function joinCircle(circleId) {
    const { data } = await axios.post(`/api/fund/circles/${circleId}/join`)
    return data
  }

  async function fetchDashboard(circleId) {
    loading.value = true
    error.value = null
    try {
      const { data } = await axios.get(`/api/fund/circles/${circleId}/dashboard`)
      dashboard.value = data
      return data
    } catch (err) {
      error.value = err.response?.data?.message || 'حدث خطأ في تحميل لوحة التحكم'
      throw err
    } finally {
      loading.value = false
    }
  }

  async function contribute(circleId, paymentRef = null) {
    const { data } = await axios.post(`/api/fund/circles/${circleId}/contribute`, {
      payment_ref: paymentRef,
    })
    // Refresh dashboard
    await fetchDashboard(circleId)
    return data
  }

  function $reset() {
    circles.value = []
    currentCircle.value = null
    dashboard.value = null
    loading.value = false
    error.value = null
    pagination.value = null
  }

  return {
    circles, currentCircle, dashboard, loading, error, pagination,
    fetchCircles, fetchCircle, createCircle, joinCircle,
    fetchDashboard, contribute, $reset,
  }
})
