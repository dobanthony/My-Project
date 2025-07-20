<template>
  <DashboardLayout>
    <div class="container">
      <div class="bg-success text-white p-2 mb-3 rounded-top-4">
        <h3>Conversations with Customers</h3>
      </div>

      <div v-if="shops.length">
        <Link
          v-for="shop in shops"
          :key="shop.id"
          :href="`/user/inbox/${shop.id}`"
          class="text-decoration-none"
        >
          <div
            class="border rounded p-3 mb-3 d-flex align-items-center hover-shadow"
            style="cursor: pointer;"
          >
            <!-- 🖼️ Shop Logo -->
            <img
              :src="`/storage/${shop.shop_logo}`"
              alt="Shop Logo"
              class="me-3 rounded-circle"
              style="width: 60px; height: 60px; object-fit: cover;"
            />

            <!-- 🛍️ Shop Details -->
            <div class="flex-grow-1">
              <strong class="text-success">{{ shop.shop_name }}</strong>

              <!-- 💬 Latest message preview -->
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
                  No messages yet.
                </template>
              </p>
            </div>
          </div>
        </Link>
      </div>

      <div v-else>
        <p class="text-muted">You don't have any messages yet.</p>
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
.hover-shadow:hover {
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  transition: 0.2s;
}
</style>
