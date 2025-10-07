<template>
  <div class="container py-5">
    <!-- Header -->
    <div class="text-center mb-4">
      <h2 class="fw-bold">
        <i class="bi bi-receipt-cutoff me-2 text-success"></i>
        Checkout for <span class="text-primary">{{ product.name }}</span>
      </h2>
      <p class="text-muted">Fill in your details to complete the order.</p>
    </div>

    <div class="card p-4 shadow-sm border-0 rounded-4">
      <form @submit.prevent="submit">
        
        <!-- Personal Info -->
        <h5 class="mb-3 text-success fw-semibold">
          <i class="bi bi-person-vcard-fill me-2 text-secondary"></i>
          Personal Information
        </h5>
        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label fw-semibold small text-success">
              <i class="bi bi-person-fill me-1 text-secondary"></i> Full Name
            </label>
            <input
              v-model="form.full_name"
              type="text"
              class="form-control"
              :class="{ 'is-invalid': form.errors.full_name }"
              required
            />
            <div v-if="form.errors.full_name" class="invalid-feedback">
              {{ form.errors.full_name }}
            </div>
          </div>

          <div class="col-md-6 mb-3">
            <label class="form-label fw-semibold small text-success">
              <i class="bi bi-telephone-fill me-1 text-secondary"></i> Phone Number
            </label>
            <input
              v-model="form.phone_number"
              type="text"
              class="form-control"
              :class="{ 'is-invalid': form.errors.phone_number }"
              required
            />
            <div v-if="form.errors.phone_number" class="invalid-feedback">
              {{ form.errors.phone_number }}
            </div>
          </div>
        </div>

        <div class="mb-3">
          <label class="form-label fw-semibold small text-success">
            <i class="bi bi-envelope-fill me-1 text-secondary"></i> Email
          </label>
          <input
            v-model="form.email"
            type="email"
            class="form-control"
            :class="{ 'is-invalid': form.errors.email }"
            required
          />
          <div v-if="form.errors.email" class="invalid-feedback">
            {{ form.errors.email }}
          </div>
        </div>

        <div class="mb-3">
          <label class="form-label fw-semibold small text-success">
            <i class="bi bi-geo-alt-fill me-1 text-secondary"></i> Delivery Address
          </label>
          <textarea
            v-model="form.delivery_address"
            class="form-control"
            rows="2"
            :class="{ 'is-invalid': form.errors.delivery_address }"
            required
          ></textarea>
          <div v-if="form.errors.delivery_address" class="invalid-feedback">
            {{ form.errors.delivery_address }}
          </div>
        </div>

        <div class="mb-4">
          <label class="form-label fw-semibold small text-success">
            <i class="bi bi-sticky-fill me-1 text-secondary"></i> Notes (Optional)
          </label>
          <textarea v-model="form.notes" class="form-control" rows="2"></textarea>
        </div>

        <!-- Quantity -->
        <div class="mb-4">
          <label class="form-label fw-semibold small text-success">
            <i class="bi bi-stack me-1 text-secondary"></i> Quantity
          </label>
          <input
            v-model.number="form.quantity"
            type="number"
            min="1"
            :max="product.stock"
            class="form-control w-25"
            required
            :class="{ 'is-invalid': form.errors.quantity }"
          />
          <div v-if="form.errors.quantity" class="invalid-feedback">
            {{ form.errors.quantity }}
          </div>
        </div>

        <!-- Buttons -->
        <div class="d-flex align-items-center">
          <button type="submit" class="btn btn-success px-4" :disabled="form.processing">
            <span v-if="form.processing" class="spinner-border spinner-border-sm me-2"></span>
            <i v-else class="bi bi-cart-check-fill me-1"></i> Place Order
          </button>
          <Link :href="`/product/${product.id}`" class="btn btn-outline-secondary ms-2">
            <i class="bi bi-arrow-left-circle me-1"></i> Back
          </Link>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { useForm, Link } from "@inertiajs/vue3";
import { defineProps, computed, onMounted } from "vue";

const props = defineProps({
  product: Object,
  quantity: Number,
  lastDeliveryInfo: Object,
  customizations: Object,
});

const form = useForm({
  product_id: props.product.id,
  full_name: props.lastDeliveryInfo?.full_name || "",
  phone_number: props.lastDeliveryInfo?.phone_number || "",
  email: props.lastDeliveryInfo?.email || "",
  delivery_address: props.lastDeliveryInfo?.delivery_address || "",
  notes: props.lastDeliveryInfo?.notes || "",
  quantity: props.quantity || 1,

  // Customization details
  color: props.customizations?.color || "",
  size: props.customizations?.size || "",
  material: props.customizations?.material || "",
  pattern: props.customizations?.pattern || "",
  custom_name: props.customizations?.custom_name || "",
  custom_description: props.customizations?.custom_description || "",
  selected_image: props.customizations?.selected_image || "",
});

// ✅ detect if there’s any customization
const hasCustomization = computed(() => {
  return (
    form.color ||
    form.size ||
    form.material ||
    form.pattern ||
    form.custom_name ||
    form.custom_description
  );
});

onMounted(() => {
  if (!form.full_name && props.lastDeliveryInfo) {
    form.full_name = props.lastDeliveryInfo.full_name || "";
    form.phone_number = props.lastDeliveryInfo.phone_number || "";
    form.email = props.lastDeliveryInfo.email || "";
    form.delivery_address = props.lastDeliveryInfo.delivery_address || "";
    form.notes = props.lastDeliveryInfo.notes || "";
  }
});

const submit = () => {
  form.post("/checkout");
};
</script>

<style>
input.form-control:focus,
textarea.form-control:focus {
  border-color: #28a745;
  box-shadow: 0 0 0 0.25rem rgba(40, 167, 69, 0.25);
}
textarea.form-control {
  border-color: #28a745;
}
</style>
