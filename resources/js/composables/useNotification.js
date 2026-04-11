import { ref } from 'vue'

const notifications = ref([])
let idCounter = 0

export function useNotification() {
  function notify(message, type = 'info', duration = 4000) {
    const id = ++idCounter
    notifications.value.push({ id, message, type })

    if (duration > 0) {
      setTimeout(() => remove(id), duration)
    }

    return id
  }

  function success(message, duration) {
    return notify(message, 'success', duration)
  }

  function error(message, duration) {
    return notify(message, 'danger', duration)
  }

  function warning(message, duration) {
    return notify(message, 'warning', duration)
  }

  function remove(id) {
    notifications.value = notifications.value.filter(n => n.id !== id)
  }

  function clear() {
    notifications.value = []
  }

  return { notifications, notify, success, error, warning, remove, clear }
}
