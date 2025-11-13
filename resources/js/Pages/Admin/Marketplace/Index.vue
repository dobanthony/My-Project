<template>
  <AdminDashboardLayout>
    <div class="container py-3">
      <!-- Header -->
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold text-dark mb-0 d-flex align-items-center">
          <i class="bi bi-box-seam text-success me-2"></i> Seller Products
        </h4>
        <Link href="/admin/marketplace/archived" class="btn btn-outline-dark btn-sm">
          <i class="bi bi-archive me-1"></i> View Archived
        </Link>
      </div>

      <!-- 🔍 Search Form -->
      <form @submit.prevent="searchProducts" class="row g-2 mb-3">
        <div class="col-9 col-sm-10">
          <input
            v-model="search"
            type="text"
            class="form-control"
            placeholder="Search by product or shop name..."
          />
        </div>
        <div class="col-3 col-sm-2 d-grid">
          <button type="submit" class="btn btn-success">
            <i class="bi bi-search"></i> Search
          </button>
        </div>
      </form>

      <!-- ✅ Product Table -->
      <div class="table-responsive">
        <table class="table table-hover align-middle">
          <thead class="table-success">
            <tr>
              <th>Name</th>
              <th>Seller</th>
              <th>Created</th>
              <th class="text-center">Action</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="product in products.data" :key="product.id">
              <td>{{ product.name }}</td>
              <td>{{ product.shop?.shop_name || 'N/A' }}</td>
              <td>{{ formatDate(product.created_at) }}</td>
              <td class="text-center">
                <div class="d-flex justify-content-center gap-2">
                  <Link :href="`/admin/marketplace/${product.id}`" class="btn btn-success btn-sm">
                    <i class="bi bi-eye"></i> View
                  </Link>
                  <button
                    @click="showArchiveModal(product)"
                    class="btn btn-outline-secondary btn-sm"
                  >
                    <i class="bi bi-archive"></i> Archive
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="products.data.length === 0">
              <td colspan="4" class="text-center text-muted py-4">
                <i class="bi bi-inbox me-1"></i> No products found.
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- ✅ Pagination -->
      <nav v-if="products.links.length > 3" class="mt-3">
        <ul class="pagination justify-content-center">
          <li
            v-for="link in products.links"
            :key="link.label"
            class="page-item"
            :class="{ active: link.active, disabled: !link.url }"
          >
            <Link
              v-if="link.url"
              class="page-link"
              :href="link.url"
              v-html="link.label"
            />
            <span v-else class="page-link" v-html="link.label"></span>
          </li>
        </ul>
      </nav>
    </div>

    <!-- 🗃️ Archive Confirmation Modal -->
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
          <div class="modal-header bg-secondary text-white">
            <h5 class="modal-title" id="archiveModalLabel">
              <i class="bi bi-archive me-1"></i> Archive Product
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            Are you sure you want to archive
            <strong>{{ selectedProduct?.name }}</strong>?
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
              Cancel
            </button>
            <button type="button" class="btn btn-secondary" @click="confirmArchive">
              <i class="bi bi-archive"></i> Confirm
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- ✅ Toast Notification -->
    <div
      class="toast-container position-fixed bottom-0 end-0 p-3"
      style="z-index: 9999"
    >
      <div
        class="toast align-items-center text-bg-success border-0"
        role="alert"
        ref="successToastRef"
        aria-live="assertive"
        aria-atomic="true"
      >
        <div class="d-flex">
          <div class="toast-body">
            <i class="bi bi-check-circle-fill me-2"></i>{{ toastMessage }}
          </div>
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
import { router, usePage, Link } from '@inertiajs/vue3'
import { ref, onMounted } from 'vue'
import AdminDashboardLayout from '@/Layouts/AdminDashboardLayout.vue'
import * as bootstrap from 'bootstrap'

const props = defineProps({
  products: Object,
  filters: Object,
})

const search = ref(props.filters?.search || '')
const selectedProduct = ref(null)
const archiveModalRef = ref(null)
const successToastRef = ref(null)
const archiveModal = ref(null)
const successToast = ref(null)
const toastMessage = ref('')

// 🔍 Search
const searchProducts = () => {
  router.get('/admin/marketplace', { search: search.value }, { preserveScroll: true })
}

// 🗃️ Show Archive Modal
const showArchiveModal = (product) => {
  selectedProduct.value = product
  archiveModal.value.show()
}

// ✅ Confirm Archive
const confirmArchive = () => {
  router.post(
    `/admin/marketplace/${selectedProduct.value.id}/archive`,
    {},
    {
      preserveScroll: true,
      onSuccess: () => {
        archiveModal.value.hide()
        toastMessage.value = 'Product archived successfully!'
        successToast.value.show()
      },
    }
  )
}

// 📅 Format Date
const formatDate = (date) =>
  new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  })

// 🎬 Initialize Bootstrap modals/toasts
onMounted(() => {
  archiveModal.value = new bootstrap.Modal(archiveModalRef.value)
  successToast.value = new bootstrap.Toast(successToastRef.value)
})
</script>
