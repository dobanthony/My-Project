<template>
  <DashboardLayout>
    <div class="container">
      <h4 class="mb-4 text-dark">
        <i class="bi bi-envelope me-2"></i>{{ shop.shop_name }}
      </h4>

      <!-- Chat Messages -->
      <div
        ref="chatBox"
        class="chat-box mb-3 p-3 border rounded bg-light"
        style="max-height: 400px; overflow-y: auto;"
      >
        <div v-for="(msg, index) in messages" :key="msg.id" class="mb-2">
          <div
            class="d-flex"
            :class="msg.sender.id === shop.user_id ? 'justify-content-start' : 'justify-content-end'"
          >
            <div
              class="d-flex flex-column"
              :class="msg.sender.id === shop.user_id ? 'align-items-start' : 'align-items-end'"
            >
              <!-- Avatar -->
              <img
                :src="msg.sender.id === shop.user_id
                  ? `/storage/${shop.shop_logo}`
                  : msg.sender.avatar
                    ? `/storage/${msg.sender.avatar}`
                    : '/images/default-user.png'"
                alt="Profile"
                class="rounded-circle me-2"
                style="width: 40px; height: 40px; object-fit: cover;"
              />

              <!-- Message Bubble -->
              <div
                class="p-2 rounded"
                :class="msg.sender.id === shop.user_id ? 'bg-white' : 'bg-success text-white'"
                style="max-width: 70%; min-width: 80px;"
              >
                <div class="fw-bold small mb-1">
                  {{ msg.sender.id === shop.user_id ? shop.shop_name : msg.sender.first_name }}
                </div>
                <div>{{ msg.message }}</div>
              </div>

              <!-- Time + Status -->
              <div class="text-muted small mt-1">
                {{ formatTime(msg.created_at) }}
                <span v-if="msg.sender.id !== shop.user_id">
                  • {{ msg.is_read ? 'Delivered' : 'Sent' }}
                </span>
              </div>

              <!-- Seen Time -->
              <div
                v-if="msg.sender.id !== shop.user_id && isLastOwnMessage(index) && msg.is_read"
                class="text-success small mt-1"
              >
                <i class="bi bi-check-all me-2"></i>Seen {{ formatSeenTime(msg.updated_at || msg.created_at) }}
              </div>

              <!-- Product inside message -->
              <div v-if="msg.product" class="card mt-2 border position-relative" style="max-width: 250px;">
                <!-- ⚠ Report Badge -->
                <span
                  v-if="msg.product.is_reported"
                  class="badge bg-danger position-absolute top-0 start-0 m-1"
                >
                  ⚠ Reported
                </span>
                <img
                  :src="msg.product.image ? `/storage/${msg.product.image}` : 'https://via.placeholder.com/100x100?text=No+Image'"
                  class="card-img-top"
                  style="height: 120px; object-fit: cover;"
                />
                <div class="card-body p-2">
                  <h6 class="card-title mb-1 text-success">{{ msg.product.name }}</h6>
                  <p class="text-muted small mb-1">₱{{ parseFloat(msg.product.price).toFixed(2) }}</p>
                  <Link :href="`/product/${msg.product.id}`" class="text-decoration-none small">🔍 View Product</Link>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Send Message -->
      <form @submit.prevent="sendMessage">
        <!-- Pinned Product inside input area (only before first send) -->
        <div v-if="pinnedProduct && !hasSentProduct" class="card mb-2 border bg-light shadow-sm position-relative">
          <!-- ⚠ Report Badge for pinned product -->
          <span
            v-if="pinnedProduct.is_reported"
            class="badge bg-danger position-absolute top-0 start-0 m-1"
          >
            ⚠ Reported
          </span>
          <div class="row g-0 align-items-center">
            <div class="col-auto">
              <img
                :src="pinnedProduct.image ? `/storage/${pinnedProduct.image}` : 'https://via.placeholder.com/100x100?text=No+Image'"
                class="img-fluid rounded-start"
                style="width: 80px; height: 80px; object-fit: cover;"
              />
            </div>
            <div class="col">
              <div class="card-body py-2">
                <h6 class="card-title mb-1 text-success">{{ pinnedProduct.name }}</h6>
                <p class="card-text text-muted small mb-1">₱{{ parseFloat(pinnedProduct.price).toFixed(2) }}</p>
                <Link :href="`/product/${pinnedProduct.id}`" class="text-decoration-none small">🔍 View Product</Link>
              </div>
            </div>
            <!-- Remove Button -->
            <button
              type="button"
              class="btn-close position-absolute top-0 end-0 m-2"
              aria-label="Remove"
              @click="removePinnedProduct"
            ></button>
          </div>
        </div>

        <!-- Input box -->
        <div class="d-flex">
          <textarea
            v-model="newMessage"
            class="form-control me-2"
            rows="1"
            placeholder="Type a message..."
            required
          ></textarea>
          <button class="btn btn-success"><i class="bi bi-send-fill"></i></button>
        </div>
      </form>
    </div>
  </DashboardLayout>
</template>

<script setup>
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import { ref, onMounted, onBeforeUnmount, onUpdated } from 'vue'
import { router, usePage, Link } from '@inertiajs/vue3'
import { defineProps } from 'vue'

const props = defineProps({
  shop: Object,
  messages: Array,
  pinnedProduct: Object,
})

const userId = usePage().props.auth.user.id
const newMessage = ref('')
const chatBox = ref(null)
const pinnedProduct = ref(props.pinnedProduct)
const hasSentProduct = ref(false)

let interval = null

onUpdated(() => {
  if (chatBox.value) {
    chatBox.value.scrollTop = chatBox.value.scrollHeight
  }
})

const sendMessage = () => {
  router.post('/messages/send', {
    shop_id: props.shop.id,
    receiver_id: props.shop.user_id,
    message: newMessage.value,
    product_id: !hasSentProduct.value && pinnedProduct.value ? pinnedProduct.value.id : null,
  }, {
    onSuccess: () => {
      newMessage.value = ''
      if (pinnedProduct.value && !hasSentProduct.value) {
        hasSentProduct.value = true
        pinnedProduct.value = null
      }
    }
  })
}

const removePinnedProduct = () => {
  pinnedProduct.value = null
}

function formatTime(datetime) {
  const date = new Date(datetime)
  return date.toLocaleString('en-PH', {
    hour: '2-digit',
    minute: '2-digit',
    hour12: true,
    month: 'short',
    day: 'numeric'
  })
}

function formatSeenTime(datetime) {
  const date = new Date(datetime)
  return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: true })
}

function isLastOwnMessage(index) {
  const ownMessages = props.messages.filter(m => m.sender.id === userId)
  return ownMessages.length && props.messages[index].id === ownMessages.at(-1).id
}

onMounted(() => {
  interval = setInterval(() => {
    router.reload({ only: ['messages'] })
  }, 3000)
})

onBeforeUnmount(() => {
  clearInterval(interval)
})
</script>

<style scoped>
textarea.form-control {
  border-color: #28a745;
  box-shadow: none;
}
textarea.form-control:focus {
  border-color: #28a745;
  box-shadow: 0 0 0 0.25rem rgba(40, 167, 69, 0.5);
}
.chat-box {
  max-height: 500px;
  overflow-y: auto;
  background-color: #f7f7f7;
}
textarea {
  resize: none;
}
</style>
