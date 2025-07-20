<template>
  <nav class="bg-white shadow-sm">
    <div class="container mx-auto px-4">
      <div class="flex items-center justify-between py-3">
        <!-- Brand -->
        <Link href="/about" class="text-lg font-bold text-gray-800 no-underline">
          <span class="text-green-700">CraftSmart</span>
        </Link>

        <!-- Toggler Button -->
        <button
          @click="isOpen = !isOpen"
          class="lg:hidden text-gray-800 focus:outline-none"
        >
          <svg
            class="w-6 h-6"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
            xmlns="http://www.w3.org/2000/svg"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M4 6h16M4 12h16M4 18h16"
            />
          </svg>
        </button>

        <!-- Desktop Nav Links -->
        <div class="hidden lg:flex space-x-6 items-center">
          <Link href="/about" class="text-gray-700 hover:text-green-700 no-underline">AboutUs</Link>
          <Link href="/login" class="text-gray-700 hover:text-green-700 no-underline">Login</Link>
          <Link href="/register" class="no-underline px-4 py-2 border border-black text-gray-600 rounded hover:bg-green-700 hover:text-white transition duration-300">
            Register
          </Link>
        </div>
      </div>

      <!-- Mobile Nav Links -->
      <div v-if="isOpen" class="lg:hidden pb-3 space-y-2">
        <Link href="/about" class="block text-gray-600 hover:text-green-700 no-underline">AboutUs</Link>
        <Link href="/login" class="block text-gray-600 hover:text-green-700 no-underline">Login</Link>
        <Link href="/register" class="inline-block no-underline mt-8 px-4 py-2 border border-green-800 text-gray-600 rounded hover:bg-green-700 hover:text-white transition duration-300">
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
