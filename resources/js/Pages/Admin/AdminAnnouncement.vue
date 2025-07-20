<template>
<AdminDashboardLayout>
  <div class="container">
    <h4 class="mb-4"><i class="bi bi-megaphone text-success me-2"></i> Announcements</h4>

    <!-- Create/Update Announcement Form -->
    <form @submit.prevent="submitForm" class="card card-body shadow-sm mb-5">
      <h5 class="mb-3">{{ form.id ? ' Update' : ' Create' }} Announcement</h5>
      <div class="mb-3">
        <label class="form-label fw-semibold">Title</label>
        <input v-model="form.title" type="text" class="form-control" placeholder="Enter title..." required />
      </div>
      <div class="mb-3">
        <label class="form-label fw-semibold">Content</label>
        <textarea
          v-model="form.content"
          class="form-control"
          rows="4"
          placeholder="Enter content..."
          required
        ></textarea>
      </div>
      <div class="d-flex align-items-center gap-2">
        <button type="submit" class="btn btn-success" :disabled="submitting">
          <span v-if="submitting">
            <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
            Submitting...
          </span>
          <span v-else>
            {{ form.id ? 'Update' : 'Create' }}
          </span>
        </button>
        <button
          v-if="form.id"
          type="button"
          class="btn btn-secondary"
          @click="resetForm"
          :disabled="submitting"
        >
          Cancel
        </button>
      </div>
    </form>

    <!-- Announcement List -->
    <div v-if="announcements.length" class="list-group shadow-sm">
      <div
        v-for="announcement in announcements"
        :key="announcement.id"
        class="list-group-item list-group-item-action d-flex justify-content-between align-items-start"
      >
        <div class="me-3">
          <h5 class="mb-1 text-success">{{ announcement.title }}</h5>
          <p class="mb-1 text-muted">{{ announcement.content }}</p>
          <small class="text-secondary"><i class="bi bi-clock text-success me-1"></i> {{ formatDate(announcement.created_at) }}</small>
        </div>
        <div class="btn-group align-self-center">
          <button class="btn btn-sm btn-primary" @click="edit(announcement)"><i class="bi bi-pencil-square me-1"> </i></button>
          <button class="btn btn-sm btn-outline-danger" @click="destroy(announcement.id)"><i class="bi bi-trash3 me-1"></i></button>
        </div>
      </div>
    </div>
    <p v-else class="text-muted text-center">No announcements found.</p>
  </div>
</AdminDashboardLayout>
</template>

<script setup>
import AdminDashboardLayout from '@/Layouts/AdminDashboardLayout.vue'
import { ref, reactive } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
  announcements: Array
})

const form = reactive({
  id: null,
  title: '',
  content: ''
})

const submitting = ref(false)

function submitForm() {
  submitting.value = true

  const payload = {
    title: form.title,
    content: form.content
  }

  const routeUrl = form.id
    ? `/admin/announcements/${form.id}`
    : '/admin/announcements'

  const method = form.id ? 'put' : 'post'

  router[method](routeUrl, payload, {
    onSuccess: resetForm,
    onFinish: () => {
      submitting.value = false
    }
  })
}

function edit(announcement) {
  form.id = announcement.id
  form.title = announcement.title
  form.content = announcement.content
}

function destroy(id) {
  if (confirm('Are you sure you want to delete this announcement?')) {
    router.delete(`/admin/announcements/${id}`)
  }
}

function resetForm() {
  form.id = null
  form.title = ''
  form.content = ''
}

function formatDate(datetime) {
  return new Date(datetime).toLocaleString('en-PH', {
    dateStyle: 'medium',
    timeStyle: 'short'
  })
}
</script>

<style scoped>
.container {
  max-width: 800px;
}

input.form-control:focus {
  border-color: #28a745; 
  box-shadow: 0 0 0 0.25rem rgba(40, 167, 69, 0.5); 
}

textarea.form-control {
  border-color: #28a745; 
  box-shadow: none;
}

textarea.form-control:focus {
  border-color: #28a745; 
  box-shadow: 0 0 0 0.25rem rgba(40, 167, 69, 0.5); 
}
</style>
