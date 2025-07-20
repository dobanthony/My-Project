<template>
  
  <div class="container py-5">
    <div class="row">
      <!-- Product Image -->
      <div class="col-md-5">
        <img
          :src="product.image ? `/storage/${product.image}` : 'https://via.placeholder.com/300x300?text=No+Image'"
          alt="Product Image"
          class="img-fluid rounded shadow"
        />
      </div>

      <!-- Product Details -->
      <div class="col-md-7">
        <h2 class="mb-3">{{ product.name }}</h2>
        <h4 class="text-success mb-3">₱{{ product.price }}</h4>
        <p><strong>Stock:</strong> {{ product.stock }}</p>
        <p class="mt-3">{{ product.description }}</p>

        <!-- Actions -->
        <div class="mt-4">
          <button class="btn btn-success me-2">Add to Cart</button>
          <button class="btn btn-outline-primary">Contact Seller</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  product: Object
});
</script>
            