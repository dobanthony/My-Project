<template>
  <div class="card shadow-sm border-0 mb-4">
    <div class="card-body text-center">
      <h2 class="h5 mb-3 text-dark">Upload Profile Photo</h2>

      <!-- Avatar Container -->
      <div class="position-relative d-inline-block mb-3">
        <label for="avatarInput" class="cursor-pointer">
          <img
            :src="previewUrl || avatarUrl"
            :class="avatarClass"
            class="rounded-circle object-fit-cover shadow-sm"
            style="width: 120px; height: 120px; cursor: pointer; transition: 0.3s;"
            title="Click to change photo"
          />
          <!-- Camera Icon Overlay -->
          <div
            class="position-absolute top-50 start-50 translate-middle bg-dark bg-opacity-50 text-white rounded-circle d-flex justify-content-center align-items-center"
            style="width: 40px; height: 40px; opacity: 0; transition: opacity 0.3s;"
          >
            <i class="bi bi-camera-fill"></i>
          </div>
        </label>
        <!-- Hidden File Input -->
        <input
          id="avatarInput"
          type="file"
          @change="handleFileChange"
          accept="image/*"
          class="d-none"
        />
      </div>

      <!-- Buttons -->
      <div class="d-flex justify-content-center gap-2">
        <button
          type="button"
          class="btn btn-outline-danger"
          @click="resetAvatar"
          :disabled="isUploading"
        >
          <i class="bi bi-x-circle me-1"></i> Remove
        </button>

        <button
          type="button"
          @click="submitAvatar"
          :disabled="isUploading || !avatar"
          class="btn btn-success"
        >
          <span v-if="isUploading" class="spinner-border spinner-border-sm me-2"></span>
          <i class="bi bi-upload me-1"></i> Upload
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import axios from 'axios'

const page = usePage()
const user = ref({ ...page.props.auth.user })

const avatar = ref(null)
const previewUrl = ref(null)
const isUploading = ref(false)

// Display current avatar from user or fallback
const avatarUrl = computed(() => {
  return user.value.avatar ? `/storage/${user.value.avatar}` : getDefaultAvatar.value
})

// File selection
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

// Upload to server
const submitAvatar = async () => {
  if (!avatar.value) return

  isUploading.value = true
  const formData = new FormData()
  formData.append('avatar', avatar.value)

  try {
    const response = await axios.post(route('profile.avatar'), formData)

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

// Reset to default
const resetAvatar = () => {
  previewUrl.value = null
  avatar.value = null
}

// Default avatar
const getDefaultAvatar = computed(() => '/images/default-avatar.jpg')

// Avatar border by role
const avatarClass = computed(() => {
  switch (user.value.role) {
    case 'admin':
      return 'border border-4 border-danger'
    case 'seller':
      return 'border border-4 border-success'
    case 'buyer':
      return 'border border-4 border-primary'
    default:
      return 'border border-3 border-secondary'
  }
})
</script>

<style scoped>
/* Hover effect for camera overlay */
label:hover div {
  opacity: 1 !important;
}
</style>
