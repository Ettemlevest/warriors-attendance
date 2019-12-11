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
            <inertia-link :href="route('albums.view', album.id)">
              <picture class="flex justify-center bg-gray-600 border-b">
                <img class="" :src="album.cover_photo_url" :alt="album.name">
              </picture>
            </inertia-link>
            <div class="p-4">
              <inertia-link :href="route('albums.view', album.id)">
                <h3 class="text-lg font-bold">{{ album.name }}</h3>
              </inertia-link>
              <p class="mt-2">
                <icon name="location" class="w-3 h-3 fill-gray mr-2" />
                <span class="text-sm text-gray-600">{{ album.place }}</span>
              </p>
              <p class="mt-2">
                <icon name="dashboard" class="w-3 h-3 fill-gray mr-2" />
                <time v-if="album.date_from === album.date_to" class="mt-2 mb-2 text-sm text-gray-600" :datetime="album.date_from">{{ album.date_from }}</time>
                <time v-else class="mt-2 mb-2 text-sm text-gray-600" :datetime="album.date_from">{{ album.date_from+' - '+album.date_to }}</time>
              </p>
              <p class="mt-2">{{ album.description }}</p>
              <div class="flex justify-between items-center">
                <inertia-link v-if="$page.auth.user.owner" class="btn-indigo w-full text-center mt-4" :href="route('albums.edit', album.id)">
                  <span>Szerkesztés</span>
                </inertia-link>
              </div>
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
