<template>
  <Head title="Archived " />
  <AdminDashboardLayout>
    <div class="container py-4">
      <!-- Header -->
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0 d-flex align-items-center">
          <i class="bi bi-archive-fill text-warning me-2"></i> Archived Users
        </h4>
        <a href="/admin/users" class="btn btn-outline-success btn-sm">
          <i class="bi bi-arrow-left me-1"></i> Back to Users
        </a>
      </div>

      <!-- No Users -->
      <div v-if="users.length === 0" class="alert alert-info">
        <i class="bi bi-info-circle me-2"></i> No archived users.
      </div>

      <!-- Table -->
      <div v-else class="table-responsive shadow-sm rounded">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-warning">
            <tr>
              <th>Name</th>
              <th>Email</th>
              <th>Role</th>
              <th>Archived At</th>
              <th class="text-center">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="user in users" :key="user.id">
              <td>{{ user.first_name }} {{ user.last_name }}</td>
              <td>{{ user.email }}</td>
              <td>
                <span class="badge bg-secondary text-capitalize">{{ user.role }}</span>
              </td>
              <td>{{ formatDate(user.deleted_at) }}</td>
              <td class="text-center">
                <button
                  @click="openRestoreModal(user)"
                  class="btn btn-sm btn-outline-success me-1"
                >
                  <i class="bi bi-arrow-clockwise"></i>
                </button>
                <button
                  @click="openDeleteModal(user)"
                  class="btn btn-sm btn-outline-danger"
                >
                  <i class="bi bi-trash3"></i>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- ✅ Restore Modal -->
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
              <i class="bi bi-arrow-clockwise me-2"></i> Restore User
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            Are you sure you want to restore <strong>{{ selectedUser?.first_name }} {{ selectedUser?.last_name }}</strong>?
          </div>
          <div class="modal-footer">
            <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
            <button class="btn btn-success btn-sm" @click="confirmRestore">Yes, Restore</button>
          </div>
        </div>
      </div>
    </div>

    <!-- 🗑️ Delete Modal -->
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
              <i class="bi bi-trash3 me-2"></i> Permanently Delete User
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            This action cannot be undone. Are you sure you want to permanently delete
            <strong>{{ selectedUser?.first_name }} {{ selectedUser?.last_name }}</strong>?
          </div>
          <div class="modal-footer">
            <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
            <button class="btn btn-danger btn-sm" @click="confirmDelete">Yes, Delete</button>
          </div>
        </div>
      </div>
    </div>

    <!-- ✅ Toast -->
    <div
      v-if="successMessage"
      ref="toastRef"
      class="toast align-items-center text-white bg-success position-fixed top-0 start-50 translate-middle-x mt-3 px-3 py-2 rounded shadow"
      style="z-index: 9999;"
      role="alert"
    >
      <div class="d-flex">
        <div class="toast-body">
          <i class="bi bi-check-circle me-2"></i> {{ successMessage }}
        </div>
        <button
          type="button"
          class="btn-close btn-close-white me-2 m-auto"
          data-bs-dismiss="toast"
        ></button>
      </div>
    </div>
  </AdminDashboardLayout>
</template>

<script setup>
import { router, usePage, Head } from '@inertiajs/vue3'
import { ref, onMounted } from 'vue'
import * as bootstrap from 'bootstrap'
import AdminDashboardLayout from '@/Layouts/AdminDashboardLayout.vue'

const props = defineProps({ users: Array })

const toastRef = ref(null)
const successMessage = ref('')
const restoreModalRef = ref(null)
const deleteModalRef = ref(null)
const selectedUser = ref(null)
let restoreModal, deleteModal

onMounted(() => {
  restoreModal = new bootstrap.Modal(restoreModalRef.value)
  deleteModal = new bootstrap.Modal(deleteModalRef.value)

  // flash success (from backend)
  const flashSuccess = usePage().props.flash?.success
  if (flashSuccess) showToast(flashSuccess)
})

function showToast(message) {
  successMessage.value = message
  if (toastRef.value) {
    const toast = new bootstrap.Toast(toastRef.value, { delay: 2500 })
    toast.show()
  }
}

function formatDate(date) {
  const d = new Date(date)
  return d.toLocaleString('en-PH', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
    hour12: true,
  })
}

// ✅ Open Modals
function openRestoreModal(user) {
  selectedUser.value = user
  restoreModal.show()
}
function openDeleteModal(user) {
  selectedUser.value = user
  deleteModal.show()
}

// ✅ Confirm Actions
function confirmRestore() {
  router.post(`/admin/users/${selectedUser.value.id}/restore`, {}, {
    onSuccess: () => {
      showToast('User restored successfully!')
      restoreModal.hide()
    },
  })
}

function confirmDelete() {
  router.delete(`/admin/users/${selectedUser.value.id}/force-delete`, {
    onSuccess: () => {
      showToast('User permanently deleted!')
      deleteModal.hide()
    },
  })
}
</script>
