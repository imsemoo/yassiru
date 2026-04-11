<template>
  <div class="dashboard-page">
    <div class="container">
      <!-- Header -->
      <div class="page-header">
        <div>
          <h1 class="page-header__title">
            <span class="page-header__title__icon" style="--header-bg: rgba(13,115,119,0.1); --header-color: #0d7377">
              <PhChatsCircle :size="24" weight="duotone" />
            </span>
            الرعاية بعد الزواج
          </h1>
          <p class="page-header__subtitle">جلسات استشارية فردية وجماعية لدعمك في حياتك الزوجية</p>
        </div>
      </div>

      <!-- Stats -->
      <div class="stats-grid">
        <div class="stat-tile" style="--tile-color: #0d7377; --tile-bg: rgba(13,115,119,0.1)">
          <div class="stat-tile__icon">
            <PhCalendarCheck :size="24" weight="duotone" />
          </div>
          <div class="stat-tile__content">
            <div class="stat-tile__value">{{ scheduledCount }}</div>
            <div class="stat-tile__label">جلسات قادمة</div>
          </div>
        </div>
        <div class="stat-tile" style="--tile-color: #1b7a4a; --tile-bg: rgba(27,122,74,0.1)">
          <div class="stat-tile__icon">
            <PhCheckCircle :size="24" weight="duotone" />
          </div>
          <div class="stat-tile__content">
            <div class="stat-tile__value">{{ completedCount }}</div>
            <div class="stat-tile__label">جلسات مكتملة</div>
          </div>
        </div>
        <div class="stat-tile" style="--tile-color: #b8860b; --tile-bg: rgba(184,134,11,0.1)">
          <div class="stat-tile__icon">
            <PhUser :size="24" weight="duotone" />
          </div>
          <div class="stat-tile__content">
            <div class="stat-tile__value">{{ individualCount }}</div>
            <div class="stat-tile__label">جلسات فردية</div>
          </div>
        </div>
        <div class="stat-tile" style="--tile-color: #1565c0; --tile-bg: rgba(21,101,192,0.1)">
          <div class="stat-tile__icon">
            <PhUsersThree :size="24" weight="duotone" />
          </div>
          <div class="stat-tile__content">
            <div class="stat-tile__value">{{ groupCount }}</div>
            <div class="stat-tile__label">جلسات جماعية</div>
          </div>
        </div>
      </div>

      <div class="row g-4">
        <!-- Book Session Form -->
        <div class="col-lg-5">
          <div class="dash-card">
            <div class="dash-card__header">
              <h3 class="dash-card__header__title">
                <PhPlusCircle :size="20" />
                حجز جلسة جديدة
              </h3>
            </div>
            <div class="dash-card__body">
              <div v-if="bookMessage" class="dash-alert" :class="bookSuccess ? 'dash-alert--success' : 'dash-alert--danger'">
                <PhCheckCircle :size="20" weight="fill" class="dash-alert__icon" v-if="bookSuccess" />
                <PhWarningCircle :size="20" weight="fill" class="dash-alert__icon" v-else />
                <div class="dash-alert__content">{{ bookMessage }}</div>
              </div>

              <form class="dash-form" @submit.prevent="bookSession">
                <!-- Type Toggle -->
                <div class="dash-form__group">
                  <label class="dash-form__label">
                    <PhListChecks :size="14" />
                    نوع الجلسة
                  </label>
                  <div class="type-toggle">
                    <button
                      type="button"
                      class="type-toggle__btn"
                      :class="{ 'is-active': form.type === 'individual' }"
                      @click="form.type = 'individual'; loadSlots()"
                    >
                      <PhUser :size="18" weight="duotone" />
                      <div>
                        <div class="title">فردية</div>
                        <div class="desc">جلسة خاصة</div>
                      </div>
                    </button>
                    <button
                      type="button"
                      class="type-toggle__btn"
                      :class="{ 'is-active': form.type === 'group' }"
                      @click="form.type = 'group'; loadSlots()"
                    >
                      <PhUsersThree :size="18" weight="duotone" />
                      <div>
                        <div class="title">جماعية</div>
                        <div class="desc">ورشة تفاعلية</div>
                      </div>
                    </button>
                  </div>
                </div>

                <!-- Date -->
                <div class="dash-form__group">
                  <label class="dash-form__label">
                    <PhCalendar :size="14" />
                    اختر التاريخ
                  </label>
                  <input
                    v-model="form.date"
                    type="date"
                    class="dash-form__input"
                    :min="minDate"
                    required
                    @change="loadSlots"
                  >
                </div>

                <!-- Slots -->
                <div v-if="slots.length" class="dash-form__group">
                  <label class="dash-form__label">
                    <PhClock :size="14" />
                    الأوقات المتاحة
                  </label>
                  <div class="slots-grid">
                    <button
                      v-for="slot in slots"
                      :key="slot.time"
                      type="button"
                      class="slot-btn"
                      :class="{
                        'is-selected': selectedSlot === slot.time,
                        'is-disabled': !slot.available,
                      }"
                      :disabled="!slot.available"
                      @click="selectedSlot = slot.time"
                    >
                      {{ slot.time }}
                    </button>
                  </div>
                </div>

                <!-- Notes -->
                <div class="dash-form__group">
                  <label class="dash-form__label">
                    <PhNotePencil :size="14" />
                    ملاحظات (اختياري)
                  </label>
                  <textarea
                    v-model="form.notes"
                    class="dash-form__textarea"
                    placeholder="ما الموضوع الذي تود مناقشته؟"
                  ></textarea>
                </div>

                <button type="submit" class="btn-action btn-action--primary w-100" :disabled="!selectedSlot || booking">
                  <span v-if="booking" class="spinner-border spinner-border-sm"></span>
                  <PhCalendarPlus :size="18" weight="bold" v-else />
                  حجز الجلسة
                </button>
              </form>
            </div>
          </div>
        </div>

        <!-- My Sessions -->
        <div class="col-lg-7">
          <div class="dash-card">
            <div class="dash-card__header">
              <h3 class="dash-card__header__title">
                <PhCalendarCheck :size="20" />
                جلساتي
              </h3>
              <span class="dash-card__header__meta">{{ sessions.length }} جلسة</span>
            </div>
            <div class="dash-card__body--flush">
              <!-- Tabs -->
              <div class="dash-tabs px-3 pt-3">
                <button
                  class="dash-tabs__item"
                  :class="{ 'is-active': activeTab === 'upcoming' }"
                  @click="activeTab = 'upcoming'"
                >
                  القادمة ({{ upcomingSessions.length }})
                </button>
                <button
                  class="dash-tabs__item"
                  :class="{ 'is-active': activeTab === 'completed' }"
                  @click="activeTab = 'completed'"
                >
                  المكتملة ({{ completedSessions.length }})
                </button>
                <button
                  class="dash-tabs__item"
                  :class="{ 'is-active': activeTab === 'cancelled' }"
                  @click="activeTab = 'cancelled'"
                >
                  الملغاة ({{ cancelledSessions.length }})
                </button>
              </div>

              <div v-if="loading" class="loading-state">
                <div class="spinner-border"></div>
                <p class="loading-state__text">جاري تحميل الجلسات...</p>
              </div>

              <div v-else-if="filteredSessions.length" class="sessions-list">
                <div v-for="s in filteredSessions" :key="s.id" class="session-row">
                  <div class="session-row__date">
                    <div class="day">{{ formatDay(s.scheduled_at) }}</div>
                    <div class="month">{{ formatMonth(s.scheduled_at) }}</div>
                  </div>
                  <div class="session-row__content">
                    <div class="session-row__title">
                      <component :is="s.type === 'individual' ? PhUser : PhUsersThree" :size="16" weight="duotone" />
                      جلسة {{ s.type === 'individual' ? 'فردية' : 'جماعية' }}
                    </div>
                    <div class="session-row__meta">
                      <span><PhClock :size="13" /> {{ formatTime(s.scheduled_at) }}</span>
                    </div>
                    <p v-if="s.notes" class="session-row__notes">{{ s.notes }}</p>
                  </div>
                  <div class="session-row__actions">
                    <span class="status-badge" :class="statusBadgeClass(s.status)">
                      {{ statusLabel(s.status) }}
                    </span>
                    <button
                      v-if="s.status === 'scheduled' && canCancel(s)"
                      class="btn-action btn-action--danger btn-action--sm"
                      :disabled="cancelling === s.id"
                      @click="cancelSession(s)"
                    >
                      <PhX :size="14" />
                      إلغاء
                    </button>
                  </div>
                </div>
              </div>

              <div v-else class="empty-state">
                <PhCalendarBlank :size="64" weight="duotone" class="empty-state__icon" />
                <h4 class="empty-state__title">لا توجد جلسات</h4>
                <p class="empty-state__desc">احجز أول جلسة من القائمة على اليمين</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { useApi } from '@/composables/useApi'
