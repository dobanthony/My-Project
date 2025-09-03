<script setup>
import { Head, useForm } from '@inertiajs/vue3';

defineProps({
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <Head title="Forgot Password" />

    <div class="d-flex justify-content-center align-items-center min-vh-100 bg-light">
        <div
            class="card shadow-lg rounded-4 p-4 animate-card"
            style="max-width: 500px; width: 100%;"
            :class="{ 'shake': form.errors.email }"
        >
            <!-- Header -->
            <div class="text-center mb-3">
                <i class="bi bi-envelope-lock-fill text-success display-5"></i>
                <h4 class="fw-bold mt-2">Forgot Password</h4>
                <p class="text-muted small">
                    No problem! Enter your email address and we’ll send you a reset link.
                </p>
            </div>

            <!-- Status Message -->
            <div v-if="status" class="alert alert-success py-2 animate-fade">
                {{ status }}
            </div>

            <!-- Form -->
            <form @submit.prevent="submit">
                <!-- Email -->
                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold">
                        <i class="bi bi-envelope-fill me-1"></i> Email Address
                    </label>
                    <input
                        id="email"
                        type="email"
                        class="form-control rounded-pill"
                        v-model="form.email"
                        required
                        autofocus
                        autocomplete="username"
                        :class="{ 'is-invalid': form.errors.email }"
                    />
                    <div v-if="form.errors.email" class="invalid-feedback">
                        {{ form.errors.email }}
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="d-grid mt-4">
                    <button
                        type="submit"
                        class="btn btn-success rounded-pill fw-semibold btn-animate"
                        :disabled="form.processing"
                    >
                        <span
                            v-if="form.processing"
                            class="spinner-border spinner-border-sm me-2"
                        ></span>
                        Email Password Reset Link
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<style scoped>
.bg-light {
    background: linear-gradient(135deg, #f8f9fa, #e9f7ef);
}

/* Card entrance animation */
.animate-card {
    animation: fadeInUp 0.6s ease-out;
}
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Fade for alerts */
.animate-fade {
    animation: fadeIn 0.5s ease-in;
}
@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

/* Button hover effect */
.btn-animate {
    transition: all 0.2s ease;
}
.btn-animate:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 12px rgba(25, 135, 84, 0.3);
}

/* Shake animation for error feedback */
.shake {
    animation: shake 0.3s ease-in-out;
}
@keyframes shake {
    0%, 100% { transform: translateX(0); }
    20%, 60% { transform: translateX(-6px); }
    40%, 80% { transform: translateX(6px); }
}
</style>
