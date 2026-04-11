<template>
  <div class="about-page">
    <!-- Hero -->
    <section class="about-hero">
      <div class="container about-hero__content">
        <div class="about-hero__eyebrow">
          <PhInfo :size="16" weight="fill" />
          عن منصة يسّرو
        </div>
        <h1 class="about-hero__title">
          نُيسّر الزواج<br>
          لا نبيع <span class="accent">الأحلام</span>
        </h1>
        <p class="about-hero__desc">
          يسّرو ليست تطبيق زواج تقليدي. نحن منظومة متكاملة تحل كل عوائق الزواج —
          من التأهيل إلى التمويل إلى الرعاية.
        </p>
      </div>
    </section>

    <!-- Problem Section -->
    <section class="about-section">
      <div class="container">
        <h2 class="about-section__title">المشكلة التي نحلّها</h2>
        <div class="about-grid">
          <div class="about-card about-card--problem">
            <div class="about-card__icon"><PhWarningCircle :size="32" weight="duotone" /></div>
            <h3>40% لا يستطيعون الزواج</h3>
            <p>تكاليف الزواج في العالم العربي تصل إلى 10 أضعاف الدخل السنوي.</p>
          </div>
          <div class="about-card about-card--problem">
            <div class="about-card__icon"><PhEyeSlash :size="32" weight="duotone" /></div>
            <h3>مواقع الزواج لا تكفي</h3>
            <p>المنصات الحالية تحل 15% فقط من المشكلة — إيجاد شريك. ماذا عن الباقي؟</p>
          </div>
          <div class="about-card about-card--problem">
            <div class="about-card__icon"><PhShieldCheck :size="32" weight="duotone" /></div>
            <h3>غياب الخصوصية</h3>
            <p>التواصل المباشر بين الجنسين يخالف الشريعة ويفقد الثقة.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Solution: 5 Pillars -->
    <section class="about-section about-section--alt">
      <div class="container">
        <h2 class="about-section__title">الأركان الخمسة</h2>
        <p class="about-section__subtitle">كل ركيزة تعتمد على الأخرى — منظومة متكاملة</p>

        <div class="pillars-list">
          <div v-for="(pillar, i) in pillars" :key="i" class="pillar-item">
            <div class="pillar-item__number">{{ i + 1 }}</div>
            <div class="pillar-item__content">
              <h3>{{ pillar.title }}</h3>
              <p>{{ pillar.desc }}</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Values -->
    <section class="about-section">
      <div class="container">
        <h2 class="about-section__title">قيمنا</h2>
        <div class="about-grid">
          <div class="about-card">
            <div class="about-card__icon" style="color:#0d7377"><PhMosque :size="32" weight="duotone" /></div>
            <h3>الالتزام الشرعي</h3>
            <p>كل خدمة معتمدة شرعياً. صفر تواصل مباشر بين الجنسين. الصندوق بدون فوائد.</p>
          </div>
          <div class="about-card">
            <div class="about-card__icon" style="color:#b8860b"><PhLockKey :size="32" weight="duotone" /></div>
            <h3>الخصوصية أولاً</h3>
            <p>لا صور، لا بروفايلات للتصفح. كل شيء يمر عبر معرّف موثوق.</p>
          </div>
          <div class="about-card">
            <div class="about-card__icon" style="color:#1b7a4a"><PhHandCoins :size="32" weight="duotone" /></div>
            <h3>التيسير لا الربح</h3>
            <p>رسالتنا تيسير الزواج. نموذج الإيرادات يخدم الاستدامة وليس الربح.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Verse -->
    <section class="about-verse">
      <div class="container">
        <blockquote class="verse-block">
          <p class="verse-block__text">
            ﴿ وَأَنكِحُوا الْأَيَامَىٰ مِنكُمْ وَالصَّالِحِينَ مِنْ عِبَادِكُمْ وَإِمَائِكُمْ ۚ
            إِن يَكُونُوا فُقَرَاءَ يُغْنِهِمُ اللَّهُ مِن فَضْلِهِ ﴾
          </p>
          <cite class="verse-block__cite">سورة النور — الآية 32</cite>
        </blockquote>
      </div>
    </section>

    <!-- CTA -->
    <section class="about-section about-section--cta">
      <div class="container" style="text-align: center">
        <h2 class="about-section__title">ابدأ رحلتك الآن</h2>
        <p style="color: #8888a0; margin-bottom: 2rem">سجّل مجاناً وأكمل الدورة التأهيلية</p>
        <router-link to="/register" class="btn-action btn-action--primary btn-action--lg">
          <PhRocketLaunch :size="18" weight="bold" />
          إنشاء حساب مجاني
        </router-link>
      </div>
    </section>
  </div>
</template>

<script setup>
import { useSeo } from '@/composables/useSeo'
import {
  PhInfo, PhWarningCircle, PhEyeSlash, PhShieldCheck,
  PhMosque, PhLockKey, PhHandCoins, PhRocketLaunch,
} from '@phosphor-icons/vue'

