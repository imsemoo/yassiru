<template>
  <div class="dashboard-page">
    <div class="container" style="max-width: 800px">
      <div v-if="loading" class="loading-state">
        <div class="spinner-border"></div>
        <p class="loading-state__text">جاري تحميل الاختبار...</p>
      </div>

      <!-- Result Screen -->
      <div v-else-if="result" class="result-screen">
        <div class="result-card" :class="result.passed ? 'is-passed' : 'is-failed'">
          <div class="result-card__icon">
            <PhTrophy :size="48" weight="duotone" v-if="result.passed" />
            <PhArrowCounterClockwise :size="48" weight="duotone" v-else />
          </div>

          <h1 class="result-card__title">
            {{ result.passed ? 'مبارك! اجتزت الاختبار' : 'لم تجتز الاختبار' }}
          </h1>

          <div class="result-card__score">
            <span class="big">{{ result.score }}</span>
            <span class="total">/ {{ result.total }}</span>
          </div>

          <p class="result-card__msg">{{ result.message }}</p>

          <!-- Certificate -->
          <div v-if="result.certificate" class="result-cert">
            <PhCertificate :size="36" weight="duotone" />
            <div>
              <strong>حصلت على شهادة الجاهزية الزواجية!</strong>
              <code>{{ result.certificate.certificate_number }}</code>
            </div>
          </div>

          <!-- Failed Info -->
          <div v-if="!result.passed" class="result-info">
            <PhInfo :size="16" />
            تحتاج 7 إجابات صحيحة على الأقل — المحاولات المتبقية: {{ result.remaining_attempts }}
          </div>

          <div class="result-actions">
            <router-link :to="`/courses/${$route.params.courseId}`" class="btn-action btn-action--primary">
              <PhArrowRight :size="16" weight="bold" />
              العودة للمسار
            </router-link>
          </div>
        </div>
      </div>

      <!-- Quiz Active -->
      <div v-else-if="questions.length">
        <div class="page-header">
          <div>
            <h1 class="page-header__title">
              <span class="page-header__title__icon" style="--header-bg: rgba(184,134,11,0.1); --header-color: #b8860b">
                <PhExam :size="24" weight="duotone" />
              </span>
              اختبار المسار
            </h1>
            <p class="page-header__subtitle">اختر الإجابة الصحيحة لكل سؤال — للنجاح: 7/10 على الأقل</p>
          </div>
        </div>

        <!-- Progress Bar -->
        <div class="quiz-progress-card">
          <div class="quiz-progress-card__top">
            <span class="quiz-progress-card__current">السؤال {{ currentIndex + 1 }} من {{ questions.length }}</span>
            <span class="quiz-progress-card__answered">
              <PhCheckCircle :size="14" weight="fill" />
              {{ answeredCount }} / {{ questions.length }}
            </span>
          </div>
          <div class="quiz-progress-card__bar">
            <div class="fill" :style="{ width: ((currentIndex + 1) / questions.length * 100) + '%' }"></div>
          </div>
        </div>

        <!-- Question -->
        <div class="dash-card">
          <div class="dash-card__body">
            <h2 class="quiz-question">
              <span class="quiz-question__num">{{ currentIndex + 1 }}.</span>
              {{ currentQuestion.question }}
            </h2>

            <div class="quiz-options">
              <button
                v-for="(option, i) in currentQuestion.options"
                :key="i"
                class="quiz-option"
                :class="{ 'is-selected': answers[currentIndex] === i }"
                @click="selectAnswer(i)"
              >
                <div class="quiz-option__letter">{{ ['أ', 'ب', 'ج', 'د'][i] }}</div>
                <span class="quiz-option__text">{{ option }}</span>
                <PhCheck :size="18" weight="bold" class="quiz-option__check" v-if="answers[currentIndex] === i" />
              </button>
            </div>
          </div>
        </div>

        <!-- Navigation -->
        <div class="quiz-nav">
          <button
            class="btn-action btn-action--outline"
            :disabled="currentIndex === 0"
            @click="currentIndex--"
          >
            <PhArrowRight :size="16" weight="bold" />
            السابق
          </button>

          <div class="quiz-dots hide-mobile">
            <button
              v-for="(_, qi) in questions"
              :key="qi"
              class="quiz-dot"
              :class="{
                'is-current': qi === currentIndex,
                'is-answered': answers[qi] !== null,
              }"
              @click="currentIndex = qi"
              :aria-label="`السؤال ${qi + 1}`"
            ></button>
          </div>

          <button
            v-if="currentIndex < questions.length - 1"
            class="btn-action btn-action--primary"
            @click="currentIndex++"
          >
            التالي
            <PhArrowLeft :size="16" weight="bold" />
          </button>
          <button
            v-else
            class="btn-action btn-action--gold"
            :disabled="answeredCount < questions.length || submitting"
            @click="submitQuiz"
          >
            <span v-if="submitting" class="spinner-border spinner-border-sm"></span>
            <PhPaperPlaneTilt :size="16" weight="bold" v-else />
            تسليم الاختبار
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { useApi } from '@/composables/useApi'
import {
  PhExam, PhCheckCircle, PhArrowRight, PhArrowLeft, PhPaperPlaneTilt,
  PhTrophy, PhCertificate, PhArrowCounterClockwise, PhInfo, PhCheck,
} from '@phosphor-icons/vue'

