<template>
  <SellerDashboardLayout>
    <div class="container">
      <div v-if="Object.keys(groupedMessages).length">
        <div
          v-for="(conversation, userId) in groupedMessages"
          :key="userId"
          class="border rounded mb-5 p-4 bg-white shadow-sm"
        >
          <h5 class="mb-4">Chat with {{ conversation.user_name }}</h5>

          <!-- 📌 Auto-pinned Reported Product -->
          <div v-if="pinnedReportedProduct" class="card mb-3 border bg-light shadow-sm">
            <div class="row g-0 align-items-center">
              <div class="col-auto">
                <img
                  :src="pinnedReportedProduct.image ? `/storage/${pinnedReportedProduct.image}` : 'https://via.placeholder.com/100x100?text=No+Image'"
                  class="img-fluid rounded-start"
                  style="width: 80px; height: 80px; object-fit: cover;"
                />
              </div>
              <div class="col">
                <div class="card-body py-2">
                  <h6 class="card-title mb-1" :class="pinnedReportedProduct.is_reported ? 'text-danger' : 'text-success'">
                    {{ pinnedReportedProduct.name }}
                    <span v-if="pinnedReportedProduct.is_reported" class="badge bg-danger ms-2">⚠ Reported</span>
                  </h6>
                  <p class="card-text text-muted small mb-1">
                    ₱{{ parseFloat(pinnedReportedProduct.price).toFixed(2) }}
                  </p>
                  <Link :href="`/product/${pinnedReportedProduct.id}`" class="text-decoration-none small">
                    🔍 View Product
                  </Link>
                </div>
              </div>
            </div>
          </div>

          <!-- 💬 Chat Box -->
          <div class="chat-box mb-3 p-3 bg-light rounded overflow-auto">
            <div
              v-for="(message, index) in conversation.messages"
              :key="message.id"
              class="d-flex mb-2"
              :class="message.sender.id === shop.user_id ? 'justify-content-end' : 'justify-content-start'"
            >
              <div
                class="d-flex flex-column"
                :class="message.sender.id === shop.user_id ? 'align-items-end' : 'align-items-start'"
              >
                <!-- Avatar -->
                <img
                  :src="message.sender.id === shop.user_id
                    ? `/storage/${shop.shop_logo}`
                    : (message.sender.avatar ? `/storage/${message.sender.avatar}` : '/images/default-avatar.jpg')"
                  class="rounded-circle mb-1"
                  style="width: 32px; height: 32px; object-fit: cover;"
                />

                <!-- Message Bubble -->
                <div
                  class="message-bubble"
                  :class="message.sender.id === shop.user_id ? 'bg-primary text-white' : 'bg-white border text-dark'"
                >
                  <div class="fw-bold small mb-1">
                    {{ message.sender.id === shop.user_id ? 'You' : message.sender.first_name }}
                  </div>
                  <div class="small">{{ message.message }}</div>
                </div>

                <!-- 🛒 Product inside message -->
                <div
                  v-if="message.product"
                  class="card mt-2 border"
                  style="max-width: 250px;"
                >
                  <img
                    :src="message.product.image ? `/storage/${message.product.image}` : 'https://via.placeholder.com/100x100?text=No+Image'"
                    class="card-img-top"
                    style="height: 120px; object-fit: cover;"
                  />
                  <div class="card-body p-2">
                    <h6 class="card-title mb-1" :class="message.product.is_reported ? 'text-danger' : 'text-success'">
                      {{ message.product.name }}
                      <span v-if="message.product.is_reported" class="badge bg-danger ms-2">⚠ Reported</span>
                    </h6>
                    <p class="text-muted small mb-1">₱{{ parseFloat(message.product.price).toFixed(2) }}</p>
                    <Link :href="`/product/${message.product.id}`" class="text-decoration-none small">🔍 View Product</Link>
                  </div>
                </div>

                <!-- Timestamp & Read Status -->
                <div class="text-muted xsmall mt-1">
                  {{ formatTime(message.created_at) }}
                  <span v-if="message.sender.id === shop.user_id">
                    • {{ message.is_read ? 'Delivered' : 'Sent' }}
                  </span>
                </div>

                <!-- Seen Indicator -->
                <div
                  v-if="message.sender.id === shop.user_id && isLastOwnMessage(conversation.messages, index) && message.is_read"
                  class="text-success xsmall mt-0"
                >
                  <i class="bi bi-check2-all me-1"></i> Seen {{ formatSeenTime(message.updated_at || message.created_at) }}
                </div>
              </div>
            </div>
          </div>

          <!-- ✉️ Reply Form -->
          <form @submit.prevent="sendReply(conversation)">
            <div class="d-flex mt-2">
              <input
                v-model="conversation.reply"
                type="text"
                class="form-control me-2"
                placeholder="Type your reply..."
              />
              <button class="btn btn-primary">Send</button>
            </div>
          </form>
        </div>
      </div>

      <div v-else>
        <p class="text-muted">No messages from customers yet.</p>
      </div>
    </div>
  </SellerDashboardLayout>
