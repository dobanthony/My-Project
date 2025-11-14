<template>
  <Head title="Archived " />
  <AdminDashboardLayout>
    <div class="container py-3">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold text-warning d-flex align-items-center">
          <i class="bi bi-archive-fill me-2"></i> Archived Products
        </h4>
        <a href="/admin/marketplace" class="btn btn-outline-success btn-sm">
          <i class="bi bi-arrow-left me-1"></i> Back to Products
        </a>
      </div>

      <!-- Toast Notification -->
      <div
        class="position-fixed top-0 end-0 p-3"
        style="z-index: 2000"
      >
        <div
          id="toast-success"
          class="toast align-items-center text-white bg-success border-0"
          role="alert"
          aria-live="assertive"
          aria-atomic="true"
        >
          <div class="d-flex">
            <div class="toast-body">
              <i class="bi bi-check-circle me-2"></i>{{ toastMessage }}
            </div>
            <button
              type="button"
              class="btn-close btn-close-white me-2 m-auto"
              data-bs-dismiss="toast"
              aria-label="Close"
            ></button>
          </div>
        </div>
      </div>

      <!-- Info -->
      <div v-if="products.length === 0" class="alert alert-info">
        <i class="bi bi-info-circle me-1"></i> No archived products.
      </div>

      <!-- Table -->
      <div v-else class="table-responsive">
        <table class="table table-hover align-middle">
          <thead class="table-warning">
            <tr>
              <th>Name</th>
              <th>Seller</th>
              <th>Archived At</th>
              <th class="text-center">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="product in products" :key="product.id">
              <td>{{ product.name }}</td>
              <td>{{ product.shop?.shop_name || 'N/A' }}</td>
              <td>{{ formatDate(product.deleted_at) }}</td>
              <td class="text-center">
                <div class="d-flex justify-content-center gap-2">
                  <button
                    @click="openRestoreModal(product)"
                    class="btn btn-outline-success btn-sm"
                  >
                    <i class="bi bi-arrow-clockwise"></i> Restore
                  </button>
                  <button
                    @click="openDeleteModal(product)"
                    class="btn btn-outline-danger btn-sm"
                  >
                    <i class="bi bi-trash3"></i> Delete
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Restore Modal -->
      <div class="modal fade" id="restoreModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content shadow">
            <div class="modal-header bg-success text-white">
              <h5 class="modal-title">
                <i class="bi bi-arrow-clockwise me-2"></i> Confirm Restore
              </h5>
              <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              Are you sure you want to restore
              <strong>{{ selectedProduct?.name }}</strong>?
            </div>
            <div class="modal-footer">
              <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
              <button class="btn btn-success" @click="confirmRestore">
                <i class="bi bi-arrow-clockwise me-1"></i> Restore
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Delete Modal -->
      <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content shadow">
            <div class="modal-header bg-danger text-white">
              <h5 class="modal-title">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> Confirm Delete
              </h5>
              <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              <p>
                Provide a reason for deleting "<strong>{{ selectedProduct?.name }}</strong>":
              </p>
              <textarea
                v-model="reason"
                class="form-control"
                rows="3"
                placeholder="Enter reason..."
              ></textarea>
            </div>
            <div class="modal-footer">
              <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
              <button class="btn btn-danger" @click="confirmDelete">
                <i class="bi bi-trash3 me-1"></i> Confirm Delete
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminDashboardLayout>
</template>

<script setup>
import { ref } from 'vue'
import { router, Head } from '@inertiajs/vue3'
import * as bootstrap from 'bootstrap'
import AdminDashboardLayout from '@/Layouts/AdminDashboardLayout.vue'

const props = defineProps({ products: Array })
const selectedProduct = ref(null)
const reason = ref('')
const toastMessage = ref('')

// ✅ Open Restore Modal
const openRestoreModal = (product) => {
  selectedProduct.value = product
  const modal = new bootstrap.Modal(document.getElementById('restoreModal'))
  modal.show()
}

// ✅ Confirm Restore
const confirmRestore = () => {
  router.post(`/admin/marketplace/${selectedProduct.value.id}/restore`, {}, {
    preserveScroll: true,
    onSuccess: () => {
      closeModal('restoreModal')
      showToast('Product restored successfully!')
      router.reload({ only: ['products'] })
    },
  })
}

// ✅ Open Delete Modal
const openDeleteModal = (product) => {
  selectedProduct.value = product
  reason.value = ''
  const modal = new bootstrap.Modal(document.getElementById('deleteModal'))
  modal.show()
}

// ✅ Confirm Delete
const confirmDelete = () => {
  if (!reason.value.trim()) {
    alert('Please provide a reason.')
    return
  }
  router.delete(`/admin/marketplace/${selectedProduct.value.id}/force-delete`, {
    data: { reason: reason.value },
    preserveScroll: true,
    onSuccess: () => {
      closeModal('deleteModal')
      showToast('Product permanently deleted!')
      router.reload({ only: ['products'] })
    },
  })
}

// ✅ Helper: Close Modal
const closeModal = (id) => {
  const modal = bootstrap.Modal.getInstance(document.getElementById(id))
  if (modal) modal.hide()
}

// ✅ Helper: Show Toast
const showToast = (message) => {
  toastMessage.value = message
  const toastEl = document.getElementById('toast-success')
  const toast = new bootstrap.Toast(toastEl)
  toast.show()
}

// ✅ Helper: Format Date
const formatDate = (date) => new Date(date).toLocaleString('en-PH')
</script>