import {
  PhChatsCircle, PhCalendarCheck, PhCheckCircle, PhUser, PhUsersThree,
  PhPlusCircle, PhListChecks, PhCalendar, PhClock, PhNotePencil,
  PhCalendarPlus, PhWarningCircle, PhCalendarBlank, PhX,
} from '@phosphor-icons/vue'

const { get, post, put, loading } = useApi()
const sessions = ref([])
const slots = ref([])
const selectedSlot = ref(null)
const booking = ref(false)
const cancelling = ref(null)
const bookMessage = ref('')
const bookSuccess = ref(false)
const activeTab = ref('upcoming')

const form = reactive({ type: 'individual', date: '', notes: '' })

const minDate = computed(() => {
  const d = new Date()
  d.setDate(d.getDate() + 1)
  return d.toISOString().split('T')[0]
})

const upcomingSessions = computed(() => sessions.value.filter(s => s.status === 'scheduled'))
const completedSessions = computed(() => sessions.value.filter(s => s.status === 'completed'))
const cancelledSessions = computed(() => sessions.value.filter(s => s.status === 'cancelled'))

const scheduledCount = computed(() => upcomingSessions.value.length)
const completedCount = computed(() => completedSessions.value.length)
const individualCount = computed(() => sessions.value.filter(s => s.type === 'individual').length)
const groupCount = computed(() => sessions.value.filter(s => s.type === 'group').length)

