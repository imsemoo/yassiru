<template>
  <div id="yassiru-app">
    <AppNavbar />
    <main>
      <router-view />
    </main>
    <AppFooter />
    <MobileBottomNav />
  </div>
</template>

<script setup>
import AppNavbar from '@/components/layout/AppNavbar.vue'
import AppFooter from '@/components/layout/AppFooter.vue'
import MobileBottomNav from '@/components/layout/MobileBottomNav.vue'
import { useAuthStore } from '@/stores/auth'
import { onMounted } from 'vue'

const auth = useAuthStore()

onMounted(() => {
  if (auth.token) {
    auth.fetchUser()
  }
})
</script>
