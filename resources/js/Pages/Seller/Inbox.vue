<template>
  <Head title="Message" />
  <SellerDashboardLayout>
    <div class="inbox-fluid py-1">

      <!-- Page Header -->
      <div class="text-center mb-5">
        <h2 class="fw-bold text-primary mb-2">
          <i class="bi bi-chat-dots-fill me-2"></i> Customer Conversations
        </h2>
        <p class="text-muted mb-0">
          Manage and respond to your customers efficiently. View product-related chats and maintain clear communication.
        </p>
      </div>

      <!-- Conversation List -->
      <div v-if="Object.keys(groupedMessages).length">
        <div
          v-for="(conversation, userId) in groupedMessages"
          :key="userId"
          class="conversation-card-fluid mb-5 p-4 shadow-sm bg-white"
        >
          <div class="d-flex align-items-center justify-content-between mb-3">
            <h5 class="fw-bold text-secondary mb-0">
              <i class="bi bi-person-circle me-2 text-primary"></i>
              Chat with {{ conversation.user_first_name || conversation.user_last_name }}
            </h5>
          </div>

          <!-- Pinned Reported Product -->
          <div v-if="pinnedReportedProduct" class="card mb-4 border-0 shadow-sm bg-light w-100">
            <div class="row g-0 align-items-center p-2">
              <div class="col-auto">
                <img
                  :src="pinnedReportedProduct.image ? `/storage/${pinnedReportedProduct.image}` : 'https://via.placeholder.com/100x100?text=No+Image'"
                  class="img-fluid rounded-3 border"
                  style="width: 80px; height: 80px; object-fit: cover;"
                />
              </div>
              <div class="col ps-3">
                <h6 class="fw-semibold mb-1" :class="pinnedReportedProduct.is_reported ? 'text-danger' : 'text-success'">
                  <i class="bi bi-box-seam me-1"></i>{{ pinnedReportedProduct.name }}
                  <span v-if="pinnedReportedProduct.is_reported" class="badge bg-danger ms-2">⚠ Reported</span>
                </h6>
                <p class="text-muted small mb-1">
                  ₱{{ parseFloat(pinnedReportedProduct.price).toFixed(2) }}
                </p>
                <Link
                  :href="`/product/${pinnedReportedProduct.id}`"
                  class="text-decoration-none small text-primary"
                >
                  <i class="bi bi-search me-1"></i>View Product
                </Link>
              </div>
            </div>
          </div>

          <!-- Chat Messages -->
          <div class="chat-box-fluid p-3 rounded-3 bg-light shadow-sm mb-3 w-100">
            <div
              v-for="(message, index) in conversation.messages"
              :key="message.id"
              class="d-flex mb-3"
              :class="message.sender.id === shop.user_id ? 'justify-content-end' : 'justify-content-start'"
            >
              <div
                class="d-flex flex-column"
                :class="message.sender.id === shop.user_id ? 'align-items-end' : 'align-items-start'"
              >
                <img
                  :src="message.sender.id === shop.user_id
                    ? `/storage/${shop.shop_logo}`
                    : (message.sender.avatar ? `/storage/${message.sender.avatar}` : '/images/default-avatar.jpg')"
                  class="rounded-circle mb-1 border"
                  style="width: 36px; height: 36px; object-fit: cover;"
                />

                <div
                  class="message-bubble-fluid shadow-sm"
                  :class="message.sender.id === shop.user_id ? 'bg-primary text-white' : 'bg-white border text-dark'"
                >
                  <div class="fw-semibold small mb-1">
                    {{ message.sender.id === shop.user_id ? 'You' : message.sender.first_name }}
                  </div>
                  <div class="small">{{ message.message }}</div>
                </div>

                <div v-if="message.product" class="card mt-2 border-0 shadow-sm w-100">
                  <img
                    :src="message.product.image ? `/storage/${message.product.image}` : 'https://via.placeholder.com/100x100?text=No+Image'"
                    class="card-img-top rounded-top"
                    style="height: 120px; object-fit: cover;"
                  />
                  <div class="card-body p-2">
                    <h6 class="card-title mb-1" :class="message.product.is_reported ? 'text-danger' : 'text-success'">
                      <i class="bi bi-box-seam me-1"></i>{{ message.product.name }}
                      <span v-if="message.product.is_reported" class="badge bg-danger ms-2">⚠ Reported</span>
                    </h6>
                    <p class="text-muted small mb-1">₱{{ parseFloat(message.product.price).toFixed(2) }}</p>
                    <Link
                      :href="`/product/${message.product.id}`"
                      class="text-decoration-none small text-primary"
                    >
                      <i class="bi bi-search me-1"></i>View Product
                    </Link>
                  </div>
                </div>

                <div class="text-muted xsmall mt-1">
                  <i class="bi bi-clock me-1"></i>{{ formatTime(message.created_at) }}
                  <span v-if="message.sender.id === shop.user_id">
                    • <i :class="message.is_read ? 'bi bi-check2-all text-success' : 'bi bi-check2 text-muted'"></i>
                    {{ message.is_read ? 'Delivered' : 'Sent' }}
                  </span>
                </div>

                <div
                  v-if="message.sender.id === shop.user_id && isLastOwnMessage(conversation.messages, index) && message.is_read"
                  class="text-success xsmall mt-0"
                >
                  <i class="bi bi-eye-fill me-1"></i> Seen {{ formatSeenTime(message.updated_at || message.created_at) }}
                </div>
              </div>
            </div>
          </div>

          <!-- Reply Form -->
          <form @submit.prevent="sendReply(conversation)" class="mt-3 w-100">
            <div class="input-group">
              <input
                v-model="conversation.reply"
                type="text"
                class="form-control rounded-start-pill"
                placeholder="Type your reply..."
              />
              <button class="btn btn-primary rounded-end-pill px-4" type="submit">
                <i class="bi bi-send-fill me-1"></i>Send
              </button>
            </div>
          </form>
        </div>
      </div>

      <div v-else class="text-center py-5">
        <i class="bi bi-chat-left-text display-4 text-muted mb-3"></i>
        <p class="text-muted fs-5">No messages from customers yet.</p>
      </div>
    </div>
  </SellerDashboardLayout>
