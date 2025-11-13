<template>
  <div class="container py-5">
    <!-- Header -->
    <div class="text-center mb-4">
      <h2 class="fw-bold">
        <i class="bi bi-receipt-cutoff me-2 text-secondary"></i>
        Checkout for <span class="text-success">{{ product.name }}</span>
      </h2>
      <p class="text-muted">Fill in your details to complete the order.</p>
    </div>

    <div class="card p-4 shadow-sm border-0 rounded-4">
      <form @submit.prevent="submit">
        <!-- Personal Info -->
        <h5 class="mb-3 text-dark fw-semibold">
          <i class="bi bi-person-vcard-fill me-2 text-secondary"></i>
          Personal Information
        </h5>

        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label fw-semibold small text-dark">
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
            <label class="form-label fw-semibold small text-dark">
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
          <label class="form-label fw-semibold small text-black">
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

        <!-- Location Info -->
        <h5 class="mb-3 text-dark fw-semibold">
          <i class="bi bi-geo-alt-fill me-2 text-secondary"></i>
          Delivery Address
        </h5>

        <div class="row mb-3">
          <!-- Province -->
          <div class="col-md-4 mb-3">
            <label class="form-label fw-semibold small text-dark">Province</label>
            <select
              v-model="form.province_id"
              class="form-select"
              :class="{ 'is-invalid': form.errors.province_id }"
              required
            >
              <option value="">Select Province</option>
              <option v-for="province in provinces" :key="province.id" :value="province.id">
                {{ province.name }}
              </option>
            </select>
            <div v-if="form.errors.province_id" class="invalid-feedback">
              {{ form.errors.province_id }}
            </div>
          </div>

          <!-- Municipality -->
          <div class="col-md-4 mb-3">
            <label class="form-label fw-semibold small text-dark">Municipality</label>
            <select
              v-model="form.municipality_id"
              class="form-select"
              :class="{ 'is-invalid': form.errors.municipality_id }"
              :disabled="!form.province_id"
              required
            >
              <option value="">Select Municipality</option>
              <option
                v-for="municipality in filteredMunicipalities"
                :key="municipality.id"
                :value="municipality.id"
              >
                {{ municipality.name }}
              </option>
            </select>
            <div v-if="form.errors.municipality_id" class="invalid-feedback">
              {{ form.errors.municipality_id }}
            </div>
          </div>

          <!-- Barangay -->
          <div class="col-md-4 mb-3">
            <label class="form-label fw-semibold small text-dark">Barangay</label>
            <select
              v-model="form.barangay_id"
              class="form-select"
              :class="{ 'is-invalid': form.errors.barangay_id }"
              :disabled="!form.municipality_id"
              required
            >
              <option value="">Select Barangay</option>
              <option
                v-for="barangay in filteredBarangays"
                :key="barangay.id"
                :value="barangay.id"
              >
                {{ barangay.name }}
              </option>
            </select>
            <div v-if="form.errors.barangay_id" class="invalid-feedback">
              {{ form.errors.barangay_id }}
            </div>
          </div>
        </div>

        <div class="mb-3">
          <label class="form-label fw-semibold small text-dark">Street Address (Optional)</label>
          <input v-model="form.street_address" type="text" class="form-control" />
        </div>

        <!-- Notes -->
        <div class="mb-4">
          <label class="form-label fw-semibold small text-dark">
            <i class="bi bi-sticky-fill me-1 text-secondary"></i> Notes (Optional)
          </label>
          <textarea v-model="form.notes" class="form-control" rows="2"></textarea>
        </div>

        <!-- Quantity -->
        <!-- <div class="mb-4">
          <label class="form-label fw-semibold small text-dark">
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

          <small v-if="product.stock === 0" class="text-danger fw-bold d-block mt-1">
            <i class="bi bi-exclamation-triangle-fill me-1"></i>
            This product is currently out of stock.
          </small>
        </div> -->

        <!-- Buttons -->
        <div class="d-flex align-items-center">
          <button type="submit" class="btn btn-primary px-4" :disabled="form.processing">
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
import { ref, computed, watch, onMounted, defineProps } from "vue";

const props = defineProps({
  product: Object,
  quantity: Number,
  lastDeliveryInfo: Object,
  provinces: Array,
  municipalities: Array,
  barangays: Array,
  customizations: Object,
});

// --- Form data
const form = useForm({
  product_id: props.product.id,
  full_name: props.lastDeliveryInfo?.full_name || "",
  phone_number: props.lastDeliveryInfo?.phone_number || "",
  email: props.lastDeliveryInfo?.email || "",
  province_id: props.lastDeliveryInfo?.province_id || "",
  municipality_id: props.lastDeliveryInfo?.municipality_id || "",
  barangay_id: props.lastDeliveryInfo?.barangay_id || "",
  street_address: props.lastDeliveryInfo?.street_address || "",
  notes: props.lastDeliveryInfo?.notes || "",
  quantity: props.quantity || 1,
  color: props.customizations?.color || "",
  size: props.customizations?.size || "",
  material: props.customizations?.material || "",
  pattern: props.customizations?.pattern || "",
  custom_name: props.customizations?.custom_name || "",
  custom_description: props.customizations?.custom_description || "",
  selected_image: props.customizations?.selected_image || "",
});

// --- Full location data
const allProvinces = ref(props.provinces);
const allMunicipalities = ref(props.municipalities);
const allBarangays = ref(props.barangays);

// --- Filtered dropdown lists
const filteredMunicipalities = computed(() =>
  allMunicipalities.value.filter((m) => m.province_id === form.province_id)
);
const filteredBarangays = computed(() =>
  allBarangays.value.filter((b) => b.municipality_id === form.municipality_id)
);

// --- Watchers for dependent resets
watch(
  () => form.province_id,
  (newVal, oldVal) => {
    if (newVal !== oldVal) {
      form.municipality_id = "";
      form.barangay_id = "";
    }
  }
);

watch(
  () => form.municipality_id,
  (newVal, oldVal) => {
    if (newVal !== oldVal) {
      form.barangay_id = "";
    }
  }
);

// --- Prefill municipalities and barangays when revisiting
onMounted(() => {
  if (form.province_id && filteredMunicipalities.value.length === 0) {
    // This ensures municipalities appear even if coming back to the form
    const provinceMunicipalities = allMunicipalities.value.filter(
      (m) => m.province_id === form.province_id
    );
    if (!provinceMunicipalities.find((m) => m.id === form.municipality_id)) {
      form.municipality_id = "";
      form.barangay_id = "";
    }
  }

  if (form.municipality_id && filteredBarangays.value.length === 0) {
    const municipalityBarangays = allBarangays.value.filter(
      (b) => b.municipality_id === form.municipality_id
    );
    if (!municipalityBarangays.find((b) => b.id === form.barangay_id)) {
      form.barangay_id = "";
    }
  }
});

// --- Submit
const submit = () => {
  if (form.quantity <= 0) {
    alert("Quantity must be at least 1.");
    return;
  }
  if (form.quantity > props.product.stock) {
    alert(`Only ${props.product.stock} items are available in stock.`);
    return;
  }
  form.post("/checkout");
};
</script>
