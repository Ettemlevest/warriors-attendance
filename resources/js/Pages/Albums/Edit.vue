<template>
  <layout>
    <div class="mb-8 flex justify-start max-w">
      <h1 class="font-bold text-3xl">
        <inertia-link class="text-indigo-500 hover:text-indigo-600" :href="route('albums')">Képek</inertia-link>
        <span class="text-indigo-500 font-medium">/</span>
        {{ form.name }}
      </h1>
    </div>
    <div class="bg-white rounded shadow overflow-hidden max-w">
      <form @submit.prevent="submit">
        <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
          <text-input v-model="form.name" :errors="$page.errors.name" class="pr-6 pb-8 w-full lg:w-1/2" label="Album neve" autofocus />
          <text-input v-model="form.place" :errors="$page.errors.place" class="pr-6 pb-8 w-full lg:w-1/2" label="Helyszín" />
          <text-input v-model="form.description" :errors="$page.errors.description" class="pr-6 pb-8 w-full" label="Leírás" />
          <text-input v-model="form.date_from" :errors="$page.errors.date_from" class="pr-6 pb-8 w-full lg:w-1/2" label="Kezdés (dátum)" type="date" timezone="Europe/Budapest" />
          <text-input v-model="form.date_to" :errors="$page.errors.date_to" class="pr-6 pb-8 w-full lg:w-1/2" label="Vége (dátum)" type="date" timezone="Europe/Budapest" />
          <!-- <file-input v-model="form.photos" :errors="$page.errors.photos" class="pr-6 pb-8 w-full" type="file" accept="image/*" label="Képek" multiple="multiple" /> -->
          <label class="form-label">Képek:</label>
          <input class="pr-6 pb-8 w-full" type="file" ref="photos" multiple="multiple" accept="image/*">
        </div>
        <div class="px-8 py-4 bg-gray-100 border-t border-gray-300 flex items-center">
          <button class="text-red-500 hover:underline tracking-widest" tabindex="-1" type="button" @click="destroy">Törlés</button>
          <loading-button :loading="sending" class="btn-indigo ml-auto" type="submit">Album mentése</loading-button>
        </div>
      </form>
    </div>
    <div class="bg-white rounded shadow overflow-hidden max-w mt-8">
      <table class="w-full">
        <tr class="text-left font-bold">
          <th class="px-6 pt-6 pb-4" colspan="3">Képek <span class="italic">({{ album.photos.length }} kép)</span></th>
        </tr>
        <tr v-for="photo in album.photos" :key="photo.id" class="hover:bg-gray-100 focus-within:bg-gray-100">
          <td class="border-t">
            <img :src="photo.path" class="block m-4">
          </td>
          <td class="border-t">
            <button class="btn hover:underline" tabindex="-1" type="button" @click="setAsCover(photo.id)">
              <icon name="star" class="flex-shrink-0 w-5 h-5 fill-current text-yellow-500" />
            </button>
          </td>
          <td class="border-t">
            <button class="btn-danger text-red-500 hover:underline" tabindex="-1" type="button" @click="destroyPhoto(photo.id)">
              <icon name="trash" class="flex-shrink-0 w-5 h-5 fill-current text-red-700" />
            </button>
          </td>
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
import FileInput from '@/Shared/FileInput'
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
    FileInput,
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
        photos: null,
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

      for(var i=0; i < this.$refs.photos.files.length; i++) {
        data.append('photos[]', this.$refs.photos.files[i] || '')
      }

      data.append('_method', 'put')

      this.$inertia.post(this.route('albums.update', this.album.id), data)
        .then(() => {
          this.sending = false
          if (Object.keys(this.$page.errors).length === 0) {
            this.form.photos = null
          }
        })
    },
    destroy() {
      if (confirm('Biztosan törlöd az albumot?')) {
        this.$inertia.delete(this.route('albums.destroy', this.album.id))
      }
    },
    destroyPhoto(photo_id) {
      if (confirm('Biztosan törlöd a képet?')) {
        this.$inertia.delete(this.route('photos.destroy', photo_id))
      }
    },
    setAsCover(photo_id) {
      this.$inertia.post(this.route('albums.cover', [this.album.id, photo_id]))
    }
  },
}
</script>
