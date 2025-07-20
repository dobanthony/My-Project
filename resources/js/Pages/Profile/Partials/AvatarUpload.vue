<template>
  <div class="mb-6">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">Upload Profile Photo</h2>

    <form @submit.prevent="submitAvatar" enctype="multipart/form-data" class="space-y-4">
      <div class="flex items-center gap-4">
        <img
          :src="previewUrl || avatarUrl"
          :class="avatarClass"
          class="w-20 h-20 rounded-full object-cover"
        />

      <input
        type="file"
        @change="handleFileChange"
        accept="image/*"
        class="text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:bg-green-700 file:text-white hover:file:bg-green-800"
      />
      </div>

      <button
        type="submit"
        :disabled="isUploading"
        class="px-4 py-2 text-white rounded hover:opacity-90 disabled:opacity-50 disabled:cursor-not-allowed"
        style="background-color: #198754;"
      >
        Upload
      </button>

    </form>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import axios from 'axios'

// Get global user and clone it reactively
const page = usePage()
const user = ref({ ...page.props.auth.user })

const avatar = ref(null)
const previewUrl = ref(null)
const isUploading = ref(false)

// Display current avatar from user or fallback
const avatarUrl = computed(() => {
  return user.value.avatar ? `/storage/${user.value.avatar}` : getDefaultAvatar.value
})

// When a file is selected, preview it before upload
const handleFileChange = (e) => {
  const file = e.target.files[0]
  avatar.value = file

  if (file) {
    const reader = new FileReader()
    reader.onload = (e) => {
      previewUrl.value = e.target.result
    }
    reader.readAsDataURL(file)
  }
}

// On submit, upload the file via axios and update global + local user
const submitAvatar = async () => {
  if (!avatar.value) return

  isUploading.value = true
  const formData = new FormData()
  formData.append('avatar', avatar.value)

  try {
    const response = await axios.post(route('profile.avatar'), formData)

    // Update global and local avatar path
    user.value.avatar = response.data.avatar
    page.props.auth.user.avatar = response.data.avatar
    previewUrl.value = null
    avatar.value = null
  } catch (err) {
    console.error(err)
  } finally {
    isUploading.value = false
  }
}

// Default avatar per role
const getDefaultAvatar = computed(() => {
  switch (user.value.role) {
    case 'admin':
      return '/images/default-admin.png'
    case 'seller':
      return '/images/default-seller.png'
    case 'user':
      return '/images/default-avatar.jpg'
    default:
      return '/images/default-avatar.jpg'
  }
})

// Avatar ring styling per role
const avatarClass = computed(() => {
  switch (user.value.role) {
    case 'admin':
      return 'ring-4 ring-red-500'
    case 'seller':
      return 'ring-4 ring-green-500'
    case 'buyer':
      return 'ring-4 ring-blue-500'
    default:
      return 'ring-2 ring-gray-300'
  }
})
</script>
