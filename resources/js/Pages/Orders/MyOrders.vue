<template>
  <DashboardLayout>
    <div class="container">

      <!-- 🔍 Search and Filter -->
      <div class="row mb-4 g-2">
        <div class="col-md-10">
          <input
            v-model="search"
            @keyup.enter="handleSearch"
            class="form-control"
            placeholder="Search by product, seller, or status"
          />
        </div>
        <div class="col-md-2">
          <button class="btn btn-success w-100" @click="handleSearch">Search</button>
        </div>
      </div>

      <!-- 🔔 Note -->
      <div v-if="hasPendingOrder" class="alert alert-warning d-flex align-items-center gap-2">
        <i class="bi bi-info-circle-fill"></i>
        <div>
          <strong>Note:</strong> Your order is pending seller approval. Stock will only be deducted once approved.
        </div>
      </div>

      <!-- ✅ Desktop View -->
      <div v-if="!isMobile" class="table-responsive">
        <table class="table table-hover align-middle text-center">
          <thead class="table-success">
            <tr>
              <th>🖼️</th>
              <th>Product</th>
              <th>Price</th>
              <th>Qty</th>
              <th>
                <div class="d-flex flex-column align-items-center gap-1">
                  <span></span>
                  <select
                    v-model="statusFilter"
                    class="form-select form-select-sm text-center"
                    style="width: 130px"
                  >
                    <option value="">All</option>
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="declined">Declined</option>
                    <option value="canceled">Canceled</option>
                  </select>
                </div>
              </th>
              <th>Seller</th>
              <th>Date</th>
              <th>Receipt</th>
              <th>Delivery</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="order in filteredOrders" :key="order.id">
              <td>
<img
  :src="order.display_image"
  alt="Product Image"
  class="rounded"
  style="width: 80px; height: 80px; object-fit: cover;"
