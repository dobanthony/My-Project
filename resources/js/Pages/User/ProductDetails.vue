<template>
  <DashboardLayout>
  <!-- ✅ Toast Notification -->
  <div class="toast-container position-fixed top-0 start-50 translate-middle-x p-3" style="z-index: 2000;">
    <div
      class="toast align-items-center text-bg-success border-0"
      role="alert"
      aria-live="assertive"
      aria-atomic="true"
      ref="successToast"
    >
      <div class="d-flex">
        <!-- to center the message in the toast modal text-center w-100 -->
        <div class="toast-body" ref="toastMessage">Success</div>
      </div>
    </div>
  </div>

  <div class="bg-light py-5 px-2 px-md-0">
    <div class="container bg-white rounded shadow p-4" style="max-width: 1000px;">
      <!-- 🧭 Breadcrumb -->
      <nav class="mb-4">
        <ol class="breadcrumb bg-light p-2 rounded mb-0">
          <li class="breadcrumb-item">
            <Link href="/" class="text-decoration-none text-success">🏠 Home</Link>
          </li>
          <li class="breadcrumb-item">
            <Link
              v-if="product.shop?.id"
              :href="`/shop/${product.shop.id}/products`"
              class="text-decoration-none text-success"
            >
              🛍 {{ product.shop?.shop_name ?? 'Shop' }}
            </Link>
          </li>
          <li class="breadcrumb-item active text-success">📦 {{ product.name }}</li>
        </ol>
      </nav>

      <!-- 🛍 Product Info -->
      <div class="row gy-4">
        <div class="col-12 col-lg-6 text-center">
          <img
            :src="product.image ? `/storage/${product.image}` : 'https://via.placeholder.com/500x400?text=No+Image'"
            class="img-fluid rounded shadow-sm border w-100"
            alt="Product Image"
            style="max-height: 450px; object-fit: contain;"
          />
        </div>

        <div class="col-12 col-lg-6">
          <h2 class="text-dark fw-bold mb-2">{{ product.name }}</h2>

          <!-- ⭐ Rating & Stats -->
          <div class="d-flex flex-wrap gap-3 mb-3">
            <div>
              <span class="fw-bold me-1 text-success">{{ averageRating }}</span>
              <span class="text-warning">
                <i
                  v-for="i in 5"
                  :key="i"
                  :class="i <= Math.round(averageRating) ? 'bi bi-star-fill' : 'bi bi-star'"
                ></i>
              </span>
            </div>
            <div>
              <span class="fw-bold text-success">{{ ratingsCount }}</span>
              <span class="text-muted"> Ratings</span>
            </div>
            <div>
              <span class="fw-bold text-success">{{ totalSold }}</span>
              <span class="text-muted"> Sold</span>
            </div>
          </div>

          <!-- 👤 Shop Info -->
          <div class="d-flex align-items-center gap-2 mb-3">
            <img
              :src="product.shop?.shop_logo ? `/storage/${product.shop.shop_logo}` : 'https://via.placeholder.com/50?text=No+Logo'"
              class="img-fluid rounded border"
              alt="Shop Logo"
              style="max-height: 50px; object-fit: contain;"
            />
            <div>
              <small class="text-muted">Sold by:</small><br />
              <strong class="text-success">{{ product.shop?.shop_name ?? 'Unknown Shop' }}</strong>
            </div>
          </div>

          <!-- 👥 Follow -->
          <div class="mt-2">
            <button
              class="btn btn-outline-success btn-sm"
              :disabled="followLoading"
              @click="toggleFollow"
            >
              <span v-if="isFollowing">✅ Following</span>
              <span v-else>➕ Follow Shop</span>
            </button>
            <div class="text-muted mt-1">Followers: {{ followerCount }}</div>
          </div>

          <!-- 📄 Description -->
          <p class="text-secondary mt-3">{{ product.description }}</p>
          <h3 class="text-success fw-bold mb-4">₱{{ parseFloat(product.price).toFixed(2) }}</h3>

          <!-- 🔢 Quantity -->
          <div class="mb-3">
            <label class="text-success form-label fw-semibold">Quantity</label>
            <input
              type="number"
              v-model.number="quantity"
              class="form-control w-100 w-md-50"
              min="1"
              :max="product.stock"
            />
            <small class="text-muted">Stock: {{ product.stock }}</small><br />
            <small class="text-danger">*Stock will be deducted after seller approval.</small>
          </div>

          <!-- 🎨 Customize / Add to Cart -->
          <div class="d-grid gap-2 d-md-flex mt-4">
            <button
              v-if="product.customization"
              @click="showCustomizeForm = !showCustomizeForm"
              class="btn btn-warning"
            >
              🎨 {{ showCustomizeForm ? 'Hide Customize' : 'Customize' }}
            </button>

            <template v-else>
              <button @click="addToCart" class="btn btn-outline-primary px-4"><i class="bi bi-cart-plus me-2"></i>Add to Cart</button>
              <button @click="buyNow" class="btn btn-success"><i class="bi bi-bag me-2"></i>Buy Now</button>
            </template>
          </div>

          <!-- 🎨 Customization Form -->
          <div
            v-if="showCustomizeForm && product.customization"
            class="mt-4 p-3 border rounded bg-light"
          >
            <h5>🧰 Customize Your Product</h5>

            <div v-if="product.customization.allow_color" class="mb-3">
              <label class="form-label">Color</label>
              <select v-model="customForm.color" class="form-select">
                <option disabled value="">Choose color</option>
                <option>Red</option>
                <option>Blue</option>
                <option>Black</option>
              </select>
            </div>

            <div v-if="product.customization.allow_size" class="mb-3">
              <label class="form-label">Size</label>
              <select v-model="customForm.size" class="form-select">
                <option disabled value="">Choose size</option>
                <option>Small</option>
                <option>Medium</option>
                <option>Large</option>
              </select>
            </div>

            <div v-if="product.customization.allow_material" class="mb-3">
              <label class="form-label">Material</label>
              <select v-model="customForm.material" class="form-select">
                <option disabled value="">Choose material</option>
                <option>Beads</option>
                <option>Leather</option>
                <option>Metal</option>
              </select>
            </div>

            <div v-if="product.customization.allow_name" class="mb-3">
              <label class="form-label">Custom Name</label>
              <input
                v-model="customForm.custom_name"
                type="text"
                class="form-control"
                placeholder="Your name or initials"
              />
            </div>

            <div class="mb-3">
              <label class="form-label">Quantity</label>
              <input
                type="number"
                v-model.number="customForm.quantity"
                class="form-control"
                min="1"
                :max="product.stock"
              />
            </div>

            <div class="d-flex gap-2 mt-3 flex-wrap">
              <button @click="submitCustomization" class="btn btn-primary">
                🛒 Add Customized to Cart
              </button>
              <button @click="buyNowCustom" class="btn btn-success">Buy Customized Now</button>
            </div>
          </div>
        </div>
      </div>

      <!-- 🏪 Shop Info -->
      <div class="card mt-5 p-4 border-0 shadow rounded-4">
        <h5 class="mb-4 text-success fw-bold">🛍 About the Seller</h5>

        <div class="row g-4 align-items-start">
          <!-- Left Section: Logo + Shop Info -->
          <div class="col-md-3 d-flex flex-column align-items-center text-center">
            <img
              :src="product.shop?.shop_logo ? `/storage/${product.shop.shop_logo}` : 'https://via.placeholder.com/100?text=No+Logo'"
              alt="Shop Logo"
              class="rounded-circle border shadow-sm mb-3"
              style="width: 120px; height: 120px; object-fit: cover;"
            />

            <h6 class="fw-bold text-success mb-3">
              {{ product.shop?.shop_name ?? 'N/A' }}
            </h6>

            <!--Horizontally aligned buttons on md+ and stacked on small screens -->
            <div class="d-flex flex-column flex-md-row gap-2 w-100 justify-content-center px-2">
              <button class="btn btn-danger" @click="goToChat">
                <i class="bi bi-chat-dots-fill me-1"></i> Chat
              </button>
              <button class="btn btn-outline-dark" @click="goToShop">
                <i class="bi bi-shop-window me-1"></i> View
              </button>
            </div>
          </div>

          <!-- Right Section: Shop Details -->
          <div class="col-md-9">
            <div class="row g-3">
              <div class="col-md-6">
                <p class="mb-2">
                  <i class="bi bi-info-circle me-2"></i>
                  <strong class="text-success">Description:</strong><br>
                  <span class="text-muted">{{ product.shop?.shop_description ?? 'N/A' }}</span>
                </p>
                <p class="mb-2">
                  <i class="bi bi-telephone me-2"></i>
                  <strong class="text-success">Phone:</strong>
                  <span class="text-muted">{{ product.shop?.phone_number ?? 'N/A' }}</span>
                </p>
                <p class="mb-2">
                  <i class="bi bi-envelope me-2"></i>
                  <strong class="text-success">Email:</strong>
                  <span class="text-muted">{{ product.shop?.email_address ?? 'N/A' }}</span>
                </p>
              </div>
              <div class="col-md-6">
                <p class="mb-2">
                  <strong class="text-success me-2">⭐ Ratings:</strong>
                  <span class="text-muted">{{ product.shop?.shop_rating ?? 0 }} ({{ product.shop?.shop_rating_count ?? 0 }} reviews)</span>
                </p>
                <p class="mb-2">
                  <i class="bi bi-plus-circle me-2"></i>
                  <strong class="text-success me-2">Followers:</strong>
                  <span class="text-muted">{{ followerCount }}</span>
                </p>
                <p class="mb-2">
                  <i class="bi bi-shop me-2"></i>
                  <strong class="text-success me-2">Shop Open Since:</strong>
                  <span class="text-muted">
                    {{ product.shop?.created_at ? new Date(product.shop.created_at).toLocaleDateString() : 'N/A' }}
                  </span>
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!--User Ratings -->
      <div v-if="ratings.length" class="mt-5">
        <h4 class="text-dark mb-3">⭐ Customer Ratings & Reviews</h4>
        <div v-for="rating in ratings" :key="rating.id" class="card my-3 p-3 border">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <div class="d-flex align-items-center gap-2">
              <img
                :src="rating.user?.avatar 
                  ? `/storage/${rating.user.avatar}` 
                  : `https://ui-avatars.com/api/?name=${encodeURIComponent(rating.user?.name ?? 'User')}`"
                alt="User Avatar"
                class="rounded-circle border"
                style="width: 40px; height: 40px; object-fit: cover;"
              />
              <strong class="text-dark">{{ rating.user?.name ?? 'Anonymous' }}</strong>
            </div>
            <small class="text-muted">{{ new Date(rating.created_at).toLocaleString() }}</small>
          </div>

          <div class="mb-2">
            <strong class="text-success">Product:</strong>
            <span v-for="i in 5" :key="'p-star-' + i" class="text-warning">
              <i :class="i <= rating.product_rating ? 'bi bi-star-fill' : 'bi bi-star'"></i>
            </span>
            <br />
            <strong class="text-success">Shop:</strong>
            <span v-for="i in 5" :key="'s-star-' + i" class="text-info">
              <i :class="i <= rating.shop_rating ? 'bi bi-star-fill' : 'bi bi-star'"></i>
            </span>
          </div>

          <p class="mb-2">{{ rating.comment }}</p>

          <div v-if="rating.image" class="mt-2">
            <img :src="`/storage/${rating.image}`" class="img-thumbnail" style="max-width: 200px;" />
          </div>
        </div>
      </div>
    </div>
  </div>
  </DashboardLayout>
