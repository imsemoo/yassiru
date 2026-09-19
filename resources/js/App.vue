<template>
  <div id="yassiru-app">
    <AppNavbar v-if="!isBare" />
    <main id="main">
      <router-view />
    </main>
    <AppFooter v-if="!isBare" />
    <MobileBottomNav v-if="!isBare" />
  </div>
</template>

<script setup>
import AppNavbar from '@/components/layout/AppNavbar.vue'
import AppFooter from '@/components/layout/AppFooter.vue'
import MobileBottomNav from '@/components/layout/MobileBottomNav.vue'
import { useAuthStore } from '@/stores/auth'
import { computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'

const auth = useAuthStore()
const route = useRoute()

// Auth screens are a threshold, not a page of the site. Showing the full
// nav there offers a "sign in" link to someone already on the sign-in page.
const isBare = computed(() => route.meta.guest === true)

onMounted(() => {
  if (auth.token) {
    auth.fetchUser()
  }
})
</script>
