<template>
  <DashboardLayout>
    <div class="container my-5">
      <!-- ✅ Toast Notification -->
      <div
        v-if="toast.message"
        class="toast-container position-fixed top-0 start-50 translate-middle-x p-3"
        style="z-index: 9999"
      >
        <div
          class="toast show align-items-center border-0"
          :class="[
            'text-white',
            toast.type === 'success' ? 'bg-success' :
            toast.type === 'danger' ? 'bg-danger' :
            toast.type === 'warning' ? 'bg-warning text-dark' :
            'bg-secondary'
          ]"
          role="alert"
          aria-live="assertive"
          aria-atomic="true"
        >
          <div class="d-flex">
            <div class="toast-body text-center w-100 fw-semibold">
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

      <!-- ❌ Cancel Confirmation Modal -->
      <div v-if="showCancelModal" class="modal fade show d-block" style="background-color: rgba(0, 0, 0, 0.5)">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Cancel Order</h5>
              <button type="button" class="btn-close" @click="showCancelModal = false"></button>
            </div>
            <div class="modal-body">Are you sure you want to cancel this order?</div>
            <div class="modal-footer">
              <button class="btn btn-secondary" @click="showCancelModal = false">Close</button>
              <button class="btn btn-danger" @click="confirmCancelOrder">Yes, Cancel</button>
            </div>
          </div>
        </div>
      </div>

      <!-- Order Card -->
      <div class="border p-4 shadow-sm rounded bg-white mx-auto" style="max-width: 800px">
        <!-- Delivery Info -->
        <div class="mb-4">
          <h5 class="text-danger display-5"><i class="bi bi-geo-alt-fill"></i> Delivery Address</h5>
          <p class="mb-1">
            <strong>{{ order.user?.name }}</strong>
            <span v-if="order.user?.phone"> ({{ order.user?.phone }})</span>
          </p>
          <p>{{ order.user?.address ?? 'No address provided' }}</p>
        </div>

        <!-- Product Info -->
        <div class="mb-4">
          <h5 class="fw-bold mb-3">Products Ordered</h5>
          <div class="border rounded p-3 mb-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <div>
                <strong>Shop Name:</strong> {{ order.product?.shop?.shop_name ?? 'N/A' }}
              </div>
              <small class="text-muted">Order ID: #{{ order.id }}</small>
            </div>
            <div class="d-flex">
              <img
                :src="order.product?.image ? `/storage/${order.product.image}` : 'https://via.placeholder.com/60'"
                class="me-3 rounded"
                width="70"
              />
              <div class="flex-grow-1">
                <h6 class="mb-1">{{ order.product?.name }}</h6>
                <p class="text-muted mb-1">{{ order.product?.description ?? 'No description' }}</p>
                <div class="d-flex justify-content-between align-items-center">
                  <div>
                    <span class="text-muted">₱{{ order.product?.price }}</span> × {{ order.quantity }}
                  </div>
                  <strong class="text-dark">₱{{ totalAmount }}</strong>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Order Info -->
        <div class="row">
          <div class="col-md-6 mb-3">
            <p><strong>Seller:</strong> {{ order.product?.shop?.user?.name ?? 'Unknown Seller' }}</p>
            <p><strong>Status:</strong>
              <span class="badge"
                :class="{
                  'bg-warning text-dark': order.status === 'pending',
                  'bg-success': order.status === 'approved',
                  'bg-danger': order.status === 'declined',
                  'bg-info text-dark': order.status === 'received',
                  'bg-dark': order.status === 'canceled'
                }">{{ order.status }}</span>
            </p>
            <p><strong>Date Ordered:</strong> {{ new Date(order.created_at).toLocaleString() }}</p>
            <p><strong>Delivery Status:</strong>
              <span class="badge"
                :class="{
                  'bg-secondary': order.delivery_status === 'pending',
                  'bg-success': order.delivery_status === 'delivered',
                  'bg-danger': order.delivery_status === 'failed'
                }">{{ order.delivery_status || 'N/A' }}</span>
            </p>
          </div>
          <div class="col-md-6 mb-3 text-md-end">
            <p><strong>Shipping Option:</strong> Standard Local</p>
            <p><strong>Estimated Delivery:</strong> {{ order.delivery_date ?? 'N/A' }}</p>
            <p><strong>Total Payment:</strong> ₱{{ totalAmount }}</p>
          </div>
        </div>

        <!-- Buyer Actions -->
        <div v-if="!props.isSeller">
          <!-- Cancel -->
          <div v-if="order.status === 'pending'" class="mt-4 text-end">
            <button class="btn btn-outline-danger" @click="cancelOrder">❌ Cancel Order</button>
          </div>

          <!-- Mark as received or report -->
          <div v-if="order.status === 'approved' && order.delivery_status === 'delivered'">
            <!-- <div v-if="!order.received_order && !hasReported" class="mt-4 text-end">
              <button class="btn btn-success me-2" @click="markAsReceived">✅ Order Received</button>
              <button class="btn btn-outline-danger" @click="showReportForm = !showReportForm">📝 Report for Not Received</button>
            </div> -->
            <div v-if="!order.received_order && !hasReported" class="mt-4 text-end">
              <button class="btn btn-success me-2" @click="markAsReceived">✅ Order Received</button>
              <button class="btn btn-outline-danger" @click="showReportForm = !showReportForm">📝 Report for Not Received</button>
            </div>

            <!-- Report Form -->
            <div v-if="showReportForm" class="mt-3 border p-3 rounded bg-light">
              <h6 class="mb-2">Report an Issue</h6>
              <textarea
                v-model="reportMessage"
                class="form-control mb-2"
                rows="4"
                placeholder="Describe the problem with your order..."
              ></textarea>
              <button class="btn btn-danger" :disabled="isSubmitting || reportMessage.trim() === ''" @click="submitReport">
                {{ isSubmitting ? 'Submitting...' : 'Submit Report' }}
              </button>
            </div>

            <div v-else-if="order.received_order" class="mt-4 alert alert-info text-center">
              ✅ This order has already been marked as received.
            </div>

            <div v-else-if="hasReported" class="mt-4 alert alert-info text-center">
              ⚠️ You have already reported this order as not received.
            </div>
          </div>
        </div>
      </div>

      <!-- Rating Modal -->
      <div v-if="showRatingModal" class="modal fade show d-block" style="background-color: rgba(0, 0, 0, 0.5)">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Rate Your Order</h5>
              <button type="button" class="btn-close" @click="showRatingModal = false"></button>
            </div>
            <div class="modal-body">
              <!-- Shop Rating -->
              <div class="mb-3">
                <label class="form-label">Shop Rating</label>
                <div>
                  <i v-for="i in 5" :key="'shop-star-' + i" class="bi fs-3 me-1 cursor-pointer"
                    :class="[hoverShopRating >= i || ratingForm.shop_rating >= i ? 'bi-star-fill text-warning' : 'bi-star text-muted']"
                    @click="ratingForm.shop_rating = i"
                    @mouseover="hoverShopRating = i"
                    @mouseleave="hoverShopRating = 0"
                  ></i>
                </div>
                <small class="text-muted">{{ ratingLabels[ratingForm.shop_rating] }}</small>
              </div>

              <!-- Product Rating -->
              <div class="mb-3">
                <label class="form-label">Product Rating</label>
                <div>
                  <i v-for="i in 5" :key="'product-star-' + i" class="bi fs-3 me-1 cursor-pointer"
                    :class="[hoverProductRating >= i || ratingForm.product_rating >= i ? 'bi-star-fill text-warning' : 'bi-star text-muted']"
                    @click="ratingForm.product_rating = i"
                    @mouseover="hoverProductRating = i"
                    @mouseleave="hoverProductRating = 0"
                  ></i>
                </div>
                <small class="text-muted">{{ ratingLabels[ratingForm.product_rating] }}</small>
              </div>

              <!-- Comment -->
              <div class="mb-3">
                <label class="form-label">Comment</label>
                <textarea class="form-control" rows="3" v-model="ratingForm.comment"></textarea>
              </div>

              <!-- Image Upload -->
              <div class="mb-3">
                <label class="form-label">Upload Photo</label>
                <input type="file" class="form-control" @change="handlePhotoUpload" accept="image/*" />
                <div v-if="imagePreview" class="position-relative d-inline-block mt-3">
                  <img :src="imagePreview" class="img-thumbnail" style="max-width: 200px;" />
                  <button class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1 rounded-circle" @click="removeImage">
                    &times;
                  </button>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button class="btn btn-secondary" @click="showRatingModal = false">Cancel</button>
              <button class="btn btn-primary" @click="submitRating">Submit Rating</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </DashboardLayout>
