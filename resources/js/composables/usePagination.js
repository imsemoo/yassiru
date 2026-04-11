import { ref, computed } from 'vue'

export function usePagination(fetchFn) {
  const currentPage = ref(1)
  const lastPage = ref(1)
  const total = ref(0)
  const loading = ref(false)

  const hasNextPage = computed(() => currentPage.value < lastPage.value)
  const hasPrevPage = computed(() => currentPage.value > 1)

  function updateFromResponse(data) {
    currentPage.value = data.current_page
    lastPage.value = data.last_page
    total.value = data.total
  }

  async function goToPage(page) {
    if (page < 1 || page > lastPage.value) return
    currentPage.value = page
    loading.value = true
    try {
      await fetchFn({ page })
    } finally {
      loading.value = false
    }
  }

  async function nextPage() {
    if (hasNextPage.value) {
      await goToPage(currentPage.value + 1)
    }
  }

  async function prevPage() {
    if (hasPrevPage.value) {
      await goToPage(currentPage.value - 1)
    }
  }

  return {
    currentPage, lastPage, total, loading,
    hasNextPage, hasPrevPage,
    updateFromResponse, goToPage, nextPage, prevPage,
  }
}
