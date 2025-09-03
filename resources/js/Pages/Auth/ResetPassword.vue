<script setup>
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    email: {
        type: String,
        required: true,
    },
    token: {
        type: String,
        required: true,
    },
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.store'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Reset Password" />

    <div class="d-flex justify-content-center align-items-center min-vh-100 bg-light">
        <div
            class="card shadow-lg rounded-4 p-4 w-100 animate-card"
            style="max-width: 500px;"
            :class="{ 'shake': form.errors.email || form.errors.password || form.errors.password_confirmation }"
        >
            <!-- Header -->
            <div class="text-center mb-3">
                <i class="bi bi-shield-lock-fill text-success display-5"></i>
                <h4 class="fw-bold mt-2">Reset Password</h4>
                <p class="text-muted small">
                    Enter your new password below to regain access.
                </p>
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

                <!-- Password -->
                <div class="mb-3">
                    <label for="password" class="form-label fw-semibold">
                        <i class="bi bi-lock-fill me-1"></i> New Password
                    </label>
                    <input
                        id="password"
                        type="password"
                        class="form-control rounded-pill"
                        v-model="form.password"
                        required
                        autocomplete="new-password"
                        :class="{ 'is-invalid': form.errors.password }"
                    />
                    <div v-if="form.errors.password" class="invalid-feedback">
                        {{ form.errors.password }}
                    </div>
                </div>

                <!-- Confirm Password -->
                <div class="mb-3">
                    <label for="password_confirmation" class="form-label fw-semibold">
                        <i class="bi bi-lock-fill me-1"></i> Confirm Password
                    </label>
                    <input
                        id="password_confirmation"
                        type="password"
                        class="form-control rounded-pill"
                        v-model="form.password_confirmation"
                        required
                        autocomplete="new-password"
                        :class="{ 'is-invalid': form.errors.password_confirmation }"
                    />
                    <div v-if="form.errors.password_confirmation" class="invalid-feedback">
                        {{ form.errors.password_confirmation }}
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
                        Reset Password
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

/* Button hover effect */
.btn-animate {
    transition: all 0.2s ease;
}
.btn-animate:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 12px rgba(25, 135, 84, 0.3);
}

/* Shake animation on error */
.shake {
    animation: shake 0.3s ease-in-out;
}
@keyframes shake {
    0%, 100% { transform: translateX(0); }
    20%, 60% { transform: translateX(-6px); }
    40%, 80% { transform: translateX(6px); }
}
</style>
