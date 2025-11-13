<template>
  <SellerDashboardLayout>
    <div class="container py-3 position-relative">
      <!-- 🔔 Toast Notification -->
      <transition name="fade">
        <div
          v-if="toast.message"
          class="toast-container position-fixed top-0 start-50 translate-middle-x p-3"
          style="z-index: 9999"
        >
          <div
            class="toast align-items-center d-flex show shadow"
            :class="toast.type === 'success' ? 'bg-success text-white' : 'bg-danger text-white'"
            role="alert"
          >
            <div class="d-flex">
              <div class="toast-body d-flex align-items-center gap-2">
                <i :class="toast.type === 'success' ? 'bi bi-check-circle-fill' : 'bi bi-x-circle-fill'"></i>
                {{ toast.message }}
              </div>
              <button
                type="button"
                class="btn-close btn-close-white me-2 m-auto"
                @click="toast.message = ''"
              ></button>
            </div>
          </div>
        </div>
      </transition>

      <h4 class="fw-bold text-secondary mb-3">
        <i class="bi bi-archive-fill me-2 text-success"></i> Archived Products
      </h4>
      
      <!-- 🔍 Search & Filter -->
      <form @submit.prevent="handleSearch" class="row g-2 mb-3 align-items-center sticky-search">
        <div class="col-12 col-md-7">
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-search"></i></span>
            <input
              type="text"
              class="form-control"
              placeholder="Search products..."
              v-model="search"
            />
          </div>
        </div>

        <div class="col-12 col-md-3">
          <button type="submit" class="btn btn-primary w-100">
            <i class="bi bi-arrow-right-circle me-1"></i> Filter
          </button>
        </div>

        <div class="col-12 col-md-2">
          <select class="form-select" v-model="selectedCategory" @change="handleSearch">
            <option value="">All Categories</option>
            <option v-for="category in categories" :key="category.id" :value="category.id">
              {{ category.name }}
            </option>
          </select>
        </div>
      </form>

      <!-- 🧭 Table -->
      <div v-if="products.data.length > 0" class="table-responsive">
        <table class="table table-bordered table-hover align-middle text-center">
          <thead class="table-success">
            <tr>
              <th>Image</th>
              <th>Name</th>
              <th>Category</th>
              <th>Price</th>
              <th>Stock</th>
              <th>Description</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="product in products.data" :key="product.id">
              <td>
                <img v-if="product.image" :src="product.image" class="img-thumbnail" style="max-width:75px" alt="Product Image" />
                <span v-else class="text-muted">No image</span>
              </td>
              <td>{{ product.name }}</td>
              <td>
                <span v-if="product.category" class="badge bg-success-subtle border border-success text-success">
                  {{ product.category }}
                </span>
                <span v-else class="text-muted">—</span>
              </td>
              <td>₱{{ product.price }}</td>
              <td>{{ product.stock }}</td>
              <td class="text-truncate" style="max-width: 200px">{{ product.description }}</td>
              <td>
                <button
                  class="btn btn-sm btn-success me-1"
                  @click="confirmRestore(product.id)"
                >
                  <i class="bi bi-arrow-counterclockwise"></i> Restore
                </button>
                <button
                  class="btn btn-sm btn-danger"
                  @click="confirmDelete(product.id)"
                >
                  <i class="bi bi-trash"></i> Delete
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- 🕳️ Empty State -->
      <div v-else class="text-center text-muted py-5">
        <i class="bi bi-archive fs-1 d-block mb-2"></i>
        No archived products found.
      </div>

      <!-- 🔢 Pagination -->
      <nav v-if="products.links.length > 3" class="d-flex justify-content-center mt-4">
        <ul class="pagination flex-wrap gap-1 justify-content-center">
          <li
            v-for="(link, index) in products.links"
            :key="index"
            class="page-item"
            :class="{ active: link.active, disabled: !link.url }"
          >
            <Link
              class="page-link"
              :href="link.url || ''"
              v-html="link.label"
              preserve-scroll
              preserve-state
            />
          </li>
        </ul>
      </nav>

      <!-- ♻️ Restore Modal -->
      <div v-if="restoreId" class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5)">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title text-success">
                <i class="bi bi-arrow-counterclockwise me-2"></i> Confirm Restore
              </h5>
              <button type="button" class="btn-close" @click="restoreId = null"></button>
            </div>
            <div class="modal-body">Are you sure you want to restore this product?</div>
            <div class="modal-footer">
              <button class="btn btn-secondary" @click="restoreId = null">Cancel</button>
              <button class="btn btn-success" :disabled="loading" @click="restoreProduct">
                <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
                Restore
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- 🗑️ Delete Modal -->
      <div v-if="deleteId" class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5)">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title text-danger">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> Confirm Delete
              </h5>
              <button type="button" class="btn-close" @click="deleteId = null"></button>
            </div>
            <div class="modal-body">
              Are you sure you want to permanently delete this archived product?
            </div>
            <div class="modal-footer">
              <button class="btn btn-secondary" @click="deleteId = null">Cancel</button>
              <button class="btn btn-danger" :disabled="loading" @click="deleteProduct">
                <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
                Delete Permanently
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </SellerDashboardLayout>
</template>

<script setup>
import { ref } from 'vue'
import { router, Link } from '@inertiajs/vue3'
import SellerDashboardLayout from '@/Layouts/SellerDashboardLayout.vue'

const props = defineProps({
  products: Object,
  search: String,
  limit: Number,
  categories: Array,
  category_id: [String, Number],
})

const search = ref(props.search ?? '')
const selectedCategory = ref(props.category_id ?? '')
const toast = ref({ message: '', type: 'success' })
const deleteId = ref(null)
const restoreId = ref(null)
const loading = ref(false)

function handleSearch() {
  router.get('/seller/products/archived', {
    search: search.value,
    category_id: selectedCategory.value,
  }, { preserveState: true, preserveScroll: true })
}

function confirmRestore(id) {
  restoreId.value = id
}

function restoreProduct() {
  if (!restoreId.value) return
  loading.value = true
  router.post(`/seller/products/${restoreId.value}/restore`, {}, {
    preserveScroll: true,
    onSuccess: () => {
      restoreId.value = null
      loading.value = false
      handleSearch()
      showToast('Product restored successfully!', 'success')
    },
    onFinish: () => (loading.value = false)
  })
}

function confirmDelete(id) {
  deleteId.value = id
}

function deleteProduct() {
  if (!deleteId.value) return
  loading.value = true
  router.delete(`/seller/products/${deleteId.value}/force-delete`, {
    preserveScroll: true,
    onSuccess: () => {
      deleteId.value = null
      loading.value = false
      handleSearch()
      showToast('Product permanently deleted!', 'danger')
    },
    onFinish: () => (loading.value = false)
  })
}

function showToast(message, type = 'success') {
  toast.value = { message, type }
  setTimeout(() => (toast.value.message = ''), 3000)
}
</script>

<style scoped>
.toast-container {
  z-index: 2000;
}
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
