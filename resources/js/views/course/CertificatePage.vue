<template>
  <div class="dashboard-page">
    <div class="container" style="max-width: 760px">
      <div v-if="loading" class="loading-state">
        <div class="spinner-border"></div>
        <p class="loading-state__text">جاري تحميل الشهادة...</p>
      </div>

      <div v-else-if="certificate">
        <router-link to="/courses" class="page-header__back">
          <PhArrowRight :size="14" weight="bold" />
          العودة للدورة
        </router-link>

        <div class="page-header">
          <div>
            <h1 class="page-header__title">
              <span class="page-header__title__icon" style="--header-bg: rgba(184,134,11,0.1); --header-color: #b8860b">
                <PhCertificate :size="24" weight="duotone" />
              </span>
              شهادتي
            </h1>
            <p class="page-header__subtitle">شهادة الجاهزية الزواجية المعتمدة</p>
          </div>
          <div class="page-header__actions">
            <button class="btn-action btn-action--primary" :disabled="downloading" @click="downloadPdf">
              <span v-if="downloading" class="spinner-border spinner-border-sm"></span>
              <PhDownloadSimple :size="16" weight="bold" v-else />
              تحميل PDF
            </button>
            <button class="btn-action btn-action--gold" @click="printCert">
              <PhPrinter :size="16" weight="bold" />
              طباعة
            </button>
          </div>
        </div>

        <!-- Certificate Display -->
        <div class="certificate-display" id="certificate-card">
          <div class="certificate-display__top-bar"></div>

          <div class="certificate-display__corner top-right"></div>
          <div class="certificate-display__corner top-left"></div>
          <div class="certificate-display__corner bottom-right"></div>
          <div class="certificate-display__corner bottom-left"></div>

          <div class="certificate-display__content">
            <img src="/logo.svg" alt="يسّرو" class="certificate-display__logo">

            <div class="certificate-display__eyebrow">YASSIRU CERTIFICATE</div>
            <h2 class="certificate-display__title">شهادة الجاهزية الزواجية</h2>
            <p class="certificate-display__org">منصة يسّرو لتيسير الزواج</p>

            <div class="certificate-display__medal">
              <PhMedal :size="64" weight="duotone" />
            </div>

            <p class="certificate-display__pre">تشهد منصة يسّرو بأن</p>
            <h1 class="certificate-display__name">{{ certificate.user_name }}</h1>
            <p class="certificate-display__post">قد أتمّ بنجاح الدورة التأهيلية الزواجية بمساراتها الأربعة بتقدير ممتاز</p>

            <!-- Track Scores -->
            <div class="certificate-display__scores">
              <div v-for="track in tracks" :key="track.key" class="track-score" :style="{ '--tr-color': track.color, '--tr-bg': track.bg }">
                <div class="track-score__icon">
                  <component :is="track.icon" :size="22" weight="duotone" />
                </div>
                <div class="track-score__label">{{ track.label }}</div>
                <div class="track-score__value">{{ certificate[`${track.key}_score`] }}</div>
              </div>
            </div>

            <!-- Cert Details -->
            <div class="certificate-display__details">
              <div class="cert-detail">
                <div class="cert-detail__label">
                  <PhHashStraight :size="13" />
                  رقم الشهادة
                </div>
                <code class="cert-detail__value">{{ certificate.certificate_number }}</code>
              </div>
              <div class="cert-detail">
                <div class="cert-detail__label">
                  <PhCalendar :size="13" />
                  تاريخ الإصدار
                </div>
                <strong class="cert-detail__value">{{ formatDate(certificate.issued_at) }}</strong>
              </div>
            </div>

            <p class="certificate-display__verify">
              <PhShieldCheck :size="14" weight="fill" />
              يمكن التحقق من هذه الشهادة عبر إدخال رقمها في المنصة
            </p>
          </div>
        </div>
      </div>

      <!-- No Certificate -->
      <div v-else>
        <div class="page-header">
          <div>
            <h1 class="page-header__title">
              <span class="page-header__title__icon" style="--header-bg: rgba(184,134,11,0.1); --header-color: #b8860b">
                <PhCertificate :size="24" weight="duotone" />
              </span>
              الشهادة
            </h1>
          </div>
        </div>

        <div class="empty-state">
          <PhCertificate :size="80" weight="duotone" class="empty-state__icon" />
          <h4 class="empty-state__title">لم تحصل على الشهادة بعد</h4>
          <p class="empty-state__desc">
            أكمل الدورة التأهيلية بمساراتها الأربعة واجتز الاختبارات للحصول على شهادتك المعتمدة
          </p>
          <router-link to="/courses" class="btn-action btn-action--primary">
            <PhBookOpen :size="18" weight="bold" />
            ابدأ الدورة التأهيلية
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useApi } from '@/composables/useApi'
import {
  PhArrowRight, PhMedal, PhShieldCheck, PhPrinter, PhCertificate, PhBookOpen,
  PhHashStraight, PhCalendar, PhDownloadSimple,
  PhBookBookmark, PhBrain, PhCurrencyCircleDollar, PhWrench,
} from '@phosphor-icons/vue'
import axios from 'axios'

const { get, loading } = useApi()
const certificate = ref(null)
const downloading = ref(false)

