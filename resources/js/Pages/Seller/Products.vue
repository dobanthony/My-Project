<template>
  <SellerDashboardLayout>
    <div class="container">

      <!-- ✅ Toast Notification -->
      <div
        v-if="toast.message"
        class="toast-container position-fixed top-0 start-50 translate-middle-x p-3"
        style="z-index: 9999"
      >
        <div
          class="toast align-items-center text-white bg-success border-0 show"
          role="alert"
        >
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
      <div class="mb-3 text-center text-md-start">
        <Link href="/seller/custom-products/create" class="btn btn-success">
          <i class="bi bi-plus-circle me-1"></i> Custom Product
        </Link>
      </div>

      <!-- No Shop Warning -->
      <div v-if="!shop" class="alert alert-warning text-center">
        ⚠️ You need to create a shop before you can manage products.
        <div class="mt-2">
          <a href="/seller/shop" class="btn btn-sm btn-primary">
            <i class="bi bi-shop me-1"></i> Go to Create Shop
          </a>
        </div>
      </div>

      <!-- If Shop Exists -->
      <div v-else>
        <!-- Create Product Form -->
        <form @submit.prevent="submit" class="mb-5">
          <div class="row g-2 mb-2">
            <div class="col-12 col-md-4">
              <input v-model="form.name" class="form-control" placeholder="Product Name" />
            </div>
            <div class="col-6 col-md-4">
              <input v-model="form.price" type="number" class="form-control" placeholder="Price" />
            </div>
            <div class="col-6 col-md-4">
              <input v-model="form.stock" type="number" class="form-control" placeholder="Stock" />
            </div>
          </div>

          <textarea v-model="form.description" class="form-control mb-2" placeholder="Description"></textarea>

          <!-- ✅ Eco-Friendly Checkbox -->
          <div class="form-check mb-2">
            <input v-model="form.eco_friendly" type="checkbox" class="form-check-input" id="ecoFriendly" />
            <label class="form-check-label" for="ecoFriendly">
              <i class="bi bi-leaf-fill text-success me-1"></i> Eco-Friendly
            </label>
          </div>

          <input type="file" @change="e => form.image = e.target.files[0]" class="form-control mb-2" />

          <button type="submit" class="btn btn-primary w-100 w-md-auto">
            <i class="bi bi-plus-lg me-1"></i> Add Product
          </button>
        </form>

        <!-- Filters -->
        <div class="row mb-4 g-2">
          <div class="col-12 col-md-6">
            <input v-model="search" @keyup.enter="handleSearch" class="form-control" placeholder="Search product name or description" />
          </div>
          <div class="col-6 col-md-3">
            <button class="btn btn-primary w-100" @click="handleSearch">
              <i class="bi bi-search me-1"></i> Search
            </button>
          </div>
        </div>

        <!-- Products Table (Desktop) -->
        <div class="table-responsive d-none d-md-block">
          <table class="table table-bordered table-hover align-middle text-center">
            <thead class="table-success">
              <tr>
                <th>Image</th>
                <th>Name</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Description</th>
                <th>Eco-Friendly</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="product in products.data" :key="product.id">
                <td>
                  <img v-if="product.image" :src="`/storage/${product.image}`" alt="Product Image" class="img-thumbnail" style="max-width: 75px" />
                  <span v-else class="text-muted">No image</span>
                </td>
                <td>{{ product.name }}</td>
                <td>₱{{ product.price }}</td>
                <td>{{ product.stock }}</td>
                <td>{{ product.description }}</td>
                <td>
                  <span v-if="product.eco_friendly" class="badge bg-success">
                    <i class="bi bi-check-circle me-1"></i> Yes
                  </span>
                  <span v-else class="badge bg-secondary">
                    <i class="bi bi-x-circle me-1"></i> No
                  </span>
                </td>
                <td>
                  <button class="btn btn-sm btn-warning me-1" @click="openEdit(product)">
                    <i class="bi bi-pencil-square"></i>
                  </button>
                  <button class="btn btn-sm btn-danger" @click="confirmDelete(product.id)">
                    <i class="bi bi-trash"></i>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Card View (Mobile) -->
        <div class="d-block d-md-none">
          <div v-for="product in products.data" :key="product.id" class="card mb-3 shadow-sm">
            <div class="card-body">
              <div class="mb-2 text-center">
                <img
                  v-if="product.image"
                  :src="`/storage/${product.image}`"
                  alt="Product Image"
                  class="img-fluid rounded"
                  style="max-height: 150px"
                />
                <span v-else class="text-muted">No image</span>
              </div>
              <p><strong><i class="bi bi-tag-fill me-1"></i>Name:</strong> {{ product.name }}</p>
              <p><strong><i class="bi bi-cash-coin me-1"></i>Price:</strong> ₱{{ product.price }}</p>
              <p><strong><i class="bi bi-box-seam me-1"></i>Stock:</strong> {{ product.stock }}</p>
              <p><strong><i class="bi bi-card-text me-1"></i>Description:</strong> {{ product.description }}</p>
              <p><strong><i class="bi bi-leaf me-1"></i>Eco-Friendly:</strong>
                <span v-if="product.eco_friendly" class="text-success"><i class="bi bi-check-circle-fill"></i> Yes</span>
                <span v-else class="text-muted"><i class="bi bi-x-circle-fill"></i> No</span>
              </p>
              <div class="d-flex gap-2 justify-content-center">
                <button class="btn btn-sm btn-warning" @click="openEdit(product)">
                  <i class="bi bi-pencil-square"></i>
                </button>
                <button class="btn btn-sm btn-danger" @click="confirmDelete(product.id)">
                  <i class="bi bi-trash"></i>
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Pagination -->
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
      </div>

      <!-- Edit Product Modal -->
      <div v-if="editingProduct" class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5)">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <form @submit.prevent="updateProduct">
              <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-pencil-square me-1"></i> Edit Product</h5>
                <button type="button" class="btn-close" @click="editingProduct = null"></button>
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

                <textarea v-model="editForm.description" class="form-control mb-2" placeholder="Description"></textarea>

                <!-- ✅ Eco-Friendly Checkbox in Edit -->
                <div class="form-check mb-2">
                  <input v-model="editForm.eco_friendly" type="checkbox" class="form-check-input" id="editEcoFriendly" />
                  <label class="form-check-label" for="editEcoFriendly">
                    <i class="bi bi-leaf-fill text-success me-1"></i> Eco-Friendly
                  </label>
                </div>

                <input type="file" @change="e => editForm.image = e.target.files[0]" class="form-control mb-3" />
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" @click="editingProduct = null">
                  <i class="bi bi-x-circle me-1"></i> Cancel
                </button>
                <button type="submit" class="btn btn-primary">
                  <i class="bi bi-save me-1"></i> Save Changes
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <!-- ✅ Delete Confirmation Modal -->
      <div v-if="deleteId" class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5)">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title"><i class="bi bi-exclamation-triangle-fill text-danger me-1"></i> Confirm Delete</h5>
              <button type="button" class="btn-close" @click="deleteId = null"></button>
            </div>
            <div class="modal-body">
              Are you sure you want to delete this product?
            </div>
            <div class="modal-footer">
              <button class="btn btn-secondary" @click="deleteId = null">
                <i class="bi bi-x-circle me-1"></i> Cancel
              </button>
              <button class="btn btn-danger" @click="deleteProduct">
                <i class="bi bi-trash me-1"></i> Delete
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
import { router, useForm, Link, usePage } from '@inertiajs/vue3'
import SellerDashboardLayout from '@/Layouts/SellerDashboardLayout.vue'

