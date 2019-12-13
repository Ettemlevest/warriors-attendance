<template>
  <layout>
    <div class="mb-8 flex justify-start max-w">
      <h1 class="font-bold text-3xl">
        <inertia-link class="text-indigo-500 hover:text-indigo-600" :href="route('albums')">Képek</inertia-link>
        <span class="text-indigo-500 font-medium">/</span>
        {{ album.name }}
      </h1>
    </div>
    <div class="container mx-auto p-8">
      <div class="flex flex-row flex-wrap -mx-2">
        <div v-for="photo in album.photos" :key="photo.id" class="w-full md:w-1/2 md:h-auto mb-4 px-2 sm:w-1/2 md:w-1/2 lg:w-1/4 mb-4 px-2 mx-4">
          <a :href="photo.original_path">
            <img :src="photo.path" class="bg-white rounded shadow p-2" />
          </a>
        </div>
      </div>
    </div>
  </layout>
</template>

<script>
import Layout from '@/Shared/Layout'
import Icon from '@/Shared/Icon'

export default {
  metaInfo() {
    return { title: this.album.name }
  },
  components: {
    Layout,
    Icon,
  },
  props: {
    album: Object,
  },
  // remember: 'form',
  data() {
    return {}
  },
  methods: {
  },
}
</script>
