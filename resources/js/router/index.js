import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const routes = [
  // Public
  {
    path: '/',
    name: 'home',
    component: () => import('@/views/public/HomePage.vue'),
  },
  {
    path: '/calculator',
    name: 'calculator',
    component: () => import('@/views/public/CalculatorPage.vue'),
  },
  {
    path: '/about',
    name: 'about',
    component: () => import('@/views/public/AboutPage.vue'),
  },
  {
    path: '/login',
    name: 'login',
    component: () => import('@/views/public/LoginPage.vue'),
    meta: { guest: true },
  },
  {
    path: '/register',
    name: 'register',
    component: () => import('@/views/public/RegisterPage.vue'),
    meta: { guest: true },
  },
  {
    path: '/forgot-password',
    name: 'forgotPassword',
    component: () => import('@/views/public/ForgotPasswordPage.vue'),
    meta: { guest: true },
  },
  {
    path: '/reset-password',
    name: 'resetPassword',
    component: () => import('@/views/public/ResetPasswordPage.vue'),
    meta: { guest: true },
  },

  // Payment
  {
    path: '/payment/status/:uuid',
    name: 'paymentStatus',
    component: () => import('@/views/payment/PaymentStatusPage.vue'),
    meta: { requiresAuth: true },
  },
  {
    path: '/payment/callback',
    name: 'paymentCallback',
    component: () => import('@/views/payment/PaymentCallbackPage.vue'),
  },

  // Courses
  {
    path: '/courses',
    name: 'courses',
    component: () => import('@/views/course/CourseListPage.vue'),
    meta: { requiresAuth: true },
  },
  {
    path: '/courses/:id',
    name: 'course',
    component: () => import('@/views/course/CoursePage.vue'),
    meta: { requiresAuth: true },
  },
  {
    path: '/courses/:courseId/lessons/:lessonId',
    name: 'lesson',
    component: () => import('@/views/course/LessonPage.vue'),
    meta: { requiresAuth: true },
  },
  {
    path: '/courses/:courseId/quiz',
    name: 'quiz',
    component: () => import('@/views/course/QuizPage.vue'),
    meta: { requiresAuth: true },
  },
  {
    path: '/certificate',
    name: 'certificate',
    component: () => import('@/views/course/CertificatePage.vue'),
    meta: { requiresAuth: true },
  },

  // Fund
  {
    path: '/fund',
    name: 'fund',
    component: () => import('@/views/fund/FundOverviewPage.vue'),
  },
  {
    path: '/circles',
    name: 'circles',
    component: () => import('@/views/fund/CircleListPage.vue'),
    meta: { requiresAuth: true },
  },
  {
    path: '/circles/create',
    name: 'createCircle',
    component: () => import('@/views/fund/CreateCirclePage.vue'),
    meta: { requiresAuth: true },
  },
  {
    path: '/circles/:id/dashboard',
    name: 'circleDashboard',
    component: () => import('@/views/fund/CircleDashboard.vue'),
    meta: { requiresAuth: true },
  },

  // Recommender
  {
    path: '/recommender',
    name: 'recommender',
    component: () => import('@/views/recommender/RecommenderDashboard.vue'),
    meta: { requiresAuth: true },
  },
  {
    path: '/recommender/add-candidate',
    name: 'addCandidate',
    component: () => import('@/views/recommender/AddCandidatePage.vue'),
    meta: { requiresAuth: true },
  },
  {
    path: '/recommender/suggestions',
    name: 'suggestions',
    component: () => import('@/views/recommender/SuggestionsPage.vue'),
    meta: { requiresAuth: true },
  },
  {
    path: '/recommender/family-requests',
    name: 'familyRequests',
    component: () => import('@/views/recommender/FamilyRequestsPage.vue'),
    meta: { requiresAuth: true },
  },

  // Weddings
  {
    path: '/weddings',
    name: 'weddings',
    component: () => import('@/views/wedding/WeddingListPage.vue'),
  },
  {
    path: '/weddings/:id',
    name: 'weddingDetail',
    component: () => import('@/views/wedding/WeddingDetailPage.vue'),
  },
  {
    path: '/my-weddings',
    name: 'myWeddings',
    component: () => import('@/views/wedding/MyWeddingsPage.vue'),
    meta: { requiresAuth: true },
  },

  // Counseling
  {
    path: '/counseling',
    name: 'counseling',
    component: () => import('@/views/counseling/CounselingPage.vue'),
    meta: { requiresAuth: true },
  },

  // Profile
  {
    path: '/profile',
    name: 'profile',
    component: () => import('@/views/user/ProfilePage.vue'),
    meta: { requiresAuth: true },
  },

  // Admin
  {
    path: '/admin',
    name: 'admin',
    component: () => import('@/views/admin/AdminDashboard.vue'),
    meta: { requiresAuth: true, requiresAdmin: true },
  },
  {
    path: '/admin/users',
    name: 'adminUsers',
    component: () => import('@/views/admin/ManageUsersPage.vue'),
    meta: { requiresAuth: true, requiresAdmin: true },
  },
  {
    path: '/admin/recommenders',
    name: 'adminRecommenders',
    component: () => import('@/views/admin/ManageRecommendersPage.vue'),
    meta: { requiresAuth: true, requiresAdmin: true },
  },
  {
    path: '/admin/reports',
    name: 'adminReports',
    component: () => import('@/views/admin/ReportsPage.vue'),
    meta: { requiresAuth: true, requiresAdmin: true },
  },
  {
    path: '/admin/weddings',
    name: 'adminWeddings',
    component: () => import('@/views/admin/ManageWeddingsPage.vue'),
    meta: { requiresAuth: true, requiresAdmin: true },
  },
  {
    path: '/admin/counseling',
    name: 'adminCounseling',
    component: () => import('@/views/admin/ManageCounselingPage.vue'),
    meta: { requiresAuth: true, requiresAdmin: true },
  },

  // 404
  {
    path: '/:pathMatch(.*)*',
    name: 'notFound',
    component: () => import('@/views/public/NotFoundPage.vue'),
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior() {
    return { top: 0 }
  },
})

router.beforeEach(async (to) => {
  const auth = useAuthStore()

  // Fetch user if we have a token but no user data
  if (auth.token && !auth.user) {
    await auth.fetchUser()
  }

  // Redirect to login if auth required
  if (to.meta.requiresAuth && !auth.isAuthenticated) {
    return { name: 'login', query: { redirect: to.fullPath } }
  }

  // Redirect to home if already logged in
  if (to.meta.guest && auth.isAuthenticated) {
    return { name: 'home' }
  }

  // Admin required
  if (to.meta.requiresAdmin && !auth.isAdmin) {
    return { name: 'home' }
  }

  // Certificate required
  if (to.meta.requiresCertificate && !auth.hasCertificate) {
    return { name: 'courses' }
  }
})

export default router
