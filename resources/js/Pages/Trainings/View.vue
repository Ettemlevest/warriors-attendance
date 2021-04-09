<template>
  <div>
    <div class="mb-8 flex justify-start max-w">
      <h1 class="font-bold text-3xl">
        <inertia-link class="text-gray-500 hover:text-gray-800" :href="route('trainings')">Edzések</inertia-link>
        <span class="text-gray-400 font-medium">/</span>
        {{ form.name }}
      </h1>
    </div>
    <div class="bg-white block max-w rounded-md shadow overflow-hidden">
      <div class="text-center sm:w-auto md:w-auto lg:flex-1 xl:flex-1 m-4 p-4">
        <h2 class="text-3xl mb-2 ml-8 mr-8 font-bold tracking-wider">
          {{ training.name }}
        </h2>
        <div class="text-gray-600 text-sm italic mb-6">{{ training.diff }}</div>
        <div class="sm:block md:block lg:flex xl:flex lg:items-center">
          <div class="mb-2 flex lg:flex-1 xl:flex-1 items-center">
            <icon name="location" class="w-5 h-5 fill-current text-gray-500 mr-2" />
            <div class="text-purple-600 text-lg italic">{{ training.place }}</div>
          </div>
          <div class="mb-2 flex lg:flex-1 xl:flex-1 items-center">
            <icon name="dashboard" class="w-5 h-5 fill-current text-gray-500 mr-2" />
            <div class="text-lg italic">{{ training.start_at_day+' '+training.start_at_time }}</div>
          </div>
          <div class="mb-2 flex lg:flex-1 xl:flex-1 items-center">
            <icon name="users" class="w-5 h-5 fill-current text-gray-500 mr-2" />
            <div class="text-lg italic">{{ training.attendees.data.length }} / {{ training.max_attendees || '&infin;' }}</div>
          </div>
        </div>
        <!-- canAttend(training) -->
        <button v-if="training.can_attend_from < new Date().toISOString() && training.start_at > new Date().toISOString() && !training.registered && (training.max_attendees === 0 || training.attendees.data.length < training.max_attendees)" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" @click="attend(training.id)">
          Jelentkezem
        </button>
        <!-- alreadyOver(training) -->
        <div v-if="training.can_attend_from > new Date().toISOString()" class="bg-yellow-600 text-white font-bold py-2 px-4 rounded">Még nem lehet jelentkezni az edzésre! Gyere vissza 7 nappal előtte.</div>
        <!-- !canAttend(training) && reachedMaxAttendees(training) -->
        <div v-if="training.can_attend_from < new Date().toISOString() && training.start_at > new Date().toISOString() && !training.registered && training.max_attendees > 0 && training.attendees.data.length >= training.max_attendees && !training.can_attend_more" class="bg-red-600 text-white font-bold py-2 px-4 rounded">Megtelt, már nem lehet jelentkezni!</div>
        <!-- canWithdraw(training) -->
        <button v-if="training.can_attend_from < new Date().toISOString() && training.start_at > new Date().toISOString() && training.registered" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded" @click="withdraw(training.id)">
          Lemondás
        </button>
        <!-- canAttend(training) && reachedMaxAttendees(training) -->
        <button v-if="training.can_attend_from < new Date().toISOString() && training.start_at > new Date().toISOString() && !training.registered && training.max_attendees && training.attendees.data.length >= training.max_attendees && training.can_attend_more" class="bg-blue-600 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded" @click="attend(training.id)">
          Megtelt, mégis jelentkezem. Vállalom a 10 burpeet beugrásnak!
        </button>
      </div>
    </div>
    <div class="bg-white rounded shadow overflow-hidden max-w mt-8">
      <table class="w-full whitespace-no-wrap">
        <tr class="text-left font-bold">
          <th class="px-6 pt-6 pb-4 flex justify-between">
            <div>Jelentkezve</div>
            <div class="italic text-gray-500 font-normal">{{ training.attendees.data.length }} fő</div>
          </th>
        </tr>
        <tr v-for="attendee in training.attendees.data" :key="attendee.user_id" class="hover:bg-gray-100 focus-within:bg-gray-100">
          <td class="border-t px-6 py-4 flex items-center">
            <img v-if="attendee.photo" class="block w-8 h-8 rounded-full mr-2 -my-2" :src="attendee.photo">
            {{ attendee.name }}
          </td>
        </tr>
        <tr v-if="training.attendees.data.length === 0">
          <td class="border-t px-6 py-4 text-center" colspan="4">Nem jelentkeztek még az edzésre</td>
        </tr>
      </table>
    </div>
  </div>
</template>

<script>
import Layout from '@/Shared/Layout'
import Icon from '@/Shared/Icon'

export default {
  metaInfo() {
    return { title: this.form.name }
  },
  components: {
    Icon,
  },
  layout: Layout,
  props: {
    training: Object,
  },
  data() {
    return {
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
