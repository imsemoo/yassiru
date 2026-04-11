<template>
  <nav class="mobile-nav">
    <router-link
      v-for="item in navItems"
      :key="item.to"
      :to="item.to"
      class="mobile-nav-item"
    >
      <component :is="item.icon" :size="22" :weight="isActive(item.to) ? 'fill' : 'regular'" />
      <span>{{ item.label }}</span>
    </router-link>
  </nav>
</template>

<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { PhHouse, PhBookOpen, PhHeart, PhHandCoins, PhCalculator, PhUserCircle } from '@phosphor-icons/vue'

const route = useRoute()
const auth = useAuthStore()

const navItems = computed(() => {
  const items = [
    { to: '/', label: 'الرئيسية', icon: PhHouse },
    { to: '/courses', label: 'التأهيل', icon: PhBookOpen },
    { to: '/weddings', label: 'الأعراس', icon: PhHeart },
    { to: '/fund', label: 'الصندوق', icon: PhHandCoins },
  ]

  if (auth.isAuthenticated) {
    items.push({ to: '/profile', label: 'حسابي', icon: PhUserCircle })
  } else {
    items.push({ to: '/calculator', label: 'الحاسبة', icon: PhCalculator })
  }

  return items
})

function isActive(to) {
  if (to === '/') return route.path === '/'
  return route.path.startsWith(to)
}
</script>
