import { createApp } from 'vue'
import { createPinia } from 'pinia'
import { createUnhead, headSymbol } from '@unhead/vue'
import axios from 'axios'
import router from './router'
import App from './App.vue'
import './assets/scss/app.scss'

// Global axios interceptor: auto-logout on 401
axios.interceptors.response.use(
  response => response,
  error => {
    if (error.response?.status === 401
      && !error.config.url?.includes('/auth/login')
      && !error.config.url?.includes('/auth/user')
    ) {
      localStorage.removeItem('token')
      delete axios.defaults.headers.common['Authorization']
      router.push({ name: 'login' })
    }
    return Promise.reject(error)
  }
)

const head = createUnhead()

const app = createApp(App)
app.use(createPinia())
app.provide(headSymbol, head)
app.use(router)
app.mount('#app')