const route = useRoute()
const { get, post, loading } = useApi()

const questions = ref([])
const answers = ref([])
const currentIndex = ref(0)
const result = ref(null)
const submitting = ref(false)

const currentQuestion = computed(() => questions.value[currentIndex.value])
const answeredCount = computed(() => answers.value.filter(a => a !== null).length)

function selectAnswer(i) {
  answers.value[currentIndex.value] = i
  if (currentIndex.value < questions.value.length - 1) {
    setTimeout(() => currentIndex.value++, 350)
  }
}

onMounted(async () => {
  try {
    const data = await get(`/api/courses/${route.params.courseId}/quiz`)
    questions.value = data.questions || data
    answers.value = new Array(questions.value.length).fill(null)
  } catch { /* handled */ }
})

async function submitQuiz() {
  submitting.value = true
  try {
    result.value = await post(`/api/courses/${route.params.courseId}/quiz`, { answers: answers.value })
    window.scrollTo({ top: 0, behavior: 'smooth' })
  } catch { /* handled */ }
  submitting.value = false
}
</script>

<style lang="scss" scoped>
.quiz-progress-card {
  background: #fff;
  border: 1px solid #f0ece4;
  border-radius: 14px;
  padding: 1rem 1.25rem;
  margin-bottom: 1.25rem;

  &__top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.65rem;
  }

  &__current {
    font-size: 0.85rem;
    font-weight: 800;
    color: #0d7377;
    background: rgba(13,115,119,0.08);
    padding: 0.35rem 0.85rem;
    border-radius: 100px;
  }

  &__answered {
    font-size: 0.78rem;
    color: #8888a0;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;

    svg { color: #1b7a4a; }
  }

  &__bar {
    height: 8px;
    background: #f0ece4;
    border-radius: 100px;
    overflow: hidden;

    .fill {
      height: 100%;
      background: linear-gradient(90deg, #0d7377 0%, #b8860b 100%);
      border-radius: 100px;
      transition: width 0.4s;
    }
  }
}

.quiz-question {
  font-size: 1.2rem;
  font-weight: 800;
  color: #1a1a2a;
  line-height: 1.75;
  margin: 0 0 1.5rem;

  &__num {
    color: #0d7377;
    margin-left: 0.5rem;
  }
}

.quiz-options {
  display: flex;
  flex-direction: column;
  gap: 0.6rem;
}

.quiz-option {
  display: flex;
  align-items: center;
  gap: 0.85rem;
  padding: 1rem 1.25rem;
  background: #faf9f6;
  border: 2px solid #e0d8cc;
  border-radius: 14px;
  cursor: pointer;
  transition: all 0.2s;
  text-align: right;
  font-family: inherit;
  width: 100%;

  &:hover:not(.is-selected) {
    border-color: #0d7377;
    background: #fff;
  }

  &__letter {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: #fff;
    border: 2px solid #e0d8cc;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    color: #4a4a5e;
    flex-shrink: 0;
    transition: all 0.2s;
  }

  &__text {
    flex: 1;
    font-size: 0.95rem;
    color: #1a1a2a;
    font-weight: 600;
  }

  &__check {
    color: #fff;
    flex-shrink: 0;
  }

  &.is-selected {
    background: linear-gradient(135deg, #0d7377 0%, #095456 100%);
    border-color: #095456;
    box-shadow: 0 8px 24px rgba(13,115,119,0.25);

    .quiz-option__letter {
      background: rgba(255,255,255,0.2);
      border-color: rgba(255,255,255,0.3);
      color: #fff;
    }

    .quiz-option__text { color: #fff; }
  }
}

.quiz-nav {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 0.75rem;
  margin-top: 1.5rem;
}

.quiz-dots {
  display: flex;
  gap: 0.4rem;
}

.quiz-dot {
  width: 11px;
  height: 11px;
  border-radius: 50%;
  border: none;
  background: #e0d8cc;
  cursor: pointer;
  padding: 0;
  transition: all 0.2s;

  &:hover { transform: scale(1.2); }

  &.is-answered { background: #1b7a4a; }
  &.is-current {
    background: #0d7377;
    transform: scale(1.3);
  }
}

// Result Screen
.result-screen {
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 60vh;
}

.result-card {
  background: #fff;
  border-radius: 24px;
  padding: 3rem 2rem;
  text-align: center;
  max-width: 520px;
  width: 100%;
  border: 1px solid #f0ece4;
  box-shadow: 0 20px 60px rgba(13, 26, 42, 0.06);

  &.is-passed {
    border-color: rgba(27,122,74,0.3);
    background: linear-gradient(135deg, #fff 0%, rgba(27,122,74,0.04) 100%);
  }

  &.is-failed {
    border-color: rgba(192,57,43,0.3);
    background: linear-gradient(135deg, #fff 0%, rgba(192,57,43,0.04) 100%);
  }

  &__icon {
    width: 96px;
    height: 96px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1.5rem;
  }

  &.is-passed &__icon {
    background: rgba(27,122,74,0.1);
    color: #1b7a4a;
  }

  &.is-failed &__icon {
    background: rgba(192,57,43,0.1);
    color: #c0392b;
  }

  &__title {
    font-size: 1.5rem;
    font-weight: 900;
    margin-bottom: 1rem;
  }

  &.is-passed &__title { color: #1b7a4a; }
  &.is-failed &__title { color: #c0392b; }

  &__score {
    font-weight: 900;
    color: #1a1a2a;
    margin-bottom: 0.75rem;
    line-height: 1;

    .big {
      font-size: 4rem;
      letter-spacing: -0.04em;
    }

    .total {
      font-size: 1.5rem;
      color: #8888a0;
    }
  }

  &__msg {
    color: #4a4a5e;
    margin-bottom: 1.5rem;
  }
}

.result-cert {
  display: flex;
  align-items: center;
  gap: 1rem;
  background: linear-gradient(135deg, rgba(184,134,11,0.08) 0%, rgba(13,115,119,0.05) 100%);
  border: 2px dashed rgba(184,134,11,0.3);
  border-radius: 14px;
  padding: 1.25rem;
  text-align: right;
  margin-bottom: 1.5rem;

  > svg { color: #b8860b; flex-shrink: 0; }

  strong {
    display: block;
    color: #1a1a2a;
    margin-bottom: 0.4rem;
    font-size: 0.95rem;
  }

  code {
    display: block;
    color: #b8860b;
    font-weight: 800;
    font-size: 1.1rem;
  }
}

.result-info {
  font-size: 0.85rem;
  color: #8888a0;
  margin-bottom: 1.5rem;
  display: inline-flex;
  align-items: center;
  gap: 0.3rem;
}

.result-actions {
  display: flex;
  justify-content: center;
}
</style>
