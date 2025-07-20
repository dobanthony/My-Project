<template>
  <AdminDashboardLayout>
    <div class="container">
      <h4 class="mb-4"><i class="bi bi-box-seam me-1 text-success"></i>Sellers Products</h4>

      <!-- Flash Success Message -->
      <div v-if="message" class="alert alert-success">
        {{ message }}
      </div>

      <!-- Toast Notification -->
      <div
        class="toast-container position-fixed top-0 start-50 translate-middle-x p-3"
        style="z-index: 1055"
      >
        <div
          id="deleteToast"
          class="toast align-items-center text-bg-success border-0"
          role="alert"
          aria-live="assertive"
          aria-atomic="true"
          data-bs-delay="3000"
          data-bs-autohide="true"
        >
          <div class="d-flex">
            <div class="toast-body">
              ✅ Product deleted successfully!
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

      <!-- Sort Dropdown -->
      <div class="mb-3 d-flex justify-content-end">
        <select v-model="sort" class="form-select w-auto" @change="applySort">
          <option value="all"> All Products</option>
          <option value="newest"> Newest (Last 2 Days)</option>
          <option value="oldest"> Oldest (Before 2 Days)</option>
        </select>
      </div>

      <!-- Product Table (Responsive) -->
      <div class="table-responsive">
        <table class="table table-bordered table-hover">
          <thead class="table-success">
            <tr>
              <th>Name</th>
              <th>Seller</th>
              <th>Created</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="product in products" :key="product.id">
              <td>{{ product.name }}</td>
              <td>{{ product.user?.name }}</td>
              <td>{{ formatDate(product.created_at) }}</td>
              <td>
                <div class="d-flex flex-wrap gap-2">
                  <button class="btn btn-primary btn-sm" @click="openViewModal(product)">
                    <i class="bi bi-eye me-1 text-white"></i>View
                  </button>
                  <button class="btn btn-outline-danger btn-sm" @click="openDeleteModal(product)">
                    <i class="bi bi-trash3 me-1 text-danger"></i>Delete
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- View Product Modal -->
      <div class="modal fade" id="viewModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-scrollable modal-lg">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">🛍️ Product Details</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" v-if="selectedProduct">
              <div class="row g-3 align-items-start">
                <div class="col-12 col-md-6">
                  <img
                    :src="getImageUrl(selectedProduct.image)"
                    class="img-fluid rounded w-100"
                    alt="Product Image"
                  />
                </div>
                <div class="col-12 col-md-6">
                  <h4>{{ selectedProduct.name }}</h4>
                  <p class="mb-1"><strong>Description:</strong></p>
                  <p>{{ selectedProduct.description }}</p>
                  <p><strong>Price:</strong> ₱{{ Number(selectedProduct.price).toFixed(2) }}</p>
                  <p><strong>Stock:</strong> {{ selectedProduct.stock }}</p>
                  <p><strong>Seller:</strong> {{ selectedProduct.user?.name }}</p>
                  <p v-if="selectedProduct.shop">
                    <strong>Shop:</strong> {{ selectedProduct.shop.name }}
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Delete Confirmation Modal -->
      <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Delete Product</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              <p>
                Write a reason to delete
                "<strong>{{ selectedProduct?.name }}</strong>":
              </p>
              <textarea v-model="reason" class="form-control" rows="3" required></textarea>
            </div>
            <div class="modal-footer">
              <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
              <button class="btn btn-danger" @click="confirmDelete">Confirm Delete</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminDashboardLayout>
</template>

<script setup>
import { ref } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import AdminDashboardLayout from '@/Layouts/AdminDashboardLayout.vue'
import * as bootstrap from 'bootstrap'

const props = defineProps({ products: Array, sort: String })

const page = usePage()
const flash = page.props.flash || {}
const message = flash.message || null

const selectedProduct = ref(null)
const reason = ref('')
const sort = ref(props.sort || 'all')

// View Modal
const openViewModal = (product) => {
  selectedProduct.value = product
  new bootstrap.Modal(document.getElementById('viewModal')).show()
}

// Delete Modal
const openDeleteModal = (product) => {
  selectedProduct.value = product
  reason.value = ''
  new bootstrap.Modal(document.getElementById('deleteModal')).show()
}

// Confirm Delete
const confirmDelete = () => {
  if (!reason.value.trim()) {
    alert('Please provide a reason.')
    return
  }

  router.delete(`/admin/marketplace/${selectedProduct.value.id}`, {
    data: { reason: reason.value },
    preserveScroll: true,
    onSuccess: () => {
      const modal = bootstrap.Modal.getInstance(document.getElementById('deleteModal'))
      if (modal) modal.hide()

      const toastEl = document.getElementById('deleteToast')
      if (toastEl) {
        const toast = new bootstrap.Toast(toastEl)
        toast.show()
      }

      router.reload({ only: ['products'] })
    },
  })
}

// Sort filter
const applySort = () => {
  router.get('/admin/marketplace', { sort: sort.value }, { preserveState: true })
}

// Format Date
const formatDate = (dateString) => {
  const date = new Date(dateString)
  return date.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  })
}

// Get Image
const getImageUrl = (imagePath) => {
  return imagePath
    ? `/storage/${imagePath}`
    : 'https://via.placeholder.com/400x300?text=No+Image'
}
</script>
