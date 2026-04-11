<template>
  <section class="section-padding text-center d-flex align-items-center" style="min-height: 80vh">
    <div class="container">
      <PhMagnifyingGlass :size="72" weight="duotone" class="text-muted mb-3" style="opacity: 0.3" />
      <h1 class="fw-bold mb-2" style="font-size: 4rem; color: var(--bs-primary)">404</h1>
      <h3 class="fw-bold mb-3">الصفحة غير موجودة</h3>
      <p class="text-muted mb-4 mx-auto" style="max-width: 400px">
        عذراً، الصفحة التي تبحث عنها غير متوفرة أو تم نقلها.
      </p>
      <div class="d-flex justify-content-center gap-3">
        <router-link to="/" class="btn btn-y px-4">
          <PhHouse :size="18" />
          العودة للرئيسية
        </router-link>
        <button class="btn btn-outline-primary px-4" @click="$router.back()">
          <PhArrowRight :size="18" />
          الصفحة السابقة
        </button>
      </div>
    </div>
  </section>
</template>

<script setup>
import { PhMagnifyingGlass, PhHouse, PhArrowRight } from '@phosphor-icons/vue'
</script>
