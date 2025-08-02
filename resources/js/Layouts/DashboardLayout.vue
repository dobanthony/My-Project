<template>
  <div>
    <!-- Sidebar -->
    <nav id="sidebar" class="p-3 text-white bg-success" :class="{ show: sidebarOpen }">
      <!-- Brand -->
      <h4 class="text-center fw-bold mb-3">CraftSmart</h4>
      <hr class="bg-secondary" />

      <!-- Navigation -->
      <ul class="nav flex-column mb-3">
        <!-- Home -->
        <li class="nav-item">
          <Link href="/user/dashboard" class="nav-link text-white">
            <i class="bi bi-house-door me-2"></i> Home
          </Link>
        </li>

        <!-- Browse All Products -->
        <li class="nav-item">
          <Link href="/view" class="nav-link text-white">
            <i class="bi bi-grid me-2"></i> Products
          </Link>
        </li>

        <!-- My Orders -->
        <li class="nav-item">
          <Link href="/my-orders" class="nav-link text-white">
            <i class="bi bi-receipt me-2"></i> Orders
          </Link>
        </li>

        <!-- Inbox -->
        <li class="nav-item position-relative">
          <Link
            href="/user/inbox"
            class="nav-link d-flex justify-content-between align-items-center text-white"
            @click="inboxClicked = true"
          >
            <span><i class="bi bi-envelope me-2"></i> Message</span>
            <span
              v-if="!inboxClicked && unreadMessagesCount > 0"
              class="badge bg-danger ms-2"
              style="font-size: 0.75rem;"
            >
              {{ unreadMessagesCount }}
            </span>
          </Link>
        </li>

        <!-- Apply as Seller -->
        <li class="nav-item">
          <Link href="/apply-seller" class="nav-link text-white">
            <i class="bi bi-briefcase me-2"></i> Apply as Seller
          </Link>
        </li>
      </ul>

      <hr class="bg-secondary" />
    </nav>

    <!-- Desktop Sidebar Toggle -->
    <button
      id="hideSidebarBtn"
      v-show="!isMobile && sidebarOpen"
      @click="closeSidebar"
      title="Hide Sidebar"
    >
      &#8592;
    </button>
    <button
      id="showSidebarBtn"
      v-show="!isMobile && !sidebarOpen"
      @click="openSidebar"
      title="Show Sidebar"
    >
      &#8594;
    </button>

    <!-- Topbar -->
    <nav id="topbar" class="d-flex justify-content-between align-items-center px-3 shadow-sm">
      <div class="d-flex align-items-center gap-3">
        <button id="toggleArrow" class="btn p-1" @click="openSidebar">&#9776;</button>
        <h4 class="fw-bold text-success">CraftSmart</h4>
      </div>

      <!-- RIGHT: Notifications + Cart + Profile -->
      <div class="d-flex align-items-center gap-3 position-relative">
        <!-- 🔔 Notifications -->
        <Link href="/user/notifications" class="text-decoration-none position-relative">
          <i class="bi bi-bell fs-5 text-success"></i>
          <span
            v-if="store.hasUnread"
            class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
            style="font-size: 0.6rem;"
          >
            {{ store.unreadCount }}
          </span>
        </Link>

        <!-- 🛒 Cart Icon -->
        <Link href="/cart" class="text-decoration-none position-relative">
          <i class="bi bi-cart-check fs-5 text-success"></i>
          <span
            v-if="cartItemCount > 0"
            class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
            style="font-size: 0.6rem;"
          >
            {{ cartItemCount }}
          </span>
        </Link>

        <!-- 👤 Profile Avatar -->
        <div class="position-relative">
          <img
            :src="user?.avatar ? `/storage/${user.avatar}` : getDefaultAvatar"
            class="avatar cursor-pointer"
            @click="toggleProfileModal"
          />

          <!-- Profile Modal Dropdown -->
          <div v-show="profileModalOpen" class="profile-modal shadow rounded bg-white">
            <div class="d-flex align-items-center gap-3 p-3 border-bottom">
              <img
                :src="user?.avatar ? `/storage/${user.avatar}` : getDefaultAvatar"
                class="avatar-lg"
              />
              <div>
                <div class="fw-bold">
                  Hi, {{ page.props.auth?.user?.first_name ?? 'N/A' }}
                </div>
                <div class="text-muted small">
                  {{ page.props.auth?.user?.email ?? 'N/A' }}
                </div>
                <div class="text-primary small bg-light px-2 py-1 rounded d-inline">
                  {{ page.props.auth?.user?.role ?? 'N/A' }}
                </div>
              </div>
            </div>
            <div>
              <Link class="dropdown-item" href="/profile">My Profile</Link>
              <Link class="dropdown-item" href="/logout" method="post" as="button">
                Sign Out
              </Link>
            </div>
          </div>
        </div>
      </div>
    </nav>

    <!-- Overlay for mobile sidebar -->
    <div id="overlay" :class="{ show: isMobile && sidebarOpen }" @click="closeSidebar"></div>

    <!-- Main Content -->
    <main id="content" :class="{ shifted: sidebarOpen && !isMobile }">
      <slot />
    </main>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, onUnmounted } from 'vue'
