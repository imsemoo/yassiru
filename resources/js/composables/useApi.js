import axios from 'axios'
import { ref } from 'vue'

export function useApi() {
  const loading = ref(false)
  const error = ref(null)

  async function request(method, url, data = null) {
    loading.value = true
    error.value = null
    try {
      const response = await axios({ method, url, data })
      return response.data
    } catch (err) {
      if (err.response?.status === 422) {
        error.value = err.response.data.errors
      } else if (err.response?.data?.message) {
        error.value = err.response.data.message
      } else {
        error.value = 'حدث خطأ غير متوقع'
      }
      throw err
    } finally {
      loading.value = false
    }
  }

  const get = (url) => request('get', url)
  const post = (url, data) => request('post', url, data)
  const put = (url, data) => request('put', url, data)
  const del = (url) => request('delete', url)

  return { loading, error, get, post, put, del }
}
