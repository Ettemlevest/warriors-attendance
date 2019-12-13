<template>
  <div>
    <div class="mb-4">
      <inertia-link class="flex items-center group py-3" :href="route('dashboard')">
        <icon name="dashboard" class="w-4 h-4 mr-2 fill-current" :class="isUrl('') ? 'text-white' : 'text-indigo-300 group-hover:text-white'" />
        <div :class="isUrl('') ? 'text-white' : 'text-indigo-300 group-hover:text-white'">Kezdőlap</div>
      </inertia-link>
    </div>
    <div class="mb-4">
      <inertia-link class="flex items-center group py-3" :href="route('trainings')">
        <icon name="book" class="w-4 h-4 mr-2 fill-current" :class="isUrl('trainings') ? 'text-white' : 'text-indigo-300 group-hover:text-white'" />
        <div :class="isUrl('trainings') ? 'text-white' : 'text-indigo-300 group-hover:text-white'">Edzések</div>
      </inertia-link>
    </div>
    <div class="mb-4">
      <inertia-link class="flex items-center group py-3" :href="route('albums')">
        <icon name="office" class="w-4 h-4 mr-2 fill-current" :class="isUrl('albums') ? 'text-white' : 'text-indigo-300 group-hover:text-white'" />
        <div :class="isUrl('albums') ? 'text-white' : 'text-indigo-300 group-hover:text-white'">Képek</div>
      </inertia-link>
    </div>
    <div class="mb-4" v-if="$page.auth.user.owner">
      <inertia-link class="flex items-center group py-3" :href="route('messages')">
        <icon name="chat-bubble-dots" class="w-4 h-4 mr-2 fill-current" :class="isUrl('messages') ? 'text-white' : 'text-indigo-300 group-hover:text-white'" />
        <div :class="isUrl('messages') ? 'text-white' : 'text-indigo-300 group-hover:text-white'">Üzenetek</div>
      </inertia-link>
    </div>
    <div class="mb-4">
      <inertia-link class="flex items-center group py-3 ml-6" :href="route('logout')" method="post">
        <div class="text-indigo-300 group-hover:text-white">Kilépés</div>
      </inertia-link>
    </div>
  </div>
</template>

<script>
import Icon from '@/Shared/Icon'

export default {
  components: {
    Icon,
  },
  methods: {
    isUrl(...urls) {
      if (urls[0] === '') {
        return location.pathname.substr(1) === ''
      }

      return urls.filter(url => location.pathname.substr(1).startsWith(url)).length
    },
  },
}
</script>
