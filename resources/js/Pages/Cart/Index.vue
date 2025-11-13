<template>
  <DashboardLayout>
    <div class="container py-4">
      <!-- 🧾 Page Header -->
      <div class="mb-4 text-center">
        <h3 class="fw-bold text-success">
          <i class="bi bi-cart3 me-2"></i> My Shopping Cart
        </h3>
        <p class="text-muted">
          Review your selected items, update quantities, or proceed to checkout when ready.
        </p>
      </div>

      <!-- 🔔 Empty Cart -->
      <div v-if="cartItems.length === 0" class="alert alert-info text-center py-4 shadow-sm rounded">
        <i class="bi bi-cart-x fs-3 d-block mb-2"></i>
        <strong>Your cart is empty!</strong>
        <p class="text-muted mb-0">Browse products and add items to your cart to get started.</p>
      </div>

      <!-- 📦 Cart Content -->
      <div v-else class="bg-white shadow-sm rounded p-3">
        <!-- 🖥️ Desktop Table -->
        <div class="d-none d-md-block table-responsive">
          <table class="table align-middle table-hover text-center">
            <thead class="table-success">
              <tr>
                <th>
                  <input type="checkbox" @change="toggleAllSelection" :checked="allSelected" />
                </th>
                <th class="text-start">Product</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Total</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="item in cartItems" :key="item.id">
                <td>
                  <input type="checkbox" :value="item.id" v-model="selectedCartIds" />
                </td>
                <td class="text-start">
                  <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-box-seam text-success fs-5"></i>
                    <span class="fw-semibold text-break">
                      {{ item.product?.name ?? 'Unknown Product' }}
                    </span>
                  </div>
                </td>
                <td class="text-secondary">₱{{ Number(item.product?.price ?? 0).toFixed(2) }}</td>
                <td>
                  <div class="d-flex justify-content-center align-items-center">
                    <input
                      v-model.number="item.quantity"
                      type="number"
                      min="1"
                      class="form-control form-control-sm text-center"
                      style="width: 70px;"
                      @change="updateQuantity(item)"
                    />
                  </div>
                </td>
                <td class="fw-bold text-success">
                  ₱{{ (item.quantity * Number(item.product?.price ?? 0)).toFixed(2) }}
                </td>
                <td>
                  <button @click="confirmDelete(item)" class="btn btn-outline-danger btn-sm rounded-circle">
                    <i class="bi bi-trash"></i>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- 📱 Mobile Card View -->
        <div class="d-md-none">
          <div v-for="item in cartItems" :key="item.id" class="card mb-3 border-0 shadow-sm rounded-3">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="d-flex align-items-center">
                  <input
                    type="checkbox"
                    :value="item.id"
                    v-model="selectedCartIds"
                    class="form-check-input me-2"
                  />
                  <strong class="text-break">{{ item.product?.name ?? 'Unknown Product' }}</strong>
                </div>
                <button @click="confirmDelete(item)" class="btn btn-sm btn-outline-danger rounded-circle">
                  <i class="bi bi-trash"></i>
                </button>
              </div>

              <div class="mb-2 text-secondary">
                <small>Price:</small>
                <div>₱{{ Number(item.product?.price ?? 0).toFixed(2) }}</div>
              </div>

              <div class="mb-2">
                <small class="text-secondary">Quantity:</small>
                <input
                  v-model.number="item.quantity"
                  type="number"
                  min="1"
                  class="form-control form-control-sm text-center"
                  @change="updateQuantity(item)"
                />
              </div>

              <div>
                <small class="text-secondary">Total:</small>
                <div class="fw-bold text-success fs-6">
                  ₱{{ (item.quantity * Number(item.product?.price ?? 0)).toFixed(2) }}
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- 💳 Cart Summary -->
        <div
          class="mt-4 border-top pt-3 d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3"
        >
          <div class="d-flex align-items-center gap-2">
            <i class="bi bi-cash-stack text-success fs-4"></i>
            <div>
              <strong>Selected Total:</strong>
              <span class="fs-5 text-success fw-bold">₱{{ selectedTotal.toFixed(2) }}</span>
            </div>
          </div>
          <button
            class="btn btn-success px-4 py-2 fw-semibold"
            :disabled="selectedCartIds.length === 0"
            @click="buyNow"
          >
            <i class="bi bi-bag-check-fill me-2"></i>Proceed to Checkout
          </button>
        </div>
      </div>
    </div>

    <!-- ✅ Toast Notification -->
    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 2000">
      <div
        class="toast align-items-center text-bg-success border-0 shadow"
        role="alert"
        aria-live="assertive"
        aria-atomic="true"
        ref="successToast"
      >
        <div class="d-flex">
          <div class="toast-body fw-semibold" ref="toastMessage">Success</div>
          <button
            type="button"
            class="btn-close btn-close-white me-2 m-auto"
            data-bs-dismiss="toast"
            aria-label="Close"
          ></button>
        </div>
      </div>
    </div>

    <!-- 🗑️ Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" ref="deleteModalRef">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-3">
          <div class="modal-header bg-danger text-white">
            <h5 class="modal-title fw-bold">
              <i class="bi bi-exclamation-triangle me-2"></i>Confirm Removal
            </h5>
            <button
              type="button"
              class="btn-close btn-close-white"
              data-bs-dismiss="modal"
              aria-label="Close"
            ></button>
          </div>
          <div class="modal-body">
            Are you sure you want to remove
            <strong class="text-danger">{{ itemToDelete?.product?.name }}</strong>
            from your cart?
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
              <i class="bi bi-x-circle me-1"></i>Cancel
            </button>
            <button type="button" class="btn btn-danger" @click="deleteConfirmed">
              <i class="bi bi-trash me-1"></i>Remove
            </button>
          </div>
        </div>
      </div>
    </div>
  </DashboardLayout>
