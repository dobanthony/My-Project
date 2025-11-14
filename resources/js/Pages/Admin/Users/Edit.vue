<template>
  <Head title="Edit " />
  <AdminDashboardLayout>
    <div class="container py-4">
      <!-- Page Title -->
      <h4 class="mb-4 fw-bold text-success d-flex align-items-center">
        <i class="bi bi-pencil-square me-2"></i> Edit User
      </h4>

      <!-- Form Card -->
      <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-4">
          <form @submit.prevent="submit">
            <div class="row g-4">
              <!-- First Name -->
              <div class="col-md-6">
                <label class="form-label fw-semibold">
                  <i class="bi bi-person me-2 text-success"></i> First Name
                </label>
                <input
                  v-model="form.first_name"
                  type="text"
                  class="form-control rounded-3"
                  :class="{ 'is-invalid': form.errors.first_name }"
                  placeholder="Enter first name"
                />
                <div class="invalid-feedback" v-if="form.errors.first_name">
                  {{ form.errors.first_name }}
                </div>
              </div>

              <!-- Middle Name -->
              <div class="col-md-6">
                <label class="form-label fw-semibold">
                  <i class="bi bi-person me-2 text-success"></i> Middle Name (Optional)
                </label>
                <input
                  v-model="form.middle_name"
                  type="text"
                  class="form-control rounded-3"
                  :class="{ 'is-invalid': form.errors.middle_name }"
                  placeholder="Enter middle name"
                />
                <div class="invalid-feedback" v-if="form.errors.middle_name">
                  {{ form.errors.middle_name }}
                </div>
              </div>

              <!-- Last Name -->
              <div class="col-md-6">
                <label class="form-label fw-semibold">
                  <i class="bi bi-person me-2 text-success"></i> Last Name
                </label>
                <input
                  v-model="form.last_name"
                  type="text"
                  class="form-control rounded-3"
                  :class="{ 'is-invalid': form.errors.last_name }"
                  placeholder="Enter last name"
                />
                <div class="invalid-feedback" v-if="form.errors.last_name">
                  {{ form.errors.last_name }}
                </div>
              </div>

              <!-- Email -->
              <div class="col-md-6">
                <label class="form-label fw-semibold">
                  <i class="bi bi-envelope-at me-2 text-success"></i> Email
                </label>
                <input
                  v-model="form.email"
                  type="email"
                  class="form-control rounded-3"
                  :class="{ 'is-invalid': form.errors.email }"
                  placeholder="Enter email"
                />
                <div class="invalid-feedback" v-if="form.errors.email">
                  {{ form.errors.email }}
                </div>
              </div>

              <!-- Phone -->
              <div class="col-md-6">
                <label class="form-label fw-semibold">
                  <i class="bi bi-telephone me-2 text-success"></i> Phone Number
                </label>
                <input
                  v-model="form.phone"
                  type="text"
                  class="form-control rounded-3"
                  :class="{ 'is-invalid': form.errors.phone }"
                  placeholder="Enter phone number"
                />
                <div class="invalid-feedback" v-if="form.errors.phone">
                  {{ form.errors.phone }}
                </div>
              </div>

              <!-- Address -->
              <div class="col-md-6">
                <label class="form-label fw-semibold">
                  <i class="bi bi-house me-2 text-success"></i> Address
                </label>
                <input
                  v-model="form.address"
                  type="text"
                  class="form-control rounded-3"
                  :class="{ 'is-invalid': form.errors.address }"
                  placeholder="Enter address"
                />
                <div class="invalid-feedback" v-if="form.errors.address">
                  {{ form.errors.address }}
                </div>
              </div>

              <!-- Date of Birth -->
              <div class="col-md-6">
                <label class="form-label fw-semibold">
                  <i class="bi bi-cake2 me-2 text-success"></i> Date of Birth
                </label>
                <input
                  v-model="form.dob"
                  type="date"
                  class="form-control rounded-3"
                  :class="{ 'is-invalid': form.errors.dob }"
                />
                <div class="invalid-feedback" v-if="form.errors.dob">
                  {{ form.errors.dob }}
                </div>
              </div>

              <!-- Role -->
              <div class="col-md-6">
                <label class="form-label fw-semibold">
                  <i class="bi bi-person-badge me-2 text-success"></i> Role
                </label>
                <select
                  v-model="form.role"
                  class="form-select rounded-3"
                  :class="{ 'is-invalid': form.errors.role }"
                >
                  <option value="user">User</option>
                  <option value="seller">Seller</option>
                  <option value="admin">Admin</option>
                </select>
                <div class="invalid-feedback" v-if="form.errors.role">
                  {{ form.errors.role }}
                </div>
              </div>
            </div>

            <!-- Action Buttons -->
            <div class="d-flex justify-content-end gap-2 mt-4">
              <button
                type="submit"
                class="btn btn-success px-4 rounded-pill"
                :disabled="form.processing"
              >
                <i class="bi bi-check-circle me-2"></i>
                {{ form.processing ? 'Updating...' : 'Update User' }}
              </button>
              <Link
                href="/admin/users"
                class="btn btn-outline-secondary px-4 rounded-pill"
              >
                <i class="bi bi-x-circle me-2"></i> Cancel
              </Link>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AdminDashboardLayout>
</template>

<script setup>
import { useForm, Link, router, Head } from '@inertiajs/vue3'
import AdminDashboardLayout from '@/Layouts/AdminDashboardLayout.vue'

const props = defineProps({
  user: Object
})

const form = useForm({
  first_name: props.user.first_name || '',
  middle_name: props.user.middle_name || '',
  last_name: props.user.last_name || '',
  email: props.user.email || '',
  phone: props.user.phone || '',
  address: props.user.address || '',
  dob: props.user.dob || '',
  role: props.user.role
})

function submit() {
  form.put(`/admin/users/${props.user.id}`, {
    preserveScroll: true,
    onSuccess: () => {
      router.visit('/admin/users')
    }
  })
}
</script>
