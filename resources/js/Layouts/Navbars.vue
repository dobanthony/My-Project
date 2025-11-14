<template>
  <nav class="bg-white shadow-sm">
    <div class="container mx-auto px-4">
      <div class="flex items-center justify-between py-3">
        
        <!-- Brand -->
        <Link href="/" class="text-lg font-bold text-gray-800 no-underline">
          <span class="text-green-700">CraftSmart</span>
        </Link>

        <!-- Hamburger Button -->
        <button
          @click="isOpen = !isOpen"
          class="lg:hidden text-gray-800 focus:outline-none"
        >
          <svg
            class="w-7 h-7"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M4 6h16M4 12h16M4 18h16"
            />
          </svg>
        </button>

        <!-- Desktop Menu -->
        <div class="hidden lg:flex space-x-6 items-center">
          <Link href="/about" class="text-gray-700 hover:text-green-700 no-underline">About Us</Link>

          <Link href="/login" class="text-gray-700 hover:text-green-700 no-underline">Login</Link>

          <Link
            href="/register"
            class="no-underline px-4 py-2 border border-green-700 text-gray-700 rounded hover:bg-green-700 hover:text-white transition duration-300"
          >
            Register
          </Link>
        </div>
      </div>

      <!-- Mobile Dropdown -->
      <div
        v-if="isOpen"
        class="lg:hidden pb-3 mt-1 space-y-3 animate-fade-in"
      >
        <Link href="/about" class="block text-gray-700 hover:text-green-700 no-underline">
          About Us
        </Link>

        <Link href="/login" class="block text-gray-700 hover:text-green-700 no-underline">
          Login
        </Link>

        <Link
          href="/register"
          class="inline-block px-4 py-2 border border-green-700 rounded text-gray-700 hover:bg-green-700 hover:text-white transition duration-300 no-underline"
        >
          Register
        </Link>
      </div>
    </div>
  </nav>

  <div class="container mx-auto px-4 py-4">
    <slot />
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'

const isOpen = ref(false)
</script>

<style>
@keyframes fade-in {
  from { opacity: 0; transform: translateY(-5px); }
  to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in {
  animation: fade-in 0.2s ease-out;
}
</style>