import { Link, router, usePage } from '@inertiajs/vue3'
import { useNotificationStore } from '@/stores/notification'

const store = useNotificationStore()
const page = usePage()

const inboxClicked = ref(false)
const user = page.props.auth?.user
const getDefaultAvatar = '/images/default-avatar.jpg'

const sidebarOpen = ref(false)
const isMobile = ref(false)
const profileModalOpen = ref(false)

const openSidebar = () => { sidebarOpen.value = true }
const closeSidebar = () => { sidebarOpen.value = false }
const toggleProfileModal = () => {
  profileModalOpen.value = !profileModalOpen.value
}

const closeProfileModal = (e) => {
  if (!e.target.closest('.profile-modal') && !e.target.closest('.avatar')) {
    profileModalOpen.value = false
  }
}

const handleResize = () => {
  isMobile.value = window.innerWidth < 768
  sidebarOpen.value = !isMobile.value
}

const unreadMessagesCount = computed(() => {
  return page.props.unreadMessagesCount ?? 0
})

// --- CART STATE ---
const cartItems = ref(page.props.cart?.items ? [...page.props.cart.items] : [])
const cartItemCount = computed(() =>
  cartItems.value.reduce((sum, it) => sum + (it.quantity || 0), 0)
)

// keep cart synced on prop changes
watch(
  () => page.props.cart,
  (newCart) => {
    cartItems.value = newCart?.items ? [...newCart.items] : []
  }
)

let pollingInterval = null

onMounted(() => {
  handleResize()
  window.addEventListener('resize', handleResize)
  document.addEventListener('click', closeProfileModal)

  // Detect if inbox is active
  if (page.url.startsWith('/user/inbox')) {
    inboxClicked.value = true
  }

  // Initial Notifications
  store.setNotifications(page.props.notifications || [])

  // 🔄 Poll every 10 seconds including cart
  pollingInterval = setInterval(() => {
    router.reload({
      only: ['notifications', 'unreadMessagesCount', 'cart'],
      preserveScroll: true,
      preserveState: true,
      onSuccess: (p) => {
        store.setNotifications(p.props.notifications || [])
        // cartItems will update via watcher
      }
    })
  }, 10000)
})

onUnmounted(() => {
  window.removeEventListener('resize', handleResize)
  document.removeEventListener('click', closeProfileModal)
  clearInterval(pollingInterval)
})

// Logout action
function logout() {
  router.post('/logout')
}
</script>

<style scoped>
body {
  overflow-x: hidden;
}

#sidebar .nav-link:hover {
  background-color: #ffffff;
  color: #198754 !important; /* Bootstrap's success color */
  border-radius: 0.375rem;
}

#sidebar {
  width: 250px;
  height: 100vh;
  position: fixed;
  top: 0;
  left: -250px;
  z-index: 1060;
  transition: left 0.3s ease;
}

#sidebar.show {
  left: 0;
}

#sidebar .nav-link {
  color: #ffffff;
}

#sidebar .nav-link.active {
  background-color: #495057;
}

#topbar {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 60px;
  background-color: #ffffff;
  border-bottom: 1px solid #dee2e6;
  z-index: 1050;
}

.logo {
  font-size: 1.25rem;
  font-weight: 600;
}

.avatar {
  border-radius: 50%;
  width: 40px;
  height: 40px;
  object-fit: cover;
  cursor: pointer;
}

.avatar-lg {
  border-radius: 50%;
  width: 48px;
  height: 48px;
  object-fit: cover;
}

.profile-modal {
  position: absolute;
  top: 55px;
  right: 0;
  width: 260px;
  z-index: 9999;
  border: 1px solid #dee2e6;
}

.profile-modal .dropdown-item {
  padding: 10px 16px;
  cursor: pointer;
  transition: background 0.2s;
}

.profile-modal .dropdown-item:hover {
  background-color: #f8f9fa;
}

#overlay {
  display: none;
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.5);
  z-index: 1040;
}

#overlay.show {
  display: block;
}

#toggleArrow {
  border: none;
  background: transparent;
  font-size: 1.5rem;
}

#hideSidebarBtn,
#showSidebarBtn {
  display: none;
  position: fixed;
  top: 10px;
  z-index: 1065;
  background-color: #198754;
  color: white;
  border: none;
  padding: 4px 10px;
  font-size: 1.25rem;
}

#hideSidebarBtn {
  left: 250px;
  border-radius: 0 5px 5px 0;
}

#showSidebarBtn {
  left: -20px;
  border-radius: 0 5px 5px 0;
  box-shadow: 2px 0 5px rgba(0, 0, 0, 0.3);
  opacity: 0.9;
}

#content {
  padding: 1.5rem;
  margin-top: 70px;
  transition: margin-left 0.3s ease;
}

#content.shifted {
  margin-left: 250px;
}

@media (min-width: 768px) {
  #hideSidebarBtn,
  #showSidebarBtn {
    display: block;
  }

  #toggleArrow {
    display: none !important;
  }
}
</style>
