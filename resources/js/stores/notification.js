// stores/notification.js or .ts
import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

export const useNotificationStore = defineStore('notification', () => {
  const notifications = ref([])

  const unreadCount = computed(() =>
    notifications.value.filter(n => !n.read_at).length
  )

  const hasUnread = computed(() =>
    notifications.value.some(n => !n.read_at)
  )

  function setNotifications(data) {
    notifications.value = data
  }

  function markAsRead(id) {
    const notif = notifications.value.find(n => n.id === id)
    if (notif && !notif.read_at) {
      notif.read_at = new Date().toISOString()
    }
  }

  function markAllAsRead() {
    notifications.value = notifications.value.map(n => ({
      ...n,
      read_at: n.read_at ?? new Date().toISOString()
    }))
  }

  return {
    notifications,
    unreadCount,
    hasUnread,
    setNotifications,
    markAsRead,
    markAllAsRead,
  }
})
