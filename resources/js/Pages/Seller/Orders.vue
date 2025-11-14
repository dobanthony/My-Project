<template>
  <Head title="Orders " />
  <SellerDashboardLayout>
    <div class="container py-1">

      <!-- 🧭 Page Header -->
      <div class="rounded-3 p-3 mb-4 shadow-sm border">
        <div class="d-flex align-items-center">
          <i class="bi bi-bag-check-fill fs-3 text-primary me-3"></i>
          <div>
            <h3 class="fw-bold mb-0 text-dark">Order Management</h3>
            <small class="text-muted">
              Manage, approve, and track your customer orders efficiently in one place.
            </small>
          </div>
        </div>
      </div>

      <!-- 🔍 Search & Filters -->
      <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
          <div class="row gy-2 gx-2 align-items-center">
            
            <!-- Search -->
            <div class="col-12 col-sm-6 col-md-6">
              <div class="input-group">
                <input
                  v-model="search"
                  @keyup.enter="handleSearch"
                  class="form-control"
                  placeholder="Search by buyer, product..."
                />
                <span class="input-group-text bg-light"><i class="bi bi-search text-primary"></i></span>
              </div>
            </div>

            <!-- Status Filter -->
            <div class="col-6 col-sm-3 col-md-3">
              <div class="input-group">
                <span class="input-group-text bg-light"><i class="bi bi-funnel text-primary"></i></span>
                <select v-model="statusFilter" class="form-select" @change="handleSearch">
                  <option value="">All Statuses</option>
                  <option value="pending">⏳ Pending</option>
                  <option value="approved">✅ Approved</option>
                  <option value="declined">❌ Declined</option>
                  <option value="canceled">🚫 Canceled</option>
                </select>
              </div>
            </div>

            <!-- Category Filter -->
            <div class="col-6 col-sm-6 col-md-3">
              <div class="input-group">
                <span class="input-group-text bg-light"><i class="bi bi-tags text-primary"></i></span>
                <select v-model="categoryFilter" class="form-select" @change="handleSearch">
                  <option value="">All Categories</option>
                  <option
                    v-for="category in categories"
                    :key="category.id"
                    :value="category.id"
                  >
                    {{ category.name }}
                  </option>
                </select>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- 🧾 Table View (Desktop) -->
      <div class="table-responsive d-none d-md-block">
        <table class="table table-hover align-middle border rounded shadow-sm">
          <thead class="table-success text-dark">
            <tr>
              <!-- <th><i class="bi bi-person me-1"></i>Buyer</th> -->
              <th><i class="bi bi-box-seam me-1"></i>Product</th>
              <th><i class="bi bi-flag me-1"></i>Status</th>
              <th><i class="bi bi-calendar-event me-1"></i>Delivery Date</th>
              <th><i class="bi bi-truck me-1"></i>Delivery Status</th>
              <th><i class="bi bi-gear me-1"></i>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="order in orders.data" :key="order.id" class="align-middle">
              <!-- <td>{{ order.user.first_name }}</td> -->
              <td>{{ order.product.name }}</td>
              <td>
                <span :class="['badge px-3 py-2', statusBadge(order.status)]">
                  {{ order.status }}
                </span>
              </td>
              <td>
                <div v-if="order.status === 'pending'">
                  <input
                    type="date"
                    v-model="deliveryDates[order.id]"
                    class="form-control form-control-sm"
                    :min="minDate"
                    :max="maxDate"
                  />
                </div>
                <span v-else>{{ order.delivery_date ?? 'N/A' }}</span>
              </td>
              <td>
                <div v-if="order.status === 'approved'">
                  <select
                    class="form-select form-select-sm"
                    v-model="deliveryStatuses[order.id]"
                    :disabled="deliveryStatuses[order.id] !== 'pending'"
                    @change="updateDeliveryStatus(order.id)"
                  >
                    <option value="pending">Pending</option>
                    <option value="delivered">Delivered</option>
                    <option value="failed">Failed</option>
                  </select>
                </div>
                <span v-else class="badge bg-secondary">
                  {{ order.delivery_status ?? 'N/A' }}
                </span>
              </td>
              <td>
                <div class="d-flex gap-1 justify-content-center flex-wrap">
                  <Link :href="route('seller.orders.view', order.id)" class="btn btn-sm btn-outline-primary">
                    View
                  </Link>
                  <button
                    v-if="order.status === 'pending'"
                    @click="attemptApprove(order.id)"
                    class="btn btn-sm btn-success"
                  >
                    Approved
                  </button>
                  <button v-if="order.status === 'pending'" @click="decline(order.id)" class="btn btn-sm btn-danger">
                    Decline
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- 📱 Mobile View -->
      <div class="d-block d-md-none">
        <div v-for="order in orders.data" :key="order.id" class="card mb-3 shadow-sm border-0">
          <div class="card-body">
            <p><i class="bi bi-person-fill me-1 text-primary"></i><strong>Buyer:</strong> {{ order.user.first_name }}</p>
            <p><i class="bi bi-box2-fill me-1 text-primary"></i><strong>Product:</strong> {{ order.product.name }}</p>
            <p><i class="bi bi-hash me-1 text-primary"></i><strong>Qty:</strong> {{ order.quantity }}</p>
            <p><i class="bi bi-flag me-1 text-primary"></i><strong>Status:</strong>
              <span :class="['badge', statusBadge(order.status)]">{{ order.status }}</span>
            </p>
            <p><i class="bi bi-calendar-check me-1 text-primary"></i><strong>Delivery Date:</strong>
              <span v-if="order.status !== 'pending'">{{ order.delivery_date ?? 'N/A' }}</span>
              <input
                v-else
                type="date"
                v-model="deliveryDates[order.id]"
                class="form-control form-control-sm"
                :min="minDate"
                :max="maxDate"
              />
            </p>
            <p><i class="bi bi-truck me-1 text-primary"></i><strong>Delivery Status:</strong>
              <span v-if="order.status !== 'approved'" class="badge bg-secondary">
                {{ order.delivery_status ?? 'N/A' }}
              </span>
              <select
                v-else
                class="form-select form-select-sm"
                v-model="deliveryStatuses[order.id]"
                :disabled="deliveryStatuses[order.id] !== 'pending'"
                @change="updateDeliveryStatus(order.id)"
              >
                <option value="pending">Pending</option>
                <option value="delivered">Delivered</option>
                <option value="failed">Failed</option>
              </select>
            </p>
            <div class="d-flex flex-wrap gap-2 justify-content-center mt-3">
              <Link :href="route('seller.orders.view', order.id)" class="btn btn-sm btn-primary">
                <i class="bi bi-eye me-1"></i> View
              </Link>
              <button
                v-if="order.status === 'pending'"
                @click="attemptApprove(order.id)"
                class="btn btn-sm btn-success"
              >
                <i class="bi bi-check2-circle me-1"></i> Approve
              </button>
              <button v-if="order.status === 'pending'" @click="decline(order.id)" class="btn btn-sm btn-danger">
                <i class="bi bi-x-circle me-1"></i> Decline
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- 🚫 Empty State -->
      <div v-if="orders.data.length === 0" class="alert alert-light border text-center shadow-sm mt-3">
        <i class="bi bi-inbox text-secondary fs-4 d-block mb-2"></i>
        <p class="mb-0">No matching orders found.</p>
      </div>

      <!-- 📄 Pagination -->
      <nav v-if="orders.links.length > 3" class="mt-4">
        <ul class="pagination justify-content-center flex-wrap gap-1">
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

      <!-- ✅ Toast -->
      <div
        v-if="toastMessage"
        class="position-fixed top-0 start-50 translate-middle-x p-3 w-100"
        style="z-index: 1055; max-width: 360px;"
      >
        <div class="toast show text-white bg-success d-flex align-items-center shadow">
          <div class="toast-body">
            <span v-html="toastMessage"></span>
          </div>
          <button
            type="button"
            class="btn-close btn-close-white me-2 m-auto"
            @click="toastMessage = ''"
          ></button>
        </div>
      </div>

      <!-- ⚠ Alert Modal -->
      <div v-if="showAlertModal" class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">{{ alertTitle }}</h5>
              <button type="button" class="btn-close" @click="closeAlert"></button>
            </div>
            <div class="modal-body">
              <p>{{ alertMessage }}</p>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" @click="closeAlert">Close</button>
            </div>
          </div>
        </div>
      </div>

    </div>
  </SellerDashboardLayout>
