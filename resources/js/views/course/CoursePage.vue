<template>
  <div class="dashboard-page">
    <div class="container">
      <div v-if="loading" class="loading-state">
        <div class="spinner-border"></div>
        <p class="loading-state__text">جاري تحميل المسار...</p>
      </div>

      <div v-else-if="course">
        <router-link to="/courses" class="page-header__back">
          <PhArrowRight :size="14" weight="bold" />
          العودة للمسارات
        </router-link>

        <div class="page-header">
          <div>
            <h1 class="page-header__title">
              <span class="page-header__title__icon" :style="{ '--header-bg': config.bg, '--header-color': config.color }">
                <component :is="config.icon" :size="24" weight="duotone" />
              </span>
              {{ course.title }}
            </h1>
            <p class="page-header__subtitle">{{ config.label }}</p>
          </div>
        </div>

        <p class="text-muted mb-4" style="line-height: 1.95; max-width: 720px">{{ course.description }}</p>

        <div class="row g-4">
          <!-- Lessons -->
          <div class="col-lg-8">
            <div class="dash-card">
              <div class="dash-card__header">
                <h3 class="dash-card__header__title">
                  <PhListNumbers :size="20" />
                  الدروس
                </h3>
                <span class="dash-card__header__meta">{{ course.lessons?.length || 0 }} درس</span>
              </div>
              <div class="dash-card__body--flush">
                <div class="lessons-list">
                  <router-link
                    v-for="(lesson, i) in course.lessons"
                    :key="lesson.id"
                    :to="`/courses/${course.id}/lessons/${lesson.id}`"
                    class="lesson-row"
                    :class="{ 'is-completed': lesson.is_completed }"
                  >
                    <div class="lesson-row__number">
                      <PhCheckCircle :size="20" weight="fill" v-if="lesson.is_completed" />
                      <span v-else>{{ i + 1 }}</span>
                    </div>
                    <div class="lesson-row__content">
                      <div class="lesson-row__title">{{ lesson.title }}</div>
                      <div class="lesson-row__meta">
                        <span><PhClock :size="13" /> {{ lesson.duration_minutes }} دقيقة</span>
                        <span><PhPlayCircle :size="13" /> {{ lessonTypeLabel(lesson.type) }}</span>
                      </div>
                    </div>
                    <PhCaretLeft :size="18" class="lesson-row__arrow" />
                  </router-link>
                </div>
              </div>
            </div>

            <!-- Quiz Section -->
            <div class="dash-card quiz-card" :class="course.quiz_passed ? 'is-passed' : ''">
              <div class="dash-card__body">
                <div class="quiz-header">
                  <div class="quiz-header__icon" :class="course.quiz_passed ? 'is-passed' : 'is-pending'">
                    <PhExam :size="28" weight="duotone" />
                  </div>
                  <div>
                    <h3 class="quiz-header__title">اختبار المسار</h3>
                    <p class="quiz-header__desc">10 أسئلة — اجتياز بـ 7/10 على الأقل</p>
                  </div>
                </div>

                <div v-if="course.quiz_passed" class="dash-alert dash-alert--success mt-3 mb-0">
                  <PhTrophy :size="20" weight="fill" class="dash-alert__icon" />
                  <div class="dash-alert__content">
                    <div class="dash-alert__title">تم اجتياز الاختبار!</div>
                    النتيجة: <strong>{{ course.quiz_score }}/10</strong>
                  </div>
                </div>
                <div v-else-if="allLessonsCompleted">
                  <div v-if="course.quiz_attempts >= 3" class="dash-alert dash-alert--danger mt-3 mb-0">
                    <PhWarningCircle :size="20" weight="fill" class="dash-alert__icon" />
                    <div class="dash-alert__content">
                      <div class="dash-alert__title">استنفدت جميع المحاولات</div>
                      تم استخدام 3/3 محاولات
                    </div>
                  </div>
                  <div v-else class="quiz-cta mt-3">
                    <div class="quiz-cta__info">
                      <PhInfo :size="16" />
                      المحاولات المتاحة: {{ 3 - (course.quiz_attempts || 0) }} من 3
                    </div>
                    <router-link :to="`/courses/${course.id}/quiz`" class="btn-action btn-action--primary">
                      <PhExam :size="18" weight="bold" />
                      ابدأ الاختبار
                    </router-link>
                  </div>
                </div>
                <div v-else class="dash-alert dash-alert--warning mt-3 mb-0">
                  <PhLock :size="20" weight="fill" class="dash-alert__icon" />
                  <div class="dash-alert__content">
                    <div class="dash-alert__title">الاختبار مقفل</div>
                    أكمل جميع الدروس أولاً لفتح الاختبار
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Sidebar -->
          <div class="col-lg-4">
            <div class="dash-card course-sidebar">
              <div class="dash-card__body">
                <div class="progress-circle">
                  <svg viewBox="0 0 140 140">
                    <circle cx="70" cy="70" r="60" fill="none" stroke="#f0ece4" stroke-width="12" />
                    <circle
                      cx="70" cy="70" r="60" fill="none"
                      :stroke="config.color" stroke-width="12"
                      stroke-linecap="round"
                      :stroke-dasharray="377"
                      :stroke-dashoffset="377 - (377 * progressPercent / 100)"
                      transform="rotate(-90 70 70)"
                      style="transition: stroke-dashoffset 0.8s"
                    />
                  </svg>
                  <div class="progress-circle__center">
                    <div class="percent">{{ progressPercent }}%</div>
                    <div class="label">مكتمل</div>
                  </div>
                </div>

                <div class="course-stats">
                  <div class="course-stat">
                    <PhBookOpen :size="18" />
                    <div>
                      <div class="label">الدروس</div>
                      <div class="value">{{ completedCount }} / {{ course.lessons?.length || 0 }}</div>
                    </div>
                  </div>
                  <div class="course-stat">
                    <PhClock :size="18" />
                    <div>
                      <div class="label">المدة الإجمالية</div>
                      <div class="value">{{ course.duration_hours }} ساعة</div>
                    </div>
                  </div>
                  <div class="course-stat">
                    <PhExam :size="18" />
                    <div>
                      <div class="label">الاختبار</div>
                      <div class="value" :class="course.quiz_passed ? 'text-success' : 'text-muted'">
                        {{ course.quiz_passed ? 'ناجح' : 'لم يُجتز' }}
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useRoute } from 'vue-router'
import { useApi } from '@/composables/useApi'
import { useSeo } from '@/composables/useSeo'
import {
  PhArrowRight, PhListNumbers, PhCheckCircle, PhClock, PhPlayCircle,
  PhCaretLeft, PhExam, PhLock, PhWarningCircle, PhTrophy, PhInfo, PhBookOpen,
  PhBookBookmark, PhBrain, PhCurrencyCircleDollar, PhWrench,
} from '@phosphor-icons/vue'