const filteredSessions = computed(() => {
  if (activeTab.value === 'upcoming') return upcomingSessions.value
  if (activeTab.value === 'completed') return completedSessions.value
  return cancelledSessions.value
})

const statusLabel = (s) => ({ scheduled: 'محجوز', completed: 'مكتمل', cancelled: 'ملغي' })[s] || s
const statusBadgeClass = (s) => ({
  scheduled: 'status-badge--info',
  completed: 'status-badge--success',
  cancelled: 'status-badge--muted',
})[s] || 'status-badge--muted'

function canCancel(session) {
  const scheduledAt = new Date(session.scheduled_at)
  const hoursRemaining = (scheduledAt - new Date()) / (1000 * 60 * 60)
  return hoursRemaining >= 24
}

function formatDay(dt) {
  return new Date(dt).getDate()
}
function formatMonth(dt) {
  return new Date(dt).toLocaleDateString('ar-EG', { month: 'short' })
}
function formatTime(dt) {
  return new Date(dt).toLocaleTimeString('ar-EG', { hour: '2-digit', minute: '2-digit' })
}

async function loadSlots() {
  if (!form.date) return
  selectedSlot.value = null
  try {
    slots.value = await get(`/api/counseling/slots?date=${form.date}&type=${form.type}`)
  } catch { slots.value = [] }
}

