<template>
  <div class="container py-5">
    <!-- Header -->
    <div class="text-center mb-4">
      <h2 class="fw-bold text-success">
        <i class="bi bi-truck me-2 text-secondary"></i> Delivery Information
      </h2>
      <p class="text-muted">Fill in your details to complete the checkout process.</p>
    </div>

    <div class="card p-4 shadow-sm border-0 rounded-4">
      <form @submit.prevent="submit">
        <!-- Delivery Info -->
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label fw-semibold">
              <i class="bi bi-person-fill me-1 text-secondary"></i> Full Name
            </label>
            <input v-model="form.full_name" type="text" class="form-control" placeholder="John Doe" required />
          </div>

          <div class="col-md-6">
            <label class="form-label fw-semibold">
              <i class="bi bi-telephone-fill me-1 text-secondary"></i> Phone Number
            </label>
            <input v-model="form.phone_number" type="text" class="form-control" placeholder="+63 900 000 0000" required />
          </div>

          <div class="col-md-12">
            <label class="form-label fw-semibold">
              <i class="bi bi-envelope-fill me-1 text-secondary"></i> Email
            </label>
            <input v-model="form.email" type="email" class="form-control" placeholder="example@email.com" required />
          </div>

          <!-- Province -->
          <div class="col-md-4">
            <label class="form-label fw-semibold">
              <i class="bi bi-geo-alt-fill me-1 text-secondary"></i> Province
            </label>
            <select v-model="form.province_id" class="form-select" required @change="onProvinceChange">
              <option disabled value="">Select Province</option>
              <option v-for="province in provinces" :key="province.id" :value="province.id">{{ province.name }}</option>
            </select>
          </div>

          <!-- Municipality -->
          <div class="col-md-4">
            <label class="form-label fw-semibold">
              <i class="bi bi-geo-alt-fill me-1 text-secondary"></i> Municipality
            </label>
            <select v-model="form.municipality_id" class="form-select" required @change="onMunicipalityChange">
              <option disabled value="">Select Municipality</option>
              <option v-for="municipality in filteredMunicipalities" :key="municipality.id" :value="municipality.id">
                {{ municipality.name }}
              </option>
            </select>
          </div>

          <!-- Barangay -->
          <div class="col-md-4">
            <label class="form-label fw-semibold">
              <i class="bi bi-geo-alt-fill me-1 text-secondary"></i> Barangay
            </label>
            <select v-model="form.barangay_id" class="form-select" required>
              <option disabled value="">Select Barangay</option>
              <option v-for="barangay in filteredBarangays" :key="barangay.id" :value="barangay.id">{{ barangay.name }}</option>
            </select>
          </div>

          <!-- Street -->
          <div class="col-12">
            <label class="form-label fw-semibold">
              <i class="bi bi-house-fill me-1 text-secondary"></i> Street Address
            </label>
            <input v-model="form.street_address" type="text" class="form-control" placeholder="123 Main Street" />
          </div>

          <!-- Notes -->
          <div class="col-12">
            <label class="form-label fw-semibold">
              <i class="bi bi-sticky-fill me-1 text-secondary"></i> Notes (Optional)
            </label>
            <textarea v-model="form.notes" class="form-control" rows="2" placeholder="Add any delivery instructions..."></textarea>
          </div>
        </div>

        <!-- Submit -->
        <div class="d-flex align-items-center mt-4">
          <button type="submit" class="btn btn-primary px-4" :disabled="form.processing">
            <span v-if="form.processing" class="spinner-border spinner-border-sm me-2"></span>
            <i v-else class="bi bi-cart-check-fill me-1"></i> Place Orders
          </button>
          <Link href="/cart" class="btn btn-outline-secondary ms-2">
            <i class="bi bi-arrow-left-circle me-1"></i> Back to Cart
          </Link>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { useForm, Link } from "@inertiajs/vue3";
import { defineProps, computed, watch } from "vue";

const props = defineProps({
  cartItems: Array,
  lastDeliveryInfo: Object,
  provinces: Array,
  municipalities: Array,
  barangays: Array,
});

// Prefill orders from cart
const orders = props.cartItems.map(item => ({
  product_id: item.product.id,
  quantity: item.quantity || 1,
  product: item.product,
  color: item.color || "",
  size: item.size || "",
  material: item.material || "",
  custom_name: item.custom_name || "",
  custom_description: item.custom_description || "",
  selected_image: item.selected_image || "",
}));

const form = useForm({
  full_name: props.lastDeliveryInfo?.full_name || "",
  phone_number: props.lastDeliveryInfo?.phone_number || "",
  email: props.lastDeliveryInfo?.email || "",
  province_id: props.lastDeliveryInfo?.province_id || "",
  municipality_id: props.lastDeliveryInfo?.municipality_id || "",
  barangay_id: props.lastDeliveryInfo?.barangay_id || "",
  street_address: props.lastDeliveryInfo?.street_address || "",
  notes: props.lastDeliveryInfo?.notes || "",
  orders,
});

// Dynamic filtering
const filteredMunicipalities = computed(() =>
  props.municipalities.filter(m => m.province_id === form.province_id)
);

const filteredBarangays = computed(() =>
  props.barangays.filter(b => b.municipality_id === form.municipality_id)
);

// Reset dependent dropdowns when parent changes
const onProvinceChange = () => {
  form.municipality_id = "";
  form.barangay_id = "";
};

const onMunicipalityChange = () => {
  form.barangay_id = "";
};

const submit = () => {
  form.post("/checkout-bulk/store");
};
</script>
