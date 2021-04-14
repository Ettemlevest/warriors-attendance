<template>
  <div>
    <h1 class="mb-2 font-bold text-3xl">Üdvözlünk!</h1>
    <p class="mb-8 leading-normal">Szia Warrior! Itt jelentkezhetsz a következő edzésekre.</p>

    <div v-if="this.message" class="mb-8 flex bg-gray-500 rounded-md max-w">
      <div class="flex items-center p-4">
        <icon name="notifications" class="hidden md:block mr-4 flex-shrink-0 w-8 h-8 fill-white" />
        <div class="text-white text-sm">
          <p class="font-bold text-xl mb-2">{{ this.message.title }}</p>
          {{ this.message.body }}
        </div>
      </div>
    </div>

    <h2 class="mb-4 font-bold text-3xl">Közelgő edzések</h2>
    <div class="flex items-center flex-col lg:flex-row lg:justify-around">
      <!-- <div class="lg:mr-4">
        <v-calendar
            :locale="{ id: 'hu', firstDayOfWeek: 2, masks: { title: 'YYYY MMM' } }"
          />
      </div> -->
      <div class="mt-4 lg:m-0 w-3/4">
        <div v-for="training in trainings" :key="training.id" class="m-4 p-6 rounded-md border-2 w-full"
            :class="{
              'bg-green-100': training.type === 'easy',
              'border-green-300': training.type === 'easy',
              'bg-orange-100': training.type === 'running',
              'border-orange-300': training.type === 'running',
              'bg-red-100': training.type === 'hard',
              'border-red-300': training.type === 'hard',
              'bg-indigo-100': training.type === 'other',
              'border-indigo-300': training.type === 'other'
            }"
        >
          <h2 class="text-xl font-bold">
            <inertia-link class="flex justify-center tracking-wider" :href="route($page.props.auth.user.owner ? 'trainings.edit' : 'trainings.view', training.id)">
              <icon :name="training.type" class="h-8 w-8 mr-2 fill-current" />
              {{ training.name }}
            </inertia-link>
          </h2>
          <div class="text-gray-600 text-sm italic mb-4 text-center">
            {{ typeForHumans(training.type) }} - {{ training.diff }}
          </div>
          <div class="flex flex-col lg:flex-row">
            <div class="mb-3 flex items-center flex-1">
              <icon name="location" class="flex w-4 h-4 fill-current text-gray-500 mr-2" />
              <div class="text-purple-600 italic">{{ training.place }}</div>
            </div>
            <div class="mb-3 flex items-center flex-1">
              <icon name="dashboard" class="flex w-4 h-4 fill-current text-gray-500 mr-2" />
              <div class="italic">{{ training.formatted_start_at }}</div>
            </div>
            <div class="mb-3 flex items-center flex-1">
              <icon name="users" class="flex w-4 h-4 fill-current text-gray-500 mr-2" />
              <div class="italic">{{ training.attendees.length }} / {{ training.max_attendees || '&infin;' }}</div>
            </div>
          </div>

          <!-- <div>{{ training }}</div> -->

          <button
              v-if="canAttend(training)"
              class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold p-2 rounded w-full tracking-wider"
              @click="attend(training.id)"
          >
            Jelentkezem
          </button>

          <button
              v-if="canWithdraw(training)"
              class="bg-red-600 hover:bg-red-700 text-white font-bold p-2 rounded w-full tracking-wider"
              @click="withdraw(training.id)"
          >
          Lemondás
        </button>

        <!-- !canAttend(training) && reachedMaxAttendees(training) -->
        <div
            v-if="!canAttend(training) && reachedMaxAttendees(training) && !training.registered"
            class="bg-orange-500 text-white tracking-wider p-2 rounded-md italic w-full text-center"
        >
          Megtelt, már nem lehet jelentkezni!
        </div>

        <div
            v-if="training.can_attend_from > new Date().toISOString()"
            class="bg-orange-500 text-white tracking-wider p-2 rounded-md italic w-full text-center"
        >
          Még nem lehet jelentkezni az edzésre! Gyere vissza 7 nappal előtte.
        </div>

        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Icon from '@/Shared/Icon'
import Layout from '@/Shared/Layout'

export default {
  metaInfo: { title: 'Kezdőlap' },
  components: {
    Icon,
  },
  layout: Layout,
  props: {
    message: Object,
    trainings: Array,
  },
  methods: {
    attend(training_id) {
      this.$inertia.post(this.route('trainings.attend', training_id), {}, {
        preserveScroll: true,
      })
    },
    withdraw(training_id) {
      this.$inertia.delete(this.route('trainings.withdraw', training_id), {}, {
        preserveScroll: true,
      })
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
    canAttend(training) {
      const now = new Date().toISOString()

      return training.start_at > now
          && ! training.registered
          &&   training.can_attend_from < now
          && ! this.reachedMaxAttendees(training)
    },
    canWithdraw(training) {
      const now = new Date().toISOString()

      return training.registered && training.start_at > now
    },
    reachedMaxAttendees(training) {
      return training.max_attendees != 0
          && (training.attendees.length >= training.max_attendees)
    },
  }
}
</script>
