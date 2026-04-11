import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import axios from 'axios'

export const useRecommenderStore = defineStore('recommender', () => {
  const recommender = ref(null)
  const candidates = ref([])
  const recommendations = ref([])
  const suggestions = ref([])
  const familyRequests = ref([])
  const loading = ref(false)
  const error = ref(null)

  const isApproved = computed(() => recommender.value?.is_approved ?? false)

  const maleCandidates = computed(() =>
    candidates.value.filter(c => c.gender === 'male' && c.status === 'active')
  )

  const femaleCandidates = computed(() =>
    candidates.value.filter(c => c.gender === 'female' && c.status === 'active')
  )

  async function register(formData) {
    const { data } = await axios.post('/api/recommender/register', formData)
    recommender.value = data.recommender
    return data
  }

  async function fetchDashboard() {
    loading.value = true
    error.value = null
    try {
      const { data } = await axios.get('/api/recommender/dashboard')
      recommender.value = data.recommender
      candidates.value = data.candidates
      recommendations.value = data.recommendations
      return data
    } catch (err) {
      error.value = err.response?.data?.message || 'حدث خطأ في تحميل لوحة التحكم'
    } finally {
      loading.value = false
    }
  }

  async function addCandidate(formData) {
    const { data } = await axios.post('/api/recommender/candidates', formData)
    candidates.value.push(data.candidate)
    return data
  }

  async function fetchSuggestions() {
    loading.value = true
    error.value = null
    try {
      const { data } = await axios.get('/api/recommender/suggestions')
      suggestions.value = data
      return data
    } catch (err) {
      error.value = err.response?.data?.message || 'حدث خطأ في تحميل الاقتراحات'
    } finally {
      loading.value = false
    }
  }

  async function createRecommendation(formData) {
    const { data } = await axios.post('/api/recommender/recommend', formData)
    return data
  }

  async function fetchFamilyRequests() {
    loading.value = true
    error.value = null
    try {
      const { data } = await axios.get('/api/recommender/family-requests')
      familyRequests.value = data
      return data
    } catch (err) {
      error.value = err.response?.data?.message || 'حدث خطأ في تحميل طلبات العائلات'
    } finally {
      loading.value = false
    }
  }

  async function respondToFamilyRequest(requestId, responseData) {
    const { data } = await axios.put(`/api/recommender/family-requests/${requestId}`, responseData)
    await fetchFamilyRequests()
    return data
  }

  function $reset() {
    recommender.value = null
    candidates.value = []
    recommendations.value = []
    suggestions.value = []
    familyRequests.value = []
    loading.value = false
    error.value = null
  }

  return {
    recommender, candidates, recommendations, suggestions, familyRequests,
    loading, error, isApproved, maleCandidates, femaleCandidates,
    register, fetchDashboard, addCandidate, fetchSuggestions,
    createRecommendation, fetchFamilyRequests, respondToFamilyRequest, $reset,
  }
})
