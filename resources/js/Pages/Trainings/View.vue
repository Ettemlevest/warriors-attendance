<template>
  <div>
    <div class="mb-8 flex justify-start max-w">
      <h1 class="font-bold text-3xl">
        <inertia-link class="text-gray-500 hover:text-gray-800" :href="route('trainings')">Edzések</inertia-link>
        <span class="text-gray-400 font-medium">/</span>
        {{ training.name }}
      </h1>
    </div>
    <div class="bg-white block max-w rounded-md shadow overflow-hidden">
      <div class="text-center sm:w-auto md:w-auto lg:flex-1 xl:flex-1 m-4 p-4">
        <h2 class="text-3xl mb-2 ml-8 mr-8 font-bold tracking-wider flex justify-center">
          <icon :name="training.type" class="h-8 w-8 mr-2 fill-current" />
          {{ training.name }}
        </h2>
        <div class="text-gray-600 text-sm italic mb-4 text-center">
          {{ typeForHumans(training.type) }} - {{ training.diff }}
        </div>
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
        <inertia-link
          v-if="$page.props.auth.user.phone_missing"
          class="block bg-indigo-600 hover:bg-indigo-700 text-white font-bold p-2 rounded w-full tracking-wider text-center"
          :href="route('users.edit', $page.props.auth.user.id)"
        >
          Hiányzó telefonszám megadása
        </inertia-link>

        <button v-if="training.can_attend_from < new Date().toISOString() && training.start_at > new Date().toISOString() && !training.registered && (training.max_attendees === 0 || training.attendees.data.length < training.max_attendees) && ! $page.props.auth.user.phone_missing" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold p-2 rounded w-full tracking-wider" @click="attend(training.id)">
          Jelentkezem
        </button>
        <!-- alreadyOver(training) -->
        <div v-if="training.can_attend_from > new Date().toISOString() && ! $page.props.auth.user.phone_missing" class="bg-orange-500 text-white tracking-wider p-2 rounded-md italic w-full text-center">Még nem lehet jelentkezni az edzésre! Gyere vissza 7 nappal előtte.</div>
        <!-- !canAttend(training) && reachedMaxAttendees(training) -->
        <div v-if="training.can_attend_from < new Date().toISOString()
        && training.start_at > new Date().toISOString()
        && !training.registered
        && training.max_attendees > 0
        && training.attendees.data.length >= training.max_attendees
        && ! $page.props.auth.user.phone_missing
          " class="bg-orange-500 text-white tracking-wider p-2 rounded-md italic w-full text-center">Megtelt, már nem lehet jelentkezni!</div>
        <!-- canWithdraw(training) -->
        <button v-if="training.can_attend_from < new Date().toISOString() && training.start_at > new Date().toISOString() && training.registered && ! $page.props.auth.user.phone_missing" class="bg-red-600 hover:bg-red-700 text-white font-bold p-2 rounded w-full tracking-wider" @click="withdraw(training.id)">
          Lemondás
        </button>
        <!-- canAttend(training) && reachedMaxAttendees(training) -->
        <!-- <button v-if="training.can_attend_from < new Date().toISOString() && training.start_at > new Date().toISOString() && !training.registered && training.max_attendees && training.attendees.data.length >= training.max_attendees && training.can_attend_more" class="bg-blue-600 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded" @click="attend(training.id)">
          Megtelt, mégis jelentkezem. Vállalom a 10 burpeet beugrásnak!
        </button> -->
      </div>
    </div>

    <div v-if="training.description" class="bg-white rounded shadow overflow-hidden max-w mt-8">
      <div class="p-8">
        <h2 class="font-bold text-xl mb-4 border-b-2">Leírás</h2>
        <div class="leading-6" v-html="training.description"></div>
      </div>
    </div>

    <div v-if="training.registered" class="bg-white rounded shadow overflow-hidden max-w mt-8">
      <div class="p-8">
        <h2 class="font-bold text-xl mb-4 border-b-2">Edzés naplóm</h2>
        <form @submit.prevent="update">
          <textarea-input v-model="form.comment" :error="form.errors.comment" class="w-full pb-8" />

          <div>
            <loading-button :loading="form.processing" class="btn-indigo ml-auto" type="submit">Napló mentése</loading-button>
          </div>
        </form>
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
import TextareaInput from "../../Shared/TextareaInput.vue";
import LoadingButton from "../../Shared/LoadingButton.vue";

export default {
  metaInfo() {
    return { title: this.form.name }
  },
  components: {
    LoadingButton,
    TextareaInput,
    Icon,
  },
  layout: Layout,
  props: {
    training: Object,
  },
  remember: 'form',
  data() {
    return {
      form: this.$inertia.form({
        comment: this.training.comment,
      }),
    }
  },
  methods: {
    update() {
      this.form.put(this.route('trainings.comment', this.training.id))
    },
    attend(training_id) {
      this.$inertia.post(this.route('trainings.attend', training_id), null)
    },
    withdraw(training_id) {
      this.$inertia.delete(this.route('trainings.withdraw', training_id), null)
    },
    typeForHumans(type) {
      const types = {
        easy: 'Felzárkóztató edzés',
        running: 'Futó edzés',
        hard: 'Haladó edzés',
        other: 'Egyéb edzés',
      }

      return types[type]
    },
  },
}
</script>
