<template>
  <AdminDashboardLayout>
    <div class="container py-1">
      <!-- Page Header -->
      <div
        class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3"
      >
        <h4 class="mb-0 fw-bold text-success d-flex align-items-center">
          <i class="bi bi-box-seam me-2"></i> Seller Products
        </h4>

        <!-- Sort Dropdown -->
        <div>
          <select
            v-model="sort"
            class="form-select shadow-sm"
            @change="applySort"
          >
            <option value="all">📦 All Products</option>
            <option value="newest">✨ Newest (Last 2 Days)</option>
            <option value="oldest">⏳ Oldest (Before 2 Days)</option>
          </select>
        </div>
      </div>

      <!-- Flash Message -->
      <div v-if="message" class="alert alert-success alert-dismissible fade show">
        <i class="bi bi-check-circle me-1"></i> {{ message }}
        <button
          type="button"
          class="btn-close"
          data-bs-dismiss="alert"
        ></button>
      </div>

      <!-- Table Section -->
      <div class="table-responsive">
        <table class="table table-hover table-striped align-middle">
          <thead class="table-success">
            <tr>
              <th><i class="bi bi-tag me-1"></i> Name</th>
              <th><i class="bi bi-shop me-1"></i> Seller</th>
              <th><i class="bi bi-calendar me-1"></i> Created</th>
              <th class="text-center"><i class="bi bi-gear me-1"></i> Action</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="product in products" :key="product.id">
              <td class="fw-semibold">{{ product.name }}</td>
              <td>{{ product.shop?.shop_name || 'N/A' }}</td>
              <td>{{ formatDate(product.created_at) }}</td>
              <td class="text-center">
                <div
                  class="d-flex flex-column flex-md-row justify-content-center align-items-center gap-2"
                >
                  <Link
                    :href="`/admin/marketplace/${product.id}`"
                    class="btn btn-success btn-sm d-flex align-items-center gap-1"
                  >
                    <i class="bi bi-eye"></i>
                    <span>View</span>
                  </Link>
                  <button
                    class="btn btn-outline-danger btn-sm d-flex align-items-center gap-1"
                    @click="openDeleteModal(product)"
                  >
                    <i class="bi bi-trash3"></i>
                    <span>Delete</span>
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="products.length === 0">
              <td colspan="4" class="text-center text-muted py-4">
                <i class="bi bi-inbox me-1"></i> No products found.
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Delete Modal -->
      <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content shadow">
            <div class="modal-header bg-danger text-white">
              <h5 class="modal-title d-flex align-items-center">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> Confirm Delete
              </h5>
              <button
                type="button"
                class="btn-close btn-close-white"
                data-bs-dismiss="modal"
              ></button>
            </div>
            <div class="modal-body">
              <p>
                Please provide a reason for deleting
                "<strong>{{ selectedProduct?.name }}</strong>":
              </p>
              <textarea
                v-model="reason"
                class="form-control"
                rows="3"
                placeholder="Enter reason..."
                required
              ></textarea>
            </div>
            <div class="modal-footer">
              <button class="btn btn-secondary" data-bs-dismiss="modal">
                Cancel
              </button>
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
import { router, usePage, Link } from '@inertiajs/vue3'
import AdminDashboardLayout from '@/Layouts/AdminDashboardLayout.vue'
import * as bootstrap from 'bootstrap'

const props = defineProps({ products: Array, sort: String })

const page = usePage()
const flash = page.props.flash || {}
const message = flash.message || null

const selectedProduct = ref(null)
const reason = ref('')
const sort = ref(props.sort || 'all')

const openDeleteModal = (product) => {
  selectedProduct.value = product
  reason.value = ''
  new bootstrap.Modal(document.getElementById('deleteModal')).show()
}

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
      router.reload({ only: ['products'] })
    },
  })
}

const applySort = () => {
  router.get('/admin/marketplace', { sort: sort.value }, { preserveState: true })
}

const formatDate = (dateString) => {
  const date = new Date(dateString)
  return date.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  })
}
</script>