/>
              </td>
              <td class="text-wrap">{{ order.product?.name }}</td>
              <td>₱{{ parseFloat(order.product?.price).toFixed(2) }}</td>
              <td>{{ order.quantity }}</td>
              <td>
                <span
                  class="badge"
                  :class="{
                    'bg-warning text-dark': order.status === 'pending',
                    'bg-success': order.status === 'approved',
                    'bg-danger': order.status === 'declined',
                    'bg-secondary': order.status === 'canceled'
                  }"
                >
                  {{ capitalize(order.status) }}
                </span>
                <span v-if="order.reported" class="badge bg-danger ms-1">Reported</span>
              </td>
              <td>{{ order.product?.shop?.user?.first_name ?? 'Unknown' }}</td>
              <td class="text-nowrap">{{ formatDate(order.created_at) }}</td>
              <td>
                <button
                  v-if="!order.received_order && order.status !== 'canceled' && order.status !== 'declined' && !order.reported"
                  @click="$inertia.visit(`/receipt/${order.id}`)"
                  class="btn btn-sm btn-outline-success"
                >
                  View
                </button>
                <span v-else class="text-muted small">
                  {{ order.reported ? 'Reported – Receipt not available' : 'Not available' }}
                </span>
              </td>
              <td class="text-nowrap">{{ order.delivery_date ?? 'N/A' }}</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- 📱 Mobile View -->
      <div v-else class="row g-3">
        <div v-for="order in filteredOrders" :key="order.id" class="col-12">
          <div class="card border shadow-sm">
            <div class="card-body">
              <div class="d-flex gap-3 mb-3">
                <img
                  :src="order.product?.image ? `/storage/${order.product.image}` : 'https://via.placeholder.com/80'"
                  class="rounded"
                  style="width: 80px; height: 80px; object-fit: cover"
                />
                <div>
                  <h5 class="mb-0">{{ order.product?.name }}</h5>
                  <small class="text-muted">₱{{ parseFloat(order.product?.price).toFixed(2) }}</small>
                </div>
              </div>

              <p><strong>Qty:</strong> {{ order.quantity }}</p>
              <p>
                <strong>Status:</strong>
                <span
                  class="badge"
                  :class="{
                    'bg-warning text-dark': order.status === 'pending',
                    'bg-success': order.status === 'approved',
                    'bg-danger': order.status === 'declined',
                    'bg-secondary': order.status === 'canceled'
                  }"
                >
                  {{ capitalize(order.status) }}
                </span>
                <span v-if="order.reported" class="badge bg-danger ms-1">Reported</span>
              </p>
              <p><strong>Seller:</strong> {{ order.product?.shop?.user?.name ?? 'Unknown' }}</p>
              <p><strong>Date:</strong> {{ formatDate(order.created_at) }}</p>
              <p><strong>Delivery:</strong> {{ order.delivery_date ?? 'N/A' }}</p>

              <button
                v-if="!order.received_order && order.status !== 'canceled' && order.status !== 'declined' && !order.reported"
                @click="$inertia.visit(`/receipt/${order.id}`)"
                class="btn btn-sm btn-outline-primary w-100"
              >
                View Receipt
              </button>
              <span v-else class="text-muted small d-block text-center mt-2">
                {{ order.reported ? 'Reported – Receipt not available' : 'Receipt not available' }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- ❌ No Results -->
      <div v-if="filteredOrders.length === 0" class="alert alert-info mt-3">
        You have no orders matching your search and filter.
      </div>

      <!-- 🔢 Pagination -->
      <nav v-if="orders.links.length > 3" class="mt-4 d-flex justify-content-center">
        <ul class="pagination">
          <li
            v-for="(link, index) in orders.links"
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

    <!-- ✅ Toast -->
    <div
      class="toast-container position-fixed top-0 start-50 translate-middle-x p-3"
      style="z-index: 9999"
    >
      <div
        id="orderSuccessToast"
        class="toast align-items-center text-bg-success border-0"
        role="alert"
        aria-live="assertive"
        aria-atomic="true"
      >
        <div class="d-flex">
          <div class="toast-body">✅ Order placed! Waiting for seller approval.</div>
          <button
            type="button"
            class="btn-close btn-close-white me-2 m-auto"
            data-bs-dismiss="toast"
            aria-label="Close"
          ></button>
        </div>
      </div>
    </div>
  </DashboardLayout>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import { usePage, router, Link } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'

// Props
const props = defineProps({
  orders: Object,
  search: String,
})

// Reactive states
const search = ref(props.search || '')
const statusFilter = ref('')
const isMobile = ref(window.innerWidth <= 768)

// Toast handling
const page = usePage()
onMounted(() => {
  const msg = page.props.flash?.success
  if (msg) {
    const toastEl = document.getElementById('orderSuccessToast')
    toastEl.querySelector('.toast-body').innerText = msg
    new bootstrap.Toast(toastEl).show()
  }
})

// Resize tracking
const handleResize = () => {
  isMobile.value = window.innerWidth <= 768
}
onMounted(() => window.addEventListener('resize', handleResize))
onBeforeUnmount(() => window.removeEventListener('resize', handleResize))

// Polling: refresh orders every 10 seconds
let pollingInterval = null
onMounted(() => {
  pollingInterval = setInterval(() => {
    router.reload({ preserveScroll: true, preserveState: true })
  }, 10000) // 10 seconds
})
onBeforeUnmount(() => {
  if (pollingInterval) {
    clearInterval(pollingInterval)
  }
})

// Methods
const handleSearch = () => {
  router.get('/my-orders', {
    search: search.value,
  }, {
    preserveScroll: true,
    preserveState: true,
  })
}

// Filters
const filteredOrders = computed(() => {
  if (!statusFilter.value) return props.orders.data
  return props.orders.data.filter(order => order.status === statusFilter.value)
})

const hasPendingOrder = computed(() =>
  props.orders.data.some(order => order.status === 'pending')
)

// Helpers
const formatDate = date => new Date(date).toLocaleString()
const capitalize = str => str.charAt(0).toUpperCase() + str.slice(1)
</script>

<style scoped>
input.form-control:focus {
  border-color: #28a745; /* green */
  box-shadow: 0 0 0 0.25rem rgba(40, 167, 69, 0.5); /* green with 50% opacity */
}
.pagination .page-link {
  color: rgb(0, 0, 0);
  background-color: rgb(255, 255, 255);
  border-color: #28a745;
}
.pagination .page-link:hover {
  color: white;
  background-color: #28a745; /* green */
  border-color: #ffffff;
}
.pagination .page-link:focus {
  border-color: #28a745;
  box-shadow: 0 0 0 0.25rem rgba(0, 0, 0, 0.5); /* black with 50% opacity */
}
</style>
