<template>
  <div class="container py-5">
    <h2 class="mb-4">🧾 Delivery Information</h2>

    <div class="card p-4 shadow-sm">
      <form @submit.prevent="submit">
        <!-- Delivery Info -->
        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label">Full Name</label>
            <input v-model="form.full_name" type="text" class="form-control" required />
          </div>

          <div class="col-md-6 mb-3">
            <label class="form-label">Phone Number</label>
            <input v-model="form.phone_number" type="text" class="form-control" required />
          </div>
        </div>

        <div class="mb-3">
          <label class="form-label">Email</label>
          <input v-model="form.email" type="email" class="form-control" required />
        </div>

        <div class="mb-3">
          <label class="form-label">Delivery Address</label>
          <textarea v-model="form.delivery_address" class="form-control" rows="3" required></textarea>
        </div>

        <div class="mb-3">
          <label class="form-label">Notes (Optional)</label>
          <textarea v-model="form.notes" class="form-control" rows="2"></textarea>
        </div>

        <hr />

        <!-- Orders & Customizations -->
        <div v-for="(order, index) in form.orders" :key="index" class="mb-4 border rounded p-3 bg-light">
          <h5 class="mb-2">🧺 {{ order.product.name }} (x{{ order.quantity }})</h5>

          <!-- Customization options -->
          <div v-if="order.product.customization">
            <div v-if="order.product.customization.allow_color" class="mb-2">
              <label class="form-label">Color</label>
              <select v-model="order.color" class="form-select">
                <option disabled value="">Choose color</option>
                <option>Red</option>
                <option>Blue</option>
                <option>Black</option>
              </select>
            </div>

            <div v-if="order.product.customization.allow_size" class="mb-2">
              <label class="form-label">Size</label>
              <select v-model="order.size" class="form-select">
                <option disabled value="">Choose size</option>
                <option>Small</option>
                <option>Medium</option>
                <option>Large</option>
              </select>
            </div>

            <div v-if="order.product.customization.allow_material" class="mb-2">
              <label class="form-label">Material</label>
              <select v-model="order.material" class="form-select">
                <option disabled value="">Choose material</option>
                <option>Beads</option>
                <option>Leather</option>
                <option>Metal</option>
              </select>
            </div>

            <div v-if="order.product.customization.allow_name" class="mb-2">
              <label class="form-label">Custom Name</label>
              <input v-model="order.custom_name" type="text" class="form-control" placeholder="e.g. John's Bracelet" />
            </div>

            <div v-if="order.product.customization.allow_description" class="mb-2">
              <label class="form-label">Custom Description</label>
              <textarea v-model="order.custom_description" class="form-control" rows="2" placeholder="Describe your custom request..."></textarea>
            </div>
          </div>
        </div>

        <button type="submit" class="btn btn-success">🛒 Place Orders</button>
        <Link href="/cart" class="btn btn-outline-secondary ms-2">⬅ Back to Cart</Link>
      </form>
    </div>
  </div>
</template>

<script setup>
import { useForm, Link } from '@inertiajs/vue3'
import { defineProps } from 'vue'

const props = defineProps({
  cartItems: Array,
  lastDeliveryInfo: Object
})

// 🛒 Map cart items into form.orders with prefilled custom values (if any)
const orders = props.cartItems
  .filter(item => item.product && item.product.id)
  .map(item => ({
    product_id: item.product.id,
    quantity: item.quantity || 1,
    product: item.product, // for display only

    // Customization fields – prefilled if passed from cart
    color: item.color || '',
    size: item.size || '',
    material: item.material || '',
    custom_name: item.custom_name || '',
    custom_description: item.custom_description || ''
  }))

const form = useForm({
  full_name: props.lastDeliveryInfo?.full_name || '',
  phone_number: props.lastDeliveryInfo?.phone_number || '',
  email: props.lastDeliveryInfo?.email || '',
  delivery_address: props.lastDeliveryInfo?.delivery_address || '',
  notes: props.lastDeliveryInfo?.notes || '',
  orders
})

const submit = () => {
  form.post('/checkout-bulk/store')
}
</script>
