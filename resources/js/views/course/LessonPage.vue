<template>
  <div class="dashboard-page">
    <div class="container" style="max-width: 880px">
      <div v-if="loading" class="loading-state">
        <div class="spinner-border"></div>
        <p class="loading-state__text">جاري تحميل الدرس...</p>
      </div>

      <div v-else-if="lesson">
        <router-link :to="`/courses/${$route.params.courseId}`" class="page-header__back">
          <PhArrowRight :size="14" weight="bold" />
          العودة للمسار
        </router-link>

        <!-- Lesson Header Card -->
        <div class="dash-card lesson-header-card">
          <div class="dash-card__body">
            <div class="lesson-header">
              <div class="lesson-header__icon">
                <PhPlayCircle :size="28" weight="duotone" v-if="lesson.type === 'video'" />
                <PhArticle :size="28" weight="duotone" v-else-if="lesson.type === 'article'" />
                <PhPuzzlePiece :size="28" weight="duotone" v-else />
              </div>
              <div class="lesson-header__content">
                <div class="lesson-header__breadcrumb">
                  {{ lesson.course_title }} <PhCaretLeft :size="12" /> الدرس {{ lesson.order_index }}
                </div>
                <h1 class="lesson-header__title">{{ lesson.title }}</h1>
                <div class="lesson-header__meta">
                  <span><PhClock :size="14" /> {{ lesson.duration_minutes }} دقيقة</span>
                  <span><PhTag :size="14" /> {{ lessonTypeLabel(lesson.type) }}</span>
                  <span v-if="completed" class="status-badge status-badge--success">
                    <PhCheckCircle :size="12" weight="fill" />
                    مكتمل
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Video Player -->
        <div v-if="lesson.video_url" class="video-wrap">
          <VideoPlayer
            :url="lesson.video_url"
            @progress="onVideoProgress"
            @ended="onVideoEnded"
          />
        </div>

        <!-- Lesson Content -->
        <div class="dash-card">
          <div class="dash-card__body">
            <div class="lesson-content" v-html="sanitize(lesson.content)"></div>
          </div>
        </div>

        <!-- Complete Section -->
        <div class="dash-card complete-card">
          <div class="dash-card__body">
            <div v-if="completed" class="completed-state">
              <div class="completed-state__icon">
                <PhCheckCircle :size="40" weight="fill" />
              </div>
              <h3 class="completed-state__title">تم إكمال هذا الدرس!</h3>
              <p class="completed-state__desc">يمكنك الانتقال للدرس التالي في المسار</p>
              <router-link :to="`/courses/${$route.params.courseId}`" class="btn-action btn-action--primary">
                <PhArrowRight :size="16" weight="bold" />
                العودة للمسار
              </router-link>
            </div>
            <div v-else class="complete-prompt">
              <PhBookOpen :size="36" weight="duotone" />
              <div class="complete-prompt__text">
                <h3>هل أنهيت هذا الدرس؟</h3>
                <p>سجّل إكمالك للانتقال للدرس التالي</p>
              </div>
              <button class="btn-action btn-action--primary" :disabled="completing" @click="completeLesson">
                <span v-if="completing" class="spinner-border spinner-border-sm"></span>
                <PhCheckCircle :size="18" weight="bold" v-else />
                إكمال الدرس
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import { useRoute } from 'vue-router'
import { useApi } from '@/composables/useApi'
import { useSeo } from '@/composables/useSeo'
import VideoPlayer from '@/components/course/VideoPlayer.vue'
import DOMPurify from 'dompurify'

function sanitize(html) {
  return DOMPurify.sanitize(html || '', {
    ALLOWED_TAGS: [
      'h2', 'h3', 'h4', 'h5', 'p', 'ul', 'ol', 'li',
      'strong', 'em', 'b', 'i', 'br', 'a', 'blockquote',
      'span', 'div', 'hr', 'code', 'pre',
      'table', 'thead', 'tbody', 'tr', 'th', 'td',
    ],
    ALLOWED_ATTR: ['href', 'target', 'class', 'style', 'border', 'cellpadding', 'cellspacing'],
  })
}
import {
  PhArrowRight, PhPlayCircle, PhArticle, PhPuzzlePiece,
  PhClock, PhTag, PhCheckCircle, PhCaretLeft, PhBookOpen,
} from '@phosphor-icons/vue'

const route = useRoute()
const { get, post, loading } = useApi()
const lesson = ref(null)
const completed = ref(false)
const completing = ref(false)
let lastSavedProgress = 0

function lessonTypeLabel(type) {
  return ({ video: 'فيديو', article: 'مقال', interactive: 'تفاعلي' })[type] || type
}

onMounted(async () => {
  try {
    lesson.value = await get(`/api/courses/${route.params.courseId}/lessons/${route.params.lessonId}`)
    completed.value = !!lesson.value.is_completed
  } catch { /* handled */ }
})

watch(lesson, (l) => {
  if (l) {
    useSeo({
      title: l.title,
      description: `${l.course_title} — الدرس ${l.order_index}: ${l.title}`,
      path: `/courses/${route.params.courseId}/lessons/${l.id}`,
    })
  }
})

