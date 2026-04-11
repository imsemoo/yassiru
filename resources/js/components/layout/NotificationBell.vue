<template>
  <div class="position-relative d-inline-block" v-if="auth.isAuthenticated">
    <button class="btn btn-link nav-link position-relative p-1" @click="toggle">
      <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
        <path d="M8 16a2 2 0 0 0 2-2H6a2 2 0 0 0 2 2zM8 1.918l-.797.161A4.002 4.002 0 0 0 4 6c0 .628-.134 2.197-.459 3.742-.16.767-.376 1.566-.663 2.258h10.244c-.287-.692-.502-1.49-.663-2.258C12.134 8.197 12 6.628 12 6a4.002 4.002 0 0 0-3.203-3.92L8 1.917z"/>
      </svg>
      <span v-if="unreadCount > 0" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem">
        {{ unreadCount > 9 ? '9+' : unreadCount }}
      </span>
    </button>

    <!-- Dropdown -->
    <div v-if="open" class="position-absolute bg-white shadow-lg rounded-3 p-0" style="left: 0; top: 100%; width: 320px; max-height: 400px; overflow-y: auto; z-index: 1050">
      <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
        <h6 class="fw-bold mb-0">الإشعارات</h6>
        <button v-if="unreadCount > 0" class="btn btn-link btn-sm p-0" @click="markAllRead">تعليم الكل كمقروء</button>
      </div>

      <div v-if="notifications.length">
        <div
          v-for="n in notifications"
          :key="n.id"
          class="p-3 border-bottom"
          :class="{ 'bg-light': !n.read_at }"
        >
          <p class="mb-1 small">{{ n.data?.message || 'إشعار جديد' }}</p>
          <small class="text-muted">{{ timeAgo(n.created_at) }}</small>
        </div>
      </div>
      <div v-else class="p-4 text-center text-muted small">
        لا توجد إشعارات
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import axios from 'axios'

const auth = useAuthStore()
const open = ref(false)
const notifications = ref([])
const unreadCount = ref(0)
let interval = null

function toggle() {
  open.value = !open.value
  if (open.value) loadNotifications()
}

async function loadNotifications() {
  try {
    const { data } = await axios.get('/api/notifications')
    notifications.value = data
  } catch { /* ignore */ }
}

async function loadCount() {
  if (!auth.isAuthenticated) return
  try {
    const { data } = await axios.get('/api/notifications/unread-count')
    unreadCount.value = data.count
  } catch { /* ignore */ }
}

async function markAllRead() {
  try {
    await axios.post('/api/notifications/read')
    unreadCount.value = 0
    notifications.value = notifications.value.map(n => ({ ...n, read_at: new Date() }))
  } catch { /* ignore */ }
}

function timeAgo(date) {
  const seconds = Math.floor((new Date() - new Date(date)) / 1000)
  if (seconds < 60) return 'الآن'
  if (seconds < 3600) return `منذ ${Math.floor(seconds / 60)} دقيقة`
  if (seconds < 86400) return `منذ ${Math.floor(seconds / 3600)} ساعة`
  return `منذ ${Math.floor(seconds / 86400)} يوم`
}

// Close on click outside
function handleClickOutside(e) {
  if (!e.target.closest('.position-relative')) open.value = false
}

onMounted(() => {
  loadCount()
  interval = setInterval(loadCount, 60000)
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  clearInterval(interval)
  document.removeEventListener('click', handleClickOutside)
})
</script>
