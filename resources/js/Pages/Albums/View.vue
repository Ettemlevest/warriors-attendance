<template>
  <div>
    <div class="mb-8 flex justify-start max-w">
      <h1 class="font-bold text-3xl">
        <inertia-link class="text-gray-500 hover:text-gray-800" :href="route('albums')">Képek</inertia-link>
        <span class="text-gray-400 font-medium">/</span>
        {{ album.name }}
      </h1>
    </div>

    <silent-box :gallery="album.photos">
      <template v-slot:silentbox-item="{ silentboxItem }">
        <img :src="silentboxItem.thumbnail" class="bg-white rounded-md shadow m-2 p-2" />
      </template>
    </silent-box>
  </div>
</template>

<script>
import Layout from '@/Shared/Layout'
import Icon from '@/Shared/Icon'

export default {
  metaInfo() {
    return { title: this.album.name }
  },
  components: {
    Icon,
  },
  layout: Layout,
  props: {
    album: Object,
  },
  data() {
    return {}
  },
  methods: {
  },
}
</script>
