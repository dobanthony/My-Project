<template>
  <Head title="ChatBot " />
  <DashboardLayout>
    <div class="container py-1">
      <!-- 🧭 Page Header -->
      <div class="mb-4 text-center">
        <h3 class="fw-bold text-primary mb-2">
          <i class="bi bi-chat-dots me-2"></i> CraftSmartBot Chat Assistant
        </h3>
        <p class="text-muted">
          Chat with our smart assistant to explore products, check your orders, or get instant help.
        </p>
      </div>

      <!-- 💬 Chat Card -->
      <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
        <div class="card-header bg-primary text-white d-flex align-items-center">
          <i class="bi bi-robot me-2 fs-5"></i>
          <strong>CraftSmartBot</strong>
        </div>

        <!-- Chat messages -->
        <div ref="chatContainer" class="card-body chat-body bg-light">
          <div
            v-for="(msg, i) in messages"
            :key="i"
            class="chat-message mb-3"
            :class="msg.from"
          >
            <div class="message-bubble shadow-sm">
              {{ msg.text }}
            </div>
          </div>

          <!-- Bot typing indicator -->
          <div v-if="botTyping" class="chat-message bot mb-2">
            <div class="message-bubble typing">
              <i class="bi bi-three-dots"></i> CraftSmartBot is typing...
            </div>
          </div>
        </div>

        <!-- Quick Replies -->
        <div class="p-2 border-top bg-white d-flex flex-wrap gap-2 justify-content-start">
          <button
            v-for="(quick, i) in quickMessages"
            :key="i"
            @click="sendMessage(quick)"
            class="btn btn-outline-primary btn-sm quick-chip"
          >
            <i class="bi bi-lightning-charge me-1"></i>{{ quick }}
          </button>
        </div>

        <!-- Message input -->
        <div class="card-footer bg-white border-top">
          <form @submit="sendMessage(null, $event)" class="d-flex align-items-center">
            <input
              v-model="messageInput"
              type="text"
              class="form-control me-2 rounded-pill"
              placeholder="Type your message..."
            />
            <button class="btn btn-primary rounded-circle px-3">
              <i class="bi bi-send-fill"></i>
            </button>
          </form>
        </div>
      </div>
    </div>
  </DashboardLayout>
</template>

<script setup>
import { ref, watch, nextTick } from 'vue'
import axios from 'axios'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import { Head } from '@inertiajs/vue3'

const messages = ref([
  { from: 'bot', text: '🤖 Hi there! I’m CraftSmartBot. Type something to chat with me!' }
])

const quickMessages = ref([
  'Hi',
  'Help',
  'Cheapest product',
  'Most expensive product',
  'My latest order',
  'Delivery info'
])

const messageInput = ref('')
const chatContainer = ref(null)
const botTyping = ref(false)

async function sendMessage(msg = null, event = null) {
  if (event) event.preventDefault()

  const messageToSend = msg || messageInput.value
  if (!messageToSend) return

  messages.value.push({ from: 'user', text: messageToSend })
  messageInput.value = ''
  botTyping.value = true
  await nextTick()
  chatContainer.value.scrollTop = chatContainer.value.scrollHeight

  try {
    const response = await axios.post(route('botman.handle'), { message: messageToSend })
    const botReply = response.data?.botman_reply || "🤖 Sorry, I couldn't process your message."
    setTimeout(() => {
      messages.value.push({ from: 'bot', text: botReply })
      botTyping.value = false
    }, 600)
  } catch (error) {
    messages.value.push({ from: 'bot', text: "⚠️ An error occurred while sending your message." })
    botTyping.value = false
  }
}

watch(messages, async () => {
  await nextTick()
  if (chatContainer.value) {
    chatContainer.value.scrollTo({
      top: chatContainer.value.scrollHeight,
      behavior: 'smooth'
    })
  }
})
</script>

<style scoped>
.chat-body {
  height: 420px;
  overflow-y: auto;
  padding: 1.25rem;
  background: #f8f9fa;
}

/* Layout for messages */
.chat-message {
  display: flex;
  align-items: flex-end;
}

.chat-message.user {
  justify-content: flex-end;
}

.chat-message.bot {
  justify-content: flex-start;
}

/* Message bubbles */
.message-bubble {
  max-width: 75%;
  padding: 0.7rem 1rem;
  border-radius: 1.25rem;
  word-wrap: break-word;
  white-space: pre-wrap;
  transition: all 0.3s ease;
}

.chat-message.user .message-bubble {
  background: var(--bs-primary);
  color: white;
  border-bottom-right-radius: 0.3rem;
}

.chat-message.bot .message-bubble {
  background: #e9ecef;
  color: #212529;
  border-bottom-left-radius: 0.3rem;
}

/* Typing bubble */
.message-bubble.typing {
  font-style: italic;
  opacity: 0.8;
}

/* Quick reply chips */
.quick-chip {
  border-radius: 50px;
  padding: 0.4rem 0.9rem;
  transition: all 0.25s;
}

.quick-chip:hover {
  background: var(--bs-primary);
  color: #fff;
  border-color: var(--bs-primary);
}

/* Input focus styling */
input.form-control:focus {
  border-color: var(--bs-primary);
  box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}
</style>
