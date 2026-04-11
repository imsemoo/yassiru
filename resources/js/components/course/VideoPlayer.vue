<template>
  <div class="video-player" :class="{ 'video-player--ready': isReady }">
    <!-- YouTube / Vimeo -->
    <div
      v-if="provider !== 'html5'"
      ref="playerRef"
      :data-plyr-provider="provider"
      :data-plyr-embed-id="embedId"
    ></div>

    <!-- Direct video URL -->
    <video v-else ref="playerRef" playsinline>
      <source :src="url" />
    </video>
  </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount, computed } from 'vue'
import Plyr from 'plyr'
import 'plyr/dist/plyr.css'

const props = defineProps({
  url: { type: String, required: true },
})

const emit = defineEmits(['progress', 'ended'])

const playerRef = ref(null)
const isReady = ref(false)
let player = null
let lastReportedPercent = -1

// Detect provider from URL
const provider = computed(() => {
  if (/youtube\.com|youtu\.be/.test(props.url)) return 'youtube'
  if (/vimeo\.com/.test(props.url)) return 'vimeo'
  return 'html5'
})

// Extract embed ID for YouTube/Vimeo
const embedId = computed(() => {
  if (provider.value === 'youtube') {
    const match = props.url.match(/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([a-zA-Z0-9_-]+)/)
    return match ? match[1] : props.url
  }
  if (provider.value === 'vimeo') {
    const match = props.url.match(/vimeo\.com\/(?:video\/)?(\d+)/)
    return match ? match[1] : props.url
  }
  return ''
})

onMounted(() => {
  if (!playerRef.value) return

  player = new Plyr(playerRef.value, {
    controls: [
      'play-large', 'play', 'progress', 'current-time',
      'duration', 'mute', 'volume', 'settings', 'fullscreen',
    ],
    settings: ['quality', 'speed'],
    speed: { selected: 1, options: [0.5, 0.75, 1, 1.25, 1.5, 2] },
    i18n: {
      restart: 'إعادة',
      play: 'تشغيل',
      pause: 'إيقاف',
      fastForward: 'تقديم {seektime} ثانية',
      rewind: 'ترجيع {seektime} ثانية',
      seek: 'التنقل',
      played: 'تم تشغيله',
      buffered: 'تم تحميله',
      currentTime: 'الوقت الحالي',
      duration: 'المدة',
      volume: 'الصوت',
      mute: 'كتم',
      unmute: 'إلغاء الكتم',
      enableCaptions: 'تفعيل الترجمة',
      disableCaptions: 'إيقاف الترجمة',
      enterFullscreen: 'ملء الشاشة',
      exitFullscreen: 'إنهاء ملء الشاشة',
      frameTitle: 'مشغل {title}',
      captions: 'ترجمة',
      settings: 'إعدادات',
      speed: 'السرعة',
      normal: 'عادي',
      quality: 'الجودة',
      loop: 'تكرار',
    },
  })

  player.on('ready', () => {
    isReady.value = true
  })

  player.on('timeupdate', () => {
    if (!player.duration) return
    const percent = Math.floor((player.currentTime / player.duration) * 100)
    const bracket = Math.floor(percent / 10) * 10
    if (bracket > lastReportedPercent && bracket > 0) {
      lastReportedPercent = bracket
      emit('progress', bracket)
    }
  })

  player.on('ended', () => {
    emit('ended')
  })
})

onBeforeUnmount(() => {
  if (player) {
    player.destroy()
    player = null
  }
})
</script>

<style scoped>
.video-player {
  width: 100%;
  border-radius: 16px;
  overflow: hidden;
  background: #000;
}

.video-player :deep(.plyr) {
  direction: ltr;
  border-radius: 16px;
}

.video-player :deep(.plyr--video) {
  border-radius: 16px;
}

.video-player :deep(.plyr__control--overlaid) {
  background: rgba(13, 115, 119, 0.85);
}

.video-player :deep(.plyr--full-ui input[type=range]) {
  color: #0d7377;
}

.video-player :deep(.plyr__menu__container [role=menu]) {
  direction: rtl;
  text-align: right;
}
</style>
