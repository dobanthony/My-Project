<template>
  <SellerDashboardLayout>
    <div class="container py-4">

      <!-- 🧭 Page Header -->
      <div class="text-center mb-4">
        <h2 class="fw-bold text-primary mb-2">
          <i class="bi bi-chat-dots-fill me-2"></i> Conversations with Customers
        </h2>
        <p class="text-muted mb-0">
          View and continue your chats with customers. Stay connected and keep your buyers informed.
        </p>
      </div>

      <!-- 💬 Conversation List -->
      <div v-if="users.length">
        <Link
          v-for="user in users"
          :key="user.id"
          :href="`/seller/inbox/${user.id}`"
          class="text-decoration-none"
        >
          <div
            class="conversation-card border rounded-3 p-3 mb-3 d-flex align-items-center shadow-sm bg-white hover-scale"
          >
            <!-- 🖼️ User Avatar -->
            <img
              :src="user.avatar ? `/storage/${user.avatar}` : '/images/default-avatar.jpg'"
              alt="User Avatar"
              class="me-3 rounded-circle border"
              style="width: 60px; height: 60px; object-fit: cover;"
            />

            <!-- 👤 User Info -->
            <div class="flex-grow-1">
              <div class="d-flex justify-content-between align-items-center">
                <strong class="text-dark fw-semibold fs-5">
                  <i class="bi bi-person-circle me-1 text-primary"></i>{{ user.first_name }}
                </strong>

                <!-- 📅 Message time (optional, if available) -->
                <small v-if="user.latest_message" class="text-muted">
                  <i class="bi bi-clock me-1"></i>{{ new Date(user.latest_message.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) }}
                </small>
              </div>

              <!-- 💬 Latest message preview -->
              <p
                class="mb-0 small mt-1"
                :class="{
                  'text-dark fw-semibold': !user.latest_message?.is_read && user.latest_message?.receiver_id === $page.props.auth.user.id,
                  'text-muted': user.latest_message?.is_read || user.latest_message?.receiver_id !== $page.props.auth.user.id
                }"
              >
                <template v-if="user.latest_message">
                  <strong>
                    {{ user.latest_message.sender_id === $page.props.auth.user.id ? 'You' : user.first_name }}:
                  </strong>
                  {{ truncate(user.latest_message.message, 60) }}
                </template>
                <template v-else>
                  <em class="text-muted">No messages yet.</em>
                </template>
              </p>
            </div>

            <!-- ➡️ Open chat icon -->
            <i class="bi bi-chevron-right text-secondary ms-3"></i>
          </div>
        </Link>
      </div>

      <!-- 🕳️ Empty state -->
      <div v-else class="text-center py-5">
        <i class="bi bi-chat-left-text display-4 text-muted mb-3"></i>
        <p class="text-muted fs-5">No customer conversations yet.</p>
      </div>
    </div>
  </SellerDashboardLayout>
</template>

<script setup>
import SellerDashboardLayout from '@/Layouts/SellerDashboardLayout.vue'
import { defineProps } from 'vue'
import { Link } from '@inertiajs/vue3'

const props = defineProps({
  users: Array
})

function truncate(text, length = 50) {
  return text && text.length > length ? text.substring(0, length) + '...' : text
}
</script>

<style scoped>
.hover-scale {
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.hover-scale:hover {
  transform: scale(1.02);
  box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12);
}

.conversation-card {
  transition: 0.3s;
  border-left: 4px solid transparent;
}
.conversation-card:hover {
  border-left-color: var(--bs-primary);
  background-color: #f8f9fa;
}
</style>
