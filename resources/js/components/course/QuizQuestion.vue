<template>
  <div class="ycard p-3 mb-3">
    <h6 class="fw-bold mb-3">
      <span class="badge bg-primary ms-2">{{ questionNumber }}</span>
      {{ question.question }}
    </h6>

    <div class="d-flex flex-column gap-2">
      <label
        v-for="(option, index) in question.options"
        :key="index"
        class="quiz-option p-3 rounded-3 cursor-pointer"
        :class="{
          'quiz-option--selected': modelValue === index,
          'quiz-option--correct': showResult && index === question.correct,
          'quiz-option--wrong': showResult && modelValue === index && index !== question.correct,
        }"
      >
        <input
          type="radio"
          :name="'q-' + questionNumber"
          :value="index"
          :checked="modelValue === index"
          :disabled="showResult"
          class="d-none"
          @change="$emit('update:modelValue', index)"
        />
        <span>{{ option }}</span>
      </label>
    </div>
  </div>
</template>

<script setup>
defineProps({
  question: { type: Object, required: true },
  questionNumber: { type: Number, required: true },
  modelValue: { type: Number, default: null },
  showResult: { type: Boolean, default: false },
})

defineEmits(['update:modelValue'])
</script>

<style scoped>
.quiz-option {
  border: 2px solid var(--border-color, #e0d8cc);
  transition: all 0.2s;
  cursor: pointer;
}
.quiz-option:hover:not(.quiz-option--correct):not(.quiz-option--wrong) {
  border-color: var(--primary, #0d7377);
  background: var(--primary-light, #e8f5f5);
}
.quiz-option--selected {
  border-color: var(--primary, #0d7377);
  background: var(--primary-light, #e8f5f5);
}
.quiz-option--correct {
  border-color: var(--success, #1b7a4a);
  background: #e8f5e9;
}
.quiz-option--wrong {
  border-color: var(--danger, #c0392b);
  background: #fdecea;
}
</style>
