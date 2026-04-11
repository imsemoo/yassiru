import { ref, computed } from 'vue'

export function useValidation() {
  const errors = ref({})

  const hasErrors = computed(() => Object.keys(errors.value).length > 0)

  function setErrors(serverErrors) {
    if (typeof serverErrors === 'string') {
      errors.value = { _general: [serverErrors] }
    } else {
      errors.value = serverErrors || {}
    }
  }

  function getError(field) {
    const fieldErrors = errors.value[field]
    return fieldErrors ? fieldErrors[0] : null
  }

  function clearError(field) {
    if (field) {
      delete errors.value[field]
    } else {
      errors.value = {}
    }
  }

  function clearAll() {
    errors.value = {}
  }

  return { errors, hasErrors, setErrors, getError, clearError, clearAll }
}
