<template>
  <AdminDashboardLayout>
    <div class="container py-4">
      <!-- Page Header -->
      <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
        <h4 class="mb-3 mb-md-0">
          <i class="bi bi-box-seam me-1 text-success"></i>Sellers Products
        </h4>

        <!-- Sort Dropdown -->
        <select
          v-model="sort"
          class="form-select w-auto"
          @change="applySort"
        >
          <option value="all">All Products</option>
          <option value="newest">Newest (Last 2 Days)</option>
          <option value="oldest">Oldest (Before 2 Days)</option>
        </select>
      </div>

      <!-- Flash Message -->
      <div v-if="message" class="alert alert-success">
        {{ message }}
      </div>

      <!-- Table Section -->
      <div class="table-responsive-sm">
        <table class="table table-bordered table-hover align-middle text-nowrap">
          <thead class="table-success">
            <tr>
              <th>Name</th>
              <th>Seller</th>
              <th>Created</th>
              <th class="text-center">Action</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="product in products" :key="product.id">
              <td>{{ product.name }}</td>
              <td>{{ product.shop?.shop_name || 'N/A' }}</td>
              <td>{{ formatDate(product.created_at) }}</td>
              <td class="text-center">
                <div class="d-flex flex-column flex-md-row justify-content-center align-items-center gap-2">
                  <Link
                    :href="`/admin/marketplace/${product.id}`"
                    class="btn btn-success btn-sm w-95 w-md-auto"
                  >
                    <i class="bi bi-eye me-1 text-white"></i>View
                  </Link>
                  <button
                    class="btn btn-outline-danger btn-sm w-95 w-md-auto"
                    @click="openDeleteModal(product)"
                  >
                    <i class="bi bi-trash3 me-1"></i>Delete
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Delete Modal -->
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
