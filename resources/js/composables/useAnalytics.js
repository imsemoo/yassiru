/**
 * Analytics composable — track events via Google Analytics gtag
 */
export function useAnalytics() {
  function trackEvent(eventName, params = {}) {
    if (typeof window.gtag === 'function') {
      window.gtag('event', eventName, params)
    }
  }

  return {
    // Courses
    trackCourseStart: (courseTitle) => trackEvent('course_start', { course_title: courseTitle }),
    trackLessonComplete: (lessonTitle) => trackEvent('lesson_complete', { lesson_title: lessonTitle }),
    trackQuizSubmit: (courseTitle, passed) => trackEvent('quiz_submit', { course_title: courseTitle, passed }),
    trackCertificateEarned: () => trackEvent('certificate_earned'),

    // Fund
    trackCircleCreate: (name) => trackEvent('circle_create', { circle_name: name }),
    trackCircleJoin: (name) => trackEvent('circle_join', { circle_name: name }),
    trackContribution: (amount) => trackEvent('contribution_paid', { value: amount, currency: 'EGP' }),

    // Wedding
    trackWeddingRegister: (title) => trackEvent('wedding_register', { wedding_title: title }),

    // Calculator
    trackCalculatorUse: (city, level) => trackEvent('calculator_use', { city, level }),

    // Auth
    trackSignUp: () => trackEvent('sign_up'),
    trackLogin: () => trackEvent('login'),

    // Generic
    trackEvent,
  }
}
