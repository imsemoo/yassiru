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
import { PhHouse, PhBookOpen, PhHeart, PhHandCoins, PhCalculator, PhUserCircle, PhUsers, PhChats, PhShieldCheck } from '@phosphor-icons/vue'

const route = useRoute()
const auth = useAuthStore()

const navItems = computed(() => {
  // Admin
  if (auth.isAdmin) {
    return [
      { to: '/admin', label: 'الإدارة', icon: PhShieldCheck },
      { to: '/admin/users', label: 'مستخدمون', icon: PhUsers },
      { to: '/admin/weddings', label: 'أعراس', icon: PhHeart },
      { to: '/admin/reports', label: 'بلاغات', icon: PhShieldCheck },
      { to: '/profile', label: 'حسابي', icon: PhUserCircle },
    ]
  }

  // Recommender
  if (auth.isRecommender) {
    return [
      { to: '/recommender', label: 'لوحتي', icon: PhUsers },
      { to: '/recommender/add-candidate', label: 'إضافة', icon: PhUsers },
      { to: '/recommender/suggestions', label: 'اقتراحات', icon: PhHeart },
      { to: '/recommender/family-requests', label: 'طلبات', icon: PhChats },
      { to: '/profile', label: 'حسابي', icon: PhUserCircle },
    ]
  }

  // Counselor
  if (auth.isCounselor) {
    return [
      { to: '/counselor', label: 'جلساتي', icon: PhChats },
      { to: '/profile', label: 'حسابي', icon: PhUserCircle },
    ]
  }

  // Regular user (default)
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