</template>

<script setup>
import { ref, watch, onMounted, onBeforeUnmount } from 'vue'
import { router, Link, Head } from '@inertiajs/vue3'
import SellerDashboardLayout from '@/Layouts/SellerDashboardLayout.vue'

const props = defineProps({
  orders: Object,
  filters: Object,
  categories: Array,
})

const search = ref(props.filters.search || '')
const statusFilter = ref(props.filters.status || '')
const categoryFilter = ref(props.filters.category_id || '')
const categories = ref(props.categories || [])

const deliveryDates = ref({})
const deliveryStatuses = ref({})
const toastMessage = ref('')
const loadingStates = ref({})

// Alert modal
const alertTitle = ref('')
const alertMessage = ref('')
const showAlertModal = ref(false)
const showAlert = (title, message) => {
  alertTitle.value = title
  alertMessage.value = message
  showAlertModal.value = true
}
const closeAlert = () => showAlertModal.value = false

// Dates
const today = new Date()
const formatDate = (date) => {
  const year = date.getFullYear()
  const month = String(date.getMonth() + 1).padStart(2, '0')
  const day = String(date.getDate()).padStart(2, '0')
  return `${year}-${month}-${day}`
}
const minDate = formatDate(today)
const maxDate = formatDate(new Date(today.setDate(today.getDate() + 7)))

