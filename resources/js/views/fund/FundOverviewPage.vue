<template>
  <!-- ============================================ -->
  <!-- 1. HERO — Promise + Visual Cycle            -->
  <!-- ============================================ -->
  <section class="fund-hero">
    <div class="container fund-hero__content">
      <div class="row align-items-center g-5">
        <!-- Left: Headline + CTA -->
        <div class="col-lg-7">
          <div class="fund-hero__eyebrow anim">
            <PhScales :size="16" weight="fill" />
            صندوق تعاوني — معتمد شرعياً
          </div>

          <h1 class="fund-hero__title anim anim-delay-1">
            احصل على
            <span class="amount">15,000 ج.م</span><br>
            لتجهيز زواجك بدون فوائد
          </h1>

          <p class="fund-hero__subtitle anim anim-delay-2">
            صندوق تعاوني رقمي يجمع 15 شخصاً، كل واحد يدفع ألف جنيه شهرياً،
            والكل يستلم مبلغ تجهيز زواجه كاملاً عند دوره — بدون ربا، بدون ضمانات بنكية، بدون تعقيد.
          </p>

          <div class="fund-hero__cta-group anim anim-delay-3">
            <a href="#calculator" class="fund-hero__cta-primary" @click.prevent="scrollTo('calculator')">
              <PhCalculator :size="20" weight="bold" />
              صمّم حلقتك الآن
            </a>
            <router-link to="/circles" class="fund-hero__cta-secondary">
              <PhListMagnifyingGlass :size="20" weight="bold" />
              تصفّح الحلقات المتاحة
            </router-link>
          </div>

          <div class="fund-hero__trust anim anim-delay-4">
            <div class="fund-hero__trust__item">
              <PhScales :size="18" weight="fill" class="text-success" />
              قرض حسن
            </div>
            <div class="fund-hero__trust__item">
              <PhShieldCheck :size="18" weight="fill" class="text-primary" />
              5 طبقات حماية
            </div>
            <div class="fund-hero__trust__item">
              <PhEye :size="18" weight="fill" style="color: #b8860b" />
              شفافية كاملة
            </div>
          </div>
        </div>

        <!-- Right: Cycle Visual -->
        <div class="col-lg-5">
          <div class="cycle-visual anim anim-delay-3">
            <div class="cycle-circle"></div>

            <div class="cycle-center">
              <div class="cycle-center__amount">15,000</div>
              <div class="cycle-center__label">جنيه يستلمها العضو</div>
            </div>

            <div
              v-for="(_, i) in 12"
              :key="i"
              class="cycle-member"
              :class="{ active: i === activeMember, received: i < activeMember }"
              :style="getMemberPos(i, 12)"
            >
              {{ i + 1 }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ============================================ -->
  <!-- 2. COMPARISON — Yassiru vs Alternatives     -->
  <!-- ============================================ -->
  <section class="compare-section">
    <div class="container">
      <div class="text-center mb-3">
        <span class="section-eyebrow">المقارنة الصريحة</span>
      </div>

      <h2 class="section-title-lg text-center">
        ليه يسّرو أحسن من<br>
        البنك والقرض والجمعية التقليدية؟
      </h2>
      <p class="section-sub-lg text-center">
        قارن بنفسك وشوف الفرق الحقيقي
      </p>

      <div class="compare-table">
        <table>
          <thead>
            <tr>
              <th></th>
              <th>قرض البنك</th>
              <th>قرض شخصي</th>
              <th>جمعية تقليدية</th>
              <th class="col-yassiru">يسّرو</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="row in compareRows" :key="row.label">
              <td>{{ row.label }}</td>
              <td><component :is="iconFor(row.bank)" :size="20" :class="classFor(row.bank)" /></td>
              <td><component :is="iconFor(row.loan)" :size="20" :class="classFor(row.loan)" /></td>
              <td><component :is="iconFor(row.traditional)" :size="20" :class="classFor(row.traditional)" /></td>
              <td class="col-yassiru"><component :is="iconFor(row.yassiru)" :size="20" :class="classFor(row.yassiru)" /></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </section>

  <!-- ============================================ -->
  <!-- 3. HOW IT WORKS — 4 Steps + Real Example   -->
  <!-- ============================================ -->
  <section class="fund-how-section">
    <div class="container">
      <div class="text-center mb-3">
        <span class="section-eyebrow">كيف يعمل؟</span>
      </div>

      <h2 class="section-title-lg text-center">
        4 خطوات بسيطة من الانضمام للقبض
      </h2>

      <div class="fund-steps">
        <div
          v-for="(step, i) in steps"
          :key="step.title"
          class="fund-step"


        >
          <div class="fund-step__number">{{ i + 1 }}</div>
          <h4 class="fund-step__title">{{ step.title }}</h4>
          <p class="fund-step__desc">{{ step.desc }}</p>
          <div class="fund-step__example">{{ step.example }}</div>
        </div>
      </div>

      <div class="fund-example-banner">
        <div class="fund-example-banner__title">مثال حقيقي</div>
        <p class="fund-example-banner__text">
          15 عضو، كل واحد يدفع <strong>1,000 ج.م</strong> شهرياً لمدة 15 شهراً.<br>
          عند دورك تستلم <strong>15,000 ج.م</strong> دفعة واحدة لتجهيز زواجك.
        </p>
      </div>
    </div>
  </section>

  <!-- ============================================ -->
  <!-- 4. INTERACTIVE CALCULATOR                   -->
  <!-- ============================================ -->
  <section class="fund-calc-section" id="calculator">
    <div class="container fund-calc__inner">
      <div class="fund-calc__header">
        <h2>صمّم حلقتك بنفسك</h2>
        <p>اختر عدد الأعضاء والمساهمة الشهرية — وشوف كم ستحصل عليه عند دورك</p>
      </div>

      <div class="fund-calc__tool">
        <div class="fund-slider">
          <div class="fund-slider__label">
            <span class="label-text">
              <PhUsers :size="18" weight="bold" />
              عدد الأعضاء
            </span>
            <span class="label-value">{{ members }}</span>
          </div>
          <input type="range" v-model.number="members" min="5" max="30" class="fund-slider__input">
          <div class="fund-slider__range">
            <span>5 أعضاء</span>
            <span>30 عضو</span>
          </div>
        </div>

        <div class="fund-slider">
          <div class="fund-slider__label">
            <span class="label-text">
              <PhCurrencyCircleDollar :size="18" weight="bold" />
              المساهمة الشهرية
            </span>
            <span class="label-value">{{ formatNumber(amount) }}</span>
          </div>
          <input type="range" v-model.number="amount" min="200" max="5000" step="100" class="fund-slider__input">
          <div class="fund-slider__range">
            <span>200 ج.م</span>
            <span>5,000 ج.م</span>
          </div>
        </div>

        <div class="fund-result">
          <div class="fund-result__label">ستحصل على</div>
          <div class="fund-result__amount">{{ formatNumber(totalPayout) }}</div>
          <div class="fund-result__currency">جنيه مصري — دفعة واحدة</div>

          <div class="fund-result__details">
            <div class="fund-result__detail">
              <PhClock :size="18" weight="bold" class="icon" />
              <div class="value">{{ members }} شهر</div>
              <div class="label">مدة الدورة</div>
            </div>
            <div class="fund-result__detail">
              <PhUsers :size="18" weight="bold" class="icon" />
              <div class="value">{{ members }} عضو</div>
              <div class="label">عدد المشاركين</div>
            </div>
            <div class="fund-result__detail">
              <PhCalculator :size="18" weight="bold" class="icon" />
              <div class="value">{{ formatNumber(amount) }}</div>
              <div class="label">شهرياً</div>
            </div>
          </div>
        </div>

        <div class="fund-calc__cta">
          <router-link to="/register" class="fund-hero__cta-primary">
            <PhRocketLaunch :size="20" weight="bold" />
            ابدأ رحلتك مجاناً
          </router-link>
          <router-link to="/circles" class="fund-hero__cta-secondary">
            <PhListMagnifyingGlass :size="20" weight="bold" />
            تصفّح الحلقات
          </router-link>
        </div>
      </div>
    </div>
  </section>

  <!-- ============================================ -->
  <!-- 5. SECURITY LAYERS — How Your Money Is Safe -->
  <!-- ============================================ -->
  <section class="security-section">
    <div class="container">
      <div class="text-center mb-3">
        <span class="section-eyebrow">حماية فلوسك</span>
      </div>

      <h2 class="section-title-lg text-center">
        5 طبقات حماية حقيقية —<br>
        مش مجرد كلام
      </h2>
      <p class="section-sub-lg text-center">
        كل طبقة مصمّمة لتحمي أموالك وتضمن استمرار الحلقة حتى آخر عضو
      </p>

      <div class="security-layers">
        <div
          v-for="(layer, i) in securityLayers"
          :key="layer.title"
          class="security-layer"
          :style="{
            '--layer-color': layer.color,
            '--layer-dark': layer.dark,
            '--layer-bg': layer.bg,
          }"


        >
          <div class="security-layer__number">{{ i + 1 }}</div>
          <div class="security-layer__content">
            <h4 class="security-layer__title">{{ layer.title }}</h4>
            <p class="security-layer__desc">{{ layer.desc }}</p>
          </div>
          <div class="security-layer__icon">
            <component :is="layer.icon" :size="28" weight="duotone" />
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ============================================ -->
  <!-- 6. SHARIA COMPLIANCE                        -->
  <!-- ============================================ -->
  <section class="sharia-section">
    <div class="container">
      <div class="text-center mb-3">
        <span class="section-eyebrow" style="color: var(--bs-success); background: rgba(27,122,74,0.08);">
          الجواب الحاسم
        </span>
      </div>

      <h2 class="section-title-lg text-center">
        هل ده ربا؟ الجواب: <span style="color: var(--bs-success)">لا</span>
      </h2>

      <div class="sharia-card">
        <div class="sharia-card__badge">
          <PhCheckCircle :size="14" weight="fill" />
          معتمد من هيئة شرعية
        </div>

        <div class="sharia-card__quote">
          «الجمعيّات الدوّارة بين الجماعة لا حرج فيها — كل عضو يدفع نفس المبلغ ويستلم نفس المبلغ، فلا فيها زيادة ولا نقصان، وهي من باب القرض الحسن المتبادل»
        </div>

        <div class="sharia-card__points">
          <div class="sharia-card__point">
            <PhCheckCircle :size="22" weight="fill" class="icon" />
            <div class="text">
              <strong>مفيش زيادة:</strong> كل عضو يدفع ويستلم نفس المبلغ بالضبط
            </div>
          </div>
          <div class="sharia-card__point">
            <PhCheckCircle :size="22" weight="fill" class="icon" />
            <div class="text">
              <strong>مفيش فوائد:</strong> صفر فوائد على التأخير أو التعجيل
            </div>
          </div>
          <div class="sharia-card__point">
            <PhCheckCircle :size="22" weight="fill" class="icon" />
            <div class="text">
              <strong>قرض حسن:</strong> الفكرة تعاون متبادل بين المسلمين
            </div>
          </div>
          <div class="sharia-card__point">
            <PhCheckCircle :size="22" weight="fill" class="icon" />
            <div class="text">
              <strong>رسوم خدمة فقط:</strong> 2-3% رسوم تشغيل المنصة، لا فوائد
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ============================================ -->
  <!-- 7. SUCCESS STORIES                          -->
  <!-- ============================================ -->
  <section class="fund-stories-section">
    <div class="container">
      <div class="text-center mb-3">
        <span class="section-eyebrow">قصص حقيقية</span>
      </div>

      <h2 class="section-title-lg text-center">
        أعضاء قبضوا فعلاً واتجوّزوا
      </h2>

      <div class="story-grid">
        <div
          v-for="(story, i) in stories"
          :key="story.name"
          class="story-card"


        >
          <div class="story-card__amount">
            <PhCurrencyCircleDollar :size="14" weight="fill" />
            قبض {{ story.amount }}
          </div>
          <p class="story-card__quote">{{ story.quote }}</p>
          <div class="story-card__author">
            <div class="story-card__author-avatar">{{ story.initial }}</div>
            <div class="story-card__author-info">
              <p class="name">{{ story.name }}</p>
              <span class="role">{{ story.role }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ============================================ -->
  <!-- 8. FAQ                                       -->
  <!-- ============================================ -->
  <section class="fund-faq-section">
    <div class="container">
      <div class="text-center mb-3">
        <span class="section-eyebrow">أسئلة الصندوق</span>
      </div>

      <h2 class="section-title-lg text-center">
        أسئلة شائعة عن صندوق التيسير
      </h2>

      <div class="faq-list">
        <div
          v-for="(item, i) in faqs"
          :key="i"
          class="faq-item"
          :class="{ 'is-open': openFaq === i }"


        >
          <button class="faq-item__question" @click="toggleFaq(i)">
            <span>{{ item.q }}</span>
            <PhCaretDown :size="20" weight="bold" class="faq-item__icon" />
          </button>
          <div class="faq-item__answer">
            <div class="faq-item__answer-inner">{{ item.a }}</div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ============================================ -->
  <!-- 9. FINAL CTA — Dual Path                    -->
  <!-- ============================================ -->
  <section class="fund-final-cta">
    <div class="container">
      <div class="fund-final-cta__inner">
        <h2 class="fund-final-cta__title">جاهز تبدأ؟</h2>
        <p class="fund-final-cta__subtitle">
          اختر طريقك — انضم لحلقة موجودة فوراً أو أنشئ حلقتك الخاصة وادعو أصدقاءك
        </p>

        <div class="fund-paths">
          <router-link to="/circles" class="fund-path fund-path--primary">
            <div class="fund-path__icon">
              <PhListMagnifyingGlass :size="32" weight="duotone" />
            </div>
            <h3 class="fund-path__title">انضم لحلقة</h3>
            <p class="fund-path__desc">تصفّح الحلقات النشطة في مدينتك واختر الأنسب لميزانيتك</p>
            <div class="fund-path__action">
              تصفّح الآن
              <PhArrowLeft :size="16" weight="bold" />
            </div>
          </router-link>

          <router-link to="/circles/create" class="fund-path fund-path--secondary">
            <div class="fund-path__icon">
              <PhPlusCircle :size="32" weight="duotone" />
            </div>
            <h3 class="fund-path__title">أنشئ حلقتك</h3>
            <p class="fund-path__desc">صمّم حلقة مخصصة وادعو عائلتك وأصدقاءك للانضمام</p>
            <div class="fund-path__action">
              ابدأ الإنشاء
              <PhArrowLeft :size="16" weight="bold" />
            </div>
          </router-link>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import {
  PhScales, PhCalculator, PhListMagnifyingGlass, PhShieldCheck, PhEye,
  PhUsers, PhCurrencyCircleDollar, PhClock, PhRocketLaunch,
  PhCaretDown, PhCheckCircle, PhXCircle, PhWarningCircle, PhArrowLeft, PhPlusCircle,
  PhIdentificationCard, PhHandshake, PhFile, PhVault, PhTrophy,
} from '@phosphor-icons/vue'

// === Cycle Visual ===
const activeMember = ref(2)
let cycleInterval

function getMemberPos(i, total) {
  const angle = (i / total) * 2 * Math.PI - Math.PI / 2
  const x = 50 + 42 * Math.cos(angle)
  const y = 50 + 42 * Math.sin(angle)
  return {
    top: `calc(${y}% - 24px)`,
    left: `calc(${x}% - 24px)`,
  }
}

onMounted(() => {
  cycleInterval = setInterval(() => {
    activeMember.value = (activeMember.value + 1) % 12
  }, 1500)
})

onUnmounted(() => {
  if (cycleInterval) clearInterval(cycleInterval)
})

// === Calculator ===
const members = ref(15)
const amount = ref(1000)
const totalPayout = computed(() => members.value * amount.value)

function formatNumber(num) {
  return new Intl.NumberFormat('ar-EG').format(num)
}

// === Comparison Table ===
const compareRows = [
  { label: 'بدون فوائد ربوية', bank: false, loan: false, traditional: true, yassiru: true },
  { label: 'بدون ضمانات بنكية', bank: false, loan: false, traditional: true, yassiru: true },
  { label: 'ضمان رقمي للفلوس', bank: true, loan: true, traditional: false, yassiru: true },
  { label: 'حماية من التخلف', bank: true, loan: true, traditional: false, yassiru: true },
  { label: 'شفافية كاملة', bank: 'warn', loan: 'warn', traditional: 'warn', yassiru: true },
  { label: 'سرعة الإجراءات', bank: false, loan: 'warn', traditional: true, yassiru: true },
  { label: 'تحت إشراف شرعي', bank: false, loan: false, traditional: 'warn', yassiru: true },
]

function iconFor(value) {
  if (value === true) return PhCheckCircle
  if (value === false) return PhXCircle
  return PhWarningCircle
}

function classFor(value) {
  if (value === true) return 'check'
  if (value === false) return 'cross'
  return 'warn'
}

// === Steps ===
const steps = [
  {
    title: 'انضم لحلقة',
    desc: 'اختر حلقة في مدينتك بعدد الأعضاء والمبلغ المناسب لك، أو أنشئ حلقتك الخاصة.',
    example: 'مثال: 15 عضو × 1,000 ج.م',
  },
  {
    title: 'وقّع العقد',
    desc: 'وقّع العقد الرقمي وقدّم ضامنك (بتأكيد OTP). كل شيء موثّق ومحمي.',
    example: 'يستغرق 5 دقائق فقط',
  },
  {
    title: 'ساهم شهرياً',
    desc: 'يدفع كل عضو نفس المبلغ كل شهر. الخصم تلقائي وتذكير قبل الموعد بـ 3 أيام.',
    example: '1,000 ج.م شهرياً',
  },
  {
    title: 'تسلّم المبلغ',
    desc: 'عند دورك تستلم المبلغ الكامل دفعة واحدة، وتستخدمه لتجهيز زواجك بحرية.',
    example: '15,000 ج.م دفعة واحدة',
  },
]

// === Security Layers ===
const securityLayers = [
  {
    title: 'توثيق الهوية الإجباري',
    desc: 'كل عضو لازم يوثّق هويته بـ OTP + national ID فريد. لا حسابات وهمية.',
    icon: PhIdentificationCard, color: '#0d7377', dark: '#095456', bg: 'rgba(13,115,119,0.1)',
  },
  {
    title: 'الضامن الإلزامي',
    desc: 'كل عضو يقدم ضامناً (قريب أو صديق ثقة) يؤكد بـ OTP على هاتفه قبل بدء الحلقة.',
    icon: PhHandshake, color: '#1565c0', dark: '#0d4a8f', bg: 'rgba(21,101,192,0.1)',
  },
  {
    title: 'العقد الرقمي الملزم',
    desc: 'عقد رقمي موقّع بـ IP + بصمة الجهاز + توقيت دقيق. ملزم قانونياً وقابل للتقاضي.',
    icon: PhFile, color: '#b8860b', dark: '#8a6508', bg: 'rgba(184,134,11,0.1)',
  },
  {
    title: 'صندوق الضمان',
    desc: 'كل عضو يدفع 5% رسم ضمان يدخل صندوق احتياطي يغطي أي تخلّف عن السداد فوراً.',
    icon: PhVault, color: '#1b7a4a', dark: '#14613b', bg: 'rgba(27,122,74,0.1)',
  },
  {
    title: 'نظام Trust Score',
    desc: 'كل عضو له نقاط ثقة. الأعلى التزاماً يحصل على أولوية في القبض. عدالة مدروسة.',
    icon: PhTrophy, color: '#c0392b', dark: '#8b2820', bg: 'rgba(192,57,43,0.1)',
  },
]

// === Stories ===
const stories = [
  {
    name: 'محمد ع.', role: 'موظف — القاهرة', initial: 'م', amount: '15,000 ج.م',
    quote: 'انضممت لحلقة 15 عضو، دفعت 12 شهر بإلتزام، وعند دوري قبضت 15 ألف جنيه. اشتريت بيها أساسيات شقتي وأكملت زواجي بدون ديون. تجربة حلال 100%.',
  },
  {
    name: 'أحمد ك.', role: 'مدرّس — طنطا', initial: 'أ', amount: '20,000 ج.م',
    quote: 'كنت محتاج 20 ألف للشقة وكنت ميئوس. حلقة الـ 20 عضو ساعدتني، الجدول الزمني كان واضح، والـ Trust Score خلاني آخذ دوري بدري. الحمد لله.',
  },
  {
    name: 'كريم س.', role: 'طبيب أسنان — الإسكندرية', initial: 'ك', amount: '30,000 ج.م',
    quote: 'صمّمت حلقتي بنفسي مع زمايلي في الكلية، 15 شخص بـ 2,000 شهرياً. النظام شفاف جداً، وقدرت أساعد زمايلي يوصلوا لزواج ميسّر زي ما وصلت.',
  },
]

// === FAQ ===
const openFaq = ref(0)
const faqs = [
  {
    q: 'هل الصندوق التعاوني فيه ربا؟',
    a: 'لا، أبداً. الصندوق هو قرض حسن دوّار — كل عضو يدفع نفس المبلغ ويستلم نفس المبلغ. لا فوائد، لا زيادات، لا نقصان. تم اعتماده من هيئة شرعية متخصصة، والفتوى متاحة بالكامل للاطلاع.',
  },
  {
    q: 'لو حد في الحلقة ما دفعش، إيه اللي يحصل؟',
    a: 'عندنا 3 طبقات حماية: (1) الضامن يتم إشعاره ويتحمل المسؤولية، (2) صندوق الضمان يغطي المبلغ مؤقتاً عشان الحلقة تستمر، (3) العضو المتخلف يتم تجميده ثم استبعاده وحظره من جميع الحلقات المستقبلية.',
  },
  {
    q: 'إزاي يتم اختيار ترتيب القبض؟',
    a: 'بنظام Trust Score — كل عضو له نقاط ثقة بناءً على: إكمال الدورة التأهيلية، توثيق الهوية، التزام سابق في حلقات أخرى، تاريخه على المنصة. الأكثر التزاماً يحصل على أولوية، وده يضمن العدالة ويقلل المخاطر.',
  },
  {
    q: 'كم رسوم الخدمة؟ هل في رسوم خفية؟',
    a: 'رسوم الخدمة 2-3% من المبلغ الإجمالي فقط، تُعرض بوضوح قبل التوقيع. مفيش رسوم خفية. الرسوم دي بتغطي تشغيل المنصة والتذكيرات والدعم الفني. الانضمام مجاني والتسجيل مجاني.',
  },
  {
    q: 'هل أقدر أنشئ حلقة مع عائلتي وأصدقائي فقط؟',
    a: 'أكيد. تقدر تنشئ حلقة خاصة وتدعو ناس بعينهم بالـ link. ده فعلاً الأكثر أماناً لأن الأعضاء يعرفوا بعض. كل التزامات الحلقة وحمايتها هتطبّق نفس الشيء.',
  },
  {
    q: 'هل أقدر أنسحب من حلقة بعد الانضمام؟',
    a: 'قبل بدء الحلقة (مرحلة التشكيل): نعم تقدر تنسحب بدون أي تبعات. بعد بدء الحلقة: لا، إلا في حالات استثنائية بإذن الإدارة وموافقة جميع الأعضاء، لأن انسحابك بيؤثر على باقي الأعضاء.',
  },
]

function scrollTo(id) {
  const el = document.getElementById(id)
  if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' })
}

function toggleFaq(i) {
  openFaq.value = openFaq.value === i ? -1 : i
}
</script>
