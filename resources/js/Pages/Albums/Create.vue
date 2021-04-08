<template>
  <layout>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link class="text-gray-500 hover:text-gray-800" :href="route('albums')">Képek</inertia-link>
      <span class="text-gray-400 font-medium">/</span> Létrehozás
    </h1>
    <div v-if="$page.props.auth.user.owner" class="bg-white rounded-md shadow overflow-hidden max-w">
      <form @submit.prevent="store">
        <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
          <text-input v-model="form.name" :error="form.errors.name" class="pr-6 pb-8 w-full lg:w-1/2" label="Album neve" autofocus />
          <text-input v-model="form.place" :error="form.errors.place" class="pr-6 pb-8 w-full lg:w-1/2" label="Helyszín" />
          <text-input v-model="form.description" :error="form.errors.description" class="pr-6 pb-8 w-full" label="Leírás" />
          <text-input v-model="form.date_from" :error="form.errors.date_from" class="pr-6 pb-8 w-full lg:w-1/2" label="Kezdés (dátum)" type="date" timezone="Europe/Budapest" />
          <text-input v-model="form.date_to" :error="form.errors.date_to" class="pr-6 pb-8 w-full lg:w-1/2" label="Vége (dátum)" type="date" timezone="Europe/Budapest" />
        </div>
        <div class="px-8 py-4 bg-gray-100 border-t border-gray-300 flex justify-end items-center">
          <loading-button :loading="form.processing" class="btn-indigo" type="submit">Album mentése</loading-button>
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
    LoadingButton,
    SelectInput,
    TextInput,
    CheckboxInput,
  },
  layout: Layout,
  remember: 'form',
  data() {
    return {
      form: this.$inertia.form({
        name: null,
        place: null,
        description: null,
        date_from: null,
        date_to: null,
      }),
    }
  },
  methods: {
    store() {
      this.form.post(this.route('albums.store'))
    },
  }
}
</script>
