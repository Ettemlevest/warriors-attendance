<template>
  <layout>
    <h1 class="mb-8 font-bold text-3xl">Üzenetek</h1>
    <div class="mb-6 flex justify-between items-center">
      <search-filter v-model="form.search" class="w-full max-w-xl mr-4" @reset="reset">
      </search-filter>
      <inertia-link v-if="$page.auth.user.owner" class="btn-indigo" :href="route('messages.create')">
        <span>Létrehozás</span>
      </inertia-link>
    </div>
    <div class="bg-white rounded shadow overflow-x-auto">
      <table class="w-full whitespace-no-wrap">
        <tr class="text-left font-bold">
          <th class="px-6 pt-6 pb-4">Cím</th>
          <th class="px-6 pt-6 pb-4">Üzenet</th>
          <th class="px-6 pt-6 pb-4">Dátumtól</th>
          <th class="px-6 pt-6 pb-4" colspan="2">Dátumig</th>
        </tr>
        <tr v-for="message in messages.data" :key="message.id" class="hover:bg-gray-100 focus-within:bg-gray-100">
          <td class="border-t">
            <inertia-link class="px-6 py-4 flex items-center focus:text-indigo" :href="route('messages.edit', message.id)">
              {{ message.title }}
            </inertia-link>
          </td>
          <td class="border-t">
            <inertia-link class="px-6 py-4 flex items-center focus:text-indigo" :href="route('messages.edit', message.id)">
              {{ message.body }}
            </inertia-link>
          </td>
          <td class="border-t">
            <inertia-link class="px-6 py-4 flex items-center" :href="route('messages.edit', message.id)" tabindex="-1">
              <span v-if="message.showed_from">{{ message.showed_from }}</span>
              <span v-else>--</span>
            </inertia-link>
          </td>
          <td class="border-t w-px">
            <inertia-link class="px-4 text-center" :href="route('messages.edit', message.id)" tabindex="-1">
              <span v-if="message.showed_to">{{ message.showed_to }}</span>
              <span v-else>--</span>
            </inertia-link>
          </td>
          <td class="border-t w-px">
            <inertia-link class="px-4 flex items-center" :href="route('messages.edit', message.id)" tabindex="-1">
              <icon name="cheveron-right" class="block w-6 h-6 fill-gray" />
            </inertia-link>
          </td>
        </tr>
        <tr v-if="messages.data.length === 0">
          <td class="border-t px-6 py-4 text-center" colspan="4">Nincs megjeleníthető adat</td>
        </tr>
      </table>
    </div>
    <pagination :links="messages.links" />
  </layout>
</template>

<script>
import _ from 'lodash'
import Icon from '@/Shared/Icon'
import Layout from '@/Shared/Layout'
import Pagination from '@/Shared/Pagination'
import SearchFilter from '@/Shared/SearchFilter'

export default {
  metaInfo: { title: 'Üzenetek' },
  components: {
    Icon,
    Pagination,
    SearchFilter,
    Layout,
  },
  props: {
    messages: Object,
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
        this.$inertia.replace(this.route('messages', Object.keys(query).length ? query : { remember: 'forget' }))
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