const tracks = [
  { key: 'shariah', label: 'شرعي', icon: PhBookBookmark, bg: 'rgba(27,122,74,0.1)', color: '#1b7a4a' },
  { key: 'psychology', label: 'نفسي', icon: PhBrain, bg: 'rgba(21,101,192,0.1)', color: '#1565c0' },
  { key: 'financial', label: 'مالي', icon: PhCurrencyCircleDollar, bg: 'rgba(184,134,11,0.1)', color: '#b8860b' },
  { key: 'practical', label: 'عملي', icon: PhWrench, bg: 'rgba(107,114,128,0.1)', color: '#6b7280' },
]

function formatDate(d) {
  if (!d) return ''
  return new Date(d).toLocaleDateString('ar-EG', { year: 'numeric', month: 'long', day: 'numeric' })
}

async function downloadPdf() {
  downloading.value = true
  try {
    const response = await axios.get('/api/certificate/pdf', {
      responseType: 'blob',
      headers: { Authorization: `Bearer ${localStorage.getItem('token')}` },
    })
    const url = window.URL.createObjectURL(new Blob([response.data], { type: 'application/pdf' }))
    const link = document.createElement('a')
    link.href = url
    link.download = 'yassiru-certificate.pdf'
    link.click()
    window.URL.revokeObjectURL(url)
  } catch { /* handled */ }
  downloading.value = false
}

function printCert() {
  window.print()
}

onMounted(async () => {
  try {
    certificate.value = await get('/api/certificate')
  } catch { /* no certificate */ }
})
</script>

<style lang="scss" scoped>
.certificate-display {
  background: linear-gradient(135deg, #fff 0%, #faf9f6 100%);
  border: 4px double #b8860b;
  border-radius: 20px;
  padding: 3.5rem 2rem;
  text-align: center;
  position: relative;
  overflow: hidden;
  box-shadow: 0 30px 80px rgba(13, 26, 42, 0.1);

  @media (max-width: 576px) { padding: 2rem 1.25rem; }

  &__top-bar {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 8px;
    background: linear-gradient(90deg, #0d7377 0%, #b8860b 100%);
  }

  &__corner {
    position: absolute;
    width: 50px;
    height: 50px;
    border: 3px solid #b8860b;

    &.top-right { top: 24px; right: 24px; border-left: none; border-bottom: none; }
    &.top-left { top: 24px; left: 24px; border-right: none; border-bottom: none; }
    &.bottom-right { bottom: 24px; right: 24px; border-left: none; border-top: none; }
    &.bottom-left { bottom: 24px; left: 24px; border-right: none; border-top: none; }
  }

  &__content {
    position: relative;
    z-index: 1;
  }

  &__logo {
    height: 56px;
    margin-bottom: 1rem;
  }

  &__eyebrow {
    font-size: 0.75rem;
    font-weight: 800;
    color: #b8860b;
    letter-spacing: 3px;
    margin-bottom: 0.5rem;
  }

  &__title {
    font-size: 1.6rem;
    font-weight: 900;
    color: #0d7377;
    margin: 0 0 0.5rem;
    letter-spacing: -0.01em;
  }

  &__org {
    font-size: 0.85rem;
    color: #8888a0;
    margin-bottom: 1.5rem;
  }

  &__medal {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    background: linear-gradient(135deg, rgba(184,134,11,0.1) 0%, rgba(13,115,119,0.05) 100%);
    border: 2px solid rgba(184,134,11,0.3);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1.5rem;
    color: #b8860b;
  }

  &__pre {
    font-size: 0.9rem;
    color: #8888a0;
    margin-bottom: 0.5rem;
  }

  &__name {
    font-family: 'Amiri', serif;
    font-size: 2.25rem;
    font-weight: 900;
    color: #0d7377;
    margin: 0 0 0.75rem;
    letter-spacing: -0.01em;

    @media (max-width: 576px) { font-size: 1.65rem; }
  }

  &__post {
    color: #4a4a5e;
    line-height: 1.75;
    margin-bottom: 2rem;
    font-size: 0.95rem;
  }

  &__scores {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 0.75rem;
    margin-bottom: 1.5rem;

    @media (max-width: 480px) { grid-template-columns: repeat(2, 1fr); }
  }

  &__details {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.75rem;
    margin-bottom: 1.25rem;

    @media (max-width: 480px) { grid-template-columns: 1fr; }
  }

  &__verify {
    font-size: 0.78rem;
    color: #8888a0;
    margin: 0;
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;

    svg { color: #1b7a4a; }
  }
}

.track-score {
  background: var(--tr-bg);
  border-radius: 12px;
  padding: 0.85rem 0.5rem;
  text-align: center;

  &__icon {
    color: var(--tr-color);
    margin-bottom: 0.3rem;
  }

  &__label {
    font-size: 0.7rem;
    font-weight: 700;
    color: var(--tr-color);
    margin-bottom: 0.25rem;
  }

  &__value {
    font-size: 1.05rem;
    font-weight: 900;
    color: #1a1a2a;
  }
}

.cert-detail {
  background: #faf9f6;
  border: 1px solid #f0ece4;
  border-radius: 10px;
  padding: 0.85rem 1rem;
  text-align: right;

  &__label {
    display: flex;
    align-items: center;
    gap: 0.3rem;
    font-size: 0.7rem;
    color: #8888a0;
    font-weight: 700;
    margin-bottom: 0.3rem;
  }

  &__value {
    color: #0d7377;
    font-weight: 800;
    font-size: 0.95rem;
    display: block;
  }
}

@media print {
  .dashboard-page { background: #fff; padding: 0; }
  .page-header,
  .page-header__back { display: none !important; }
  .certificate-display { box-shadow: none; }
}
</style>
