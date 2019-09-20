<template>
  <layout title="Trainings">
    <h1 class="mb-8 font-bold text-3xl">Edzések</h1>
    <div class="mb-6 flex justify-between items-center">
      <search-filter v-model="form.search" class="w-full max-w-sm mr-4" @reset="reset">
        <label class="block text-grey-darkest">Kezdés:</label>
        <select v-model="form.start_at" class="mt-1 w-full form-select">
          <option :value="null" />
          <option value="next_seven_days">Következő 7 napban</option>
          <option value="prev_seven_days">Előző 7 napban</option>
          <option value="future">Jövőben</option>
          <option value="past">Múltban</option>
        </select>
      </search-filter>
      <inertia-link v-if="$page.auth.user.owner" class="btn-indigo" :href="route('trainings.create')">
        <span>Létrehozás</span>
      </inertia-link>
    </div>
    <div class="bg-white rounded shadow overflow-x-auto">
      <table class="w-full whitespace-no-wrap">
        <tr class="text-left font-bold">
          <th class="px-6 pt-6 pb-4">Edzés</th>
          <th class="px-6 pt-6 pb-4">Kezdés</th>
          <th class="px-6 pt-6 pb-4">Időtartam</th>
          <th class="px-6 pt-6 pb-4" colspan="2">Létszám</th>
        </tr>
        <tr v-for="training in trainings.data" :key="training.id" class="hover:bg-grey-lightest focus-within:bg-grey-lightest">
          <td class="border-t">
            <inertia-link class="px-6 py-4 block items-center focus:text-indigo" :href="route($page.auth.user.owner ? 'trainings.edit' : 'trainings.view', training.id)">
              <div class="leading-normal text-black text-lg">{{ training.name }}</div>
              <div class="text-grey-darker text-sm italic flex items-center"><icon name="location" class="flex w-5 h-5 fill-grey mr-2" /> {{ training.place }}</div>
            </inertia-link>
          </td>
          <td class="border-t">
            <inertia-link class="px-6 py-4 block items-center" :href="route($page.auth.user.owner ? 'trainings.edit' : 'trainings.view', training.id)" tabindex="-1">
              <div class="leading-normal">{{ training.start_at }}</div>
              <div class="text-sm italic text-grey-darker">{{ training.diff }}</div>
            </inertia-link>
          </td>
          <td class="border-t">
            <inertia-link class="px-6 py-4 flex items-center" :href="route($page.auth.user.owner ? 'trainings.edit' : 'trainings.view', training.id)" tabindex="-1">
              {{ training.length }} perc
            </inertia-link>
          </td>
          <td class="border-t">
            <inertia-link class="px-6 py-4 flex items-center" :href="route($page.auth.user.owner ? 'trainings.edit' : 'trainings.view', training.id)" tabindex="-1">
              {{ training.attendees }} / {{ training.max_attendees }}
            </inertia-link>
          </td>
          <td class="border-t w-px">
            <inertia-link class="px-4 flex items-center" :href="route($page.auth.user.owner ? 'trainings.edit' : 'trainings.view', training.id)" tabindex="-1">
              <icon name="cheveron-right" class="block w-6 h-6 fill-grey" />
            </inertia-link>
          </td>
        </tr>
        <tr v-if="trainings.data.length === 0">
          <td class="border-t px-6 py-4" colspan="4">Nincs megjeleníthető adat</td>
        </tr>
      </table>
    </div>
    <pagination :links="trainings.links" />
  </layout>
</template>

<script>
import _ from 'lodash'
import Icon from '@/Shared/Icon'
import Layout from '@/Shared/Layout'
import Pagination from '@/Shared/Pagination'
import SearchFilter from '@/Shared/SearchFilter'

export default {
  components: {
    Icon,
    Layout,
    Pagination,
    SearchFilter,
  },
  props: {
    trainings: Object,
    filters: Object,
  },
  data() {
    return {
      form: {
        search: this.filters.search,
        start_at: this.filters.start_at,
        role: this.filters.role,
      },
    }
  },
  watch: {
    form: {
      handler: _.throttle(function() {
        let query = _.pickBy(this.form)
        this.$inertia.replace(this.route('trainings', Object.keys(query).length ? query : { remember: 'forget' }))
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
