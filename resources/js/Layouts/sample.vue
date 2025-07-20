<template>
  <div>
    <!-- Sidebar -->
    <nav id="sidebar" class="p-3 text-white" :class="{ show: sidebarOpen }">
      <form class="mb-3">
        <input type="text" class="form-control form-control-sm" placeholder="Search..." />
      </form>

      <ul class="nav flex-column mb-3">
        <li class="nav-item">
          <Link href="/dashboard" class="nav-link" :class="{ active: page.url === '/dashboard' }">
            Dashboard
          </Link>
        </li>
        <li class="nav-item"><Link href="/orders" class="nav-link">Orders</Link></li>
        <li class="nav-item"><Link href="/products" class="nav-link">Products</Link></li>
        <li class="nav-item"><Link href="/customers" class="nav-link">Customers</Link></li>
      </ul>

      <hr class="bg-secondary" />
    </nav>

    <!-- Topbar -->
    <nav id="topbar" class="d-flex justify-content-between align-items-center px-3 shadow-sm">
      <div class="d-flex align-items-center gap-3">
        <button id="toggleArrow" class="btn p-1" @click="openSidebar">&#9776;</button>
        <span class="logo">🌐 MyCompany</span>
      </div>

      <!-- Profile Dropdown -->
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
              <div class="fw-bold">Hi, {{ user?.name }}</div>
              <div class="text-danger small bg-light px-2 py-1 rounded d-inline">
                {{ user?.role ?? 'N/A' }}
              </div>
              <div class="text-muted small">{{ user?.email ?? 'N/A' }}</div>
            </div>
          </div>
          <div>
            <Link class="dropdown-item" :href="route('profile.edit')">My Profile</Link>
            <Link class="dropdown-item" :href="route('logout')" method="post" as="button">
              Sign Out
            </Link>
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
import { ref, onMounted, onUnmounted } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'

// Access Inertia page props properly
const page = usePage()
const user = page.props.auth?.user

const sidebarOpen = ref(false)
const isMobile = ref(false)
const profileModalOpen = ref(false)

const openSidebar = () => (sidebarOpen.value = true)
const closeSidebar = () => (sidebarOpen.value = false)
const toggleProfileModal = () => (profileModalOpen.value = !profileModalOpen.value)

const handleResize = () => {
  isMobile.value = window.innerWidth < 768
  sidebarOpen.value = !isMobile.value
}

const closeProfileModal = (e) => {
  if (!e.target.closest('.profile-modal') && !e.target.closest('.avatar')) {
    profileModalOpen.value = false
  }
}

onMounted(() => {
  handleResize()
  window.addEventListener('resize', handleResize)
  document.addEventListener('click', closeProfileModal)
})

onUnmounted(() => {
  window.removeEventListener('resize', handleResize)
  document.removeEventListener('click', closeProfileModal)
})
</script>

<style scoped>
body {
  overflow-x: hidden;
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

#content {
  padding: 1.5rem;
  margin-top: 70px;
  transition: margin-left 0.3s ease;
}

#content.shifted {
  margin-left: 250px;
}
</style>

