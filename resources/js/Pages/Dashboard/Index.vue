<template>
  <layout title="Dashboard">
    <h1 class="mb-8 font-bold text-3xl">Kezdőlap</h1>
    <p class="mb-12 leading-normal">Szia Warrior! Itt jelentkezhetsz a következő edzésekre.</p>

    <h2 class="mb-8 font-bold text-xl">Közelgő edzések</h2>
    <p v-if="trainings.length === 0" class="mb-12 leading-normal">Egyelőre nincs felvéve új edzés. Nézz vissza később 😊</p>
    <div v-for="training in trainings" :key="training.id" class="overflow-x-auto block lg:flex-wrap xl:flex-wrap">
      <div class="text-center sm:w-auto md:w-auto lg:flex-1 xl:flex-1 m-4 p-4 bg-white rounded-lg shadow-md">
        <h2 class="text-xl mb-2 ml-8 mr-8">
          <inertia-link class="text-indigo-dark" :href="route($page.auth.user.owner ? 'trainings.edit' : 'trainings.view', training.id)">{{ training.name }}</inertia-link>
        </h2>
        <div class="text-grey-darker text-sm italic mb-6">{{ training.diff }}</div>
        <div class="sm:block md:block lg:flex xl:flex lg:items-center">
          <div class="mb-4 flex lg:flex-1 xl:flex-1">
            <icon name="location" class="flex w-5 h-5 fill-grey mr-2" />
            <div class="text-purple text-lg italic">{{ training.place }}</div>
          </div>
          <div class="mb-4 text-grey-darker items-center flex lg:flex-1 xl:flex-1">
            <icon name="dashboard" class="flex w-5 h-5 fill-grey mr-2" />
            <div class="text-lg italic">{{ training.start_at }}</div>
          </div>
          <div class="mb-4 text-grey-darker items-center flex lg:flex-1 xl:flex-1">
            <icon name="users" class="flex w-5 h-5 fill-grey mr-2" />
            <div class="text-lg italic">{{ training.attendees }} / {{ training.max_attendees }}</div>
          </div>
        </div>
        <button v-if="!training.registered && training.attendees < training.max_attendees" class="bg-blue hover:bg-blue-dark text-white font-bold py-2 px-4 rounded">
          Jelentkezem
        </button>
        <div v-if="!training.registered && training.attendees >= training.max_attendees" class="bg-red text-white font-bold py-2 px-4 rounded">Megtelt, már nem lehet jelentkezni!</div>
        <button v-if="training.registered" class="bg-red hover:bg-red-dark text-white font-bold py-2 px-4 rounded">
          Lemondás
        </button>
      </div>
    </div>
  </layout>
</template>

<script>
import Layout from '@/Shared/Layout'
import Icon from '@/Shared/Icon'

export default {
  components: {
    Icon,
    Layout,
  },
  props: {
    trainings: Array,
  },
}
</script>