</template>

<script setup>
import SellerDashboardLayout from '@/Layouts/SellerDashboardLayout.vue'
import { reactive, ref, onMounted, onBeforeUnmount } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import { defineProps } from 'vue'

const { shop, messages } = defineProps({
  shop: Object,
  messages: Array,
})

const groupedMessages = reactive({})
let interval = null

// 📌 For reported product pinning
const pinnedReportedProduct = ref(null)
const page = usePage()

function groupAllMessages() {
  const prevReplies = {}

  for (const userId in groupedMessages) {
    prevReplies[userId] = groupedMessages[userId].reply
  }

  Object.keys(groupedMessages).forEach((key) => delete groupedMessages[key])

  messages.forEach((msg) => {
    const userId = msg.sender.id !== shop.user_id ? msg.sender.id : msg.receiver.id

    if (!groupedMessages[userId]) {
      groupedMessages[userId] = {
        user_id: userId,
        user_name: msg.sender.id !== shop.user_id ? msg.sender.name : msg.receiver.name,
        messages: [],
        reply: prevReplies[userId] || '',
      }
    }

    // ✅ Normalize reported flag into `is_reported`
    if (msg.product) {
      msg.product.is_reported = msg.product.is_reported ?? msg.product.reported ?? msg.is_reported ?? false
    }

    groupedMessages[userId].messages.push(msg)
  })
}
groupAllMessages()

function sendReply(conversation) {
  if (!conversation.reply.trim()) return

  router.post('/messages/send', {
    shop_id: shop.id,
    message: conversation.reply,
    receiver_id: conversation.user_id,
    product_id: pinnedReportedProduct.value ? pinnedReportedProduct.value.id : null,
    is_reported: pinnedReportedProduct.value ? pinnedReportedProduct.value.is_reported : false, // ✅ send normalized field
  }, {
    onSuccess: () => {
      conversation.reply = ''
      pinnedReportedProduct.value = null
      router.reload({
        only: ['messages'],
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => groupAllMessages()
      })
    }
  })
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

function isLastOwnMessage(messages, index) {
  const ownMessages = messages.filter(m => m.sender.id === shop.user_id)
  return ownMessages.length && messages[index].id === ownMessages.at(-1).id
}

onMounted(() => {
  interval = setInterval(() => {
    router.reload({
      only: ['messages'],
      preserveScroll: true,
      preserveState: true,
      onSuccess: () => groupAllMessages()
    })
  }, 3000)

  const urlParams = new URLSearchParams(window.location.search)
  const reportedProductId = urlParams.get('product_id')
  const isReported = urlParams.get('is_reported') === '1' || urlParams.get('reported') === '1'

  if (reportedProductId) {
    const product = messages.find(m => m.product && m.product.id == reportedProductId)?.product
    if (product) {
      pinnedReportedProduct.value = { ...product, is_reported: isReported || product.is_reported || product.reported }
    }
  }
})

onBeforeUnmount(() => {
  clearInterval(interval)
})
</script>

<style scoped>
.chat-box {
  max-height: 400px;
  overflow-y: auto;
  padding-bottom: 1rem;
  background-color: #f8f9fa;
}

.message-bubble {
  padding: 0.5rem 0.75rem;
  border-radius: 15px;
  word-wrap: break-word;
  word-break: break-word;
  max-width: 90%;
  min-width: 80px;
  width: fit-content;
}

.xsmall {
  font-size: 0.725rem;
  line-height: 1.2;
}
</style>
