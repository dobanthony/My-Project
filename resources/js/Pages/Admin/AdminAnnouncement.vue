<template>
  <AdminDashboardLayout>
    <div class="container">
      <!-- Header -->
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">
          <i class="bi bi-megaphone text-success me-2"></i> Announcements
        </h4>
        <a href="/admin/announcements/archived" class="btn btn-outline-secondary btn-sm">
          <i class="bi bi-archive me-1"></i> View Archives
        </a>
      </div>

      <!-- Create/Update Form -->
      <form @submit.prevent="submitForm" class="card card-body shadow-sm mb-5">
        <h5 class="mb-3">{{ form.id ? 'Update' : 'Create' }} Announcement</h5>

        <div class="mb-3">
          <label class="form-label fw-semibold">Title</label>
          <input
            v-model="form.title"
            type="text"
            class="form-control"
            placeholder="Enter title..."
            required
          />
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
            <small class="text-secondary">
              <i class="bi bi-clock text-success me-1"></i>
              {{ formatDate(announcement.created_at) }}
            </small>
          </div>

          <div class="btn-group align-self-center">
            <button class="btn btn-sm btn-primary" @click="edit(announcement)">
              <i class="bi bi-pencil-square me-1"></i>
            </button>
            <button
              class="btn btn-sm btn-outline-warning"
              @click="openArchiveModal(announcement)"
            >
              <i class="bi bi-archive me-1"></i>
            </button>
          </div>
        </div>
      </div>

      <p v-else class="text-muted text-center">No announcements found.</p>
    </div>

    <!-- 🟡 Archive Confirmation Modal -->
    <div
      class="modal fade"
      id="archiveModal"
      tabindex="-1"
      aria-labelledby="archiveModalLabel"
      aria-hidden="true"
      ref="archiveModalRef"
    >
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header bg-warning text-white">
            <h5 class="modal-title" id="archiveModalLabel">
              <i class="bi bi-archive me-2"></i> Archive Announcement
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            Are you sure you want to archive <strong>{{ selectedAnnouncement?.title }}</strong>?
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
              Cancel
            </button>
            <button type="button" class="btn btn-warning" @click="confirmArchive">
              Yes, Archive
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- ✅ Toast Notification -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3">
      <div
        ref="toastRef"
        class="toast align-items-center text-bg-success border-0"
        role="alert"
        aria-live="assertive"
        aria-atomic="true"
      >
        <div class="d-flex">
          <div class="toast-body">{{ toastMessage }}</div>
          <button
            type="button"
            class="btn-close btn-close-white me-2 m-auto"
            data-bs-dismiss="toast"
          ></button>
        </div>
      </div>
    </div>
  </AdminDashboardLayout>
</template>

<script setup>
import AdminDashboardLayout from '@/Layouts/AdminDashboardLayout.vue'
import { ref, reactive } from 'vue'
import { router } from '@inertiajs/vue3'
import * as bootstrap from 'bootstrap'

const props = defineProps({
  announcements: Array
})

const form = reactive({
  id: null,
  title: '',
  content: ''
})

const submitting = ref(false)
const selectedAnnouncement = ref(null)
const archiveModalRef = ref(null)
const toastRef = ref(null)
const toastMessage = ref('')

// ✅ Bootstrap modal instance
let archiveModal
let toastInstance

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
    onSuccess: () => {
      resetForm()
      showToast(form.id ? 'Announcement updated successfully!' : 'Announcement created successfully!')
    },
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

function openArchiveModal(announcement) {
  selectedAnnouncement.value = announcement
  if (!archiveModal) {
    archiveModal = new bootstrap.Modal(archiveModalRef.value)
  }
  archiveModal.show()
}

function confirmArchive() {
  router.delete(`/admin/announcements/${selectedAnnouncement.value.id}`, {
    onSuccess: () => {
      archiveModal.hide()
      showToast('Announcement archived successfully!')
    }
  })
}

function resetForm() {
  form.id = null
  form.title = ''
  form.content = ''
}

function showToast(message) {
  toastMessage.value = message
  if (!toastInstance) {
    toastInstance = new bootstrap.Toast(toastRef.value)
  }
  toastInstance.show()
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

input.form-control:focus,
textarea.form-control:focus {
  border-color: #28a745;
  box-shadow: 0 0 0 0.25rem rgba(40, 167, 69, 0.5);
}

.toast-container {
  z-index: 2000;
}
</style>
