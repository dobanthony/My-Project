<template>
  <div class="container py-5">
    <h2 class="mb-4">🧾 Delivery Information</h2>

    <div class="card p-4 shadow-sm">
      <form @submit.prevent="submit">
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
  cartItems: Array
})

const validItems = props.cartItems.filter(item => item.product && item.product.id)

const orders = validItems.map(item => ({
  product_id: item.product.id,
  quantity: item.quantity
}))

const form = useForm({
  full_name: '',
  phone_number: '',
  email: '',
  delivery_address: '',
  notes: '',
  orders
})

const submit = () => {
  form.post('/checkout-bulk/store', {
  })
}
</script>