</template>

<script setup>
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import { router } from '@inertiajs/vue3'
import { ref, computed } from 'vue'

const props = defineProps({ cartItems: Array })

const selectedCartIds = ref([])
const successToast = ref(null)
const toastMessage = ref('')
const itemToDelete = ref(null)
const deleteModalRef = ref(null)

const showToast = (message = 'Success!') => {
  toastMessage.value = message
  const toast = new bootstrap.Toast(successToast.value)
  toast.show()
}

const allSelected = computed(() =>
  props.cartItems.length > 0 && selectedCartIds.value.length === props.cartItems.length
)

const toggleAllSelection = () => {
  selectedCartIds.value = allSelected.value ? [] : props.cartItems.map(item => item.id)
}

const updateQuantity = (item) => {
  router.patch(route('cart.update', item.id), { quantity: item.quantity }, {
    preserveScroll: true,
    onSuccess: () => showToast('Quantity updated!')
  })
}

const confirmDelete = (item) => {
  itemToDelete.value = item
  const modal = new bootstrap.Modal(deleteModalRef.value)
  modal.show()
}

const deleteConfirmed = () => {
  const modal = bootstrap.Modal.getInstance(deleteModalRef.value)
  router.delete(route('cart.destroy', itemToDelete.value.id), {
    preserveScroll: true,
    onSuccess: () => {
      showToast('Item removed from cart!')
      modal.hide()
    }
  })
}

const selectedItems = computed(() =>
  props.cartItems.filter(item => selectedCartIds.value.includes(item.id))
)

const selectedTotal = computed(() => {
  return selectedItems.value.reduce((sum, item) => {
    const price = Number(item.product?.price ?? 0)
    return sum + price * item.quantity
  }, 0)
})

const buyNow = () => {
  const items = selectedItems.value
  if (items.length === 0) return

  showToast('Redirecting to checkout...')
  setTimeout(() => {
    router.visit(route('checkout.bulkForm'), {
      data: {
        items: items.map(i => ({
          product: {
            id: i.product.id,
            name: i.product.name,
            price: i.product.price
          },
          quantity: i.quantity
        }))
      },
      method: 'post',
      preserveState: true,
      preserveScroll: true
    })
  }, 1000)
}
</script>
