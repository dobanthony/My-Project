<template>
  <SellerDashboardLayout>
    <div class="container">
      <div v-if="Object.keys(groupedMessages).length">
        <div
          v-for="(conversation, userId) in groupedMessages"
          :key="userId"
          class="border rounded mb-5 p-4 bg-white shadow-sm"
        >
          <h5 class="mb-4">Chat with {{ conversation.firs_name }}</h5>

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
                  :class="message.sender.id === shop.user_id ? 'bg-success text-white' : 'bg-white border text-dark'"
                >
                  <div class="fw-bold small mb-1">
                    {{ message.sender.id === shop.user_id ? 'You' : message.sender.first_name }}
                  </div>
                  <div class="small">{{ message.message }}</div>
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
              <button class="btn btn-success">Send</button>
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
import { reactive, onMounted, onBeforeUnmount } from 'vue'
import { router } from '@inertiajs/vue3'
import { defineProps } from 'vue'

const { shop, messages } = defineProps({
  shop: Object,
  messages: Array
})

const groupedMessages = reactive({})
let interval = null

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
  }, {
    onSuccess: () => {
      conversation.reply = ''
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
})

onBeforeUnmount(() => {
  clearInterval(interval)
})
</script>

<style scoped>
textarea.form-control {
  border-color: #28a745; /* green */
  box-shadow: none;
}
textarea.form-control:focus {
  border-color: #28a745; /* green */
  box-shadow: 0 0 0 0.25rem rgba(40, 167, 69, 0.5); /* green with 50% opacity */
}

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
