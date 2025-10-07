<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';

const showingNavigationDropdown = ref(false);
</script>

<template>
  <div class="min-vh-100 bg-light">
    <!-- Navbar -->
    <nav
      class="navbar navbar-expand-lg navbar-light bg-success border-bottom"
      style="box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);"
    >
      <div class="container">
        <!-- Brand / Logo -->
        <Link :href="route('dashboard')" class="navbar-brand fw-bold text-white">
          Dashboard
        </Link>

        <!-- Toggler -->
        <button
          class="navbar-toggler"
          type="button"
          @click="showingNavigationDropdown = !showingNavigationDropdown"
        >
          <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navbar Links -->
        <div
          class="collapse navbar-collapse"
          :class="{ show: showingNavigationDropdown }"
        >
          <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            <li class="nav-item">
              <Link
                :href="route('dashboard')"
                class="nav-link"
                :class="{ active: route().current('dashboard') }"
              >
                <i class="bi bi-speedometer2 me-1"></i> Dashboard
              </Link>
            </li>
          </ul>

          <!-- Right Side -->
          <ul class="navbar-nav ms-auto">
            <li class="nav-item dropdown">
              <a
                class="nav-link dropdown-toggle text-white"
                href="#"
                id="navbarDropdown"
                role="button"
                data-bs-toggle="dropdown"
                aria-expanded="false"
              >
                <i class="bi bi-person-circle me-1"></i>
                {{ $page.props.auth.user.name }}
              </a>
              <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                <li>
                  <Link class="dropdown-item" :href="route('profile.edit')">
                    <i class="bi bi-person-lines-fill me-2"></i> Profile
                  </Link>
                </li>
                <li><hr class="dropdown-divider" /></li>
                <li>
                  <Link
                    class="dropdown-item text-danger"
                    :href="route('logout')"
                    method="post"
                    as="button"
                  >
                    <i class="bi bi-box-arrow-right me-2"></i> Log Out
                  </Link>
                </li>
              </ul>
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <!-- Header Slot -->
    <header v-if="$slots.header" class="bg-white shadow-sm py-3 mb-4">
      <div class="container">
        <slot name="header" />
      </div>
    </header>

    <!-- Main Content -->
    <main class="container py-4">
      <slot />
    </main>
  </div>
</template>
