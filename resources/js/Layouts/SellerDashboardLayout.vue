<template>
  <div>
    <!-- Sidebar -->
     <nav id="sidebar" class="p-3 bg-success text-white" :class="{ show: sidebarOpen }">
        <!-- Navigation Links -->
         <hr class="bg-secondary" />
        <ul class="nav flex-column mb-3">

          <!-- Dashboard -->
          <li class="nav-item">
            <Link
              :href="route('seller.analytics')"
              class="nav-link text-white d-flex align-items-center"
              :class="{ active: isActive('seller.analytics') }"
            >
              <i class="bi bi-speedometer2 me-2"></i>
              Dashboard
            </Link>
          </li>

          <!-- Orders with badge -->
          <li class="nav-item position-relative">
            <Link href="/seller/orders" class="nav-link text-white d-flex align-items-center">
              <i class="bi bi-bag-check me-2"></i>
              Orders
              <span
                v-if="pendingOrdersCount > 0"
                class="badge bg-danger ms-2"
                style="font-size: 0.7rem;"
              >
                {{ pendingOrdersCount }}
              </span>
            </Link>
          </li>

          <!-- Products -->
          <li class="nav-item">
            <Link :href="route('seller.products.index')" class="nav-link text-white d-flex align-items-center">
              <i class="bi bi-box-seam me-2"></i>
              Products
            </Link>
          </li>

          <!-- My Shop -->
          <li class="nav-item">
            <Link :href="route('seller.shop')" class="nav-link text-white d-flex align-items-center">
              <i class="bi bi-shop me-2"></i>
              My Shop
            </Link>
          </li>

          <!-- Inbox -->
          <li class="nav-item position-relative">
            <Link
              href="/seller/inbox"
              class="nav-link text-white d-flex align-items-center"
              @click="inboxClicked = true"
            >
              <i class="bi bi-envelope-fill me-2"></i>
              Message
              <span
                v-if="!inboxClicked && unreadMessagesCount > 0"
                class="badge bg-danger ms-2"
                style="font-size: 0.7rem;"
              >
                {{ unreadMessagesCount }}
              </span>
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

      <!-- Profile Avatar Dropdown -->
    <div class="d-flex align-items-center gap-3 position-relative">
    <!-- Notification Bell -->
    <Link
        href="/seller/notifications"
        class="text-decoration-none position-relative"
    >
        <i class="bi bi-bell fs-5 text-secondary"></i>

        <span
        v-if="store.unreadCount > 0"
        class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
        style="font-size: 0.6rem;"
        >
        {{ store.unreadCount }}
        </span>
    </Link>

    <!-- Avatar + Profile Modal -->
    <div class="position-relative">
        <img
        :src="user?.avatar ? `/storage/${user.avatar}` : getDefaultAvatar"
        class="avatar cursor-pointer"
        @click="toggleProfileModal"
        />

        <div v-show="profileModalOpen" class="profile-modal shadow rounded bg-white">
            <div class="d-flex align-items-center gap-3 p-3 border-bottom">
                <img
                :src="user?.avatar ? `/storage/${user.avatar}` : getDefaultAvatar"
                class="avatar-lg"
                />
                <div>
                <div class="fw-bold">Hi, {{ $page.props.auth?.user?.first_name ?? 'N/A' }}</div>
                <div class="text-muted small">{{ $page.props.auth?.user?.email ?? 'N/A' }}</div>
                <div class="text-primary small bg-light px-2 py-1 rounded d-inline">
                    {{ $page.props.auth?.user?.role ?? 'N/A' }}
                </div>
                </div>
            </div>
                <div>
                    <Link class="dropdown-item" href="/profile">My Profile</Link>
                    <Link class="dropdown-item" href="/logout" method="post" as="button">Log Out</Link>
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
import { ref, onMounted, onUnmounted, watch, computed } from 'vue'
import { Link, router, usePage } from '@inertiajs/vue3'
import { useNotificationStore } from '@/stores/notification'

// Props from Inertia page
const page = usePage()

// States
const inboxClicked = ref(false)
const sidebarOpen = ref(false)
const isMobile = ref(false)
const profileModalOpen = ref(false)

// User and store
const user = page.props.auth?.user
const getDefaultAvatar = '/images/default-avatar.jpg'
const store = useNotificationStore()

// Computed values
const unreadMessagesCount = computed(() => page.props.unreadMessagesCount ?? 0)
const pendingOrdersCount = computed(() => page.props.pendingOrdersCount ?? 0)

// 🕒 Polling setup
let pollingInterval = null
const fetchLatestData = () => {
  router.reload({
    only: ['notifications', 'unreadMessagesCount', 'pendingOrdersCount'],
    preserveScroll: true,
    preserveState: true,
  })
}

// ✅ Setup when component mounts
onMounted(() => {
  // Initial notification store sync
  if (page.props.notifications) {
    store.setNotifications(page.props.notifications)
  }

  // Prevent badge if already on inbox
  if (page.url.startsWith('/seller/inbox')) {
    inboxClicked.value = true
  }

  // Start polling every 5s
  pollingInterval = setInterval(fetchLatestData, 5000)

  // Initial responsive state & listeners
  handleResize()
  window.addEventListener('resize', handleResize)
  document.addEventListener('click', closeProfileModal)
})

// ✅ Cleanup on component unmount
onUnmounted(() => {
  clearInterval(pollingInterval)
  window.removeEventListener('resize', handleResize)
  document.removeEventListener('click', closeProfileModal)
})

// 🔄 Sync store on notification change
watch(
  () => page.props.notifications,
  (newNotifications) => {
    if (newNotifications) {
      store.setNotifications(newNotifications)
    }
  }
)

// Utility
function isActive(routeName) {
  return page.component.includes(routeName)
}

// Sidebar controls
const openSidebar = () => { sidebarOpen.value = true }
const closeSidebar = () => { sidebarOpen.value = false }

// Profile modal controls
const toggleProfileModal = () => {
  profileModalOpen.value = !profileModalOpen.value
}
const closeProfileModal = (e) => {
  if (!e.target.closest('.profile-modal') && !e.target.closest('.avatar')) {
    profileModalOpen.value = false
  }
}

// Handle responsive layout
const handleResize = () => {
  isMobile.value = window.innerWidth < 768
  sidebarOpen.value = !isMobile.value
}

// Logout
const logout = () => {
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
  background-color: #343a40;
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
