<template>
  <div class="p-6 bg-indigo-800 min-h-screen flex justify-center items-center">
    <div class="w-full max-w-md">
      <!-- <logo class="block mx-auto w-full max-w-xs fill-white" height="50" /> -->
      <form class="mt-8 bg-white rounded-lg shadow-xl overflow-hidden" @submit.prevent="register">
        <div class="px-10 py-12">
          <h1 class="text-center font-bold text-3xl">Regisztráció</h1>
          <div class="mx-auto mt-6 w-24 border-b-2" />
          <text-input v-model="form.name" :error="form.errors.name" class="mt-10" label="Név" type="text" autofocus />
          <text-input v-model="form.email" :error="form.errors.email" class="mt-6" label="Email" type="email" autocapitalize="off" />
          <text-input v-model="form.password" :error="form.errors.password" class="mt-6" label="Jelszó" type="password" />
          <text-input v-model="form.password_confirmation" :error="form.errors.password_confirmation" class="mt-6" label="Jelszó megerősítés" type="password" />
        </div>
        <div class="px-10 py-4 bg-gray-100 border-t border-gray-100 flex justify-between items-center">
          <a class="hover:underline" tabindex="-1" href="login">Bejelentkezés</a>
          <loading-button :loading="form.processing" class="btn-indigo" type="submit">Regisztráció</loading-button>
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
  metaInfo: { title: 'Regisztráció' },
  components: {
    LoadingButton,
    Logo,
    TextInput,
  },
  data() {
    return {
      form: this.$inertia.form({
        name: null,
        email: null,
        password: null,
        password_confirmation: null,
        remember: false,
      }),
    }
  },
  methods: {
    register() {
      this.form
        .transform(data => ({
          ...data,
          remember: data.remember ? 'on' : '',
        }))
        .post(this.route('register'))
    },
  },
}
</script>
