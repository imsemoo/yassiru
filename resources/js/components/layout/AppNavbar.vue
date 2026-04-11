<template>
  <header class="yheader" :class="{ 'is-scrolled': isScrolled }">
    <div class="container">
      <div class="yheader__inner">
        <!-- Brand -->
        <router-link to="/" class="yheader__brand" @click="closeDrawer">
          <img src="/logo.svg" alt="يسّرو">
        </router-link>

        <!-- Desktop Nav -->
        <nav class="yheader__nav">
          <router-link
            v-for="link in navLinks"
            :key="link.to"
            :to="link.to"
            class="yheader__link"
          >
            {{ link.label }}
          </router-link>
        </nav>

        <!-- Desktop Actions -->
        <div class="yheader__actions">
          <template v-if="auth.isAuthenticated">
            <NotificationBell />
            <router-link to="/profile" class="yheader__user">
              <div class="yheader__user__avatar">{{ userInitial }}</div>
              <span class="yheader__user__name">{{ auth.user?.name }}</span>
            </router-link>
            <button class="yheader__btn-logout" @click="handleLogout" aria-label="تسجيل الخروج">
              <PhSignOut :size="18" weight="bold" />
            </button>
          </template>
          <template v-else>
            <router-link to="/login" class="yheader__btn-login">
              تسجيل الدخول
            </router-link>
            <router-link to="/register" class="yheader__btn-cta">
              <PhRocketLaunch :size="16" weight="bold" />
              ابدأ مجاناً
            </router-link>
          </template>
        </div>

        <!-- Mobile Actions -->
        <div class="yheader__mobile-actions">
          <NotificationBell v-if="auth.isAuthenticated" />
          <button class="yheader__hamburger" @click="openDrawer" aria-label="فتح القائمة">
            <PhList :size="22" weight="bold" />
          </button>
        </div>
      </div>
    </div>
  </header>

  <!-- Mobile Drawer -->
  <Teleport to="body">
    <div
      class="ydrawer-overlay"
      :class="{ 'is-open': drawerOpen }"
      @click="closeDrawer"
    ></div>

    <aside class="ydrawer" :class="{ 'is-open': drawerOpen }">
      <!-- Header -->
      <div class="ydrawer__header">
        <router-link to="/" class="ydrawer__header__brand" @click="closeDrawer">
          <img src="/logo.svg" alt="يسّرو">
        </router-link>
        <button class="ydrawer__close" @click="closeDrawer" aria-label="إغلاق">
          <PhX :size="20" weight="bold" />
        </button>
      </div>

      <!-- User Card (logged in) -->
      <div v-if="auth.isAuthenticated" class="ydrawer__user-card">
        <div class="ydrawer__user-card__avatar">{{ userInitial }}</div>
        <div class="ydrawer__user-card__info">
          <p class="name">{{ auth.user?.name }}</p>
          <span class="role">{{ roleLabel }}</span>
        </div>
      </div>

      <!-- Nav -->
      <nav class="ydrawer__nav">
        <router-link
          v-for="link in navLinks"
          :key="link.to"
          :to="link.to"
          class="ydrawer__link"
          @click="closeDrawer"
        >
          <div class="ydrawer__link__icon">
            <component :is="link.icon" :size="18" weight="bold" />
          </div>
          {{ link.label }}
        </router-link>

        <template v-if="auth.isAuthenticated">
          <div class="ydrawer__divider"></div>
          <router-link to="/profile" class="ydrawer__link" @click="closeDrawer">
            <div class="ydrawer__link__icon">
              <PhUserCircle :size="18" weight="bold" />
            </div>
            ملفي الشخصي
          </router-link>
        </template>
      </nav>

      <!-- Footer -->
      <div class="ydrawer__footer">
        <template v-if="auth.isAuthenticated">
          <button class="ydrawer__btn ydrawer__btn--danger" @click="handleLogout">
            <PhSignOut :size="18" weight="bold" />
            تسجيل الخروج
          </button>
        </template>
        <template v-else>
          <router-link to="/register" class="ydrawer__btn ydrawer__btn--primary" @click="closeDrawer">
            <PhRocketLaunch :size="18" weight="bold" />
            ابدأ مجاناً
          </router-link>
          <router-link to="/login" class="ydrawer__btn ydrawer__btn--outline" @click="closeDrawer">
            <PhSignIn :size="18" weight="bold" />
            تسجيل الدخول
          </router-link>
        </template>
      </div>
    </aside>
  </Teleport>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useRouter, useRoute } from 'vue-router'
