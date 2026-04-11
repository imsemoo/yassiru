<template>
  <footer class="footer-y">
    <div class="container">
      <div class="row g-4 g-lg-5">
        <!-- Brand Column -->
        <div class="col-lg-4">
          <div class="d-flex align-items-center gap-2 mb-3">
            <img src="/logo.svg" alt="يسّرو" height="100" style="filter: brightness(0) invert(1);">
          </div>
          <p class="mb-3" style="line-height: 1.9; font-size: 0.9rem;">
            منصة تيسير الزواج الأولى في العالم الإسلامي.
            نجمع بين التأهيل الشرعي والنفسي، التوفيق عبر معرّفين موثوقين،
            الصندوق التعاوني، والأعراس الجماعية — لنجعل الزواج يسيراً كما أمر الله.
          </p>
          <div class="d-flex gap-2">
            <a href="mailto:islam@yassiru.com" class="btn btn-sm" style="background: rgba(255,255,255,0.1); color: #fff;" aria-label="البريد">
              <PhEnvelope :size="18" />
            </a>
            <a href="https://yassiru.com" target="_blank" rel="noopener" class="btn btn-sm" style="background: rgba(255,255,255,0.1); color: #fff;" aria-label="الموقع">
              <PhGlobe :size="18" />
            </a>
          </div>
        </div>

        <!-- Quick Links -->
        <div class="col-6 col-lg-2">
          <h6 class="footer-title">المنصة</h6>
          <ul class="footer-links">
            <li><router-link to="/courses"><PhBookOpen :size="16" /> التأهيل</router-link></li>
            <li><router-link to="/fund"><PhHandCoins :size="16" /> الصندوق</router-link></li>
            <li><router-link to="/weddings"><PhHeart :size="16" /> الأعراس</router-link></li>
            <li><router-link to="/calculator"><PhCalculator :size="16" /> الحاسبة</router-link></li>
          </ul>
        </div>

        <!-- Support -->
        <div class="col-6 col-lg-2">
          <h6 class="footer-title">حسابك</h6>
          <ul class="footer-links">
            <li><router-link to="/login"><PhSignIn :size="16" /> تسجيل الدخول</router-link></li>
            <li><router-link to="/register"><PhUserPlus :size="16" /> إنشاء حساب</router-link></li>
            <li><router-link to="/profile"><PhSquaresFour :size="16" /> ملفي</router-link></li>
            <li><router-link to="/counseling"><PhChatsCircle :size="16" /> الاستشارات</router-link></li>
          </ul>
        </div>

        <!-- Contact -->
        <div class="col-lg-4">
          <h6 class="footer-title">تواصل معنا</h6>
          <ul class="footer-links">
            <li>
              <a href="mailto:islam@yassiru.com">
                <PhEnvelope :size="16" /> islam@yassiru.com
              </a>
            </li>
            <li>
              <a href="https://yassiru.com" target="_blank">
                <PhGlobe :size="16" /> yassiru.com
              </a>
            </li>
          </ul>
          <div class="mt-3 p-3 rounded" style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);">
            <p class="mb-0 small" style="line-height: 1.8;">
              <PhShieldCheck :size="16" class="ms-1" style="color: #1b7a4a;" />
              المنصة معتمدة شرعياً — لا ربا، لا تواصل مباشر بين الجنسين، كل شيء يمر عبر المعرّف وولي الأمر.
            </p>
          </div>
        </div>
      </div>

      <!-- Bottom -->
      <div class="footer-bottom">
        جميع الحقوق محفوظة &copy; {{ new Date().getFullYear() }} يسّرو — منصة تيسير الزواج
      </div>
    </div>
  </footer>
</template>

<script setup>
import {
  PhBookOpen, PhHandCoins, PhHeart, PhCalculator,
  PhShieldCheck, PhEnvelope, PhGlobe,
  PhSignIn, PhUserPlus, PhSquaresFour, PhChatsCircle,
} from '@phosphor-icons/vue'
</script>
