<template>
  <Head title="Users " />
  <AdminDashboardLayout>
    <div class="container py-4">
      <!-- Page Title -->
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold d-flex align-items-center mb-0">
          <i class="bi bi-people-fill me-2 text-success"></i>
          User Management
        </h2>
        <Link
          href="/admin/users/archived"
          class="btn btn-outline-secondary btn-sm"
        >
          <i class="bi bi-archive-fill me-1"></i>
          View Archived Users
        </Link>
      </div>

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
                  {{ displayRole(user.role) }}
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
                <!-- ✅ Archive Button -->
                <button
                  @click="openArchiveModal(user)"
                  class="btn btn-sm btn-outline-warning"
                >
                  <i class="bi bi-archive"></i>
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

    <!-- ✅ Archive Confirmation Modal -->
    <div
      class="modal fade"
      id="archiveUserModal"
      tabindex="-1"
      aria-labelledby="archiveUserModalLabel"
      aria-hidden="true"
      ref="archiveModal"
    >
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg">
          <div class="modal-header bg-warning text-dark">
            <h5 class="modal-title" id="archiveUserModalLabel">
              <i class="bi bi-archive me-2"></i> Confirm Archive
            </h5>
            <button
              type="button"
              class="btn-close"
              data-bs-dismiss="modal"
              aria-label="Close"
            ></button>
          </div>
          <div class="modal-body">
            Are you sure you want to archive
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
              class="btn btn-warning text-dark"
              @click="confirmArchive"
            >
              <i class="bi bi-archive me-1"></i> Yes, Archive
            </button>
          </div>
        </div>
      </div>
    </div>
  </AdminDashboardLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { Link, router, usePage, Head } from '@inertiajs/vue3'
import AdminDashboardLayout from '@/Layouts/AdminDashboardLayout.vue'
import * as bootstrap from 'bootstrap'

const props = defineProps({
  users: Array,
})

// ✅ Toast logic
const toastRef = ref(null)
const successMessage = ref(usePage().props.flash?.success || '') // 🟩 make reactive
onMounted(() => {
  if (successMessage.value && toastRef.value) {
    const toast = new bootstrap.Toast(toastRef.value, { delay: 2500 })
    toast.show()
  }
})

// ✅ Archive modal logic
const selectedUser = ref({})
const archiveModal = ref(null)

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

function displayRole(role) {
  return role === 'user' ? 'buyer' : role
}

function roleBadgeClass(role) {
  switch (role) {
    case 'admin': return 'bg-success'
    case 'seller': return 'bg-danger'
    case 'user':
    case 'buyer': return 'bg-primary'
    default: return 'bg-secondary'
  }
}

function openArchiveModal(user) {
  selectedUser.value = user
  const modal = new bootstrap.Modal(archiveModal.value)
  modal.show()
}

function confirmArchive() {
  if (selectedUser.value && selectedUser.value.id) {
    router.delete(`/admin/users/${selectedUser.value.id}`, {
      onSuccess: () => {
        // 🟩 Hide modal
        const modal = bootstrap.Modal.getInstance(archiveModal.value)
        modal.hide()

        // 🟩 Show success toast
        successMessage.value = 'User successfully archived!'
        setTimeout(() => {
          const toast = new bootstrap.Toast(toastRef.value, { delay: 2500 })
          toast.show()
        }, 200)
      },
      onFinish: () => {
        selectedUser.value = {}
      },
    })
  }
}
</script>
