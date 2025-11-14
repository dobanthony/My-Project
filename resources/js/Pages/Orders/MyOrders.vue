<template>
  <Head title="Orders " />
  <DashboardLayout>
    <div class="container py-4">

      <!-- 🌟 Page Header -->
      <div class="mb-4 text-center">
        <h2 class="fw-bold text-primary mb-2">
          <i class="bi bi-bag-check-fill me-2"></i> My Orders
        </h2>
        <p class="text-muted mb-0">
          View and track all your orders, check their current status, and access receipts easily.
        </p>
      </div>

      <!-- 🔍 Search and Filter -->
      <div class="row mb-4 g-2 align-items-center">
        <div class="col-md-13">
          <div class="input-group shadow-sm">
            <input
              v-model="search"
              @keyup.enter="handleSearch"
              class="form-control border-start-0"
              placeholder="Search by product, seller, or status"
            />
            <span class="input-group-text bg-white border-end-0">
              <i class="bi bi-search text-primary"></i>
            </span>
          </div>
        </div>
        <!-- <div class="col-md-2 d-grid">
          <button class="btn btn-primary shadow-sm" @click="handleSearch">
            <i class="bi bi-search me-1"></i> Search
          </button>
        </div> -->
      </div>

      <!-- ✅ Desktop View -->
      <div v-if="!isMobile" class="table-responsive mt-3 shadow-sm rounded-4 overflow-hidden">
        <table class="table table-hover align-middle text-center mb-0">
          <thead class="table-success text-uppercase">
            <tr>
              <th><i class="bi bi-image"></i></th>
              <th><i class="bi bi-box-seam me-1"></i> Product</th>
              <th><i class="bi bi-currency-exchange me-1"></i> Price</th>
              <th><i class="bi bi-123 me-1"></i> Qty</th>
              <th>
                <div class="d-flex flex-column align-items-center gap-1">
                  <select
                    v-model="statusFilter"
                    class="form-select form-select-sm text-center border-0 bg-light rounded-pill"
                    style="width: 130px;"
                  >
                    <option value="">All</option>
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="declined">Declined</option>
                    <option value="canceled">Canceled</option>
                  </select>
                </div>
              </th>
              <th><i class="bi bi-person-circle me-1"></i> Seller</th>
              <th><i class="bi bi-calendar-event me-1"></i> Date</th>
              <th><i class="bi bi-receipt me-1"></i> Receipt</th>
              <th><i class="bi bi-truck me-1"></i> Delivery</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="order in filteredOrders" :key="order.id" class="align-middle">
              <td>
                <img
                  :src="order.display_image"
                  alt="Product Image"
                  class="rounded-3 shadow-sm"
                  style="width: 160px; height: 70px; object-fit: cover;"
                />
              </td>
              <td class="fw-semibold text-wrap">{{ order.product?.name }}</td>
              <td>₱{{ parseFloat(order.product?.price).toFixed(2) }}</td>
              <td>{{ order.quantity }}</td>
              <td>
                <span
                  class="badge px-3 py-2 rounded-pill"
                  :class="{
                    'bg-warning text-dark': order.status === 'pending',
                    'bg-success': order.status === 'approved',
                    'bg-danger': order.status === 'declined',
                    'bg-secondary': order.status === 'canceled'
                  }"
                >
                  {{ capitalize(order.status) }}
                </span>
                <span v-if="order.reported" class="badge bg-danger rounded-pill ms-1">Reported</span>
              </td>
              <td>{{ order.product?.shop?.user?.first_name ?? 'Unknown' }}</td>
              <td class="text-nowrap">{{ formatDate(order.created_at) }}</td>
              <td>
                <button
                  v-if="!order.received_order && order.status !== 'canceled' && order.status !== 'declined' && !order.reported"
                  @click="$inertia.visit(`/receipt/${order.id}`)"
                  class="btn btn-sm btn-outline-success rounded-pill shadow-sm"
                >
                  <i class="bi bi-eye"></i> View
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
      <div v-else class="row g-3 mt-2">
        <div v-for="order in filteredOrders" :key="order.id" class="col-12">
          <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body">
              <div class="d-flex gap-3 mb-3">
                <img
                  :src="order.product?.image ? `/storage/${order.product.image}` : 'https://via.placeholder.com/80'"
                  class="rounded-3 shadow-sm"
                  style="width: 80px; height: 80px; object-fit: cover"
                />
                <div>
                  <h5 class="mb-1 fw-bold">{{ order.product?.name }}</h5>
                  <small class="text-muted">₱{{ parseFloat(order.product?.price).toFixed(2) }}</small>
                </div>
              </div>

              <p><strong>Qty:</strong> {{ order.quantity }}</p>
              <p>
                <strong>Status:</strong>
                <span
                  class="badge px-3 py-2 rounded-pill"
                  :class="{
                    'bg-warning text-dark': order.status === 'pending',
                    'bg-success': order.status === 'approved',
                    'bg-danger': order.status === 'declined',
                    'bg-secondary': order.status === 'canceled'
                  }"
                >
                  {{ capitalize(order.status) }}
                </span>
                <span v-if="order.reported" class="badge bg-danger rounded-pill ms-1">Reported</span>
              </p>
              <p><strong>Seller:</strong> {{ order.product?.shop?.user?.name ?? 'Unknown' }}</p>
              <p><strong>Date:</strong> {{ formatDate(order.created_at) }}</p>
              <p><strong>Delivery:</strong> {{ order.delivery_date ?? 'N/A' }}</p>

              <button
                v-if="!order.received_order && order.status !== 'canceled' && order.status !== 'declined' && !order.reported"
                @click="$inertia.visit(`/receipt/${order.id}`)"
                class="btn btn-sm btn-outline-primary w-100 rounded-pill shadow-sm"
              >
                <i class="bi bi-receipt"></i> View Receipt
              </button>
              <span v-else class="text-muted small d-block text-center mt-2">
                {{ order.reported ? 'Reported – Receipt not available' : 'Receipt not available' }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- ❌ No Results -->
      <div v-if="filteredOrders.length === 0" class="alert alert-info mt-4 text-center rounded-3 shadow-sm">
        <i class="bi bi-clipboard-x me-2"></i> You have no orders matching your search and filter.
      </div>

      <!-- 🔢 Pagination -->
      <nav v-if="orders.links.length > 3" class="mt-4 d-flex justify-content-center">
        <ul class="pagination shadow-sm rounded-pill overflow-hidden">
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
        class="toast align-items-center text-bg-success border-0 shadow-lg"
        role="alert"
        aria-live="assertive"
        aria-atomic="true"
      >
        <div class="d-flex">
          <div class="toast-body">
            <i class="bi bi-check-circle-fill me-2"></i> Order placed! Waiting for seller approval.
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
  </DashboardLayout>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import { usePage, router, Link } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import { Head } from '@inertiajs/vue3'

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
  }, 10000)
})
onBeforeUnmount(() => {
  if (pollingInterval) clearInterval(pollingInterval)
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
.table thead th {
  font-weight: 600;
  letter-spacing: 0.5px;
}

.table-hover tbody tr:hover {
  background-color: #f8fafc;
  transition: 0.3s ease;
}

.pagination .page-link {
  color: #0b84ff;
  background-color: #fff;
  border-color: #0b84ff;
  transition: all 0.25s ease;
}

.pagination .page-link:hover {
  color: #fff;
  background-color: #0b5ed7;
  border-color: #0b5ed7;
}

.page-item.active .page-link {
  color: #fff;
  background-color: #0b84ff;
  border-color: #0b84ff;
  box-shadow: 0 4px 12px rgba(11, 132, 255, 0.3);
}
</style>
