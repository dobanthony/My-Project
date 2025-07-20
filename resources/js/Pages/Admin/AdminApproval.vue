<template>
  <div class="container mt-5">
    <div class="card shadow">
      <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
        <h4 class="mb-0">🛍️ Seller Applications (Pending)</h4>
      </div>
      <div class="card-body">

        <!-- Flash Messages -->
        <div v-if="$page.props.flash?.success" class="alert alert-success">
          {{ $page.props.flash.success }}
        </div>
        <div v-if="$page.props.flash?.error" class="alert alert-danger">
          {{ $page.props.flash.error }}
        </div>

        <!-- No applications -->
        <div v-if="pendingSellers.length === 0" class="text-muted">
          No pending applications.
        </div>

        <!-- Table -->
        <table v-if="pendingSellers.length > 0" class="table table-bordered">
          <thead class="table-light">
            <tr>
              <th>Name</th>
              <th>Email</th>
              <th>Submitted At</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="seller in pendingSellers" :key="seller.id">
              <td>{{ seller.name }}</td>
              <td>{{ seller.email }}</td>
              <td>{{ formatDate(seller.created_at) }}</td>
              <td>
                <button class="btn btn-success btn-sm me-2" @click="approve(seller.id)">
                  ✅ Approve
                </button>
                <button class="btn btn-danger btn-sm" @click="decline(seller.id)">
                  ❌ Decline
                </button>
              </td>
            </tr>
          </tbody>
        </table>

      </div>
    </div>
  </div>
</template>

<script setup>
import { router } from '@inertiajs/vue3'
import { defineProps } from 'vue'

const props = defineProps({
  pendingSellers: Array
})

const approve = (userId) => {
  router.post(route('admin.seller.approve', userId))
}

const decline = (userId) => {
  router.post(route('admin.seller.decline', userId))
}

const formatDate = (date) => {
  return new Date(date).toLocaleString()
}
</script>
