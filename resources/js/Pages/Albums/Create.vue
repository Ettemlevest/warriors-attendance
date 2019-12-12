<template>
  <layout>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link class="text-indigo-300 hover:text-indigo-600" :href="route('albums')">Képek</inertia-link>
      <span class="text-indigo-300 font-medium">/</span> Létrehozás
    </h1>
    <div v-if="$page.auth.user.owner" class="bg-white rounded shadow overflow-hidden max-w">
      <form @submit.prevent="submit">
        <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
          <text-input v-model="form.name" :errors="$page.errors.name" class="pr-6 pb-8 w-full lg:w-1/2" label="Album neve" autofocus />
          <text-input v-model="form.place" :errors="$page.errors.place" class="pr-6 pb-8 w-full lg:w-1/2" label="Helyszín" />
          <text-input v-model="form.description" :errors="$page.errors.description" class="pr-6 pb-8 w-full" label="Leírás" />
          <text-input v-model="form.date_from" :errors="$page.errors.date_from" class="pr-6 pb-8 w-full lg:w-1/2" label="Kezdés (dátum)" type="date" timezone="Europe/Budapest" />
          <text-input v-model="form.date_to" :errors="$page.errors.date_to" class="pr-6 pb-8 w-full lg:w-1/2" label="Vége (dátum)" type="date" timezone="Europe/Budapest" />
        </div>
        <div class="px-8 py-4 bg-gray-100 border-t border-gray-300 flex justify-end items-center">
          <loading-button :loading="sending" class="btn-indigo" type="submit">Album mentése</loading-button>
        </div>
      </form>
    </div>
  </layout>
</template>

<script>
import Layout from '@/Shared/Layout'
import LoadingButton from '@/Shared/LoadingButton'
import SelectInput from '@/Shared/SelectInput'
import TextInput from '@/Shared/TextInput'
import CheckboxInput from '@/Shared/CheckboxInput'

export default {
  metaInfo: { title: 'Album hozzáadása' },
  components: {
    Layout,
    LoadingButton,
    SelectInput,
    TextInput,
    CheckboxInput,
  },
  // remember: 'form',
  data() {
    return {
      sending: false,
      form: {
        name: null,
        place: null,
        description: null,
        date_from: null,
        date_to: null,
      }
    }
  },
  methods: {
    submit() {
      this.sending = true

      var data = new FormData()
      data.append('name', this.form.name || '')
      data.append('description', this.form.description || '')
      data.append('place', this.form.place || '')
      data.append('date_from', this.form.date_from || '')
      data.append('date_to', this.form.date_to || '')

      this.$inertia.post(this.route('albums.store'), data)
        .then(() => this.sending = false)
    }
  },
}
</script>