</template>

<script setup>
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
  order: Object,
  userId: Number,
  isSeller: Boolean,
   hasReported: Boolean, 
})

const hasReported = ref(props.hasReported)

const totalAmount = computed(() => {
  return (props.order.product?.price ?? 0) * (props.order.quantity ?? 1)
})

const toast = ref({ message: '', type: 'success' })
const showCancelModal = ref(false)
const showReportForm = ref(false)
const reportMessage = ref('')
const isSubmitting = ref(false)
// const hasReported = ref(false)
const showRatingModal = ref(false)
const hoverProductRating = ref(0)
const hoverShopRating = ref(0)
const imagePreview = ref(null)

const ratingForm = ref({
  product_rating: null,
  shop_rating: null,
  comment: '',
  image: null
})

const ratingLabels = {
  1: 'Very Bad',
  2: 'Bad',
  3: 'Okay',
  4: 'Good',
  5: 'Excellent'
}

const showToast = (message, type = 'success') => {
  toast.value = { message, type }
  setTimeout(() => (toast.value.message = ''), 5000)
}

const cancelOrder = () => showCancelModal.value = true

const confirmCancelOrder = () => {
  router.post(`/orders/${props.order.id}/cancel`, {}, {
    onSuccess: () => {
      showToast('❌ Order has been canceled. Redirecting to My Orders...', 'danger')
      router.visit('/my-orders', { replace: true, preserveScroll: true })
    }
  })
  showCancelModal.value = false
}

