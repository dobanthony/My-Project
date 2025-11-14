<template>
  <Head title="Archive " />
  <AdminDashboardLayout>
    <div class="container">
      <!-- Header -->
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">
          <i class="bi bi-archive text-warning me-2"></i> Archived Announcements
        </h4>
        <a href="/admin/announcements" class="btn btn-outline-success btn-sm">
          <i class="bi bi-arrow-left me-1"></i> Back to Active
        </a>
      </div>

      <!-- List -->
      <div v-if="announcements.length" class="list-group shadow-sm">
        <div
          v-for="announcement in announcements"
          :key="announcement.id"
          class="list-group-item d-flex justify-content-between align-items-start"
        >
          <div class="me-3">
            <h5 class="mb-1 text-warning">{{ announcement.title }}</h5>
            <p class="mb-1 text-muted">{{ announcement.content }}</p>
            <small class="text-secondary">
              <i class="bi bi-clock text-warning me-1"></i>
              Archived: {{ formatDate(announcement.deleted_at) }}
            </small>
          </div>
          <div class="btn-group align-self-center">
            <button
              class="btn btn-sm btn-success"
              @click="openModal('restore', announcement)"
            >
              <i class="bi bi-arrow-counterclockwise me-1"></i>
            </button>
            <button
              class="btn btn-sm btn-danger"
              @click="openModal('delete', announcement)"
            >
              <i class="bi bi-trash3 me-1"></i>
            </button>
          </div>
        </div>
      </div>
      <p v-else class="text-muted text-center">No archived announcements found.</p>
    </div>

    <!-- 🟢 Restore Modal -->
    <div
      class="modal fade"
      id="restoreModal"
      tabindex="-1"
      aria-labelledby="restoreModalLabel"
      aria-hidden="true"
      ref="restoreModalRef"
    >
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header bg-success text-white">
            <h5 class="modal-title" id="restoreModalLabel">
              <i class="bi bi-arrow-counterclockwise me-2"></i>Restore Announcement
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            Are you sure you want to restore
            <strong>{{ selectedAnnouncement?.title }}</strong>?
          </div>
          <div class="modal-footer">
            <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button class="btn btn-success" @click="confirmRestore">Restore</button>
          </div>
        </div>
      </div>
    </div>

    <!-- 🔴 Delete Modal -->
    <div
      class="modal fade"
      id="deleteModal"
      tabindex="-1"
      aria-labelledby="deleteModalLabel"
      aria-hidden="true"
      ref="deleteModalRef"
    >
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header bg-danger text-white">
            <h5 class="modal-title" id="deleteModalLabel">
              <i class="bi bi-trash3 me-2"></i>Permanent Delete
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            This will permanently delete
            <strong>{{ selectedAnnouncement?.title }}</strong>.
            <br />
            <span class="text-danger">This action cannot be undone!</span>
          </div>
          <div class="modal-footer">
            <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button class="btn btn-danger" @click="confirmDelete">Delete</button>
          </div>
        </div>
      </div>
    </div>

    <!-- ✅ Toast Notification -->
    <div
      class="toast-container position-fixed top-0 end-0 p-3"
      style="z-index: 1100"
    >
      <div
        id="successToast"
        class="toast align-items-center text-bg-success border-0"
        role="alert"
        aria-live="assertive"
        aria-atomic="true"
        ref="toastRef"
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
import { ref } from 'vue'
import { router, Head } from '@inertiajs/vue3'
import * as bootstrap from 'bootstrap'

const props = defineProps({
  announcements: Array
})

const selectedAnnouncement = ref(null)
const restoreModalRef = ref(null)
const deleteModalRef = ref(null)
const toastRef = ref(null)
const toastMessage = ref('')
let restoreModal, deleteModal, toastInstance

function openModal(type, announcement) {
  selectedAnnouncement.value = announcement
  if (type === 'restore') {
    restoreModal = new bootstrap.Modal(restoreModalRef.value)
    restoreModal.show()
  } else if (type === 'delete') {
    deleteModal = new bootstrap.Modal(deleteModalRef.value)
    deleteModal.show()
  }
}

function confirmRestore() {
  router.put(`/admin/announcements/${selectedAnnouncement.value.id}/restore`, {}, {
    onSuccess: () => {
      showToast('Announcement restored successfully!')
      restoreModal.hide()
    }
  })
}

function confirmDelete() {
  router.delete(`/admin/announcements/${selectedAnnouncement.value.id}/force`, {
    onSuccess: () => {
      showToast('Announcement permanently deleted!')
      deleteModal.hide()
    }
  })
}

function formatDate(datetime) {
  return new Date(datetime).toLocaleString('en-PH', {
    dateStyle: 'medium',
    timeStyle: 'short'
  })
}

function showToast(message) {
  toastMessage.value = message
  toastInstance = new bootstrap.Toast(toastRef.value)
  toastInstance.show()
}
</script>

<style scoped>
.container {
  max-width: 800px;
}
.list-group-item {
  transition: background-color 0.2s ease;
}
.list-group-item:hover {
  background-color: #fffaf2;
}
</style>
