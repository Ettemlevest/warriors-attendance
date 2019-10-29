<template>
  <layout>
    <h1 class="mb-8 font-bold text-3xl">Képek</h1>
    <div class="mb-6 flex justify-between items-center">
      <inertia-link v-if="$page.auth.user.owner" class="btn-indigo" :href="route('albums.create')">
        <span>Létrehozás</span>
      </inertia-link>
    </div>
    <div class="container mx-auto p-8">
      <div class="flex flex-row flex-wrap -mx-2">
        <div v-for="album in albums" :key="album.id" class="w-full sm:w-1/2 md:w-1/2 lg:w-1/4 mb-4 px-2">
          <div class="relative bg-white rounded border">
            <inertia-link :href="route($page.auth.user.owner ? 'albums.edit' : 'albums.view', album.id)">
              <picture class="block bg-grey-dark border-b">
                <img class="block" src="https://via.placeholder.com/800x600/EDF2F7/E2E8F0/&amp;text=Album" :alt="album.name">
              </picture>
            </inertia-link>
            <div class="p-4">
              <inertia-link :href="route($page.auth.user.owner ? 'albums.edit' : 'albums.view', album.id)">
                <h3 class="text-lg font-bold">{{ album.name }}</h3>
              </inertia-link>
              <p class="mt-2">
                <icon name="location" class="w-3 h-3 fill-grey mr-2" />
                <span class="text-sm text-grey-dark">{{ album.place }}</span>
              </p>
              <p class="mt-2">
                <icon name="dashboard" class="w-3 h-3 fill-grey mr-2" />
                <time class="mt-2 mb-2 text-sm text-grey-dark" :datetime="album.date_from">{{ album.date_from }}</time>
              </p>
              <p class="mt-2">{{ album.description }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </layout>
</template>

<script>
import _ from 'lodash'
import Icon from '@/Shared/Icon'
import Layout from '@/Shared/Layout'
import Pagination from '@/Shared/Pagination'
import SearchFilter from '@/Shared/SearchFilter'

export default {
  metaInfo: { title: 'Képek' },
  components: {
    Icon,
    Layout,
    Pagination,
    SearchFilter,
  },
  props: {
    albums: Array,
  },
  data() {
    return {}
  },
  methods: {},
}
</script>