// Validate delivery date
const isValidDate = (date) => {
  if (!date) return false
  const d = new Date(date)
  const min = new Date(minDate)
  const max = new Date(maxDate)
  return d >= min && d <= max
}

const statusBadge = (status) => {
  switch (status) {
    case 'pending': return 'bg-warning text-dark'
    case 'approved': return 'bg-success'
    case 'declined': return 'bg-danger'
    case 'canceled': return 'bg-secondary'
    default: return 'bg-light text-dark'
  }
}

// Watch & data updates
watch(() => props.orders, (newOrders) => {
  deliveryStatuses.value = {}
  newOrders.data.forEach(order => {
    deliveryStatuses.value[order.id] = order.delivery_status || 'pending'
    loadingStates.value[order.id] = false
  })
}, { immediate: true })

// Search handler
const handleSearch = () => {
  router.get('/seller/orders', {
    search: search.value,
    status: statusFilter.value,
    category_id: categoryFilter.value,
  }, { preserveScroll: true, preserveState: true })
}

// Attempt approve with date validation
const attemptApprove = (id) => {
  if (!isValidDate(deliveryDates.value[id])) {
    showAlert('⚠ Invalid Date', `Please select a delivery date from ${minDate} to ${maxDate}.`)
    return
  }
  approve(id)
}

// Approve order
const approve = (id) => {
  const order = props.orders.data.find(o => o.id === id)
  if (!order || order.status !== 'pending') return
  if (loadingStates.value[id]) return
  loadingStates.value[id] = true

  router.post(`/seller/orders/${id}/approve`, {
    delivery_date: deliveryDates.value[id],
  }, {
    preserveScroll: true,
    onSuccess: () => {
      toastMessage.value = '<i class="bi bi-check-circle-fill me-2"></i>Order approved.'
      router.reload({ only: ['orders'] })
    },
    onFinish: () => { loadingStates.value[id] = false }
  })
}

// Decline order
const decline = (id) => {
  if (loadingStates.value[id]) return
  loadingStates.value[id] = true
  router.post(`/seller/orders/${id}/decline`, {}, {
    preserveScroll: true,
    onSuccess: () => {
      toastMessage.value = '<i class="bi bi-x-circle-fill me-2"></i>Order declined.'
      router.reload({ only: ['orders'] })
    },
    onFinish: () => { loadingStates.value[id] = false }
  })
}

// Update delivery status
const updateDeliveryStatus = (id) => {
  const status = deliveryStatuses.value[id]
  const iconMap = {
    pending: 'bi-box-seam',
    delivered: 'bi-truck',
    failed: 'bi-exclamation-triangle-fill',
  }
  router.post(`/seller/orders/${id}/delivery-status`, {
    delivery_status: status,
  }, {
    preserveScroll: true,
    onSuccess: () => {
      toastMessage.value = `<i class="bi ${iconMap[status]} me-2"></i>Delivery status updated to "${status}".`
      router.reload({ only: ['orders'] })
    }
  })
}

// Toast auto-dismiss
watch(toastMessage, (val) => {
  if (val) setTimeout(() => toastMessage.value = '', 4000)
})

let pollingInterval = null
onMounted(() => pollingInterval = setInterval(() => router.reload({ only: ['orders'] }), 10000))
onBeforeUnmount(() => pollingInterval && clearInterval(pollingInterval))
</script>

<style scoped>
.table {
  border-radius: 0.5rem;
  overflow: hidden;
}
.page-item.active .page-link {
  background-color: #0d6efd;
  border-color: #0d6efd;
  color: white;
  box-shadow: 0 2px 10px rgba(13, 110, 253, 0.3);
}
</style>
