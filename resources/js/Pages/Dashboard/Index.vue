<template>
  <layout>
    <h1 class="mb-8 font-bold text-3xl">Üdvözlünk!</h1>
    <p class="mb-12 leading-normal">Szia Warrior! Itt jelentkezhetsz a következő edzésekre.</p>

    <div v-if="this.message" class="mb-8 flex items-center justify-between bg-blue rounded max-w">
      <div class="flex items-center">
        <icon name="notifications" class="ml-8 mr-4 flex-shrink-0 w-8 h-8 fill-white" />
        <div class="py-4 text-white text-sm font-medium">
          <p class="font-bold text-xl mb-2">{{ this.message.title }}</p>
          {{ this.message.body }}
        </div>
      </div>
    </div>

    <h2 class="mb-8 font-bold text-xl">Közelgő edzések</h2>
    <p v-if="trainings.length === 0" class="mb-12 leading-normal">Egyelőre nincs felvéve új edzés. Nézz vissza később 😊</p>
    <div v-for="training in trainings" :key="training.id" class="overflow-x-auto block lg:flex-wrap xl:flex-wrap">
      <div class="text-center sm:w-auto md:w-auto lg:flex-1 xl:flex-1 m-4 p-4 bg-white rounded-lg shadow-md">
        <h2 class="text-xl mb-2 ml-8 mr-8">
          <inertia-link class="text-indigo-600" :href="route($page.auth.user.owner ? 'trainings.edit' : 'trainings.view', training.id)">{{ training.name }}</inertia-link>
        </h2>
        <div class="text-gray-600er text-sm italic mb-6">{{ training.diff }}</div>
        <div class="sm:block md:block lg:flex xl:flex lg:items-center">
          <div class="mb-4 flex lg:flex-1 xl:flex-1">
            <icon name="location" class="flex w-5 h-5 fill-gray mr-2" />
            <div class="text-purple text-lg italic">{{ training.place }}</div>
          </div>
          <div class="mb-4 text-gray-600er items-center flex lg:flex-1 xl:flex-1">
            <icon name="dashboard" class="flex w-5 h-5 fill-gray mr-2" />
            <div class="text-lg italic">{{ training.start_at }}</div>
          </div>
          <div class="mb-4 text-gray-600er items-center flex lg:flex-1 xl:flex-1">
            <icon name="users" class="flex w-5 h-5 fill-gray mr-2" />
            <div class="text-lg italic">{{ training.attendees.length }} / {{ training.max_attendees || '&infin;' }}</div>
          </div>
        </div>
        <div></div>
        <button v-if="!training.registered && (training.max_attendees === 0 || training.attendees.length < training.max_attendees)" class="bg-blue hover:bg-blue-600 text-white font-bold py-2 px-4 rounded" @click="attend(training.id)">
          Jelentkezem
        </button>
        <div v-if="!training.registered && training.attendees.length >= training.max_attendees && !training.can_attend_more" class="bg-red text-white font-bold py-2 px-4 rounded">Megtelt, már nem lehet jelentkezni!</div>
        <button v-if="training.registered" class="bg-red hover:bg-red-600 text-white font-bold py-2 px-4 rounded" @click="withdraw(training.id)">
          Lemondás
        </button>
        <button v-if="!training.registered && training.max_attendees && training.attendees.length >= training.max_attendees && training.can_attend_more" class="bg-blue hover:bg-blue-600 text-white font-bold py-2 px-4 rounded" @click="attend(training.id)">
          Megtelt, mégis jelentkezem. Vállalom a 10 burpeet beugrásnak!
        </button>
      </div>
    </div>
  </layout>
</template>

<script>
import Layout from '@/Shared/Layout'
import Icon from '@/Shared/Icon'

export default {
  metaInfo: { title: 'Kezdőlap' },
  components: {
    Icon,
    Layout,
  },
  props: {
    message: Object,
    trainings: Array,
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
