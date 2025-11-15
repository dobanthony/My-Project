<template>
  <Head title="Create " />
  <SellerDashboardLayout>
    <div class="container py-1">
      <!-- 🌟 Page Header -->
      <div class="text-center mb-5">
        <h2 class="fw-bold text-primary mb-2">
          <i class="bi bi-box-seam-fill text-success me-2"></i> Product Management
        </h2>
        <p class="text-muted mb-0">
          Manage your shop's products, edit details, update stock, and ensure your catalog stays up-to-date.
        </p>
      </div>

      <!-- ✅ Toast Notification -->
      <div
        v-if="toast.message"
        class="toast-container position-fixed top-0 start-50 translate-middle-x p-3"
        style="z-index: 9999"
      >
        <div class="toast align-items-center text-white bg-success border-0 show shadow-lg" role="alert">
          <div class="d-flex">
            <div class="toast-body d-flex align-items-center gap-2">
              <i class="bi bi-check-circle-fill"></i>
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

      <!-- ➕ Create Custom Product -->
      <div class="mb-4 text-start">
        <Link href="/seller/custom-products/create" class="btn btn-outline-primary shadow-sm">
          <i class="bi bi-plus-circle me-1"></i> Create Custom Product
        </Link>
      </div>

      <!-- 🚫 No Shop Warning -->
      <div v-if="!shop" class="alert alert-warning text-center shadow-sm">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        You need to create a shop before managing products.
        <div class="mt-3">
          <a href="/seller/shop" class="btn btn-sm btn-primary">
            <i class="bi bi-shop me-1"></i> Go to Create Shop
          </a>
        </div>
      </div>

      <!-- ✅ Product Management -->
      <div v-else>
        <!-- 📝 Create Product Form -->
        <div class="card shadow-sm border-0 mb-5">
          <div class="card-header bg-light text-dark">
            <i class="bi bi-plus-circle-fill text-primary me-1"></i> Add New Product
          </div>
          <div class="card-body">
            <form @submit.prevent="submit">
              <div class="row g-3 mb-3">
                <div class="col-12 col-md-5">
                  <input v-model="form.name" class="form-control" placeholder="Product Name" />
                </div>
                <div class="col-6 col-md-2">
                  <input v-model="form.price" type="number" class="form-control" placeholder="Price" />
                </div>
                <div class="col-6 col-md-2">
                  <input v-model="form.stock" type="number" class="form-control" placeholder="Stock" />
                </div>
                <div class="col-12 col-md-3">
                  <select v-model="form.category_id" class="form-select">
                    <option value="">Select Category</option>
                    <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                      {{ cat.name }}
                    </option>
                  </select>
                </div>
              </div>

              <textarea
                v-model="form.description"
                class="form-control mb-3"
                placeholder="Product Description"
              ></textarea>

              <input type="file" @change="e => form.image = e.target.files[0]" class="form-control mb-3" />

              <button type="submit" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Add Product
              </button>
            </form>
          </div>
        </div>

        <!-- 🔍 Modern Search + Category Filter -->
<form @submit.prevent="handleSearch"
      class="row g-2 mb-4 sticky-search rounded-4 shadow-sm px-3 py-2 bg-white backdrop-blur">

  <!-- Search Input -->
  <div class="col-12 col-md-10">
    <div class="input-group rounded-pill overflow-hidden border border-primary-subtle">
      <input
        type="text"
        v-model="search"
        class="form-control border-0 shadow-none"
        placeholder="Search by name or description"
        aria-label="Search products"
      />
      <span class="input-group-text bg-transparent border-0">
        <i class="bi bi-search text-primary"></i>
      </span>
    </div>
  </div>

  <!-- Category Filter -->
  <div class="col-12 col-md-2">
    <select
      v-model="selectedCategory"
      class="form-select rounded-pill border-primary-subtle"
      @change="handleSearch"
    >
      <option value="">All Categories</option>
      <option v-for="cat in categories" :key="cat.id" :value="cat.id">
        {{ cat.name }}
      </option>
    </select>
  </div>
