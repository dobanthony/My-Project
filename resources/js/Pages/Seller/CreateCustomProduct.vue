<template>
  <SellerDashboardLayout>
    <div class="container">
      <h2 class="mb-4">➕ Create Custom Product</h2>

      <form @submit.prevent="submit">
        <!-- Basic Product Info -->
        <input v-model="form.name" class="form-control mb-2" placeholder="Product Name" />
        <input v-model="form.price" type="number" class="form-control mb-2" placeholder="Price" />
        <input v-model="form.stock" type="number" class="form-control mb-2" placeholder="Stock" />
        <textarea v-model="form.description" class="form-control mb-2" placeholder="Description"></textarea>
        <input type="file" @change="e => form.image = e.target.files[0]" class="form-control mb-3" />

        <!-- Customization Options -->
        <h5>Customization Options</h5>

        <div class="form-check">
          <input v-model="form.allow_color" type="checkbox" class="form-check-input" id="color" />
          <label class="form-check-label" for="color">Allow Color</label>
        </div>

        <div class="form-check">
          <input v-model="form.allow_size" type="checkbox" class="form-check-input" id="size" />
          <label class="form-check-label" for="size">Allow Size</label>
        </div>

        <div class="form-check">
          <input v-model="form.allow_material" type="checkbox" class="form-check-input" id="material" />
          <label class="form-check-label" for="material">Allow Material</label>
        </div>

        <div class="form-check">
          <input v-model="form.allow_name" type="checkbox" class="form-check-input" id="name" />
          <label class="form-check-label" for="name">Allow Custom Name</label>
        </div>

        <!-- ✅ New: Allow Description -->
        <div class="form-check">
          <input v-model="form.allow_description" type="checkbox" class="form-check-input" id="description" />
          <label class="form-check-label" for="description">Allow Description</label>
        </div>

        <button class="btn btn-success mt-3">Create Custom Product</button>
      </form>
    </div>
  </SellerDashboardLayout>
</template>

<script setup>
import SellerDashboardLayout from '@/Layouts/SellerDashboardLayout.vue'
import { useForm } from '@inertiajs/vue3'

const form = useForm({
  name: '',
  price: '',
  stock: '',
  description: '',
  image: null,
  allow_color: false,
  allow_size: false,
  allow_material: false,
  allow_name: false,
  allow_description: false, // ✅ NEW
})

const submit = () => {
  form.post('/seller/custom-products', { forceFormData: true })
}
</script>

<style scoped>
input.form-control:focus {
  border-color: #28a745; /* green */
  box-shadow: 0 0 0 0.25rem rgba(40, 167, 69, 0.5); /* green with 50% opacity */
}
textarea.form-control:focus {
  border-color: #28a745; /* green */
  box-shadow: 0 0 0 0.25rem rgba(40, 167, 69, 0.5); /* green with 50% opacity */
}
.form-check-input:checked {
  background-color: #28a745 !important; /* Bootstrap success green */
  border-color: #28a745 !important;
}
.form-check-input {
  cursor: pointer;
}
.form-check-input:focus {
  border-color: #28a745; /* green */
  box-shadow: 0 0 0 0.25rem rgba(40, 167, 69, 0.5); /* green with 50% opacity */
}
</style>
