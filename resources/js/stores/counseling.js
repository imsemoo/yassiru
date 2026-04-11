import { defineStore } from 'pinia'
import { ref } from 'vue'
import axios from 'axios'

export const useCounselingStore = defineStore('counseling', () => {
  const sessions = ref([])
  const availableSlots = ref([])
  const loading = ref(false)
  const error = ref(null)
  const pagination = ref(null)

  async function fetchSessions() {
    loading.value = true
    error.value = null
    try {
      const { data } = await axios.get('/api/counseling')
      sessions.value = data.data
      pagination.value = {
        current_page: data.current_page,
        last_page: data.last_page,
        total: data.total,
      }
    } catch (err) {
      error.value = err.response?.data?.message || 'حدث خطأ في تحميل الجلسات'
    } finally {
      loading.value = false
    }
  }

  async function bookSession(formData) {
    const { data } = await axios.post('/api/counseling', formData)
    await fetchSessions()
    return data
  }

  async function cancelSession(sessionId) {
    const { data } = await axios.put(`/api/counseling/${sessionId}/cancel`)
    await fetchSessions()
    return data
  }

  async function fetchAvailableSlots(date, type) {
    loading.value = true
    error.value = null
    try {
      const { data } = await axios.get('/api/counseling/slots', {
        params: { date, type },
      })
      availableSlots.value = data
      return data
    } catch (err) {
      error.value = err.response?.data?.message || 'حدث خطأ في تحميل المواعيد'
    } finally {
      loading.value = false
    }
  }

  function $reset() {
    sessions.value = []
    availableSlots.value = []
    loading.value = false
    error.value = null
    pagination.value = null
  }

  return {
    sessions, availableSlots, loading, error, pagination,
    fetchSessions, bookSession, cancelSession, fetchAvailableSlots, $reset,
  }
})
