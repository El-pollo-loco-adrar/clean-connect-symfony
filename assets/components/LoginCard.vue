<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-100 px-4">
    <!-- CARD -->
    <div class="w-full max-w-md bg-white rounded-xl shadow-lg border p-8">

      <!-- Logo -->
      <div class="flex justify-center mb-6">
        <img src="/Logo.png" alt="Logo" class="h-16" />
      </div>

      <!-- Titre -->
      <h2 class="text-2xl font-bold text-center mb-6">
        Connexion
      </h2>

      <!-- FORMULAIRE -->
      <form class="space-y-4" @submit.prevent="login">

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">
            Mon email
          </label>
          <input
            type="email"
            v-model="email"
            required
            class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
          />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">
            Mon mot de passe
          </label>
          <input
            type="password"
            v-model="password"
            required
            autocomplete="current-password"
            class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
          />
        </div>

        <div class="text-right">
          <a href="#" class="text-sm italic underline text-gray-600 hover:text-blue-600">
            Mot de passe oublié ?
          </a>
        </div>

        <!-- Bouton principal -->
        <button
          type="submit"
          class="w-full bg-blue-600 text-white py-2 rounded-lg font-medium hover:bg-blue-700 transition"
        >
          Connexion
        </button>

      </form>

      <!-- AUTRES ACTIONS -->
      <div class="mt-6 space-y-2">
        <button
          @click="loginPro"
          class="w-full border border-blue-600 text-blue-600 py-2 rounded-lg hover:bg-blue-50 transition"
        >
          Connexion pro
        </button>

        <p class="text-center text-sm mt-4">
          Pas encore de compte ?
          <a href="#" class="underline font-medium">
            Je m’inscris
          </a>
        </p>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const email = ref('')
const password = ref('')

const login = () => {
  console.log('Login', email.value, password.value)
}

const loginPro = () => {
  console.log('Login PRO')
}
</script>