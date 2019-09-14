<template>
  <layout :title="`${form.name}`">
    <div class="mb-8 flex justify-start max-w-lg">
      <h1 class="font-bold text-3xl">
        <inertia-link class="text-indigo-light hover:text-indigo-dark" :href="route('trainings')">Edzések</inertia-link>
        <span class="text-indigo-light font-medium">/</span>
        {{ form.name }}
      </h1>
    </div>
    <div class="bg-white rounded shadow overflow-hidden max-w-lg">
      <form @submit.prevent="submit">
        <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
          <text-input v-model="form.name" :errors="$page.errors.name" class="pr-6 pb-8 w-full lg:w-1/2" label="Edzés neve" />
          <text-input v-model="form.place" :errors="$page.errors.place" class="pr-6 pb-8 w-full lg:w-1/2" label="Helyszín" />
          <text-input v-model="form.start_at" :errors="$page.errors.start_at" class="pr-6 pb-8 w-full lg:w-1/2" label="Kezdés" />
          <text-input v-model="form.length" :errors="$page.errors.length" class="pr-6 pb-8 w-full lg:w-1/2" label="Időtartam (perc)" type="number" />
          <text-input v-model="form.max_attendees" :errors="$page.errors.max_attendees" class="pr-6 pb-8 w-full lg:w-1/2" label="Max. létszám" type="number" />
        </div>
        <div class="px-8 py-4 bg-grey-lightest border-t border-grey-lighter flex items-center">
          <button class="text-red hover:underline" tabindex="-1" type="button" @click="destroy">Törlés</button>
          <loading-button :loading="sending" class="btn-indigo ml-auto" type="submit">Edzés mentése</loading-button>
        </div>
      </form>
    </div>
    <div class="bg-white rounded shadow overflow-hidden max-w-lg mt-8">
      <table class="w-full whitespace-no-wrap">
        <tr class="text-left font-bold">
          <th class="px-6 pt-6 pb-4">Jelentkezve</th>
        </tr>
        <tr v-for="attendee in training.attendees" :key="attendee.user_id" class="hover:bg-grey-lightest focus-within:bg-grey-lightest">
          <td class="border-t px-6 py-4 flex items-center">
            {{ attendee.name }}
          </td>
        </tr>
        <tr v-if="training.attendees.length === 0">
          <td class="border-t px-6 py-4 text-center" colspan="4">Nem jelentkeztek még az edzésre</td>
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

export default {
  components: {
    Layout,
    LoadingButton,
    SelectInput,
    TextInput,
  },
  props: {
    training: Object,
  },
  remember: 'form',
  data() {
    return {
      sending: false,
      form: {
        name: this.training.name,
        place: this.training.place,
        start_at: this.training.start_at,
        length: this.training.length,
        attendees: this.training.attendees,
        max_attendees: this.training.max_attendees,
      },
    }
  },
  methods: {
    submit() {
      this.sending = true

      var data = new FormData()
      data.append('name', this.form.name || '')
      data.append('place', this.form.place || '')
      data.append('start_at', this.form.start_at || '')
      data.append('length', this.form.length || '')
      data.append('max_attendees', this.form.max_attendees || '')
      data.append('_method', 'put')

      this.$inertia.post(this.route('trainings.update', this.training.id), data)
        .then(() => {
          this.sending = false
          if (Object.keys(this.$page.errors).length === 0) {
            this.form.photo = null
            this.form.password = null
          }
        })
    },
    destroy() {
      if (confirm('Biztosan törlöd az edzést?')) {
        this.$inertia.delete(this.route('trainings.destroy', this.training.id))
      }
    },
  },
}
</script>
