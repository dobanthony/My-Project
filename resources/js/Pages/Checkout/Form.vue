<template>
  <div class="container py-5">
    <h2 class="mb-4">🧾 Checkout for {{ product.name }}</h2>

    <div class="card p-4 shadow-sm">
      <form @submit.prevent="submit">
        <!-- Personal Info -->
        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label">Full Name</label>
            <input v-model="form.full_name" type="text" class="form-control" :class="{ 'is-invalid': form.errors.full_name }" required />
            <div v-if="form.errors.full_name" class="invalid-feedback">{{ form.errors.full_name }}</div>
          </div>

          <div class="col-md-6 mb-3">
            <label class="form-label">Phone Number</label>
            <input v-model="form.phone_number" type="text" class="form-control" :class="{ 'is-invalid': form.errors.phone_number }" required />
            <div v-if="form.errors.phone_number" class="invalid-feedback">{{ form.errors.phone_number }}</div>
          </div>
        </div>

        <div class="mb-3">
          <label class="form-label">Email</label>
          <input v-model="form.email" type="email" class="form-control" :class="{ 'is-invalid': form.errors.email }" required />
          <div v-if="form.errors.email" class="invalid-feedback">{{ form.errors.email }}</div>
        </div>

        <div class="mb-3">
          <label class="form-label">Delivery Address</label>
          <textarea v-model="form.delivery_address" class="form-control" rows="3" :class="{ 'is-invalid': form.errors.delivery_address }" required></textarea>
          <div v-if="form.errors.delivery_address" class="invalid-feedback">{{ form.errors.delivery_address }}</div>
        </div>

        <div class="mb-3">
          <label class="form-label">Notes (optional)</label>
          <textarea v-model="form.notes" class="form-control" rows="2"></textarea>
        </div>

        <!-- Customization (Optional, for later use) -->
        <div v-if="product.customization" class="border rounded p-3 bg-light mb-4">
          <h5>🎨 Product Customization</h5>

          <div v-if="product.customization.allow_color" class="mb-3">
            <label class="form-label">Color</label>
            <select v-model="form.color" class="form-select">
              <option disabled value="">Choose color</option>
              <option>Red</option>
              <option>Blue</option>
              <option>Black</option>
            </select>
          </div>

          <div v-if="product.customization.allow_size" class="mb-3">
            <label class="form-label">Size</label>
            <select v-model="form.size" class="form-select">
              <option disabled value="">Choose size</option>
              <option>Small</option>
              <option>Medium</option>
              <option>Large</option>
            </select>
          </div>

          <div v-if="product.customization.allow_material" class="mb-3">
            <label class="form-label">Material</label>
            <select v-model="form.material" class="form-select">
              <option disabled value="">Choose material</option>
              <option>Beads</option>
              <option>Leather</option>
              <option>Metal</option>
            </select>
          </div>

          <div v-if="product.customization.allow_name" class="mb-3">
            <label class="form-label">Custom Name</label>
            <input v-model="form.custom_name" type="text" class="form-control" placeholder="e.g. John or initials" />
          </div>
        </div>

        <div class="mb-3">
          <label class="form-label">Quantity</label>
          <input
            v-model.number="form.quantity"
            type="number"
            min="1"
            :max="product.stock"
            class="form-control w-25"
            required
            :class="{ 'is-invalid': form.errors.quantity }"
          />
          <div v-if="form.errors.quantity" class="invalid-feedback">{{ form.errors.quantity }}</div>
        </div>

        <button type="submit" class="btn btn-success">🛒 Place Order</button>
        <Link :href="`/product/${product.id}`" class="btn btn-outline-secondary ms-2">⬅️ Back</Link>
      </form>
    </div>
  </div>
</template>

<script setup>
import { useForm, Link } from '@inertiajs/vue3'
import { defineProps } from 'vue'

const props = defineProps({
  product: Object,
  quantity: Number
})

const form = useForm({
  product_id: props.product.id,
  full_name: '',
  phone_number: '',
  email: '',
  delivery_address: '',
  notes: '',
  quantity: props.quantity || 1,

  // Custom fields (not required yet)
  color: '',
  size: '',
  material: '',
  custom_name: ''
})

const submit = () => {
  form.post('/checkout')
}
</script>
