<template>
  <SellerDashboardLayout>
    <div class="container py-4">
      <div class="bg-success text-white p-2 mb-1 rounded-top-2">
        <h3>Conversations with Customers</h3>
      </div>

      <div v-if="users.length">
        <Link
          v-for="user in users"
          :key="user.id"
          :href="`/seller/inbox/${user.id}`"
          class="text-decoration-none"
        >
          <div
            class="border rounded p-3 mb-3 d-flex align-items-center hover-shadow"
            style="cursor: pointer;"
          >
            <!-- 🖼️ User Avatar -->
            <img
              :src="user.avatar ? `/storage/${user.avatar}` : '/images/default-user.png'"
              alt="User Avatar"
              class="me-3 rounded-circle"
              style="width: 60px; height: 60px; object-fit: cover;"
            />

            <!-- 👤 User Details -->
            <div class="flex-grow-1">
              <strong class="text-success">{{ user.first_name }}</strong>

              <!-- 💬 Latest message preview -->
              <p
                class="mb-0 small"
                :class="{
                  'text-dark fw-bold': !user.latest_message?.is_read && user.latest_message?.receiver_id === $page.props.auth.user.id,
                  'text-muted': user.latest_message?.is_read || user.latest_message?.receiver_id !== $page.props.auth.user.id
                }"
              >
                <template v-if="user.latest_message">
                  <strong>
                    {{ user.latest_message.sender_id === $page.props.auth.user.id ? 'You' : user.name }}:
                  </strong>
                  {{ truncate(user.latest_message.message, 50) }}
                </template>
                <template v-else>
                  No messages yet.
                </template>
              </p>
            </div>
          </div>
        </Link>
      </div>

      <div v-else>
        <p class="text-muted">No customer conversations yet.</p>
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
.hover-shadow:hover {
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  transition: 0.2s;
}
</style>
