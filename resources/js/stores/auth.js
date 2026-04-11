import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import axios from 'axios'

export const useAuthStore = defineStore('auth', () => {
  const user = ref(null)
  const token = ref(localStorage.getItem('token'))

  // Set axios default header immediately if token exists
  if (token.value) {
    axios.defaults.headers.common['Authorization'] = `Bearer ${token.value}`
  }

  const isAuthenticated = computed(() => !!token.value)

  // Role checks (single source of truth)
  const role = computed(() => user.value?.role || 'user')
  const isUser = computed(() => role.value === 'user')
  const isRecommender = computed(() => role.value === 'recommender')
  const isCounselor = computed(() => role.value === 'counselor')
  const isAdmin = computed(() => role.value === 'admin')

  const hasCertificate = computed(() => !!user.value?.has_certificate)

  // Feature-based helpers (use these in components instead of raw role checks)
  const canTakeCourses = computed(() => isUser.value)
  const canEarnCertificate = computed(() => isUser.value)
  const canJoinFund = computed(() => isUser.value && hasCertificate.value)
  const canRegisterWedding = computed(() => isUser.value && hasCertificate.value)
  const canBookCounseling = computed(() => isUser.value)
  const canAccessRecommenderPanel = computed(() => isRecommender.value || isAdmin.value)
  const canAccessCounselorPanel = computed(() => isCounselor.value || isAdmin.value)
  const canAccessAdminPanel = computed(() => isAdmin.value)

  // Default home route per role (used for redirects)
  const defaultHome = computed(() => {
    if (isAdmin.value) return '/admin'
    if (isRecommender.value) return '/recommender'
    if (isCounselor.value) return '/counselor'
    return '/'
  })

  function setAuth(userData, tokenValue) {
    user.value = userData
    token.value = tokenValue
    localStorage.setItem('token', tokenValue)
    axios.defaults.headers.common['Authorization'] = `Bearer ${tokenValue}`
  }

  function clearAuth() {
    user.value = null
    token.value = null
    localStorage.removeItem('token')
    delete axios.defaults.headers.common['Authorization']
  }

  async function login(credentials) {
    const { data } = await axios.post('/api/auth/login', credentials)
    setAuth(data.user, data.token)
    return data
  }

  async function register(formData) {
    const { data } = await axios.post('/api/auth/register', formData)
    setAuth(data.user, data.token)
    return data
  }

  async function logout() {
    try {
      await axios.post('/api/auth/logout')
    } finally {
      clearAuth()
    }
  }

  async function fetchUser() {
    if (!token.value) return
    try {
      axios.defaults.headers.common['Authorization'] = `Bearer ${token.value}`
      const { data } = await axios.get('/api/auth/user')
      // UserResource returns { data: {...} }, plain array returns the object directly
      user.value = data.data || data
    } catch {
      clearAuth()
    }
  }

  return {
    user, token,
    // Auth state
    isAuthenticated,
    // Roles
    role, isUser, isRecommender, isCounselor, isAdmin,
    // Certificate
    hasCertificate,
    // Feature permissions
    canTakeCourses, canEarnCertificate, canJoinFund, canRegisterWedding,
    canBookCounseling, canAccessRecommenderPanel, canAccessCounselorPanel, canAccessAdminPanel,
    // Navigation
    defaultHome,
    // Actions
    login, register, logout, fetchUser, clearAuth,
  }
})
