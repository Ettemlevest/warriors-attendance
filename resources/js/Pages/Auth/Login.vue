<template>
  <div class="p-6 bg-indigo-800 min-h-screen flex justify-center items-center">
    <div class="w-full max-w-md">
      <!-- <img class="h-20 w-20 mr-4" src="/logo.png" /> -->
      <form class="mt-8 bg-white rounded-lg shadow-xl overflow-hidden" @submit.prevent="login">
        <div class="px-10 py-12">
          <h1 class="text-center font-bold text-3xl">Kiskunlacháza Warriors</h1>
          <div class="mx-auto mt-6 w-24 border-b-2" />
          <text-input v-model="form.email" :error="form.errors.email" class="mt-10" label="Email" type="email" autofocus autocapitalize="off" />
          <text-input v-model="form.password" :error="form.errors.password" class="mt-6" label="Jelszó" type="password" />
          <label class="mt-6 select-none flex items-center" for="remember">
            <input id="remember" v-model="form.remember" class="mr-1" type="checkbox" />
            <span class="text-sm">Remember Me</span>
          </label>
        </div>
        <div class="px-10 py-4 bg-gray-100 border-t border-gray-100 flex justify-between items-center">
          <a class="hover:underline" tabindex="-1" href="register">Regisztráció</a>
          <loading-button :loading="form.processing" class="btn-indigo" type="submit">Belépés</loading-button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import Logo from '@/Shared/Logo'
import TextInput from '@/Shared/TextInput'
import LoadingButton from '@/Shared/LoadingButton'

export default {
  metaInfo: { title: 'Bejelentkezés' },
  components: {
    LoadingButton,
    Logo,
    TextInput,
  },
  data() {
    return {
      form: this.$inertia.form({
        email: null,
        password: null,
        remember: false,
      }),
    }
  },
  methods: {
    login() {
      this.form
        .transform(data => ({
          ...data,
          remember: data.remember ? 'on' : '',
        }))
        .post(this.route('login.attempt'))
    },
  },
}
</script>