import NotificationBell from './NotificationBell.vue'
import {
  PhList, PhX, PhUserCircle, PhSignOut, PhSignIn, PhRocketLaunch,
  PhHouse, PhBookOpen, PhHandCoins, PhHeart, PhCalculator,
  PhChats, PhUsers, PhShieldCheck,
} from '@phosphor-icons/vue'

const auth = useAuthStore()
const router = useRouter()
const route = useRoute()
const drawerOpen = ref(false)
const isScrolled = ref(false)

const userInitial = computed(() => {
  const name = auth.user?.name || ''
  return name.charAt(0).toUpperCase() || '؟'
})

const roleLabel = computed(() => {
  const role = auth.user?.role
  return { admin: 'مدير', recommender: 'معرّف', counselor: 'مستشار', user: 'عضو' }[role] || 'عضو'
})

const navLinks = computed(() => {
  // Admin sees their own dashboard + oversight
  if (auth.isAdmin) {
    return [
      { to: '/admin', label: 'الإدارة', icon: PhShieldCheck },
      { to: '/admin/users', label: 'المستخدمون', icon: PhUsers },
      { to: '/admin/recommenders', label: 'المعرّفون', icon: PhUsers },
      { to: '/admin/weddings', label: 'الأعراس', icon: PhHeart },
      { to: '/admin/reports', label: 'البلاغات', icon: PhShieldCheck },
    ]
  }

  // Recommender (imam/teacher) — only sees recommender features
  if (auth.isRecommender) {
    return [
      { to: '/recommender', label: 'لوحتي', icon: PhUsers },
      { to: '/recommender/add-candidate', label: 'إضافة مرشح', icon: PhUsers },
      { to: '/recommender/suggestions', label: 'الاقتراحات', icon: PhHeart },
      { to: '/recommender/family-requests', label: 'طلبات العائلات', icon: PhChats },
    ]
  }

  // Counselor — only sees their sessions
  if (auth.isCounselor) {
    return [
      { to: '/counselor', label: 'جلساتي', icon: PhChats },
    ]
  }

  // Regular user (default) — sees marriage-seeking features
  const links = [
    { to: '/', label: 'الرئيسية', icon: PhHouse },
    { to: '/courses', label: 'التأهيل', icon: PhBookOpen },
    { to: '/fund', label: 'الصندوق', icon: PhHandCoins },
    { to: '/weddings', label: 'الأعراس', icon: PhHeart },
    { to: '/calculator', label: 'الحاسبة', icon: PhCalculator },
  ]

  if (auth.isAuthenticated) {
    links.push({ to: '/counseling', label: 'الاستشارات', icon: PhChats })
  }

  return links
})

function openDrawer() {
  drawerOpen.value = true
  document.body.classList.add('drawer-open')
}

function closeDrawer() {
  drawerOpen.value = false
  document.body.classList.remove('drawer-open')
}

// Close drawer on route change
watch(() => route.fullPath, () => closeDrawer())

// Close drawer on Escape
function handleEscape(e) {
  if (e.key === 'Escape' && drawerOpen.value) closeDrawer()
}

function handleScroll() {
  isScrolled.value = window.scrollY > 20
}

onMounted(() => {
  window.addEventListener('scroll', handleScroll)
  document.addEventListener('keydown', handleEscape)
})

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll)
  document.removeEventListener('keydown', handleEscape)
  document.body.classList.remove('drawer-open')
})

async function handleLogout() {
  closeDrawer()
  await auth.logout()
  router.push('/')
}
</script>
