<script setup>

import { ref, watch, nextTick } from 'vue'
import { useForm } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'

// Chat messages
const messages = ref([
  { from: 'bot', text: '🤖 Hi! Type something to chat with me.' }
])

// Form for sending message
const form = useForm({ message: '' })

// Reference to chat container for auto-scroll
const chatContainer = ref(null)

// Send message function
async function sendMessage() {
  if (!form.message) return

  // Push user message
  messages.value.push({ from: 'user', text: form.message })

  // Send message to backend
  await form.post(route('botman.handle'), {
    preserveState: true,
    onSuccess: (page) => {
      // Get the bot reply from flash
      const botReply = page.props.flash.botman_reply
      if (botReply) {
        messages.value.push({ from: 'bot', text: botReply })
      }
      form.reset()
    }
  })
}

// Auto-scroll when messages update
watch(messages, async () => {
  await nextTick()
  if (chatContainer.value) {
    chatContainer.value.scrollTop = chatContainer.value.scrollHeight
  }
})
</script>

<template>
    <DashboardLayout>
        <div class="container mt-4">
            <div class="card shadow">
            <div class="card-header bg-primary text-white">
                Chat with BotMan
            </div>

            <div
                class="card-body"
                style="height: 400px; overflow-y: auto;"
                ref="chatContainer"
            >
                <div v-for="(msg, i) in messages" :key="i" class="mb-2">
                <!-- User message -->
                <div v-if="msg.from === 'user'" class="text-end">
                    <span
                    class="badge bg-success p-2"
                    style="white-space: pre-wrap; word-wrap: break-word; display: inline-block; max-width: 100%;"
                    >
                    {{ msg.text }}
                    </span>
                </div>

                <!-- Bot message -->
                <div v-else class="text-start">
                    <span
                    class="badge bg-secondary p-2"
                    style="white-space: pre-wrap; word-wrap: break-word; display: inline-block; max-width: 100%;"
                    >
                    {{ msg.text }}
                    </span>
                </div>
                </div>
            </div>

            <div class="card-footer">
                <form @submit.prevent="sendMessage" class="d-flex">
                <input
                    v-model="form.message"
                    type="text"
                    class="form-control me-2"
                    placeholder="Type a message..."
                />
                <button class="btn btn-primary">Send</button>
                </form>
            </div>
            </div>
        </div>
  </DashboardLayout>
</template>