async function bookSession() {
  booking.value = true
  bookMessage.value = ''
  try {
    const scheduledAt = `${form.date} ${selectedSlot.value}:00`
    const result = await post('/api/counseling', {
      type: form.type,
      scheduled_at: scheduledAt,
      notes: form.notes,
    })
    bookMessage.value = result.message
    bookSuccess.value = true
    selectedSlot.value = null
    form.notes = ''
    slots.value = []
    const data = await get('/api/counseling')
    sessions.value = data.data || data
  } catch (err) {
    bookMessage.value = err.response?.data?.message || 'حدث خطأ'
    bookSuccess.value = false
  }
  booking.value = false
}

async function cancelSession(s) {
  cancelling.value = s.id
  try {
    await put(`/api/counseling/${s.id}/cancel`)
    s.status = 'cancelled'
  } catch { /* handled */ }
  cancelling.value = null
}

onMounted(async () => {
  try {
    const data = await get('/api/counseling')
    sessions.value = data.data || data
  } catch { /* handled */ }
})
</script>

<style lang="scss" scoped>
.type-toggle {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 0.5rem;

  &__btn {
    display: flex;
    align-items: center;
    gap: 0.65rem;
    padding: 0.85rem 1rem;
    background: #faf9f6;
    border: 1.5px solid #e0d8cc;
    border-radius: 11px;
    cursor: pointer;
    transition: all 0.2s;
    text-align: right;
    font-family: inherit;

    .title { font-size: 0.9rem; font-weight: 800; color: #1a1a2a; }
    .desc { font-size: 0.7rem; color: #8888a0; }

    &:hover { border-color: #0d7377; }

    &.is-active {
      background: #e8f5f5;
      border-color: #0d7377;
      color: #0d7377;

      .title { color: #0d7377; }
    }
  }
}

.slots-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(72px, 1fr));
  gap: 0.5rem;
}

.slot-btn {
  padding: 0.65rem 0.5rem;
  background: #faf9f6;
  border: 1.5px solid #e0d8cc;
  border-radius: 10px;
  font-weight: 700;
  font-size: 0.85rem;
  color: #1a1a2a;
  cursor: pointer;
  transition: all 0.2s;
  font-family: inherit;

  &:hover:not(.is-disabled) { border-color: #0d7377; color: #0d7377; }
  &.is-selected {
    background: linear-gradient(135deg, #0d7377 0%, #095456 100%);
    color: #fff;
    border-color: #095456;
  }
  &.is-disabled {
    opacity: 0.4;
    cursor: not-allowed;
    text-decoration: line-through;
  }
}

.sessions-list {
  display: flex;
  flex-direction: column;
}

.session-row {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1.25rem 1.5rem;
  border-bottom: 1px solid #f0ece4;
  transition: background 0.15s;

  &:hover { background: #faf9f6; }
  &:last-child { border-bottom: none; }

  &__date {
    flex-shrink: 0;
    width: 56px;
    text-align: center;
    background: #faf9f6;
    border: 1px solid #e0d8cc;
    border-radius: 12px;
    padding: 0.5rem;

    .day {
      font-size: 1.4rem;
      font-weight: 900;
      color: #0d7377;
      line-height: 1;
    }
    .month {
      font-size: 0.7rem;
      color: #8888a0;
      font-weight: 700;
      margin-top: 0.15rem;
    }
  }

  &__content { flex: 1; min-width: 0; }
  &__title {
    font-weight: 800;
    color: #1a1a2a;
    margin-bottom: 0.3rem;
    display: flex;
    align-items: center;
    gap: 0.4rem;
  }
  &__meta {
    font-size: 0.8rem;
    color: #8888a0;
    display: flex;
    gap: 0.75rem;
  }
  &__notes {
    font-size: 0.8rem;
    color: #4a4a5e;
    margin: 0.4rem 0 0;
    line-height: 1.6;
  }
  &__actions {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    flex-shrink: 0;
  }

  @media (max-width: 576px) {
    flex-direction: column;
    align-items: flex-start;

    &__actions { width: 100%; justify-content: flex-start; }
  }
}
</style>
