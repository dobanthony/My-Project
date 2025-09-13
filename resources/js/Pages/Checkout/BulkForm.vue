<template>
  <div class="container py-5">
    <!-- Header -->
    <div class="text-center mb-4">
      <h2 class="fw-bold">
        <i class="bi bi-truck me-2 text-success"></i> Delivery Information
      </h2>
      <p class="text-muted">Fill in your details to complete the checkout process.</p>
    </div>

    <div class="card p-4 shadow-sm border-0 rounded-4">
      <form @submit.prevent="submit">
        <!-- Delivery Info -->
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label fw-semibold">
              <i class="bi bi-person-fill me-1 text-primary"></i> Full Name
            </label>
            <input
              v-model="form.full_name"
              type="text"
              class="form-control"
              placeholder="John Doe"
              required
            />
          </div>

          <div class="col-md-6">
            <label class="form-label fw-semibold">
              <i class="bi bi-telephone-fill me-1 text-primary"></i> Phone Number
            </label>
            <input
              v-model="form.phone_number"
              type="text"
              class="form-control"
              placeholder="+63 900 000 0000"
              required
            />
          </div>

          <div class="col-md-6">
            <label class="form-label fw-semibold">
              <i class="bi bi-envelope-fill me-1 text-primary"></i> Email
            </label>
            <input
              v-model="form.email"
              type="email"
              class="form-control"
              placeholder="example@email.com"
              required
            />
          </div>

          <div class="col-md-6">
            <label class="form-label fw-semibold">
              <i class="bi bi-geo-alt-fill me-1 text-primary"></i> Delivery Address
            </label>
            <textarea
              v-model="form.delivery_address"
              class="form-control"
              rows="1"
              placeholder="123 Main Street, City"
              required
            ></textarea>
          </div>

          <div class="col-12">
            <label class="form-label fw-semibold">
              <i class="bi bi-sticky-fill me-1 text-secondary"></i> Notes (Optional)
            </label>
            <textarea
              v-model="form.notes"
              class="form-control"
              rows="2"
              placeholder="Add any delivery instructions..."
            ></textarea>
          </div>
        </div>

        <hr class="my-4" />

        <!-- Orders & Customizations -->
        <h4 class="mb-3">
          <i class="bi bi-basket-fill me-2 text-success"></i> Your Orders
        </h4>

        <div class="row g-4">
          <div
            v-for="(order, index) in form.orders"
            :key="index"
            class="col-md-4 col-sm-6 col-12"
          >
            <div class="border rounded-3 p-3 bg-light h-100 shadow-sm">
              <!-- Product header -->
              <h5 class="mb-3 text-dark fw-semibold small">
                <i class="bi bi-bag-check-fill text-success me-2"></i>
                {{ order.product.name }}
                <span class="badge bg-primary ms-1">x{{ order.quantity }}</span>
              </h5>

              <!-- Customization options -->
              <div v-if="order.product.customization" class="row g-3">
                <div v-if="order.product.customization.allow_color" class="col-12">
                  <label class="form-label">
                    <i class="bi bi-palette-fill me-1"></i> Color
                  </label>
                  <select v-model="order.color" class="form-select">
                    <option disabled value="">Choose color</option>
                    <option>Red</option>
                    <option>Blue</option>
                    <option>Black</option>
                  </select>
                </div>

                <div v-if="order.product.customization.allow_size" class="col-12">
                  <label class="form-label">
                    <i class="bi bi-aspect-ratio-fill me-1"></i> Size
                  </label>
                  <select v-model="order.size" class="form-select">
                    <option disabled value="">Choose size</option>
                    <option>Small</option>
                    <option>Medium</option>
                    <option>Large</option>
                  </select>
                </div>

                <div v-if="order.product.customization.allow_material" class="col-12">
                  <label class="form-label">
                    <i class="bi bi-gem me-1"></i> Material
                  </label>
                  <select v-model="order.material" class="form-select">
                    <option disabled value="">Choose material</option>
                    <option>Beads</option>
                    <option>Leather</option>
                    <option>Metal</option>
                  </select>
                </div>

                <div v-if="order.product.customization.allow_name" class="col-12">
                  <label class="form-label">
                    <i class="bi bi-pencil-fill me-1"></i> Custom Name
                  </label>
                  <input
                    v-model="order.custom_name"
                    type="text"
                    class="form-control"
                    placeholder="e.g. John's Bracelet"
                  />
                </div>

                <div v-if="order.product.customization.allow_description" class="col-12">
                  <label class="form-label">
                    <i class="bi bi-card-text me-1"></i> Custom Description
                  </label>
                  <textarea
                    v-model="order.custom_description"
                    class="form-control"
                    rows="2"
                    placeholder="Describe your custom request..."
                  ></textarea>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Submit -->
        <div class="d-flex align-items-center mt-4">
          <button type="submit" class="btn btn-success px-4" :disabled="form.processing">
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
import { defineProps } from "vue";

const props = defineProps({
  cartItems: Array,
  lastDeliveryInfo: Object,
});

// 🛒 Map cart items into form.orders with prefilled custom values (if any)
const orders = props.cartItems
  .filter((item) => item.product && item.product.id)
  .map((item) => ({
    product_id: item.product.id,
    quantity: item.quantity || 1,
    product: item.product, // for display only

    // Customization fields – prefilled if passed from cart
    color: item.color || "",
    size: item.size || "",
    material: item.material || "",
    custom_name: item.custom_name || "",
    custom_description: item.custom_description || "",
  }));

const form = useForm({
  full_name: props.lastDeliveryInfo?.full_name || "",
  phone_number: props.lastDeliveryInfo?.phone_number || "",
  email: props.lastDeliveryInfo?.email || "",
  delivery_address: props.lastDeliveryInfo?.delivery_address || "",
  notes: props.lastDeliveryInfo?.notes || "",
  orders,
});

const submit = () => {
  form.post("/checkout-bulk/store");
};
</script>
