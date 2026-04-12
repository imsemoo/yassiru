<template>
  <div class="dashboard-page">
    <div class="container" style="max-width: 760px">
      <div class="page-header">
        <div>
          <h1 class="page-header__title">
            <span class="page-header__title__icon" style="--header-bg: rgba(13,115,119,0.1); --header-color: #0d7377">
              <PhChats :size="24" weight="duotone" />
            </span>
            مجتمع يسّرو
          </h1>
          <p class="page-header__subtitle">شارك خبرتك واستفد من تجارب الآخرين</p>
        </div>
      </div>

      <!-- New Post Form -->
      <div v-if="auth.isUser || auth.isAdmin" class="dash-card" style="margin-bottom: 1.5rem">
        <div class="dash-card__body">
          <form @submit.prevent="submitPost">
            <div class="dash-form__group">
              <input v-model="form.title" type="text" class="dash-form__input" placeholder="عنوان المنشور..." required>
            </div>
            <div class="dash-form__group">
              <textarea v-model="form.content" class="dash-form__textarea" rows="3" placeholder="اكتب منشورك هنا..." required></textarea>
            </div>
            <div class="d-flex justify-content-between align-items-center">
              <select v-model="form.category" class="dash-form__select" style="width: auto" required>
                <option value="advice">نصيحة</option>
                <option value="experience">تجربة</option>
                <option value="question">سؤال</option>
                <option value="tip">معلومة مفيدة</option>
              </select>
              <button type="submit" class="btn-action btn-action--primary btn-action--sm" :disabled="submitting">
                <span v-if="submitting" class="spinner-border spinner-border-sm"></span>
                <PhPaperPlaneTilt :size="14" v-else />
                نشر
              </button>
            </div>
          </form>
          <div v-if="postMsg" class="dash-alert dash-alert--success" style="margin-top: 0.75rem">
            <PhCheckCircle :size="16" weight="fill" class="dash-alert__icon" />
            <div class="dash-alert__content">{{ postMsg }}</div>
          </div>
        </div>
      </div>

      <!-- Posts List -->
      <div v-if="loading" class="loading-state">
        <div class="spinner-border"></div>
        <p class="loading-state__text">جاري تحميل المنشورات...</p>
      </div>

      <div v-else-if="posts.length === 0" class="empty-state">
        <PhChats :size="56" weight="duotone" class="empty-state__icon" />
        <h4 class="empty-state__title">لا توجد منشورات بعد</h4>
        <p class="empty-state__desc">كن أول من يشارك تجربته مع مجتمع يسّرو</p>
      </div>

      <div v-else class="posts-list">
        <div v-for="post in posts" :key="post.id" class="post-card">
          <div class="post-card__header">
            <div class="post-card__avatar">{{ (post.user?.name || '؟').charAt(0) }}</div>
            <div>
              <div class="post-card__author">{{ post.user?.name || 'مستخدم' }}</div>
              <div class="post-card__date">{{ formatDate(post.created_at) }}</div>
            </div>
            <span class="badge-cat">{{ catLabel(post.category) }}</span>
          </div>
          <h3 class="post-card__title">{{ post.title }}</h3>
          <p class="post-card__content">{{ post.content }}</p>
        </div>
      </div>

      <!-- Pagination -->
      <div v-if="lastPage > 1" class="pagination-wrap">
        <button class="btn-action btn-action--sm" :disabled="page === 1" @click="loadPosts(page - 1)">السابق</button>
        <span class="pagination-info">{{ page }} / {{ lastPage }}</span>
        <button class="btn-action btn-action--sm" :disabled="page === lastPage" @click="loadPosts(page + 1)">التالي</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useApi } from '@/composables/useApi'
import { useAuthStore } from '@/stores/auth'
import { PhChats, PhPaperPlaneTilt, PhCheckCircle } from '@phosphor-icons/vue'

const auth = useAuthStore()
const { get, post: apiPost, loading } = useApi()
const posts = ref([])
const page = ref(1)
const lastPage = ref(1)
const submitting = ref(false)
const postMsg = ref('')
const form = ref({ title: '', content: '', category: 'advice' })

function catLabel(c) {
  return { advice: 'نصيحة', experience: 'تجربة', question: 'سؤال', tip: 'معلومة' }[c] || c
}

function formatDate(d) {
  if (!d) return ''
  return new Date(d).toLocaleDateString('ar-EG', { year: 'numeric', month: 'short', day: 'numeric' })
}

async function loadPosts(p = 1) {
  try {
    const result = await get(`/api/community/posts?page=${p}`)
    posts.value = result.data || result
    page.value = result.current_page || p
    lastPage.value = result.last_page || 1
  } catch { /* handled by useApi */ }
}

async function submitPost() {
  submitting.value = true
  postMsg.value = ''
  try {
    const result = await apiPost('/api/community/posts', form.value)
    postMsg.value = result?.message || 'تم نشر المنشور بنجاح'
    form.value = { title: '', content: '', category: 'advice' }
    setTimeout(() => (postMsg.value = ''), 5000)
    await loadPosts(1)
  } catch { /* handled by useApi */ }
  submitting.value = false
}

onMounted(() => loadPosts())
</script>

<style lang="scss" scoped>
.posts-list { display: flex; flex-direction: column; gap: 1rem; }

.post-card {
  background: #fff;
  border: 1px solid #e0d8cc;
  border-radius: 16px;
  padding: 1.25rem;

  &__header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 0.75rem;
  }

  &__avatar {
    width: 40px; height: 40px; border-radius: 50%;
    background: rgba(13,115,119,0.1); color: #0d7377;
    display: flex; align-items: center; justify-content: center;
    font-weight: 900; font-size: 1rem; flex-shrink: 0;
  }

  &__author { font-weight: 800; font-size: 0.9rem; color: #1a1a2a; }
  &__date { font-size: 0.75rem; color: #8888a0; }

  &__title {
    font-size: 1.05rem; font-weight: 800; color: #1a1a2a;
    margin: 0 0 0.5rem;
  }

  &__content {
    font-size: 0.9rem; color: #4a4a5e; line-height: 1.8; margin: 0;
  }
}

.badge-cat {
  margin-right: auto;
  padding: 0.25rem 0.75rem; border-radius: 100px;
  font-size: 0.7rem; font-weight: 700;
  background: rgba(184,134,11,0.08); color: #b8860b;
}

.pagination-wrap {
  display: flex; justify-content: center; align-items: center;
  gap: 1rem; margin-top: 1.5rem;
}

.pagination-info { font-size: 0.85rem; color: #8888a0; font-weight: 600; }
</style>