</template>

<script setup>
import SellerDashboardLayout from '@/Layouts/SellerDashboardLayout.vue'
import { reactive, ref, onMounted, onBeforeUnmount } from 'vue'
import { router, usePage, Link, Head } from '@inertiajs/vue3'
import { defineProps } from 'vue'

const { shop, messages } = defineProps({
  shop: Object,
  messages: Array,
})

const groupedMessages = reactive({})
let interval = null
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
    if (msg.product) {
      msg.product.is_reported = msg.product.is_reported ?? msg.product.reported ?? msg.is_reported ?? false
    }
    groupedMessages[userId].messages.push(msg)
  })
}
groupAllMessages()

function sendReply(conversation) {
  const messageText = conversation.reply.trim()
  if (!messageText) return

  const temp = messageText
  conversation.reply = ''

  router.post('/messages/send', {
    shop_id: shop.id,
    message: temp,
    receiver_id: conversation.user_id,
    product_id: pinnedReportedProduct.value ? pinnedReportedProduct.value.id : null,
    is_reported: pinnedReportedProduct.value ? pinnedReportedProduct.value.is_reported : false,
  }, {
    onSuccess: () => {
      pinnedReportedProduct.value = null
      router.reload({
        only: ['messages'],
        preserveScroll: true,
        preserveState: true,
        onSuccess: groupAllMessages
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
      onSuccess: groupAllMessages
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
.inbox-fluid {
  width: 100%;
  padding: 0 0.5rem;
  max-width: 100%;
}

.conversation-card-fluid {
  width: 100%;
  border-radius: 0.5rem;
  transition: 0.25s;
  border-left: 5px solid transparent;
}

.conversation-card-fluid:hover {
  border-left-color: var(--bs-primary);
  transform: translateY(-2px);
  box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
}

.chat-box-fluid {
  width: 100%;
  max-height: 500px;
  overflow-y: auto;
  background-color: #f8f9fa;
}

.message-bubble-fluid {
  padding: 0.6rem 0.9rem;
  border-radius: 1rem;
  max-width: 100%; /* allow full width inside chat box */
  word-break: break-word;
}

.xsmall {
  font-size: 0.75rem;
  line-height: 1.2;
}
</style>
