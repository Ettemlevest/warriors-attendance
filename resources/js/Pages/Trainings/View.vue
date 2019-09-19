<template>
  <layout :title="`${form.name}`">
    <div class="mb-8 flex justify-start max-w-lg">
      <h1 class="font-bold text-3xl">
        <inertia-link class="text-indigo-light hover:text-indigo-dark" :href="route('trainings')">Edzések</inertia-link>
        <span class="text-indigo-light font-medium">/</span>
        {{ form.name }}
      </h1>
    </div>
    <div class="bg-white block max-w-lg rounded shadow overflow-hidden">
      <div class="text-center sm:w-auto md:w-auto lg:flex-1 xl:flex-1 m-4 p-4">
        <h2 class="text-xl mb-2 ml-8 mr-8">
          {{ training.name }}
        </h2>
        <div class="text-grey-darker text-sm italic mb-6">{{ training.diff }}</div>
        <div class="sm:block md:block lg:flex xl:flex lg:items-center">
          <div class="mb-4 flex lg:flex-1 xl:flex-1">
            <icon name="location" class="flex w-5 h-5 fill-grey mr-2" />
            <div class="text-purple text-lg italic">{{ training.place }}</div>
          </div>
          <div class="mb-4 text-grey-darker items-center flex lg:flex-1 xl:flex-1">
            <icon name="dashboard" class="flex w-5 h-5 fill-grey mr-2" />
            <div class="text-lg italic">{{ training.start_at_day+' '+training.start_at_time }}</div>
          </div>
          <div class="mb-4 text-grey-darker items-center flex lg:flex-1 xl:flex-1">
            <icon name="users" class="flex w-5 h-5 fill-grey mr-2" />
            <div class="text-lg italic">{{ training.attendees.length }} / {{ training.max_attendees }}</div>
          </div>
        </div>
        <button v-if="!training.registered && training.attendees.length < training.max_attendees" class="bg-blue hover:bg-blue-dark text-white font-bold py-2 px-4 rounded" @click="attend(training.id)">
          Jelentkezem
        </button>
        <div v-if="!training.registered && training.attendees.length >= training.max_attendees" class="bg-red text-white font-bold py-2 px-4 rounded">Megtelt, már nem lehet jelentkezni!</div>
        <button v-if="training.registered" class="bg-red hover:bg-red-dark text-white font-bold py-2 px-4 rounded" @click="withdraw(training.id)">
          Lemondás
        </button>
      </div>
    </div>
    <div class="bg-white rounded shadow overflow-hidden max-w-lg mt-8">
      <table class="w-full whitespace-no-wrap">
        <tr class="text-left font-bold">
          <th class="px-6 pt-6 pb-4">Jelentkezve</th>
        </tr>
        <tr v-for="attendee in training.attendees" :key="attendee.user_id" class="hover:bg-grey-lightest focus-within:bg-grey-lightest">
          <td class="border-t px-6 py-4 flex items-center">
            <img v-if="attendee.photo" class="block w-8 h-8 rounded-full mr-2 -my-2" :src="attendee.photo">
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
import Icon from '@/Shared/Icon'

export default {
  components: {
    Layout,
    Icon,
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
    attend(training_id) {
      this.$inertia.post(this.route('trainings.attend', training_id), null)
    },
    withdraw(training_id) {
      this.$inertia.delete(this.route('trainings.withdraw', training_id), null)
    }
  },
}
</script>
