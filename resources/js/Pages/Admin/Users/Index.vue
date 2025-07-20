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
    const toast = new bootstrap.Toast(toastRef.value, { delay: 2000 })
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
    <div class="container">
      <h2 class="mb-3"><i class="bi bi-people me-1 text-success"></i> User Management</h2>

      <div v-if="users.length === 0" class="alert alert-info">No users found.</div>

      <table class="table table-hover table-bordered align-middle">
        <thead class="table-light">
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
            <td>{{ user.first_name }} {{ user.last_name }}</td>
            <td>{{ user.email }}</td>
            <td>
              <span :class="['badge', roleBadgeClass(user.role)]">
                {{ user.role }}
              </span>
            </td>
            <td>{{ formatDate(user.created_at) }}</td>
            <td class="text-center">
              <Link :href="`/admin/users/${user.id}`" class="btn btn-sm btn-success me-1"><i class="bi bi-eye me-1 text-white"></i>View</Link>
              <Link :href="`/admin/users/${user.id}/edit`" class="btn btn-sm btn-primary me-1"><i class="bi bi-pencil-square me-1"></i>Edit</Link>
              <button @click="openDeleteModal(user)" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash3 me-1"></i>Delete</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- ✅ Toast -->
    <div
      v-if="successMessage"
      ref="toastRef"
      class="toast align-items-center text-white bg-success position-fixed top-0 start-50 translate-middle-x mt-3 px-3 py-2 rounded"
      role="alert"
      aria-live="assertive"
      aria-atomic="true"
      style="z-index: 9999;"
    >
      <div class="d-flex">
        <div class="toast-body">
          {{ successMessage }}
        </div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
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
        <div class="modal-content">
          <div class="modal-header bg-danger text-white">
            <h5 class="modal-title" id="deleteUserModalLabel">Confirm Delete</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            Are you sure you want to delete
            <strong>{{ selectedUser?.first_name }} {{ selectedUser?.last_name }}</strong>?
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-danger" @click="confirmDelete">Yes, Delete</button>
          </div>
        </div>
      </div>
    </div>
  </AdminDashboardLayout>
</template>
