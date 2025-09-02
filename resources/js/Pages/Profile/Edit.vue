<template>
  <Head title="Profile" />

  <AuthenticatedLayout>

    <!-- Page Content -->
    <div class="container py-3 d-flex justify-content-center">
      <div class="col-lg-8 col-md-10 w-100">

        <!-- Avatar Upload -->
        <div class="card shadow-sm mb-4">
          <div class="card-body text-center">
            <AvatarUpload />
          </div>
        </div>

        <!-- Update Profile Info -->
        <div class="card shadow-sm mb-4">
          <div class="card-body">
            <UpdateProfileInformationForm
              :must-verify-email="mustVerifyEmail"
              :status="status"
            />
          </div>
        </div>

        <!-- Update Password -->
        <div class="card shadow-sm mb-4">
          <div class="card-body">
            <UpdatePasswordForm />
          </div>
        </div>

        <!-- Delete Account -->
        <div class="card shadow-sm">
          <div class="card-body">
            <DeleteUserForm />
          </div>
        </div>

      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import AvatarUpload from './Partials/AvatarUpload.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
  mustVerifyEmail: {
    type: Boolean,
  },
  status: {
    type: String,
  },
});
</script>
