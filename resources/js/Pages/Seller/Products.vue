<template>
  <SellerDashboardLayout>
    <div class="container py-4">
      <h2 class="mb-4 text-center text-md-start">🛒 My Products</h2>

      <!-- ➕ Create Custom Product -->
      <div class="mb-3 text-center text-md-start">
        <Link href="/seller/custom-products/create" class="btn btn-success">
          ➕ Create Custom Product
        </Link>
      </div>

      <!-- ⚠️ No Shop Warning -->
      <div v-if="!shop" class="alert alert-warning text-center">
        ⚠️ You need to create a shop before you can manage products.
        <div class="mt-2">
          <a href="/seller/shop" class="btn btn-sm btn-primary">Go to Create Shop</a>
        </div>
      </div>

      <!-- ✅ If Shop Exists -->
      <div v-else>
        <!-- 📝 Create Product Form -->
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
          <input type="file" @change="e => form.image = e.target.files[0]" class="form-control mb-2" />
          <button type="submit" class="btn btn-primary w-100 w-md-auto">Add Product</button>
        </form>

        <!-- 🔍 Filters -->
        <div class="row mb-4 g-2">
          <div class="col-12 col-md-6">
            <input v-model="search" @keyup.enter="handleSearch" class="form-control" placeholder="Search product name or description" />
          </div>
          <div class="col-6 col-md-3">
            <button class="btn btn-primary w-100" @click="handleSearch">Search</button>
          </div>
        </div>

        <!-- 🧾 Products Table (Desktop) -->
        <div class="table-responsive d-none d-md-block">
          <table class="table table-bordered align-middle text-center">
            <thead class="table-light">
              <tr>
                <th>Image</th>
                <th>Name</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Description</th>
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
                  <button class="btn btn-sm btn-warning me-1" @click="openEdit(product)">Edit</button>
                  <button class="btn btn-sm btn-danger" @click="deleteProduct(product.id)">Delete</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- 📱 Card View (Mobile) -->
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
              <p><strong>Name:</strong> {{ product.name }}</p>
              <p><strong>Price:</strong> ₱{{ product.price }}</p>
              <p><strong>Stock:</strong> {{ product.stock }}</p>
              <p><strong>Description:</strong> {{ product.description }}</p>
              <div class="d-flex gap-2 justify-content-center">
                <button class="btn btn-sm btn-warning" @click="openEdit(product)">Edit</button>
                <button class="btn btn-sm btn-danger" @click="deleteProduct(product.id)">Delete</button>
              </div>
            </div>
          </div>
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
      </div>

      <!-- ✏️ Edit Product Modal -->
      <div v-if="editingProduct" class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5)">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <form @submit.prevent="updateProduct">
              <div class="modal-header">
                <h5 class="modal-title">Edit Product</h5>
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
                <input type="file" @change="e => editForm.image = e.target.files[0]" class="form-control mb-3" />
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" @click="editingProduct = null">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Changes</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </SellerDashboardLayout>
</template>

<script setup>
import { ref } from 'vue'
import { router, useForm, Link } from '@inertiajs/vue3'
import SellerDashboardLayout from '@/Layouts/SellerDashboardLayout.vue'

// Props from Laravel
const props = defineProps({
  products: Object, // now paginated
  shop: Object,
  search: String,
  limit: Number
})

// State
const form = useForm({ name: '', price: '', stock: '', description: '', image: null })
const search = ref(props.search ?? '')
const limit = ref(props.limit ?? 5)
const editingProduct = ref(null)
const editForm = useForm({ id: null, name: '', price: '', stock: '', description: '', image: null })

// Add Product
function submit() {
  if (!props.shop) return alert('Please create a shop first.')
  form.post('/seller/products', {
    onSuccess: () => {
      form.reset()
      handleSearch()
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
      },
    })
}

// Delete Product
function deleteProduct(id) {
  if (confirm('Are you sure?')) {
    router.delete(`/seller/products/${id}`, {
      preserveScroll: true,
      onSuccess: () => handleSearch(),
    })
  }
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
