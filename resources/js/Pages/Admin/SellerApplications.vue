<template>
  <AdminDashboardLayout>
    <div class="container py-4">
      <h4 class="mb-4">
        <i class="bi bi-send text-success me-2"></i> Seller Applications
      </h4>

      <!-- No Applications -->
      <div v-if="applications.length === 0" class="alert alert-info">
        No applications pending.
      </div>

      <!-- Applications Table -->
      <div v-else class="table-responsive shadow-sm">
        <table class="table table-hover align-middle">
          <thead class="table-success">
            <tr>
              <th>#</th>
              <th>Name</th>
              <th>Email</th>
              <th>Phone</th>
              <th class="text-center">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(user, index) in applications" :key="user.id">
              <td>{{ index + 1 }}</td>
              <td>
                {{ user.first_name }} {{ user.middle_name }}
                {{ user.last_name }}
              </td>
              <td>{{ user.email }}</td>
              <td>{{ user.phone || 'N/A' }}</td>

              <!-- Action Buttons -->
              <td class="text-center">
                <div class="d-flex justify-content-center gap-2">
                  
                  <!-- ✅ View Button -->
                  <Link
                    :href="route('admin.seller-applications.show', user.id)"
                    class="btn btn-info btn-sm text-white"
                  >
                    <i class="bi bi-eye me-1"></i> View
                  </Link>

                  <!-- Approve -->
                  <form
                    :action="`/admin/seller-applications/${user.id}/approve`"
                    method="POST"
                  >
                    <input type="hidden" name="_token" :value="csrf" />
                    <button class="btn btn-success btn-sm">
                      <i class="bi bi-check2 me-1"></i> Approve
                    </button>
                  </form>

                  <!-- Decline -->
                  <form
                    :action="`/admin/seller-applications/${user.id}/decline`"
                    method="POST"
                  >
                    <input type="hidden" name="_token" :value="csrf" />
                    <button class="btn btn-outline-danger btn-sm">
                      <i class="bi bi-x-lg me-1"></i> Decline
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AdminDashboardLayout>
</template>

<script setup>
import AdminDashboardLayout from '@/Layouts/AdminDashboardLayout.vue'
import { Link } from '@inertiajs/vue3'

// CSRF token from meta tag
const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content')

defineProps(['applications'])
</script>

<style scoped>
.table th,
.table td {
  vertical-align: middle;
}
</style>
