<script setup>
import { Head, Link, useForm, usePage, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const form = useForm({
    first_name: '',
    middle_name: '',
    last_name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const showModal = ref(false);
const showPassword = ref(false);
const showConfirmPassword = ref(false);

const page = usePage();

// Watch backend success
watch(
    () => page.props.success,
    (val) => {
        if (val) {
            showModal.value = true;
        }
    }
);

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};

const goToDashboard = () => {
    showModal.value = false;
    router.visit(route('dashboard'));
};
</script>

<template>
    <Head title="Register" />

    <div class="d-flex flex-column justify-content-center align-items-center min-vh-100 bg-gradient">
        <div class="w-100" style="max-width: 650px; position: relative;">

            <!-- Loading Overlay -->
            <div v-if="form.processing" class="loading-overlay d-flex justify-content-center align-items-center">
                <div class="spinner-border text-success" role="status"></div>
            </div>

            <!-- Card -->
            <div class="card border-0 rounded-4 animate-card shadow-elevated">
                <div class="card-body p-4 fade-in">
                    <!-- Header -->
                    <div class="text-center mb-4">
                        <i class="bi bi-person-plus-fill text-success display-5"></i>
                        <h4 class="fw-bold text-success mt-2">Create Account</h4>
                        <p class="text-muted small">Fill in your details to get started</p>
                    </div>

                    <!-- Form -->
                    <form @submit.prevent="submit">
                        <div class="row g-3 mb-3">
                            <!-- First Name -->
                            <div class="col-md-4">
                                <label for="first_name" class="form-label fw-semibold">
                                    <i class="bi bi-person me-1"></i> First Name
                                </label>
                                <input
                                    id="first_name"
                                    type="text"
                                    class="form-control rounded-pill"
                                    v-model="form.first_name"
                                    required
                                    autofocus
                                    autocomplete="given-name"
                                    :class="{ 'is-invalid': form.errors.first_name }"
                                />
                                <div v-if="form.errors.first_name" class="invalid-feedback">
                                    {{ form.errors.first_name }}
                                </div>
                            </div>

                            <!-- Middle Name -->
                            <div class="col-md-4">
                                <label for="middle_name" class="form-label fw-semibold">
                                    <i class="bi bi-person-lines-fill me-1"></i> Middle Name
                                </label>
                                <input
                                    id="middle_name"
                                    type="text"
                                    class="form-control rounded-pill"
                                    v-model="form.middle_name"
                                    autocomplete="additional-name"
                                    :class="{ 'is-invalid': form.errors.middle_name }"
                                />
                                <div v-if="form.errors.middle_name" class="invalid-feedback">
                                    {{ form.errors.middle_name }}
                                </div>
                            </div>

                            <!-- Last Name -->
                            <div class="col-md-4">
                                <label for="last_name" class="form-label fw-semibold">
                                    <i class="bi bi-person-badge-fill me-1"></i> Last Name
                                </label>
                                <input
                                    id="last_name"
                                    type="text"
                                    class="form-control rounded-pill"
                                    v-model="form.last_name"
                                    required
                                    autocomplete="family-name"
                                    :class="{ 'is-invalid': form.errors.last_name }"
                                />
                                <div v-if="form.errors.last_name" class="invalid-feedback">
                                    {{ form.errors.last_name }}
                                </div>
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold">
                                <i class="bi bi-envelope-fill me-1"></i> Email address
                            </label>
                            <input
                                id="email"
                                type="email"
                                class="form-control rounded-pill"
                                v-model="form.email"
                                required
                                autocomplete="username"
                                :class="{ 'is-invalid': form.errors.email }"
                            />
                            <div v-if="form.errors.email" class="invalid-feedback">
                                {{ form.errors.email }}
                            </div>
                        </div>

                        <!-- Password & Confirm -->
                        <div class="row g-3 mb-3">
                            <!-- Password -->
                            <div class="col-md-6">
                                <label for="password" class="form-label fw-semibold">
                                    <i class="bi bi-lock-fill me-1"></i> Password
                                </label>
                                <div class="input-group">
                                    <input
                                        id="password"
                                        :type="showPassword ? 'text' : 'password'"
                                        class="form-control rounded-start-pill"
                                        v-model="form.password"
                                        required
                                        autocomplete="new-password"
                                        :class="{ 'is-invalid': form.errors.password }"
                                    />
                                    <button
                                        type="button"
                                        class="btn btn-outline-success rounded-end-pill"
                                        @click="showPassword = !showPassword"
                                    >
                                        <i :class="showPassword ? 'bi bi-eye-slash' : 'bi bi-eye'"></i>
                                    </button>
                                    <div v-if="form.errors.password" class="invalid-feedback d-block">
                                        {{ form.errors.password }}
                                    </div>
                                </div>
                            </div>

                            <!-- Confirm Password -->
                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label fw-semibold">
                                    <i class="bi bi-shield-lock-fill me-1"></i> Confirm Password
                                </label>
                                <div class="input-group">
                                    <input
                                        id="password_confirmation"
                                        :type="showConfirmPassword ? 'text' : 'password'"
                                        class="form-control rounded-start-pill"
                                        v-model="form.password_confirmation"
                                        required
                                        autocomplete="new-password"
                                        :class="{ 'is-invalid': form.errors.password_confirmation }"
                                    />
                                    <button
                                        type="button"
                                        class="btn btn-outline-success rounded-end-pill"
                                        @click="showConfirmPassword = !showConfirmPassword"
                                    >
                                        <i :class="showConfirmPassword ? 'bi bi-eye-slash' : 'bi bi-eye'"></i>
                                    </button>
                                    <div v-if="form.errors.password_confirmation" class="invalid-feedback d-block">
                                        {{ form.errors.password_confirmation }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="d-flex flex-column align-items-center gap-3 mt-3">

                            <button
                                type="submit"
                                class="btn btn-success rounded-pill btn-animate px-4 w-100"
                                :disabled="form.processing"
                            >
                                <span
                                    v-if="form.processing"
                                    class="spinner-border spinner-border-sm me-2"
                                ></span>
                                <i v-else class="bi bi-person-check me-1"></i>
                                Register
                            </button>

                            <Link
                                :href="route('login')"
                                class="text-decoration-none small text-success fw-semibold"
                            >
                                Already registered?
                            </Link>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Footer -->
            <footer class="text-center mt-4 fade-in-delayed">
                <small class="text-muted">
                    © {{ new Date().getFullYear() }} CraftSmart Artisan Ecommerce System ·
                    <Link href="/" class="text-decoration-none text-success fw-semibold">Home</Link>
                </small>
            </footer>
        </div>

        <!-- Success Modal -->
        <div
            v-if="showModal"
            class="modal fade show d-block"
            tabindex="-1"
            style="background: rgba(0,0,0,0.5);"
        >
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content rounded-4 shadow-lg animate-modal success-glow">

                    <!-- Header -->
                    <div class="modal-header border-0 bg-success bg-opacity-10">
                        <h5 class="modal-title text-success fw-bold d-flex align-items-center">
                            <i class="bi bi-check-circle-fill me-2 fs-4"></i>
                            Successfully Registered!
                        </h5>
                        <button
                            type="button"
                            class="btn-close"
                            @click="showModal = false"
                        ></button>
                    </div>

                    <!-- Body -->
                    <div class="modal-body text-center py-4">
                        <i class="bi bi-person-check display-4 text-success mb-3"></i>
                        <p class="fw-semibold mb-1">Your account has been created <i class="bi bi-stars text-warning ms-1"></i></p>
                        <small class="text-muted">You can now access your dashboard.</small>
                    </div>

                    <!-- Footer -->
                    <div class="modal-footer border-0">
                        <button
                            class="btn btn-success rounded-pill w-100 fw-semibold btn-animate"
                            @click="goToDashboard"
                        >
                            Go to Dashboard
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.bg-gradient {
    background: linear-gradient(135deg, #f8f9fa, #e9f7ef);
}

/* Loading overlay */
.loading-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(255, 255, 255, 0.75);
    z-index: 1050;
    border-radius: 1rem;
}

/* Card shadow upgrade */
.shadow-elevated {
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
}

/* Card animation */
.animate-card {
    transition: transform 0.4s ease, box-shadow 0.4s ease;
}
.animate-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 28px rgba(0, 0, 0, 0.2);
}

/* Fade-in effect */
.fade-in {
    animation: fadeIn 0.6s ease-in-out;
}
.fade-in-delayed {
    animation: fadeIn 1s ease-in-out;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(15px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Button animation */
.btn-animate {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.btn-animate:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 16px rgba(25, 135, 84, 0.4);
}

/* Smooth modal scale-in animation */
.animate-modal {
    animation: scaleIn 0.3s ease-out;
}
@keyframes scaleIn {
    from {
        transform: scale(0.9);
        opacity: 0;
    }
    to {
        transform: scale(1);
        opacity: 1;
    }
}

/* Success glow animation */
.success-glow {
    animation: successPulse 1.5s ease-out;
}
@keyframes successPulse {
    0% {
        box-shadow: 0 0 0 rgba(25, 135, 84, 0);
    }
    50% {
        box-shadow: 0 0 25px rgba(25, 135, 84, 0.6);
    }
    100% {
        box-shadow: 0 0 0 rgba(25, 135, 84, 0);
    }
}
</style>
