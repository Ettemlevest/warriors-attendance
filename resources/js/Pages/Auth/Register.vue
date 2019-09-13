<template>
  <div class="p-6 bg-indigo-darker min-h-screen flex justify-center items-center">
    <div class="w-full max-w-sm">
      <form class="mt-8 bg-white rounded-lg shadow-lg overflow-hidden" @submit.prevent="submit">
        <div class="px-10 py-12">
          <h1 class="text-center font-bold text-3xl">Regisztráció</h1>
          <div class="mx-auto mt-6 w-24 border-b-2" />
          <text-input v-model="form.first_name" :errors="$page.errors.first_name" class="mt-10" label="Családnév" type="text" autofocus />
          <text-input v-model="form.last_name" :errors="$page.errors.last_name" class="mt-6" label="Utónév" type="text" />
          <text-input v-model="form.email" :errors="$page.errors.email" class="mt-6" label="E-mail" type="text" />
          <text-input v-model="form.password" :errors="$page.errors.password" class="mt-6" label="Jelszó" type="password" />
          <text-input v-model="form.password_confirmation" class="mt-6" label="Jelszó megerősítés" type="password" />
        </div>
        <div class="px-10 py-4 bg-grey-lightest border-t border-grey-lighter flex justify-between items-center">
          <inertia-link class="hover:underline" :href="route('login')">Bejelentkezés</inertia-link>
          <loading-button :loading="sending" class="btn-indigo" type="submit">Regisztráció</loading-button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import LoadingButton from '@/Shared/LoadingButton'
import TextInput from '@/Shared/TextInput'

export default {
  components: {
    LoadingButton,
    TextInput,
  },
  props: {
    errors: Object,
  },
  data() {
    return {
      sending: false,
      form: {
        first_name: null,
        last_name: null,
        email: null,
        password: null,
        password_confirmation: null,
      },
    }
  },
  mounted() {
    document.title = 'Regisztráció | Kiskunlacháza Warriors'
  },
  methods: {
    submit() {
      this.sending = true
      this.$inertia.post(this.route('register'), {
        first_name: this.form.first_name,
        last_name: this.form.last_name,
        email: this.form.email,
        password: this.form.password,
        password_confirmation: this.form.password_confirmation,
      }).then(() => this.sending = false)
    },
  },
}
</script>
