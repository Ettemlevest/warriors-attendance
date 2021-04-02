<template>
  <div>
    <div class="mb-4">
      <inertia-link class="flex items-center group py-3" :href="route('dashboard')">
        <icon name="dashboard" class="w-4 h-4 mr-2" :class="isUrl('') ? 'fill-black' : 'fill-gray-700 group-hover:fill-black'" />
        <div :class="isUrl('') ? 'text-black font-bold' : 'text-gray-700 group-hover:text-black'">Kezdőlap</div>
      </inertia-link>
    </div>
    <div class="mb-4">
      <inertia-link class="flex items-center group py-3" :href="route('trainings')">
        <icon name="book" class="w-4 h-4 mr-2" :class="isUrl('trainings') ? 'fill-black' : 'fill-gray-700 group-hover:fill-black'" />
        <div :class="isUrl('trainings') ? 'text-black font-bold' : 'text-gray-700 group-hover:text-black'">Edzések</div>
      </inertia-link>
    </div>
    <div class="mb-4">
      <inertia-link class="flex items-center group py-3" :href="route('albums')">
        <icon name="office" class="w-4 h-4 mr-2" :class="isUrl('albums') ? 'fill-black' : 'fill-gray-700 group-hover:fill-black'" />
        <div :class="isUrl('albums') ? 'text-black font-bold' : 'text-gray-700 group-hover:text-black'">Képek</div>
      </inertia-link>
    </div>
    <div class="mb-4">
      <inertia-link class="flex items-center group py-3" :href="route('messages')">
        <icon name="chat-bubble-dots" class="w-4 h-4 mr-2" :class="isUrl('messages') ? 'fill-black' : 'fill-gray-700 group-hover:fill-black'" />
        <div :class="isUrl('messages') ? 'text-black font-bold' : 'text-gray-700 group-hover:text-black'">Üzenetek</div>
      </inertia-link>
    </div>
    <div class="mb-4">
      <inertia-link class="flex items-center group py-3" :href="route('users')">
        <icon name="users" class="w-4 h-4 mr-2" :class="isUrl('users') ? 'fill-black' : 'fill-gray-700 group-hover:fill-black'" />
        <div :class="isUrl('users') ? 'text-black font-bold' : 'text-gray-700 group-hover:text-black'">Warriorok</div>
      </inertia-link>
    </div>
    <div class="mb-4 ml-6">
      <inertia-link class="flex items-center group py-3" :href="route('logout')" method="post" as="button">
        <div :class="isUrl('logout') ? 'text-black font-bold' : 'text-gray-700 group-hover:text-black'">Kilépés</div>
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
      let currentUrl = this.$page.url.substr(1)
      if (urls[0] === '') {
        return currentUrl === ''
      }
      return urls.filter(url => currentUrl.startsWith(url)).length
    },
  },
}
</script>
