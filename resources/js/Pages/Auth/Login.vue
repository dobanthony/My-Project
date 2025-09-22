<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const showPassword = ref(false);
const togglePassword = () => (showPassword.value = !showPassword.value);

// Animation state
const showCard = ref(false);
onMounted(() => {
    setTimeout(() => (showCard.value = true), 100);
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Log in" />

    <div class="d-flex flex-column justify-content-center align-items-center min-vh-100 bg-gradient">
        <div class="w-100" style="max-width: 500px;">
            <!-- Status Alert -->
            <transition name="fade">
                <div
                    v-if="status"
                    class="alert alert-success alert-dismissible fade show mb-3 shadow-sm"
                    role="alert"
                >
                    <i class="bi bi-check-circle-fill me-2"></i> {{ status }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </transition>

            <!-- Card -->
            <transition name="slide-fade">
                <form
                    v-if="showCard"
                    @submit.prevent="submit"
                    class="card login-card border-0 animate-card"
                >
                    <div class="card-body p-4">
                        <!-- Header -->
                        <div class="text-center mb-4">
                            <i class="bi bi-shield-lock-fill text-success display-5"></i>
                            <h4 class="fw-bold text-success mt-2">Welcome Back</h4>
                            <p class="text-muted small">Please log in to continue</p>
                        </div>

                        <!-- Email -->
                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold text-success">
                                <i class="bi bi-envelope-fill me-1"></i> Email address
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
                            <label for="password" class="form-label fw-semibold text-success">
                                <i class="bi bi-lock-fill me-1"></i> Password
                            </label>
                            <div class="input-group">
                                <input
                                    :type="showPassword ? 'text' : 'password'"
                                    id="password"
                                    class="form-control rounded-start-pill"
                                    v-model="form.password"
                                    required
                                    autocomplete="current-password"
                                    :class="{ 'is-invalid': form.errors.password }"
                                />
                                <button
                                    type="button"
                                    class="btn btn-outline-success rounded-end-pill"
                                    @click="togglePassword"
                                    tabindex="-1"
                                >
                                    <i :class="showPassword ? 'bi bi-eye-slash' : 'bi bi-eye'"></i>
                                </button>
                                <div v-if="form.errors.password" class="invalid-feedback d-block">
                                    {{ form.errors.password }}
                                </div>
                            </div>
                        </div>

                        <!-- Remember -->
                        <div class="form-check mb-3">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                id="remember"
                                v-model="form.remember"
                            />
                            <label class="form-check-label" for="remember">Remember me</label>
                        </div>

                        <!-- Actions -->
                        <div class="d-flex flex-column gap-2">
                            <button
                                type="submit"
                                class="btn btn-success rounded-pill btn-animate"
                                :disabled="form.processing"
                            >
                                <span
                                    v-if="form.processing"
                                    class="spinner-border spinner-border-sm me-2"
                                ></span>
                                <i v-else class="bi bi-box-arrow-in-right me-1"></i>
                                Log in
                            </button>

                            <Link
                                v-if="canResetPassword"
                                :href="route('password.request')"
                                class="btn btn-link text-decoration-none small text-primary"
                            >
                                <i class="bi bi-question-circle me-1"></i> Forgot your password?
                            </Link>
                        </div>
                    </div>
                </form>
            </transition>

            <!-- Footer -->
            <footer class="text-center mt-4">
                <small class="text-muted">
                    <p class="mb-1">
                        Don’t have an account?
                        <Link href="/register" class="text-decoration-none text-success fw-semibold">
                            Register here
                        </Link>
                    </p>
                    © {{ new Date().getFullYear() }} CraftSmart·
                    <Link href="/" class="text-decoration-none text-success fw-semibold">Home</Link>
                </small>
            </footer>
        </div>
    </div>
</template>

<style scoped>
.bg-gradient {
    background: linear-gradient(135deg, #f8f9fa, #e9f7ef);
}

.login-card {
    border-radius: 1rem;
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}
.login-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
}

/* Input focus */
.form-control:focus {
    border-color: #198754;
    box-shadow: 0 0 0 0.25rem rgba(25, 135, 84, 0.25);
}

/* Fade animation */
.fade-enter-active, .fade-leave-active {
    transition: opacity 0.4s;
}
.fade-enter-from, .fade-leave-to {
    opacity: 0;
}

/* Slide + fade animation */
.slide-fade-enter-active {
    transition: all 0.6s ease;
}
.slide-fade-enter-from {
    transform: translateY(20px);
    opacity: 0;
}

/* Button hover animation */
.btn-animate {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.btn-animate:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 12px rgba(25, 135, 84, 0.3);
}
</style>