</form>


        <!-- 💻 Products Table -->
        <div class="table-responsive d-none d-md-block">
          <table class="table table-hover align-middle text-center shadow-sm">
            <thead class="table-success text-dark">
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
              <tr v-for="product in products.data" :key="product.id" class="align-middle">
                <td>
                  <img
                    v-if="product.image"
                    :src="`/storage/${product.image}`"
                    alt="Product Image"
                    class="img-thumbnail"
                    style="max-width: 70px"
                  />
                  <span v-else class="text-muted"><i class="bi bi-image"></i> No image</span>
                </td>
                <td class="fw-semibold">{{ product.name }}</td>
                <td>{{ product.category?.name || '—' }}</td>
                <td>₱{{ product.price }}</td>
                <td>{{ product.stock }}</td>
                <td class="text-truncate" style="max-width: 180px;">{{ product.description }}</td>
                <td>
                  <button class="btn btn-sm btn-outline-primary me-1" @click="openEdit(product)">
                    <i class="bi bi-pencil-square"></i>
                  </button>
                  <button class="btn btn-sm btn-outline-warning" @click="confirmArchive(product.id)">
                    <i class="bi bi-archive-fill"></i>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- 📱 Card View for Mobile -->
        <div class="d-block d-md-none">
          <div v-for="product in products.data" :key="product.id" class="card mb-3 shadow-sm border-0">
            <div class="card-body">
              <div class="text-center mb-3">
                <img
                  v-if="product.image"
                  :src="`/storage/${product.image}`"
                  alt="Product Image"
                  class="img-fluid rounded"
                  style="max-height: 150px"
                />
                <span v-else class="text-muted"><i class="bi bi-image"></i> No image</span>
              </div>
              <p><strong><i class="bi bi-tag-fill me-1"></i>Name:</strong> {{ product.name }}</p>
              <p><strong><i class="bi bi-folder-fill me-1"></i>Category:</strong> {{ product.category?.name || '—' }}</p>
              <p><strong><i class="bi bi-cash-coin me-1"></i>Price:</strong> ₱{{ product.price }}</p>
              <p><strong><i class="bi bi-box-seam me-1"></i>Stock:</strong> {{ product.stock }}</p>
              <p><strong><i class="bi bi-card-text me-1"></i>Description:</strong> {{ product.description }}</p>
              <div class="d-flex gap-2 justify-content-center">
                <button class="btn btn-sm btn-primary" @click="openEdit(product)">
                  <i class="bi bi-pencil-square"></i>
                </button>
                <button class="btn btn-sm btn-outline-warning" @click="confirmArchive(product.id)">
                  <i class="bi bi-archive"></i>
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- 📄 Pagination -->
        <nav v-if="products.links.length > 3" class="d-flex justify-content-center mt-4">
          <ul class="pagination flex-wrap justify-content-center">
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
      </div>

      <!-- ✏️ Edit Product Modal -->
      <div v-if="editingProduct" class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5)">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content shadow">
            <form @submit.prevent="updateProduct">
              <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="bi bi-pencil-square me-1"></i> Edit Product</h5>
                <button type="button" class="btn-close btn-close-white" @click="editingProduct = null"></button>
              </div>
              <div class="modal-body">
                <div class="row g-2 mb-2">
                  <div class="col-12 col-md-4">
                    <input v-model="editForm.name" class="form-control" placeholder="Name" />
                  </div>
                  <div class="col-6 col-md-4">
                    <input v-model="editForm.price" type="number" class="form-control" placeholder="Price" />
                  </div>
                  <div class="col-6 col-md-4">
                    <input v-model="editForm.stock" type="number" class="form-control" placeholder="Stock" />
                  </div>
                </div>

                <select v-model="editForm.category_id" class="form-select mb-2">
                  <option value="">Select Category</option>
                  <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                    {{ cat.name }}
                  </option>
                </select>

                <textarea v-model="editForm.description" class="form-control mb-2" placeholder="Description"></textarea>

                <input type="file" @change="e => editForm.image = e.target.files[0]" class="form-control mb-3" />
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" @click="editingProduct = null">
                  <i class="bi bi-x-circle me-1"></i> Cancel
                </button>
                <button type="submit" class="btn btn-success">
                  <i class="bi bi-save me-1"></i> Save Changes
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <!-- ⚠️ Archive Confirmation Modal -->
      <div v-if="archiveId" class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5)">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content shadow">
            <div class="modal-header bg-warning">
              <h5 class="modal-title text-dark"><i class="bi bi-archive-fill me-1"></i> Confirm Archive</h5>
              <button type="button" class="btn-close" @click="archiveId = null"></button>
            </div>
            <div class="modal-body">
              Are you sure you want to archive this product?
            </div>
            <div class="modal-footer">
              <button class="btn btn-secondary" @click="archiveId = null">
                <i class="bi bi-x-circle me-1"></i> Cancel
              </button>
              <button class="btn btn-warning text-dark" @click="archiveProduct">
                <i class="bi bi-archive me-1"></i> Archive
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </SellerDashboardLayout>
</template>

