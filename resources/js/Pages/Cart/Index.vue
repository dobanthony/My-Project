<template>
  <DashboardLayout>
    <div class="container">
      <!-- 🔔 Empty Cart -->
      <div v-if="cartItems.length === 0" class="alert alert-info text-center">
        Your cart is empty.
      </div>

      <!-- 📦 Cart Table (Desktop) -->
      <div v-else>
        <div class="d-none d-md-block table-responsive">
          <table class="table table-bordered table-hover align-middle">
            <thead class="table-success">
              <tr>
                <th style="width: 40px;">
                  <input type="checkbox" @change="toggleAllSelection" :checked="allSelected" />
                </th>
                <th>Product</th>
                <th>Price</th>
                <th style="width: 130px;">Quantity</th>
                <th>Total</th>
                <th style="width: 90px;">Action</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="item in cartItems" :key="item.id">
                <td>
                  <input type="checkbox" :value="item.id" v-model="selectedCartIds" />
                </td>
                <td class="text-break">{{ item.product?.name ?? 'Unknown Product' }}</td>
                <td>₱{{ Number(item.product?.price ?? 0).toFixed(2) }}</td>
                <td>
                  <input
                    v-model.number="item.quantity"
                    type="number"
                    min="1"
                    class="form-control form-control-sm"
                    @change="updateQuantity(item)"
                  />
                </td>
                <td>₱{{ (item.quantity * Number(item.product?.price ?? 0)).toFixed(2) }}</td>
                <td class="text-center">
                  <button @click="confirmDelete(item)" class="btn btn-danger btn-sm">
                    <i class="bi bi-trash"></i>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- 📱 Mobile Card View -->
        <div class="d-md-none">
          <div v-for="item in cartItems" :key="item.id" class="card mb-3 shadow-sm">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-start mb-2">
                <div class="d-flex align-items-center">
                  <input
                    type="checkbox"
                    :value="item.id"
                    v-model="selectedCartIds"
                    class="form-check-input me-2"
                  />
                  <strong class="text-break">{{ item.product?.name ?? 'Unknown Product' }}</strong>
                </div>
                <button @click="confirmDelete(item)" class="btn btn-sm btn-outline-danger">
                  <i class="bi bi-trash"></i>
                </button>
              </div>

              <div class="mb-2">
                <small class="text-muted">Price:</small>
                <div>₱{{ Number(item.product?.price ?? 0).toFixed(2) }}</div>
              </div>

              <div class="mb-2">
                <small class="text-muted">Quantity:</small>
                <input
                  v-model.number="item.quantity"
                  type="number"
                  min="1"
                  class="form-control form-control-sm"
                  @change="updateQuantity(item)"
                />
              </div>

              <div>
                <small class="text-muted">Total:</small>
                <div class="fw-bold text-success">
                  ₱{{ (item.quantity * Number(item.product?.price ?? 0)).toFixed(2) }}
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- 💳 Cart Summary -->
        <div class="mt-4 d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
          <div>
            <strong>Selected Total:</strong>
            <span class="fs-5 text-success">₱{{ selectedTotal.toFixed(2) }}</span>
          </div>
          <button class="btn btn-success" :disabled="selectedCartIds.length === 0" @click="buyNow">
            <i class="bi bi-bag me-2"></i>Buy Now
          </button>
        </div>
      </div>
    </div>

    <!-- ✅ Toast Notification -->
    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 2000">
      <div class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true" ref="successToast">
        <div class="d-flex">
          <div class="toast-body" ref="toastMessage">Success</div>
          <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" ref="deleteModalRef">
      <div
        class="modal-dialog modal-lg"
        style="position: absolute; top: 5%; left: 50%; transform: translateX(-50%)"
      >
        <div class="modal-content">
          <div class="modal-header bg-success text-white">
            <h5 class="modal-title"><i class="bi bi-trash"></i> Confirm Removal</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            Are you sure you want to remove <strong>{{ itemToDelete?.product?.name }}</strong> from your cart?
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-outline-success" data-bs-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-danger" @click="deleteConfirmed">Remove</button>
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

  showToast('Redirecting to delivery info...')
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