const markAsReceived = () => {
  router.post(`/orders/${props.order.id}/received`, {}, {
    onSuccess: () => {
      showToast('✅ Order marked as received.', 'success')
      showRatingModal.value = true
    }
  })
}

const submitReport = () => {
  if (!reportMessage.value.trim()) {
    showToast('⚠️ Please enter a report message.', 'warning')
    return
  }

  isSubmitting.value = true
  router.post(`/orders/${props.order.id}/report`, { message: reportMessage.value }, {
    onSuccess: () => {
      showReportForm.value = false
      reportMessage.value = ''
      hasReported.value = true
    },
    onFinish: () => {
      showToast('✅ Report submitted successfully.', 'success')
      isSubmitting.value = false
    }
  })
}

const handlePhotoUpload = (event) => {
  const file = event.target.files[0]
  if (file) {
    ratingForm.value.image = file
    imagePreview.value = URL.createObjectURL(file)
  }
}

const removeImage = () => {
  ratingForm.value.image = null
  imagePreview.value = null
}

const submitRating = () => {
  if (!ratingForm.value.product_rating || !ratingForm.value.shop_rating) {
    showToast('⚠️ Please rate both the product and the shop.', 'warning')
    return
  }

  const formData = new FormData()
  formData.append('product_rating', ratingForm.value.product_rating)
  formData.append('shop_rating', ratingForm.value.shop_rating)
  formData.append('comment', ratingForm.value.comment)
  if (ratingForm.value.image) formData.append('photo', ratingForm.value.image)

  router.post(`/orders/${props.order.id}/rate-shop`, formData, {
    forceFormData: true,
    onSuccess: () => {
      showToast('✅ Thank you for your rating!', 'success')
      showRatingModal.value = false
      router.visit('/my-orders', { replace: true, preserveScroll: true })
    }
  })
}
</script>

<style>
@media print {
  body * {
    visibility: hidden;
  }
  .container, .container * {
    visibility: visible;
  }
  .container {
    position: absolute;
    left: 0;
    top: 0;
    width: 100%;
  }
}
.cursor-pointer {
  cursor: pointer;
}
</style>
