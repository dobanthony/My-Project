<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="d-flex justify-content-center align-items-center min-vh-100 bg-light">
        <div class="w-100" style="max-width: 420px;">

            <!-- Card -->
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-body p-4">
                    <slot />
                </div>
            </div>

            <!-- Footer -->
            <div class="text-center mt-4 small text-muted">
                <p class="mb-1">
                    Don’t have an account?
                    <Link href="/register" class="text-decoration-none text-success fw-semibold">
                        Register here
                    </Link>
                </p>
                <p>&copy; {{ new Date().getFullYear() }} CraftSmart Artisan Ecommerce System</p>
            </div>
        </div>
    </div>
</template>
