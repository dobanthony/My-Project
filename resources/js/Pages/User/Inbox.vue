<template>
  <Head title="Inbox " />
  <DashboardLayout>
    <div class="container py-4">

      <!-- 🧭 Page Header -->
      <div class="mb-4 border-bottom pb-3">
        <div class="d-flex align-items-center mb-2">
          <i class="bi bi-chat-dots-fill fs-3 text-primary me-2"></i>
          <h2 class="fw-bold mb-0">{{ shop.shop_name }}</h2>
        </div>
        <p class="text-muted">
          Chat with <strong>{{ shop.shop_name }}</strong> directly. View messages, product links, and replies in real time.
        </p>
      </div>

      <!-- 💬 Chat Messages -->
      <div
        ref="chatBox"
        class="chat-box mb-3 p-3 rounded shadow-sm bg-light"
        style="max-height: 450px; overflow-y: auto;"
      >
        <div v-for="(msg, index) in messages" :key="msg.id" class="mb-4">
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
                    : '/images/default-avatar.jpg'"
                alt="Profile"
                class="rounded-circle shadow-sm mb-1"
                style="width: 40px; height: 40px; object-fit: cover;"
              />

              <!-- Message Bubble -->
              <div
                class="p-3 rounded-4"
                :class="msg.sender.id === shop.user_id
                  ? 'bg-white border border-1'
                  : 'bg-primary text-white'"
                style="max-width: 75%;"
              >
                <div class="fw-semibold small mb-1">
                  <i
                    class="bi"
                    :class="msg.sender.id === shop.user_id ? 'bi-shop text-primary me-1' : 'bi-person-circle text-white me-1'"
                  ></i>
                  {{ msg.sender.id === shop.user_id ? shop.shop_name : msg.sender.first_name }}
                </div>
                <div>{{ msg.message }}</div>
              </div>

              <!-- Message Meta -->
              <div class="text-muted small mt-1">
                <i class="bi bi-clock me-1"></i>{{ formatTime(msg.created_at) }}
                <span v-if="msg.sender.id !== shop.user_id">
                  • {{ msg.is_read ? 'Delivered' : 'Sent' }}
                </span>
              </div>

              <!-- Seen Indicator -->
              <div
                v-if="msg.sender.id !== shop.user_id && isLastOwnMessage(index) && msg.is_read"
                class="text-success small mt-1"
              >
                <i class="bi bi-check-all me-1"></i> Seen {{ formatSeenTime(msg.updated_at || msg.created_at) }}
              </div>

              <!-- 📦 Product Card -->
              <div
                v-if="msg.product"
                class="card mt-2 border-0 shadow-sm"
                style="max-width: 260px; border-radius: 12px;"
              >
                <span
                  v-if="msg.product.is_reported"
                  class="badge bg-danger position-absolute top-0 start-0 m-1"
                >
                  ⚠ Reported
                </span>
                <img
                  :src="msg.product.image ? `/storage/${msg.product.image}` : 'https://via.placeholder.com/100x100?text=No+Image'"
                  class="card-img-top rounded-top"
                  style="height: 130px; object-fit: cover;"
                />
                <div class="card-body p-2">
                  <h6 class="card-title text-success mb-1">{{ msg.product.name }}</h6>
                  <p class="text-muted small mb-1">₱{{ parseFloat(msg.product.price).toFixed(2) }}</p>
                  <Link :href="`/product/${msg.product.id}`" class="small text-decoration-none text-primary">
                    <i class="bi bi-box-seam me-1"></i> View Product
                  </Link>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- 📝 Send Message -->
      <form @submit.prevent="sendMessage">
        <!-- 📍 Pinned Product -->
        <div v-if="pinnedProduct && !hasSentProduct" class="card mb-3 border-0 shadow-sm bg-light position-relative rounded-3">
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
            <div class="col ps-2">
              <div class="card-body py-2">
                <h6 class="card-title text-success mb-1">{{ pinnedProduct.name }}</h6>
                <p class="text-muted small mb-1">₱{{ parseFloat(pinnedProduct.price).toFixed(2) }}</p>
                <Link :href="`/product/${pinnedProduct.id}`" class="text-decoration-none small text-primary">
                  <i class="bi bi-box-seam me-1"></i> View Product
                </Link>
              </div>
            </div>
            <button
              type="button"
              class="btn-close position-absolute top-0 end-0 m-2"
              aria-label="Remove"
              @click="removePinnedProduct"
            ></button>
          </div>
        </div>

        <!-- ✉ Input Box -->
        <div class="d-flex align-items-center bg-white rounded-4 p-2 shadow-sm border">
          <textarea
            v-model="newMessage"
            class="form-control border-0 me-2 rounded-3"
            rows="1"
            placeholder="Type a message..."
            required
            style="resize: none; background: transparent;"
          ></textarea>
          <button class="btn btn-primary px-3 rounded-3">
            <i class="bi bi-send-fill"></i>
          </button>
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
import { Head } from '@inertiajs/vue3'

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
.chat-box {
  background: #f8f9fa;
  border-radius: 12px;
}
textarea:focus {
  box-shadow: none;
}
</style>