useSeo({
  title: 'عن المنصة',
  description: 'يسّرو منظومة متكاملة لتيسير الزواج في العالم الإسلامي: تأهيل، توفيق شرعي، صندوق تعاوني، أعراس جماعية، ورعاية ما بعد الزواج.',
  path: '/about',
})

const pillars = [
  { title: 'التأهيل', desc: 'دورة شرعية ونفسية ومالية وعملية إلزامية (10-15 ساعة). شهادة جاهزية تفتح باقي الخدمات.' },
  { title: 'التوفيق عبر المعرّفين', desc: 'أئمة ومعلمون موثوقون يديرون عملية التوفيق. صفر تواصل مباشر بين الجنسين.' },
  { title: 'الصندوق التعاوني', desc: 'جمعية رقمية بدون فوائد. كل عضو يساهم شهرياً ويحصل على المبلغ كاملاً عند دوره.' },
  { title: 'الأعراس الجماعية', desc: 'أعراس منظمة لـ 10-30 عريس بتوفير 60-70% من التكاليف مع موردين معتمدين.' },
  { title: 'رعاية ما بعد الزواج', desc: 'جلسات استشارية، مجتمع أزواج للإرشاد، ودعم نفسي وشرعي مستمر.' },
]
</script>

<style lang="scss" scoped>
.about-hero {
  padding: 5rem 0 3rem;
  background: linear-gradient(180deg, rgba(13,115,119,0.04) 0%, #faf9f6 100%);
  text-align: center;

  &__eyebrow {
    display: inline-flex; align-items: center; gap: 0.4rem;
    font-size: 0.8rem; font-weight: 700; color: #0d7377;
    background: rgba(13,115,119,0.08); padding: 0.4rem 1rem; border-radius: 100px;
    margin-bottom: 1.5rem;
  }

  &__title {
    font-size: 2.5rem; font-weight: 900; color: #1a1a2a; line-height: 1.3;
    .accent { color: #0d7377; }
    @media (max-width: 768px) { font-size: 1.75rem; }
  }

  &__desc {
    font-size: 1.1rem; color: #4a4a5e; max-width: 640px; margin: 1.5rem auto 0; line-height: 1.8;
  }
}

.about-section {
  padding: 4rem 0;

  &--alt { background: rgba(13,115,119,0.02); }
  &--cta { padding: 5rem 0; }

  &__title {
    font-size: 1.75rem; font-weight: 900; color: #1a1a2a; text-align: center; margin-bottom: 0.75rem;
  }

  &__subtitle {
    text-align: center; color: #8888a0; margin-bottom: 2.5rem; font-size: 0.95rem;
  }
}

.about-grid {
  display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem;
}

.about-card {
  background: #fff; border-radius: 16px; padding: 2rem; border: 1px solid #e0d8cc;
  transition: transform 0.2s, box-shadow 0.2s;

  &:hover { transform: translateY(-4px); box-shadow: 0 8px 24px rgba(0,0,0,0.08); }

  &--problem { border-color: rgba(192,57,43,0.15); }
  &--problem &__icon { color: #c0392b; }

  &__icon { margin-bottom: 1rem; }
  h3 { font-size: 1.1rem; font-weight: 800; color: #1a1a2a; margin-bottom: 0.5rem; }
  p { font-size: 0.9rem; color: #4a4a5e; line-height: 1.7; margin: 0; }
}

.pillars-list {
  max-width: 700px; margin: 0 auto; display: flex; flex-direction: column; gap: 1.25rem;
}

.pillar-item {
  display: flex; align-items: flex-start; gap: 1.25rem;
  background: #fff; padding: 1.5rem; border-radius: 16px; border: 1px solid #e0d8cc;

  &__number {
    width: 48px; height: 48px; border-radius: 14px; flex-shrink: 0;
    background: rgba(13,115,119,0.1); color: #0d7377;
    font-size: 1.25rem; font-weight: 900;
    display: flex; align-items: center; justify-content: center;
  }

  &__content {
    h3 { font-size: 1.05rem; font-weight: 800; color: #1a1a2a; margin: 0 0 0.35rem; }
    p { font-size: 0.88rem; color: #4a4a5e; line-height: 1.7; margin: 0; }
  }
}

.about-verse {
  padding: 4rem 0; background: #1a1a2a;
}

.verse-block {
  text-align: center; max-width: 700px; margin: 0 auto;

  &__text {
    font-family: 'Amiri', serif; font-size: 1.5rem; color: #b8860b;
    line-height: 2.2; margin: 0 0 1rem;
    @media (max-width: 768px) { font-size: 1.15rem; }
  }

  &__cite {
    font-style: normal; font-size: 0.85rem; color: #8888a0;
  }
}
</style>
