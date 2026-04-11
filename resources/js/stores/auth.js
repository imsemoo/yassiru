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
  const isRecommender = computed(() => user.value?.role === 'recommender')
  const isAdmin = computed(() => user.value?.role === 'admin')
  const hasCertificate = computed(() => !!user.value?.has_certificate)

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
    user, token, isAuthenticated, isRecommender, isAdmin, hasCertificate,
    login, register, logout, fetchUser, clearAuth
  }
})