<script setup>
import { ref, watch } from 'vue'
import { router, useForm, Link, usePage, Head } from '@inertiajs/vue3'
import SellerDashboardLayout from '@/Layouts/SellerDashboardLayout.vue'

const props = defineProps({
  products: Object,
  shop: Object,
  search: String,
  limit: Number,
  categories: Array,
})

const toast = ref({ message: '' })
const page = usePage()

watch(() => page.props.flash.success, val => {
  if (val) {
    toast.value.message = val
    setTimeout(() => (toast.value.message = ''), 3000)
  }
})

const form = useForm({
  name: '',
  price: '',
  stock: '',
  description: '',
  image: null,
  category_id: '',
})

const search = ref(props.search ?? '')
const selectedCategory = ref(props.category ?? '')
const limit = ref(props.limit ?? 5)
const editingProduct = ref(null)

const editForm = useForm({
  id: null,
  name: '',
  price: '',
  stock: '',
  description: '',
  image: null,
  category_id: '',
})

const archiveId = ref(null)

function submit() {
  if (!props.shop) return alert('Please create a shop first.')
  form.post('/seller/products', {
    onSuccess: () => {
      form.reset()
      handleSearch()
      toast.value.message = 'Product created successfully!'
      setTimeout(() => (toast.value.message = ''), 3000)
    },
  })
}

function openEdit(product) {
  editingProduct.value = product
  editForm.id = product.id
  editForm.name = product.name
  editForm.price = product.price
  editForm.stock = product.stock
  editForm.description = product.description
  editForm.category_id = product.category_id
  editForm.image = null
}

function updateProduct() {
  const data = {
    _method: 'put',
    name: editForm.name,
    price: editForm.price,
    stock: editForm.stock,
    description: editForm.description,
    category_id: editForm.category_id,
  }
  if (editForm.image) data.image = editForm.image
  editForm
    .transform(() => data)
    .post(`/seller/products/${editForm.id}`, {
      forceFormData: true,
      preserveScroll: true,
      onSuccess: () => {
        editingProduct.value = null
        handleSearch()
        toast.value.message = 'Product updated successfully!'
        setTimeout(() => (toast.value.message = ''), 3000)
      },
    })
}

function confirmArchive(id) {
  archiveId.value = id
}

function archiveProduct() {
  if (!archiveId.value) return
  router.delete(`/seller/products/${archiveId.value}`, {
    preserveScroll: true,
    onSuccess: () => {
      archiveId.value = null
      handleSearch()
      toast.value.message = 'Product archived successfully!'
      setTimeout(() => (toast.value.message = ''), 3000)
    },
  })
}

function handleSearch() {
  router.get(
    '/seller/products',
    {
      search: search.value,
      category: selectedCategory.value,
      limit: limit.value,
    },
    {
      preserveState: true,
      preserveScroll: true,
    }
  )
}
</script>