// Props from Laravel
const props = defineProps({
  products: Object,
  shop: Object,
  search: String,
  limit: Number
})

// Toast state
const toast = ref({ message: '' })

// Show flash message from backend
const page = usePage()
watch(() => page.props.flash.success, (val) => {
  if (val) {
    toast.value.message = val
    setTimeout(() => toast.value.message = '', 3000)
  }
})

// State
const form = useForm({
  name: '',
  price: '',
  stock: '',
  description: '',
  image: null,
  eco_friendly: false
})

const search = ref(props.search ?? '')
const limit = ref(props.limit ?? 5)
const editingProduct = ref(null)
const editForm = useForm({
  id: null,
  name: '',
  price: '',
  stock: '',
  description: '',
  image: null,
  eco_friendly: false
})

const deleteId = ref(null)

// Add Product
function submit() {
  if (!props.shop) return alert('Please create a shop first.')
  form.post('/seller/products', {
    onSuccess: () => {
      form.reset()
      handleSearch()
      toast.value.message = 'Product created successfully!'
      setTimeout(() => toast.value.message = '', 3000)
    },
  })
}

// Edit Product
function openEdit(product) {
  editingProduct.value = product
  editForm.id = product.id
  editForm.name = product.name
  editForm.price = product.price
  editForm.stock = product.stock
  editForm.description = product.description
  editForm.image = null
  editForm.eco_friendly = !!product.eco_friendly
}

function updateProduct() {
  editForm
    .transform(data => ({ ...data, _method: 'put' }))
    .post(`/seller/products/${editForm.id}`, {
      forceFormData: true,
      preserveScroll: true,
      onSuccess: () => {
        editingProduct.value = null
        handleSearch()
        toast.value.message = 'Product updated successfully!'
        setTimeout(() => toast.value.message = '', 3000)
      },
    })
}

// Confirm delete (open modal)
function confirmDelete(id) {
  deleteId.value = id
}

// Delete Product
function deleteProduct() {
  if (!deleteId.value) return
  router.delete(`/seller/products/${deleteId.value}`, {
    preserveScroll: true,
    onSuccess: () => {
      deleteId.value = null
      handleSearch()
      toast.value.message = 'Product deleted successfully!'
      setTimeout(() => toast.value.message = '', 3000)
    },
  })
}

// Search Handler
function handleSearch() {
  router.get('/seller/products', {
    search: search.value,
    limit: limit.value,
  }, {
    preserveState: true,
    preserveScroll: true,
  })
}
</script>