const route = useRoute()
const { get, loading } = useApi()
const course = ref(null)

const trackConfigs = {
  shariah: { icon: PhBookBookmark, bg: 'rgba(27,122,74,0.1)', color: '#1b7a4a', label: 'المسار الشرعي' },
  psychology: { icon: PhBrain, bg: 'rgba(21,101,192,0.1)', color: '#1565c0', label: 'المسار النفسي' },
  financial: { icon: PhCurrencyCircleDollar, bg: 'rgba(184,134,11,0.1)', color: '#b8860b', label: 'المسار المالي' },
  practical: { icon: PhWrench, bg: 'rgba(107,114,128,0.1)', color: '#6b7280', label: 'المسار العملي' },
}

const config = computed(() => trackConfigs[course.value?.track] || trackConfigs.shariah)

function lessonTypeLabel(type) {
  return ({ video: 'فيديو', article: 'مقال', interactive: 'تفاعلي' })[type] || type
}

const allLessonsCompleted = computed(() => {
  if (!course.value?.lessons) return false
  return course.value.lessons.every(l => l.is_completed)
})

const completedCount = computed(() => course.value?.lessons?.filter(l => l.is_completed).length || 0)

const progressPercent = computed(() => {
  if (!course.value?.lessons?.length) return 0
  return Math.round((completedCount.value / course.value.lessons.length) * 100)
})

