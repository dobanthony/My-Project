<template>
  <SellerDashboardLayout>
    <div class="container mt-5">
      <h2><i class="bi bi-person-circle me-2"></i>Hello,{{ user.name }}</h2>

      <!-- Alert if no shop exists -->
      <div v-if="!shop" class="alert alert-warning mt-3">
        You have not created a shop yet. Please fill out the form below to create one.
      </div>

      <!-- Show shop details -->
      <div v-else class="card mt-3 p-3">
        <h5 class="text-success mb-3">Your Shop Details</h5>
        <p><strong>Name:</strong> {{ shop.shop_name }}</p>
        <p><strong>Description:</strong> {{ shop.shop_description }}</p>
        <p><strong>Phone Number:</strong> {{ shop.phone_number }}</p>
        <p><strong>Email Address:</strong> {{ shop.email_address }}</p>
        <div v-if="shop.shop_logo">
          <img :src="`/storage/${shop.shop_logo}`" alt="Shop Logo" class="img-thumbnail" style="max-height: 120px" />
        </div>
      </div>

      <!-- Create / Update Form -->
      <div class="card mt-4 p-4">
        <h5 class="text-success">{{ shop ? 'Update' : 'Create' }} Your Shop</h5>
        <form @submit.prevent="submit" enctype="multipart/form-data">
          <div class="mb-3">
            <label class="form-label">Shop Name</label>
            <input type="text" class="form-control" v-model="form.shop_name" required />
          </div>

          <div class="mb-3">
            <label class="form-label">Shop Description</label>
            <textarea class="form-control" rows="3" v-model="form.shop_description"></textarea>
          </div>

          <div class="mb-3">
            <label class="form-label">Phone Number</label>
            <input type="text" class="form-control" v-model="form.phone_number" />
          </div>

          <div class="mb-3">
            <label class="form-label">Email Address</label>
            <input type="email" class="form-control" v-model="form.email_address" />
          </div>

          <div class="mb-3">
            <label class="form-label">Shop Logo</label>
            <input type="file" class="form-control" @change="e => form.shop_logo = e.target.files[0]" />
          </div>

          <button type="submit" class="btn btn-success">
            {{ shop ? 'Update Shop' : 'Create Shop' }}
          </button>
        </form>
      </div>
    </div>
  </SellerDashboardLayout>
</template>

<script setup>
import SellerDashboardLayout from '@/Layouts/SellerDashboardLayout.vue'
import Flash from '@/Layouts/Flash.vue'
import { useForm } from '@inertiajs/vue3'
import { defineOptions, defineProps } from 'vue'

defineOptions({
  layout: Flash
})

const props = defineProps({
  user: Object,
  shop: Object
})

const form = useForm({
  shop_name: props.shop?.shop_name ?? '',
  shop_description: props.shop?.shop_description ?? '',
  shop_logo: null,
  phone_number: props.shop?.phone_number ?? '',
  email_address: props.shop?.email_address ?? '',
})

const submit = () => {
  form.post('/seller/shop', { forceFormData: true })
}
</script>
