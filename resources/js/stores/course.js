import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import axios from 'axios'

export const useCourseStore = defineStore('course', () => {
  const courses = ref([])
  const currentCourse = ref(null)
  const overallProgress = ref(0)
  const hasCertificate = ref(false)
  const certificate = ref(null)
  const loading = ref(false)
  const error = ref(null)

  const completedCourses = computed(() =>
    courses.value.filter(c => c.quiz_passed)
  )

  const allCoursesCompleted = computed(() =>
    courses.value.length > 0 && courses.value.every(c => c.quiz_passed)
  )

  async function fetchCourses() {
    loading.value = true
    error.value = null
    try {
      const { data } = await axios.get('/api/courses')
      courses.value = data.courses
      overallProgress.value = data.overall_progress
      hasCertificate.value = data.has_certificate
    } catch (err) {
      error.value = err.response?.data?.message || 'حدث خطأ في تحميل الدورات'
    } finally {
      loading.value = false
    }
  }

  async function fetchCourse(courseId) {
    loading.value = true
    error.value = null
    try {
      const { data } = await axios.get(`/api/courses/${courseId}`)
      currentCourse.value = data
      return data
    } catch (err) {
      error.value = err.response?.data?.message || 'حدث خطأ في تحميل المسار'
      throw err
    } finally {
      loading.value = false
    }
  }

  async function fetchLesson(courseId, lessonId) {
    const { data } = await axios.get(`/api/courses/${courseId}/lessons/${lessonId}`)
    return data
  }

  async function completeLesson(courseId, lessonId) {
    const { data } = await axios.post(`/api/courses/${courseId}/lessons/${lessonId}/complete`)
    // Refresh course data
    await fetchCourse(courseId)
    return data
  }

  async function fetchQuiz(courseId) {
    const { data } = await axios.get(`/api/courses/${courseId}/quiz`)
    return data
  }

  async function submitQuiz(courseId, answers) {
    const { data } = await axios.post(`/api/courses/${courseId}/quiz`, { answers })
    // Refresh courses to update progress
    await fetchCourses()
    return data
  }

  async function fetchCertificate() {
    try {
      const { data } = await axios.get('/api/certificate')
      certificate.value = data
      return data
    } catch {
      certificate.value = null
      return null
    }
  }

  function $reset() {
    courses.value = []
    currentCourse.value = null
    overallProgress.value = 0
    hasCertificate.value = false
    certificate.value = null
    loading.value = false
    error.value = null
  }

  return {
    courses, currentCourse, overallProgress, hasCertificate, certificate,
    loading, error, completedCourses, allCoursesCompleted,
    fetchCourses, fetchCourse, fetchLesson, completeLesson,
    fetchQuiz, submitQuiz, fetchCertificate, $reset,
  }
})
