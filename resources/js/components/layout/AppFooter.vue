<template>
  <footer class="footer-y">
    <div class="container">
      <div class="row g-4 g-lg-5">
        <!-- Brand Column -->
        <div class="col-lg-4">
          <div class="d-flex align-items-center gap-2 mb-3">
            <img src="/logo.svg" alt="يسّرو" height="100" style="filter: brightness(0) invert(1);">
          </div>
          <p class="mb-3" style="line-height: 1.9; font-size: 0.9rem;">
            منصة تيسير الزواج الأولى في العالم الإسلامي.
            نجمع بين التأهيل الشرعي والنفسي، التوفيق عبر معرّفين موثوقين،
            الصندوق التعاوني، والأعراس الجماعية — لنجعل الزواج يسيراً كما أمر الله.
          </p>
          <div class="d-flex gap-2">
            <a href="mailto:islam@yassiru.com" class="btn btn-sm" style="background: rgba(255,255,255,0.1); color: #fff;" aria-label="البريد">
              <PhEnvelope :size="18" />
            </a>
            <a href="https://yassiru.com" target="_blank" rel="noopener" class="btn btn-sm" style="background: rgba(255,255,255,0.1); color: #fff;" aria-label="الموقع">
              <PhGlobe :size="18" />
            </a>
          </div>
        </div>

        <!-- Quick Links — role-aware -->
        <div class="col-6 col-lg-2">
          <h6 class="footer-title">المنصة</h6>
          <ul class="footer-links">
            <li v-for="link in platformLinks" :key="link.to">
              <router-link :to="link.to">
                <component :is="link.icon" :size="16" />
                {{ link.label }}
              </router-link>
            </li>
          </ul>
        </div>

        <!-- Account -->
        <div class="col-6 col-lg-2">
          <h6 class="footer-title">حسابك</h6>
          <ul class="footer-links">
            <li v-for="link in accountLinks" :key="link.to">
              <router-link :to="link.to">
                <component :is="link.icon" :size="16" />
                {{ link.label }}
              </router-link>
            </li>
          </ul>
        </div>

        <!-- Contact -->
        <div class="col-lg-4">
          <h6 class="footer-title">تواصل معنا</h6>
          <ul class="footer-links">
            <li>
              <a href="mailto:islam@yassiru.com">
                <PhEnvelope :size="16" /> islam@yassiru.com
              </a>
            </li>
            <li>
              <a href="https://yassiru.com" target="_blank">
                <PhGlobe :size="16" /> yassiru.com
              </a>
            </li>
          </ul>
          <div class="mt-3 p-3 rounded" style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);">
            <p class="mb-0 small" style="line-height: 1.8;">
              <PhShieldCheck :size="16" class="ms-1" style="color: #1b7a4a;" />
              المنصة معتمدة شرعياً — لا ربا، لا تواصل مباشر بين الجنسين، كل شيء يمر عبر المعرّف وولي الأمر.
            </p>
          </div>
        </div>
      </div>

      <!-- Bottom -->
      <div class="footer-bottom">
        جميع الحقوق محفوظة &copy; {{ new Date().getFullYear() }} يسّرو — منصة تيسير الزواج
      </div>
    </div>
  </footer>
</template>

<script setup>
import { computed } from 'vue'
import { useAuthStore } from '@/stores/auth'
import {
  PhBookOpen, PhHandCoins, PhHeart, PhCalculator,
  PhShieldCheck, PhEnvelope, PhGlobe,
  PhSignIn, PhUserPlus, PhSquaresFour, PhChatsCircle,
  PhUsers, PhUsersFour,
} from '@phosphor-icons/vue'

const auth = useAuthStore()

const platformLinks = computed(() => {
  // Admin: oversight links
  if (auth.isAdmin) {
    return [
      { to: '/admin', label: 'الإدارة', icon: PhShieldCheck },
      { to: '/admin/users', label: 'المستخدمون', icon: PhUsers },
      { to: '/admin/recommenders', label: 'المعرّفون', icon: PhUsersFour },
      { to: '/admin/weddings', label: 'الأعراس', icon: PhHeart },
    ]
  }

  // Recommender
  if (auth.isRecommender) {
    return [
      { to: '/recommender', label: 'لوحتي', icon: PhUsers },
      { to: '/recommender/add-candidate', label: 'إضافة مرشح', icon: PhUserPlus },
      { to: '/recommender/suggestions', label: 'الاقتراحات', icon: PhHeart },
      { to: '/recommender/family-requests', label: 'طلبات العائلات', icon: PhChatsCircle },
    ]
  }

  // Counselor
  if (auth.isCounselor) {
    return [
      { to: '/counselor', label: 'جلساتي', icon: PhChatsCircle },
    ]
  }

  // Regular user & guests
  return [
    { to: '/courses', label: 'التأهيل', icon: PhBookOpen },
    { to: '/fund', label: 'الصندوق', icon: PhHandCoins },
    { to: '/weddings', label: 'الأعراس', icon: PhHeart },
    { to: '/calculator', label: 'الحاسبة', icon: PhCalculator },
  ]
})

const accountLinks = computed(() => {
  if (!auth.isAuthenticated) {
    return [
      { to: '/login', label: 'تسجيل الدخول', icon: PhSignIn },
      { to: '/register', label: 'إنشاء حساب', icon: PhUserPlus },
      { to: '/calculator', label: 'الحاسبة', icon: PhCalculator },
      { to: '/about', label: 'عن المنصة', icon: PhShieldCheck },
    ]
  }

  // Authenticated — show profile and role-specific entries
  const links = [{ to: '/profile', label: 'ملفي', icon: PhSquaresFour }]

  if (auth.isUser) {
    links.push({ to: '/counseling', label: 'الاستشارات', icon: PhChatsCircle })
  }

  return links
})
</script>