async function onVideoProgress(percent) {
  if (percent > lastSavedProgress) {
    lastSavedProgress = percent
    try {
      await post(`/api/courses/${route.params.courseId}/lessons/${route.params.lessonId}/progress`, {
        progress_percent: percent,
      })
    } catch { /* silent */ }
  }
}

async function onVideoEnded() {
  if (!completed.value) {
    await completeLesson()
  }
}

async function completeLesson() {
  completing.value = true
  try {
    await post(`/api/courses/${route.params.courseId}/lessons/${route.params.lessonId}/complete`)
    completed.value = true
    window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' })
  } catch { /* handled */ }
  completing.value = false
}
</script>

<style lang="scss" scoped>
.lesson-header-card {
  background: linear-gradient(135deg, #fff 0%, #faf9f6 100%);
  border-color: rgba(13,115,119,0.2);
}

.lesson-header {
  display: flex;
  align-items: center;
  gap: 1rem;

  &__icon {
    width: 64px;
    height: 64px;
    border-radius: 16px;
    background: rgba(13,115,119,0.1);
    color: #0d7377;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }

  &__content { flex: 1; min-width: 0; }

  &__breadcrumb {
    font-size: 0.78rem;
    color: #8888a0;
    font-weight: 600;
    margin-bottom: 0.25rem;
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
  }

  &__title {
    font-size: 1.4rem;
    font-weight: 900;
    color: #1a1a2a;
    margin: 0 0 0.5rem;
    letter-spacing: -0.01em;

    @media (max-width: 576px) { font-size: 1.15rem; }
  }

  &__meta {
    display: flex;
    align-items: center;
    gap: 0.85rem;
    font-size: 0.8rem;
    color: #8888a0;
    flex-wrap: wrap;

    span:not(.status-badge) {
      display: inline-flex;
      align-items: center;
      gap: 0.25rem;
    }
  }
}

.video-wrap {
  margin-bottom: 1.5rem;
}

.lesson-content {
  line-height: 1.95;
  font-size: 1.05rem;
  color: #2d2d3a;

  :deep(h2),
  :deep(h3) {
    font-weight: 800;
    color: #1a1a2a;
    margin-top: 1.5rem;
    margin-bottom: 0.75rem;
  }

  :deep(p) {
    margin-bottom: 1rem;
  }

  :deep(ul),
  :deep(ol) {
    padding-right: 1.5rem;
    margin-bottom: 1rem;
  }

  :deep(li) {
    margin-bottom: 0.5rem;
  }

  :deep(strong) {
    color: #0d7377;
  }

  :deep(blockquote) {
    background: rgba(184, 134, 11, 0.06);
    border-right: 4px solid #b8860b;
    padding: 1rem 1.25rem;
    border-radius: 12px;
    margin: 1.5rem 0;

    h4 {
      color: #b8860b;
      font-size: 1rem;
      font-weight: 800;
      margin: 0 0 0.5rem;
    }

    p {
      color: #4a4a5e;
      font-size: 0.95rem;
      margin: 0;
    }
  }

  :deep(em) {
    font-style: italic;
    color: #4a4a5e;
    font-family: 'Amiri', serif;
  }

  :deep(table) {
    width: 100%;
    border-collapse: collapse;
    margin: 1.5rem 0;
    border-radius: 12px;
    overflow: hidden;
    border: 1px solid #e0d8cc;
    font-size: 0.9rem;
  }

  :deep(th) {
    background: #f3efe8;
    padding: 0.75rem 1rem;
    text-align: right;
    font-weight: 800;
    color: #1a1a2a;
    border-bottom: 1px solid #e0d8cc;
  }

  :deep(td) {
    padding: 0.75rem 1rem;
    border-bottom: 1px solid #f0ece4;
    color: #4a4a5e;
  }

  :deep(tr:last-child td) {
    border-bottom: none;
  }

  :deep(a) {
    color: #0d7377;
    text-decoration: underline;
    font-weight: 600;

    &:hover {
      color: #095456;
    }
  }

  :deep(h4) {
    font-size: 1.05rem;
    font-weight: 800;
    color: #0d7377;
    margin-top: 1.25rem;
    margin-bottom: 0.5rem;
  }
}

.complete-card {
  background: linear-gradient(135deg, #fff 0%, rgba(13,115,119,0.04) 100%);
}

.complete-prompt {
  display: flex;
  align-items: center;
  gap: 1.25rem;

  @media (max-width: 576px) { flex-direction: column; text-align: center; }

  > svg { color: #b8860b; flex-shrink: 0; }

  &__text {
    flex: 1;

    h3 { font-size: 1.1rem; font-weight: 800; color: #1a1a2a; margin: 0 0 0.25rem; }
    p { font-size: 0.85rem; color: #8888a0; margin: 0; }
  }
}

.completed-state {
  text-align: center;
  padding: 1rem 0;

  &__icon {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: rgba(27,122,74,0.1);
    color: #1b7a4a;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1rem;
  }

  &__title {
    font-size: 1.35rem;
    font-weight: 900;
    color: #1b7a4a;
    margin: 0 0 0.5rem;
  }

  &__desc {
    color: #8888a0;
    margin-bottom: 1.5rem;
  }
}
</style>
