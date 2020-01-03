<template>
  <layout>
    <h1 class="mb-8 font-bold text-3xl">Warriorok</h1>
    <div class="mb-6 flex justify-between items-center">
      <search-filter v-model="form.search" class="w-full max-w-xl mr-4" @reset="reset">
        <label class="block text-gray-800">Jogosultság:</label>
        <select v-model="form.role" class="mt-1 w-full form-select">
          <option :value="null" />
          <option value="user">Warrior</option>
          <option value="owner">Edző</option>
        </select>
        <label class="mt-4 block text-gray-800">Töröltek:</label>
        <select v-model="form.trashed" class="mt-1 w-full form-select">
          <option :value="null" />
          <option value="with">Töröltekkel együtt</option>
          <option value="only">Csak töröltek</option>
        </select>
      </search-filter>
      <inertia-link v-if="$page.auth.user.owner" class="btn-indigo" :href="route('users.create')">
        <span>Létrehozás</span>
      </inertia-link>
    </div>
    <div class="bg-white rounded shadow overflow-x-auto scrolling-touch">
      <table class="w-full whitespace-no-wrap">
        <tr class="text-left font-bold">
          <th class="px-6 pt-6 pb-4">Név</th>
          <th class="px-6 pt-6 pb-4">E-mail</th>
          <th class="px-6 pt-6 pb-4">Életkor</th>
          <th class="px-6 pt-6 pb-4" colspan="2">Jogosultság</th>
        </tr>
        <tr v-for="user in users.data" :key="user.id" class="hover:bg-gray-100 focus-within:bg-gray-100">
          <td class="border-t">
            <inertia-link class="px-6 py-4 flex items-center focus:text-indigo" :href="route('users.edit', user.id)">
              <img v-if="user.photo" class="block w-8 h-8 rounded-full mr-2 -my-2" :src="user.photo">
              {{ user.name }}
              <icon v-if="user.deleted_at" name="trash" class="flex-shrink-0 w-3 h-3 fill-current text-gray-500 ml-2" />
            </inertia-link>
          </td>
          <td class="border-t">
            <inertia-link class="px-6 py-4 flex items-center" :href="route('users.edit', user.id)" tabindex="-1">
              {{ user.email }}
            </inertia-link>
          </td>
          <td class="border-t w-px">
            <inertia-link class="px-4 text-center" :href="route('users.edit', user.id)" tabindex="-1">
              <span v-if="user.age">{{ user.age }} év</span>
              <span v-else>--</span>
            </inertia-link>
          </td>
          <td class="border-t">
            <inertia-link class="px-6 py-4 flex items-center" :href="route('users.edit', user.id)" tabindex="-1">
              {{ user.owner ? 'Edző' : 'Warrior' }}
            </inertia-link>
          </td>
          <td class="border-t w-px">
            <inertia-link class="px-4 flex items-center" :href="route('users.edit', user.id)" tabindex="-1">
              <icon name="cheveron-right" class="block w-6 h-6 fill-gray" />
            </inertia-link>
          </td>
        </tr>
        <tr v-if="users.data.length === 0">
          <td class="border-t px-6 py-4 text-center" colspan="4">Nincs megjeleníthető adat</td>
        </tr>
      </table>
    </div>
    <pagination :links="users.links" />
  </layout>
</template>

<script>
import _ from 'lodash'
import Icon from '@/Shared/Icon'
import Layout from '@/Shared/Layout'
import Pagination from '@/Shared/Pagination'
import SearchFilter from '@/Shared/SearchFilter'

export default {
  metaInfo: { title: 'Warriorok' },
  components: {
    Icon,
    Pagination,
    SearchFilter,
    Layout,
  },
  props: {
    users: Object,
    filters: Object,
  },
  data() {
    return {
      form: {
        search: this.filters.search,
        role: this.filters.role,
        trashed: this.filters.trashed,
      },
    }
  },
  watch: {
    form: {
      handler: _.throttle(function() {
        let query = _.pickBy(this.form)
        this.$inertia.replace(this.route('users', Object.keys(query).length ? query : { remember: 'forget' }))
      }, 150),
      deep: true,
    },
  },
  methods: {
    reset() {
      this.form = _.mapValues(this.form, () => null)
    },
  },
}
</script>
