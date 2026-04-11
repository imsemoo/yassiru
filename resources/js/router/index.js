import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

// Role constants
const USER = 'user'
const RECOMMENDER = 'recommender'
const COUNSELOR = 'counselor'
const ADMIN = 'admin'

const routes = [
  // Public (accessible to all)
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

  // Payment (any authenticated user)
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

  // =============================================================
  // USER-ONLY ROUTES (marriage-seeking features)
  // =============================================================
  {
    path: '/courses',
    name: 'courses',
    component: () => import('@/views/course/CourseListPage.vue'),
    meta: { requiresAuth: true, roles: [USER] },
  },
  {
    path: '/courses/:id',
    name: 'course',
    component: () => import('@/views/course/CoursePage.vue'),
    meta: { requiresAuth: true, roles: [USER] },
  },
  {
    path: '/courses/:courseId/lessons/:lessonId',
    name: 'lesson',
    component: () => import('@/views/course/LessonPage.vue'),
    meta: { requiresAuth: true, roles: [USER] },
  },
  {
    path: '/courses/:courseId/quiz',
    name: 'quiz',
    component: () => import('@/views/course/QuizPage.vue'),
    meta: { requiresAuth: true, roles: [USER] },
  },
  {
    path: '/certificate',
    name: 'certificate',
    component: () => import('@/views/course/CertificatePage.vue'),
    meta: { requiresAuth: true, roles: [USER] },
  },

  // Fund (user only)
  {
    path: '/fund',
    name: 'fund',
    component: () => import('@/views/fund/FundOverviewPage.vue'),
    meta: { roles: [USER] }, // public-ish but steers non-users away
  },
  {
    path: '/circles',
    name: 'circles',
    component: () => import('@/views/fund/CircleListPage.vue'),
    meta: { requiresAuth: true, roles: [USER] },
  },
  {
    path: '/circles/create',
    name: 'createCircle',
    component: () => import('@/views/fund/CreateCirclePage.vue'),
    meta: { requiresAuth: true, roles: [USER] },
  },
  {
    path: '/circles/:id/dashboard',
    name: 'circleDashboard',
    component: () => import('@/views/fund/CircleDashboard.vue'),
    meta: { requiresAuth: true, roles: [USER] },
  },

  // Weddings (user only)
  {
    path: '/weddings',
    name: 'weddings',
    component: () => import('@/views/wedding/WeddingListPage.vue'),
    meta: { roles: [USER] }, // public-ish
  },
  {
    path: '/weddings/:id',
    name: 'weddingDetail',
    component: () => import('@/views/wedding/WeddingDetailPage.vue'),
    meta: { roles: [USER] },
  },
  {
    path: '/my-weddings',
    name: 'myWeddings',
    component: () => import('@/views/wedding/MyWeddingsPage.vue'),
    meta: { requiresAuth: true, roles: [USER] },
  },

  // Counseling (user only — they book sessions as clients)
  {
    path: '/counseling',
    name: 'counseling',
    component: () => import('@/views/counseling/CounselingPage.vue'),
    meta: { requiresAuth: true, roles: [USER] },
  },

  // =============================================================
  // RECOMMENDER ROUTES (imams/teachers matching candidates)
  // =============================================================
  {
    // Registration page — only regular users can apply.
    // Must be defined BEFORE /recommender to take matching priority
    // (not strictly necessary in Vue Router 4, but clearer).
    path: '/recommender/register',
    name: 'recommenderRegister',
    component: () => import('@/views/recommender/RecommenderRegisterPage.vue'),
    meta: { requiresAuth: true, roles: [USER] },
  },
  {
    path: '/recommender',
    name: 'recommender',
    component: () => import('@/views/recommender/RecommenderDashboard.vue'),
    meta: { requiresAuth: true, roles: [RECOMMENDER, ADMIN] },
  },
  {
    path: '/recommender/add-candidate',
    name: 'addCandidate',
    component: () => import('@/views/recommender/AddCandidatePage.vue'),
    meta: { requiresAuth: true, roles: [RECOMMENDER, ADMIN] },
  },
  {
    path: '/recommender/suggestions',
    name: 'suggestions',
    component: () => import('@/views/recommender/SuggestionsPage.vue'),
    meta: { requiresAuth: true, roles: [RECOMMENDER, ADMIN] },
  },
  {
    path: '/recommender/family-requests',
    name: 'familyRequests',
    component: () => import('@/views/recommender/FamilyRequestsPage.vue'),
    meta: { requiresAuth: true, roles: [RECOMMENDER, ADMIN] },
  },

  // =============================================================
  // COUNSELOR ROUTES (new)
  // =============================================================
  {
    path: '/counselor',
    name: 'counselorDashboard',
    component: () => import('@/views/counselor/CounselorDashboard.vue'),
    meta: { requiresAuth: true, roles: [COUNSELOR, ADMIN] },
  },

  // =============================================================
  // PROFILE (all authenticated users)
  // =============================================================
  {
    path: '/profile',
    name: 'profile',
    component: () => import('@/views/user/ProfilePage.vue'),
    meta: { requiresAuth: true },
  },

  // =============================================================
  // ADMIN ROUTES
  // =============================================================
  {
    path: '/admin',
    name: 'admin',
    component: () => import('@/views/admin/AdminDashboard.vue'),
    meta: { requiresAuth: true, roles: [ADMIN] },
  },
  {
    path: '/admin/users',
    name: 'adminUsers',
    component: () => import('@/views/admin/ManageUsersPage.vue'),
    meta: { requiresAuth: true, roles: [ADMIN] },
  },
  {
    path: '/admin/recommenders',
    name: 'adminRecommenders',
    component: () => import('@/views/admin/ManageRecommendersPage.vue'),
    meta: { requiresAuth: true, roles: [ADMIN] },
  },
  {
    path: '/admin/reports',
    name: 'adminReports',
    component: () => import('@/views/admin/ReportsPage.vue'),
    meta: { requiresAuth: true, roles: [ADMIN] },
  },
  {
    path: '/admin/weddings',
    name: 'adminWeddings',
    component: () => import('@/views/admin/ManageWeddingsPage.vue'),
    meta: { requiresAuth: true, roles: [ADMIN] },
  },
  {
    path: '/admin/counseling',
    name: 'adminCounseling',
    component: () => import('@/views/admin/ManageCounselingPage.vue'),
    meta: { requiresAuth: true, roles: [ADMIN] },
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

  // Guest routes (login, register) — redirect away if already authenticated
  if (to.meta.guest && auth.isAuthenticated) {
    return { path: auth.defaultHome }
  }

  // Auth required
  if (to.meta.requiresAuth && !auth.isAuthenticated) {
    return { name: 'login', query: { redirect: to.fullPath } }
  }

  // Role-based access control
  if (to.meta.roles && auth.isAuthenticated) {
    const allowed = to.meta.roles.includes(auth.role)
    if (!allowed) {
      // Redirect to role-appropriate home instead of showing 403
      return { path: auth.defaultHome }
    }
  }

  // Home page redirect for non-user roles (they should land on their own dashboard)
  if (to.path === '/' && auth.isAuthenticated && !auth.isUser) {
    return { path: auth.defaultHome }
  }
})

export default router