</template>


<script setup>
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import { ref, defineProps } from 'vue'
import { router, useForm, Link } from '@inertiajs/vue3'

const props = defineProps({
  product: Object,
  ratings: Array,
  averageRating: Number,
  ratingsCount: Number,
  totalSold: Number,
  isFollowing: Boolean,
  followerCount: Number,
  product: Object,
})

const quantity = ref(1)
const message = ref('')
const isFollowing = ref(props.isFollowing)
const followerCount = ref(props.followerCount)
const averageRating = ref(props.averageRating ?? 0)
const ratingsCount = ref(props.ratingsCount ?? 0)
const totalSold = ref(props.totalSold ?? 0)
const followLoading = ref(false)
const showCustomizeForm = ref(false)

// Toast refs
const successToast = ref(null)
const toastMessage = ref('')

//Show toast helper
const showToast = (msg = 'Success!') => {
  toastMessage.value = msg
  const toast = new bootstrap.Toast(successToast.value)
  toast.show()
}

const customForm = useForm({
  product_id: props.product.id,
  color: '',
  size: '',
  material: '',
  custom_name: '',
  quantity: 1,
})


const addToCart = () => {
  router.post('/cart/add', {
    product_id: props.product.id,
    quantity: quantity.value,
  }, {
    onSuccess: () => {
      showToast('✅ Product added to cart!')
      setTimeout(() => (message.value = ''), 3000)
    },
  })
}

const submitCustomization = () => {
  customForm.post('/cart/add', {
    onSuccess: () => {
      showCustomizeForm.value = false
      message.value = '✅ Customized product added to cart!'
      setTimeout(() => (message.value = ''), 3000)
    },
  })
}

const buyNow = () => {
  router.visit(`/checkout/${props.product.id}?quantity=${quantity.value}`)
}

const buyNowCustom = () => {
  router.visit(`/checkout/${props.product.id}?quantity=${customForm.quantity}`)
}

const toggleFollow = () => {
  followLoading.value = true
  router.post(`/shop/${props.product.shop.id}/toggle-follow`, {}, {
    preserveScroll: true,
    onFinish: () => {
      isFollowing.value = !isFollowing.value
      followerCount.value += isFollowing.value ? 1 : -1
      followLoading.value = false
    },
  })
}



const goToChat = () => {
  router.visit(`/user/inbox/${props.product.shop.id}`)
}

</script>
