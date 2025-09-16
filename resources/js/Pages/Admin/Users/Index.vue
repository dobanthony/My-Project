<script setup>
import { ref, onMounted } from 'vue'
import { Link, router, usePage } from '@inertiajs/vue3'
import AdminDashboardLayout from '@/Layouts/AdminDashboardLayout.vue'
import * as bootstrap from 'bootstrap'

const props = defineProps({
  users: Array,
})

// ✅ Toast logic
const toastRef = ref(null)
const successMessage = usePage().props.flash?.success || ''
onMounted(() => {
  if (successMessage && toastRef.value) {
    const toast = new bootstrap.Toast(toastRef.value, { delay: 2500 })
    toast.show()
  }
})

// ✅ Delete modal logic
const selectedUser = ref({})
const deleteModal = ref(null)

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

function roleBadgeClass(role) {
  switch (role) {
    case 'admin': return 'bg-success'
    case 'seller': return 'bg-danger'
    case 'user': return 'bg-primary'
    default: return 'bg-secondary'
  }
}

function openDeleteModal(user) {
  selectedUser.value = user
  const modal = new bootstrap.Modal(deleteModal.value)
  modal.show()
}

function confirmDelete() {
  if (selectedUser.value && selectedUser.value.id) {
    router.delete(`/admin/users/${selectedUser.value.id}`, {
      onFinish: () => {
        const modal = bootstrap.Modal.getInstance(deleteModal.value)
        modal.hide()
      },
    })
  }
}
</script>

<template>
  <AdminDashboardLayout>
    <div class="container py-4">
      <!-- Page Title -->
      <h2 class="mb-4 fw-bold d-flex align-items-center">
        <i class="bi bi-people-fill me-2 text-success"></i>
        User Management
      </h2>

      <!-- No users -->
      <div v-if="users.length === 0" class="alert alert-info shadow-sm">
        <i class="bi bi-info-circle me-2"></i> No users found.
      </div>

      <!-- Responsive Table -->
      <div class="table-responsive shadow-sm rounded">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-success">
            <tr>
              <th>Name</th>
              <th>Email</th>
              <th>Role</th>
              <th>Registered</th>
              <th class="text-center">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="user in users" :key="user.id">
              <td>
                <i class="bi bi-person-circle me-1 text-muted"></i>
                {{ user.first_name }} {{ user.last_name }}
              </td>
              <td>{{ user.email }}</td>
              <td>
                <span :class="['badge px-3 py-2 text-capitalize', roleBadgeClass(user.role)]">
                  {{ user.role }}
                </span>
              </td>
              <td>{{ formatDate(user.created_at) }}</td>
              <td class="text-center">
                <Link
                  :href="`/admin/users/${user.id}`"
                  class="btn btn-sm btn-success me-1"
                >
                  <i class="bi bi-eye text-white"></i>
                </Link>
                <Link
                  :href="`/admin/users/${user.id}/edit`"
                  class="btn btn-sm btn-primary me-1"
                >
                  <i class="bi bi-pencil-square"></i>
                </Link>
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

    <!-- ✅ Toast -->
    <div
      v-if="successMessage"
      ref="toastRef"
      class="toast align-items-center text-white bg-success shadow-lg position-fixed top-0 start-50 translate-middle-x mt-3 px-3 py-2 rounded"
      role="alert"
      aria-live="assertive"
      aria-atomic="true"
      style="z-index: 9999;"
    >
      <div class="d-flex">
        <div class="toast-body">
          <i class="bi bi-check-circle me-2"></i> {{ successMessage }}
        </div>
        <button
          type="button"
          class="btn-close btn-close-white me-2 m-auto"
          data-bs-dismiss="toast"
          aria-label="Close"
        ></button>
      </div>
    </div>

    <!-- ✅ Delete Confirmation Modal -->
    <div
      class="modal fade"
      id="deleteUserModal"
      tabindex="-1"
      aria-labelledby="deleteUserModalLabel"
      aria-hidden="true"
      ref="deleteModal"
    >
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg">
          <div class="modal-header bg-danger text-white">
            <h5 class="modal-title" id="deleteUserModalLabel">
              <i class="bi bi-exclamation-triangle me-2"></i> Confirm Delete
            </h5>
            <button
              type="button"
              class="btn-close btn-close-white"
              data-bs-dismiss="modal"
              aria-label="Close"
            ></button>
          </div>
          <div class="modal-body">
            Are you sure you want to delete
            <strong>{{ selectedUser?.first_name }} {{ selectedUser?.last_name }}</strong>
            (<small>{{ selectedUser?.email }}</small>)?
          </div>
          <div class="modal-footer">
            <button
              type="button"
              class="btn btn-secondary"
              data-bs-dismiss="modal"
            >
              Cancel
            </button>
            <button
              type="button"
              class="btn btn-danger"
              @click="confirmDelete"
            >
              <i class="bi bi-trash me-1"></i> Yes, Delete
            </button>
          </div>
        </div>
      </div>
    </div>
  </AdminDashboardLayout>
</template>
