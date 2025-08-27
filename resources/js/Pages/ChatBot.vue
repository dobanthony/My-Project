<script setup>
import { ref, watch, nextTick } from 'vue'
import axios from 'axios'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'

const messages = ref([
  { from: 'bot', text: '🤖 Hi! Type something to chat with me.' }
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

/**
 * Send a message to the bot.
 * @param {string|null} msg Optional message override (for quick messages)
 * @param {Event|null} event Optional event (for form submit)
 */
async function sendMessage(msg = null, event = null) {
  if (event) event.preventDefault(); // prevent form submit reload

  const messageToSend = msg || messageInput.value
  if (!messageToSend) return

  // Add user message to chat
  messages.value.push({ from: 'user', text: messageToSend })
  messageInput.value = ''

  // Show bot typing indicator
  botTyping.value = true
  await nextTick()
  chatContainer.value.scrollTop = chatContainer.value.scrollHeight

  try {
    const response = await axios.post(route('botman.handle'), { message: messageToSend })
    const botReply = response.data?.botman_reply || "🤖 Sorry, I couldn't process your message."

    // Add a slight delay to simulate typing
    setTimeout(() => {
      messages.value.push({ from: 'bot', text: botReply })
      botTyping.value = false
    }, 600)
  } catch (error) {
    messages.value.push({ from: 'bot', text: "⚠️ An error occurred while sending your message." })
    botTyping.value = false
  }
}

// Auto-scroll chat when new messages appear
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

<template>
  <DashboardLayout>
    <div class="container">
      <div class="card shadow rounded-3">
        <div class="card-header bg-success text-white rounded-top">
          <strong>Chat with BotMan</strong>
        </div>

        <!-- Chat messages -->
        <div
          class="card-body chat-body"
          ref="chatContainer"
        >
          <div v-for="(msg, i) in messages" :key="i" class="chat-message mb-2" :class="msg.from">
            <div class="message-bubble">
              {{ msg.text }}
            </div>
          </div>

          <!-- Bot typing indicator -->
          <div v-if="botTyping" class="chat-message bot mb-2">
            <div class="message-bubble typing">
              🤖 Bot is typing...
            </div>
          </div>
        </div>

        <!-- Quick messages toolbar -->
        <div class="p-2 border-top d-flex flex-wrap gap-2">
          <button
            v-for="(quick, i) in quickMessages"
            :key="i"
            @click="sendMessage(quick)"
            class="btn btn-outline-success btn-sm quick-chip"
          >
            {{ quick }}
          </button>
        </div>

        <!-- Message input -->
        <div class="card-footer">
          <form @submit="sendMessage(null, $event)" class="d-flex">
            <input
              v-model="messageInput"
              type="text"
              class="form-control me-2"
              placeholder="Type a message..."
            />
            <button class="btn btn-success">Send</button>
          </form>
        </div>
      </div>
    </div>
  </DashboardLayout>
</template>

<style scoped>
.chat-body {
  height: 400px;
  overflow-y: auto;
  padding: 1rem;
  background: #f8f9fa;
}

.chat-message {
  display: flex;
}

.chat-message.user {
  justify-content: flex-end;
}

.chat-message.bot {
  justify-content: flex-start;
}

.message-bubble {
  max-width: 70%;
  padding: 0.6rem 1rem;
  border-radius: 20px;
  background: #e2e3e5;
  word-wrap: break-word;
  white-space: pre-wrap;
  transition: all 0.3s ease;
}

.chat-message.user .message-bubble {
  background: #28a745;
  color: white;
}

.message-bubble.typing {
  font-style: italic;
  opacity: 0.7;
}

.quick-chip {
  border-radius: 20px;
  padding: 0.25rem 0.75rem;
  transition: all 0.2s;
}

.quick-chip:hover {
  background: #28a745;
  color: white;
  border-color: #28a745;
}

input.form-control:focus {
  border-color: #28a745;
  box-shadow: 0 0 0 0.25rem rgba(40, 167, 69, 0.25);
}
</style>
