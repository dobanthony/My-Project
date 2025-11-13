<template>
  <DashboardLayout>
    <div class="container py-4">

      <!-- 🌟 Page Header -->
      <div class="mb-4">
        <div class="d-flex align-items-center mb-2">
          <i class="bi bi-chat-dots-fill fs-3 text-primary me-2"></i>
          <h2 class="fw-bold mb-0">Conversations with Customers</h2>
        </div>
        <p class="text-muted">
          View and manage your messages with shop owners. Stay connected and reply quickly to keep your customers happy.
        </p>
        <hr />
      </div>

      <!-- 💬 Conversations List -->
      <div v-if="shops.length">
        <div
          v-for="shop in shops"
          :key="shop.id"
          class="card mb-3 border-0 shadow-sm hover-card"
        >
          <Link
            :href="`/user/inbox/${shop.id}`"
            class="text-decoration-none text-dark"
          >
            <div class="card-body d-flex align-items-center">

              <!-- 🏪 Shop Logo -->
              <img
                :src="`/storage/${shop.shop_logo}`"
                alt="Shop Logo"
                class="me-3 rounded-circle border"
                style="width: 60px; height: 60px; object-fit: cover;"
              />

              <!-- 📝 Shop Info -->
              <div class="flex-grow-1">
                <h5 class="mb-1 fw-semibold text-primary">
                  <i class="bi bi-shop me-1"></i>
                  {{ shop.shop_name }}
                </h5>
                <p
                  class="mb-0 small"
                  :class="{
                    'text-dark fw-bold': !shop.latest_message?.is_read && shop.latest_message?.receiver_id === $page.props.auth.user.id,
                    'text-muted': shop.latest_message?.is_read || shop.latest_message?.receiver_id !== $page.props.auth.user.id
                  }"
                >
                  <template v-if="shop.latest_message">
                    <strong>
                      {{ shop.latest_message.sender.id === $page.props.auth.user.id ? 'You' : shop.shop_name }}:
                    </strong>
                    {{ truncate(shop.latest_message.message, 50) }}
                  </template>
                  <template v-else>
                    <span class="text-muted fst-italic">No messages yet</span>
                  </template>
                </p>
              </div>

              <!-- ⏰ Time or Icon -->
              <i class="bi bi-chevron-right text-secondary"></i>
            </div>
          </Link>
        </div>
      </div>

      <!-- 😔 Empty State -->
      <div v-else class="text-center py-5">
        <i class="bi bi-inbox fs-1 text-secondary mb-2"></i>
        <h5 class="text-muted">No Conversations Yet</h5>
        <p class="text-muted small">You haven’t started any chats with shop owners yet.</p>
      </div>
    </div>
  </DashboardLayout>
</template>

<script setup>
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import { defineProps } from 'vue'
import { Link } from '@inertiajs/vue3'

const props = defineProps({
  shops: Array
})

function truncate(text, length = 50) {
  return text && text.length > length ? text.substring(0, length) + '...' : text
}
</script>

<style scoped>
.hover-card {
  transition: all 0.2s ease-in-out;
  border-radius: 12px;
}
.hover-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1);
}
</style>
