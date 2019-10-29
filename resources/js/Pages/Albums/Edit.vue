<template>
  <layout>
    <div class="mb-8 flex justify-start max-w-lg">
      <h1 class="font-bold text-3xl">
        <inertia-link class="text-indigo-light hover:text-indigo-dark" :href="route('albums')">Képek</inertia-link>
        <span class="text-indigo-light font-medium">/</span>
        {{ form.name }}
      </h1>
    </div>
    <div class="bg-white rounded shadow overflow-hidden max-w-lg">
      <form @submit.prevent="submit">
        <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
          <text-input v-model="form.name" :errors="$page.errors.name" class="pr-6 pb-8 w-full lg:w-1/2" label="Album neve" autofocus />
          <text-input v-model="form.place" :errors="$page.errors.place" class="pr-6 pb-8 w-full lg:w-1/2" label="Helyszín" />
          <text-input v-model="form.description" :errors="$page.errors.description" class="pr-6 pb-8 w-full" label="Leírás" />
          <text-input v-model="form.date_from" :errors="$page.errors.date_from" class="pr-6 pb-8 w-full lg:w-1/2" label="Kezdés (dátum)" type="date" timezone="Europe/Budapest" />
          <text-input v-model="form.date_to" :errors="$page.errors.date_to" class="pr-6 pb-8 w-full lg:w-1/2" label="Vége (dátum)" type="date" timezone="Europe/Budapest" />
        </div>
        <div class="px-8 py-4 bg-grey-lightest border-t border-grey-lighter flex items-center">
          <button class="text-red hover:underline" tabindex="-1" type="button" @click="destroy">Törlés</button>
          <loading-button :loading="sending" class="btn-indigo ml-auto" type="submit">Album mentése</loading-button>
        </div>
      </form>
    </div>
    <div class="bg-white rounded shadow overflow-hidden max-w-lg mt-8">
      <table class="w-full">
        <tr class="text-left font-bold">
          <th class="px-6 pt-6 pb-4">Képek <span class="italic">({{ album.photos.length }} kép)</span></th>
        </tr>
        <tr v-if="album.photos.length === 0">
          <td class="border-t px-6 py-4 text-center" colspan="4">Nincsenek még feltöltve képek</td>
        </tr>
      </table>
    </div>
  </layout>
</template>

<script>
import Layout from '@/Shared/Layout'
import LoadingButton from '@/Shared/LoadingButton'
import SelectInput from '@/Shared/SelectInput'
import TextInput from '@/Shared/TextInput'
import CheckboxInput from '@/Shared/CheckboxInput'
import Icon from '@/Shared/Icon'

export default {
  metaInfo() {
    return { title: this.form.name }
  },
  components: {
    Layout,
    LoadingButton,
    SelectInput,
    TextInput,
    CheckboxInput,
    Icon,
  },
  props: {
    album: Object,
  },
  // remember: 'form',
  data() {
    return {
      sending: false,
      form: {
        name: this.album.name,
        place: this.album.place,
        description: this.album.description,
        date_from: this.album.date_from,
        date_to: this.album.date_to,
        photos: this.album.photos,
      },
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
      data.append('_method', 'put')

      this.$inertia.post(this.route('albums', this.album.id), data)
        .then(() => {
          this.sending = false
          if (Object.keys(this.$page.errors).length === 0) {
            this.form.photo = null
            this.form.password = null
          }
        })
    },
    destroy() {
      if (confirm('Biztosan törlöd az albumot?')) {
        this.$inertia.delete(this.route('albums.destroy', this.album.id))
      }
    },
  },
}
</script>