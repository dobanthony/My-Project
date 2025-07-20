<template>
  <AdminDashboardLayout>
    <div class="container py-4">
      <h2 class="mb-4">✏️ Edit User</h2>

      <form @submit.prevent="submit">
        <!-- First Name -->
        <div class="mb-3">
          <label class="form-label">First Name</label>
          <input v-model="form.first_name" type="text" class="form-control" :class="{ 'is-invalid': form.errors.first_name }" />
          <div class="invalid-feedback" v-if="form.errors.first_name">{{ form.errors.first_name }}</div>
        </div>

        <!-- Middle Name -->
        <div class="mb-3">
          <label class="form-label">Middle Name (Optional)</label>
          <input v-model="form.middle_name" type="text" class="form-control" :class="{ 'is-invalid': form.errors.middle_name }" />
          <div class="invalid-feedback" v-if="form.errors.middle_name">{{ form.errors.middle_name }}</div>
        </div>

        <!-- Last Name -->
        <div class="mb-3">
          <label class="form-label">Last Name</label>
          <input v-model="form.last_name" type="text" class="form-control" :class="{ 'is-invalid': form.errors.last_name }" />
          <div class="invalid-feedback" v-if="form.errors.last_name">{{ form.errors.last_name }}</div>
        </div>

        <!-- Email -->
        <div class="mb-3">
          <label class="form-label">Email</label>
          <input v-model="form.email" type="email" class="form-control" :class="{ 'is-invalid': form.errors.email }" />
          <div class="invalid-feedback" v-if="form.errors.email">{{ form.errors.email }}</div>
        </div>

        <!-- Phone -->
        <div class="mb-3">
          <label class="form-label">Phone Number</label>
          <input v-model="form.phone" type="text" class="form-control" :class="{ 'is-invalid': form.errors.phone }" />
          <div class="invalid-feedback" v-if="form.errors.phone">{{ form.errors.phone }}</div>
        </div>

        <!-- Address -->
        <div class="mb-3">
          <label class="form-label">Address</label>
          <input v-model="form.address" type="text" class="form-control" :class="{ 'is-invalid': form.errors.address }" />
          <div class="invalid-feedback" v-if="form.errors.address">{{ form.errors.address }}</div>
        </div>

        <!-- Date of Birth -->
        <div class="mb-3">
          <label class="form-label">Date of Birth</label>
          <input v-model="form.dob" type="date" class="form-control" :class="{ 'is-invalid': form.errors.dob }" />
          <div class="invalid-feedback" v-if="form.errors.dob">{{ form.errors.dob }}</div>
        </div>

        <!-- Facebook -->
        <div class="mb-3">
          <label class="form-label">Facebook Link (Optional)</label>
          <input v-model="form.social_links.facebook" type="url" class="form-control" placeholder="https://facebook.com/username" :class="{ 'is-invalid': form.errors['social_links.facebook'] }" />
          <div class="invalid-feedback" v-if="form.errors['social_links.facebook']">{{ form.errors['social_links.facebook'] }}</div>
        </div>

        <!-- Twitter -->
        <div class="mb-3">
          <label class="form-label">Twitter Link (Optional)</label>
          <input v-model="form.social_links.twitter" type="url" class="form-control" placeholder="https://twitter.com/username" :class="{ 'is-invalid': form.errors['social_links.twitter'] }" />
          <div class="invalid-feedback" v-if="form.errors['social_links.twitter']">{{ form.errors['social_links.twitter'] }}</div>
        </div>

        <!-- Role -->
        <!-- <div class="mb-3">
          <label class="form-label">Role</label>
          <select v-model="form.role" class="form-select" :class="{ 'is-invalid': form.errors.role }">
            <option value="user">User</option>
            <option value="admin">Admin</option>
          </select>
          <div class="invalid-feedback" v-if="form.errors.role">{{ form.errors.role }}</div>
        </div> -->
        <!-- Role -->
<div class="mb-3">
  <label class="form-label">Role</label>
  <select v-model="form.role" class="form-select" :class="{ 'is-invalid': form.errors.role }">
    <option value="user">User</option>
    <option value="seller">Seller</option>
    <option value="admin">Admin</option>
  </select>
  <div class="invalid-feedback" v-if="form.errors.role">{{ form.errors.role }}</div>
</div>


        <!-- Submit Buttons -->
        <div class="d-flex gap-2">
          <button type="submit" class="btn btn-primary" :disabled="form.processing">
            {{ form.processing ? 'Updating...' : 'Update User' }}
          </button>
          <Link href="/admin/users" class="btn btn-secondary">Cancel</Link>
        </div>
      </form>
    </div>
  </AdminDashboardLayout>
</template>

<script setup>
import { useForm, Link, router } from '@inertiajs/vue3'
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
  social_links: {
    facebook: props.user.social_links?.facebook || '',
    twitter: props.user.social_links?.twitter || ''
  },
  role: props.user.role
})

function submit() {
  form.put(`/admin/users/${props.user.id}`, {
    preserveScroll: true,
    onSuccess: () => {
      // Redirect to index after update
      router.visit('/admin/users')
    }
  })
}
</script>
