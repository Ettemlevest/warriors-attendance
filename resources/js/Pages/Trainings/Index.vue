<template>
  <layout title="Trainings">
    <h1 class="mb-8 font-bold text-3xl">Edzések</h1>
    <div class="mb-6 flex justify-between items-center">
      <inertia-link class="btn-indigo" :href="route('trainings.create')">
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
        <tr v-for="training in trainings" :key="training.id" class="hover:bg-grey-lightest focus-within:bg-grey-lightest">
          <td class="border-t">
            <inertia-link class="px-6 py-4 block items-center focus:text-indigo" :href="route('trainings.edit', training.id)">
              <div class="leading-normal text-black text-lg">{{ training.name }}</div>
              <div class="text-grey-darker text-sm italic flex items-center"><icon name="location" class="flex w-5 h-5 fill-grey mr-2" /> {{ training.place }}</div>
            </inertia-link>
          </td>
          <td class="border-t">
            <inertia-link class="px-6 py-4 block items-center" :href="route('trainings.edit', training.id)" tabindex="-1">
              <div class="leading-normal">{{ training.start_at }}</div>
              <div class="text-sm italic text-grey-darker">{{ training.diff }}</div>
            </inertia-link>
          </td>
          <td class="border-t">
            <inertia-link class="px-6 py-4 flex items-center" :href="route('trainings.edit', training.id)" tabindex="-1">
              {{ training.length }} perc
            </inertia-link>
          </td>
          <td class="border-t">
            <inertia-link class="px-6 py-4 flex items-center" :href="route('trainings.edit', training.id)" tabindex="-1">
              {{ training.attendees }} / {{ training.max_attendees }}
            </inertia-link>
          </td>
          <td class="border-t w-px">
            <inertia-link class="px-4 flex items-center" :href="route('trainings.edit', training.id)" tabindex="-1">
              <icon name="cheveron-right" class="block w-6 h-6 fill-grey" />
            </inertia-link>
          </td>
        </tr>
        <tr v-if="trainings.length === 0">
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
    trainings: Array,
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
