<template>
  <layout title="Users">
    <h1 class="mb-8 font-bold text-3xl">Warriorok</h1>
    <div class="mb-6 flex justify-between items-center">
      <search-filter v-model="form.search" class="w-full max-w-sm mr-4" @reset="reset">
        <label class="block text-grey-darkest">Jogosultság:</label>
        <select v-model="form.role" class="mt-1 w-full form-select">
          <option :value="null" />
          <option value="user">Warrior</option>
          <option value="owner">Edző</option>
        </select>
        <label class="mt-4 block text-grey-darkest">Töröltek:</label>
        <select v-model="form.trashed" class="mt-1 w-full form-select">
          <option :value="null" />
          <option value="with">Töröltekkel együtt</option>
          <option value="only">Csak töröltek</option>
        </select>
      </search-filter>
      <inertia-link class="btn-indigo" :href="route('users.create')">
        <span>Létrehozás</span>
        <span class="hidden md:inline">Warrior</span>
      </inertia-link>
    </div>
    <div class="bg-white rounded shadow overflow-x-auto">
      <table class="w-full whitespace-no-wrap">
        <tr class="text-left font-bold">
          <th class="px-6 pt-6 pb-4">Név</th>
          <th class="px-6 pt-6 pb-4">E-mail</th>
          <th class="px-6 pt-6 pb-4" colspan="2">Jogosultság</th>
        </tr>
        <tr v-for="user in users" :key="user.id" class="hover:bg-grey-lightest focus-within:bg-grey-lightest">
          <td class="border-t">
            <inertia-link class="px-6 py-4 flex items-center focus:text-indigo" :href="route('users.edit', user.id)">
              <img v-if="user.photo" class="block w-5 h-5 rounded-full mr-2 -my-2" :src="user.photo">
              {{ user.name }}
              <icon v-if="user.deleted_at" name="trash" class="flex-no-shrink w-3 h-3 fill-grey ml-2" />
            </inertia-link>
          </td>
          <td class="border-t">
            <inertia-link class="px-6 py-4 flex items-center" :href="route('users.edit', user.id)" tabindex="-1">
              {{ user.email }}
            </inertia-link>
          </td>
          <td class="border-t">
            <inertia-link class="px-6 py-4 flex items-center" :href="route('users.edit', user.id)" tabindex="-1">
              {{ user.owner ? 'Edző' : 'Warrior' }}
            </inertia-link>
          </td>
          <td class="border-t w-px">
            <inertia-link class="px-4 flex items-center" :href="route('users.edit', user.id)" tabindex="-1">
              <icon name="cheveron-right" class="block w-6 h-6 fill-grey" />
            </inertia-link>
          </td>
        </tr>
        <tr v-if="users.length === 0">
          <td class="border-t px-6 py-4" colspan="4">Nincs megjeleníthető adat</td>
        </tr>
      </table>
    </div>
  </layout>
</template>

<script>
import _ from 'lodash'
import Icon from '@/Shared/Icon'
import Layout from '@/Shared/Layout'
import SearchFilter from '@/Shared/SearchFilter'

export default {
  components: {
    Icon,
    Layout,
    SearchFilter,
  },
  props: {
    users: Array,
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