onMounted(async () => {
  try {
    course.value = await get(`/api/courses/${route.params.id}`)
  } catch { /* handled */ }
})

watch(course, (c) => {
  if (c) {
    useSeo({
      title: c.title,
      description: c.description || `مسار ${c.title} في الدورة التأهيلية — ${c.lessons?.length || 0} دروس`,
      path: `/courses/${c.id}`,
      schema: {
        '@context': 'https://schema.org',
        '@type': 'Course',
        name: c.title,
        description: c.description,
        provider: { '@type': 'Organization', name: 'يسّرو' },
        inLanguage: 'ar',
      },
    })
  }
})
</script>

<style lang="scss" scoped>
.lessons-list {
  display: flex;
  flex-direction: column;
}

.lesson-row {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1.1rem 1.5rem;
  border-bottom: 1px solid #f0ece4;
  text-decoration: none;
  color: inherit;
  transition: all 0.15s;

  &:last-child { border-bottom: none; }

  &:hover {
    background: #faf9f6;
    .lesson-row__arrow { transform: translateX(-3px); color: #0d7377; }
  }

  &__number {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    background: #faf9f6;
    border: 2px solid #e0d8cc;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    color: #8888a0;
    flex-shrink: 0;
    transition: all 0.2s;
  }

  &.is-completed &__number {
    background: rgba(27,122,74,0.1);
    border-color: #1b7a4a;
    color: #1b7a4a;
  }

  &__content { flex: 1; min-width: 0; }
  &__title {
    font-weight: 700;
    color: #1a1a2a;
    margin-bottom: 0.25rem;
  }
  &__meta {
    display: flex;
    gap: 0.85rem;
    font-size: 0.78rem;
    color: #8888a0;

    span {
      display: inline-flex;
      align-items: center;
      gap: 0.25rem;
    }
  }

  &__arrow {
    color: #8888a0;
    transition: all 0.2s;
  }
}

.quiz-card {
  &.is-passed {
    background: linear-gradient(135deg, #fff 0%, rgba(27,122,74,0.04) 100%);
    border-color: rgba(27,122,74,0.3);
  }
}

.quiz-header {
  display: flex;
  align-items: center;
  gap: 1rem;

  &__icon {
    width: 56px;
    height: 56px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;

    &.is-passed {
      background: rgba(27,122,74,0.1);
      color: #1b7a4a;
    }

    &.is-pending {
      background: rgba(184,134,11,0.1);
      color: #b8860b;
    }
  }

  &__title {
    font-size: 1.15rem;
    font-weight: 800;
    color: #1a1a2a;
    margin: 0 0 0.2rem;
  }

  &__desc {
    font-size: 0.85rem;
    color: #8888a0;
    margin: 0;
  }
}

.quiz-cta {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 0.75rem;
  flex-wrap: wrap;
  padding: 1rem 1.25rem;
  background: #faf9f6;
  border-radius: 12px;

  &__info {
    display: flex;
    align-items: center;
    gap: 0.4rem;
    font-size: 0.85rem;
    color: #4a4a5e;
    font-weight: 600;
  }
}

.course-sidebar {
  position: sticky;
  top: 96px;
}

.progress-circle {
  position: relative;
  width: 180px;
  height: 180px;
  margin: 0 auto 1.5rem;

  svg { width: 100%; height: 100%; }

  &__center {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    text-align: center;

    .percent {
      font-size: 2.25rem;
      font-weight: 900;
      color: #0d7377;
      line-height: 1;
      letter-spacing: -0.02em;
    }

    .label {
      font-size: 0.75rem;
      color: #8888a0;
      font-weight: 600;
      margin-top: 0.25rem;
    }
  }
}

.course-stats {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.course-stat {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.85rem 1rem;
  background: #faf9f6;
  border-radius: 12px;

  svg { color: #8888a0; flex-shrink: 0; }

  .label {
    font-size: 0.7rem;
    color: #8888a0;
    font-weight: 600;
  }

  .value {
    font-size: 0.9rem;
    font-weight: 800;
    color: #1a1a2a;
  }
}
</style>
