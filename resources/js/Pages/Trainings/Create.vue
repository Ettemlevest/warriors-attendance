<template>
  <layout>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link class="text-indigo-500 hover:text-indigo-600" :href="route('trainings')">Edzések</inertia-link>
      <span class="text-indigo-500 font-medium">/</span> Létrehozás
    </h1>
    <div v-if="$page.auth.user.owner" class="bg-white rounded shadow overflow-hidden max-w">
      <form @submit.prevent="submit">
        <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
        <text-input v-model="form.name" :errors="$page.errors.name" class="pr-6 pb-8 w-full lg:w-1/2" label="Edzés neve" autofocus />
          <text-input v-model="form.place" :errors="$page.errors.place" class="pr-6 pb-8 w-full lg:w-1/2" label="Helyszín" />
          <text-input v-model="form.start_at_day" :errors="$page.errors.start_at_day" class="pr-6 pb-8 w-full lg:w-1/2" label="Kezdés (dátum)" type="date" timezone="Europe/Budapest" />
          <text-input v-model="form.start_at_time" :errors="$page.errors.start_at_time" class="pr-6 pb-8 w-full lg:w-1/2" label="Kezdés (időpont)" type="time" timezone="Europe/Budapest" />
          <text-input v-model="form.length" :errors="$page.errors.length" class="pr-6 pb-8 w-full lg:w-1/2" label="Időtartam (perc)" type="number" />
          <text-input v-model="form.max_attendees" :errors="$page.errors.max_attendees" class="pr-6 pb-8 w-full lg:w-1/2" label="Max. létszám" type="number" />
          <checkbox-input v-model="form.can_attend_more" :errors="$page.errors.can_attend_more" class="pr-6 pb-8 w-full lg:w-1/2" label="Maximális létszám túlléphető" :checked="form.can_attend_more" />
        </div>
        <div class="px-8 py-4 bg-gray-100 border-t border-gray-300 flex justify-end items-center">
          <loading-button :loading="sending" class="btn-indigo" type="submit">Edzés mentése</loading-button>
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
  metaInfo: { title: 'Edzés hozzáadása' },
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
        start_at_day: null,
        start_at_time: null,
        length: "60",
        attendees: null,
        max_attendees: "32",
        can_attend_more: true,
      }
    }
  },
  methods: {
    submit() {
      this.sending = true

      var data = new FormData()
      data.append('name', this.form.name || '')
      data.append('place', this.form.place || '')
      data.append('start_at_day', this.form.start_at_day || '')
      data.append('start_at_time', this.form.start_at_time || '')
      data.append('length', this.form.length || '')
      data.append('max_attendees', this.form.max_attendees || '')
      data.append('can_attend_more', this.form.can_attend_more || '')

      this.$inertia.post(this.route('trainings.store'), data)
        .then(() => this.sending = false)
    }
  },
}
</script>
