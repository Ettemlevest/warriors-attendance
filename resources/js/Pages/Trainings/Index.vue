<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">Edzések</h1>
    <div class="mb-6 flex justify-between items-center">
      <search-filter v-model="form.search" class="w-full max-w-md mr-4" @reset="reset">
        <label class="block text-gray-700">Kezdés:</label>
        <select v-model="form.start_at" class="mt-1 w-full form-select">
          <option :value="null" />
          <option value="next_seven_days">Következő 7 napban</option>
          <option value="prev_seven_days">Előző 7 napban</option>
          <option value="this_year">Idén</option>
          <option value="future">Jövőben</option>
          <option value="past">Múltban</option>
        </select>
        <label class="mt-4 block text-gray-700">Jelentkezés:</label>
        <select v-model="form.attendance" class="mt-1 w-full form-select">
          <option :value="null" />
          <option value="attended">Jelentkeztem</option>
          <option value="not_attended">Még nem jelentkeztem</option>
        </select>
        <label class="mt-4 block text-gray-700">Típus:</label>
        <select v-model="form.type" class="mt-1 w-full form-select">
          <option :value="null" />
          <option value="easy">Felzárkóztató</option>
          <option value="running">Futó</option>
          <option value="hard">Haladó</option>
          <option value="other">Egyéb</option>
        </select>
      </search-filter>
      <inertia-link v-if="$page.props.auth.user.owner" class="btn-indigo" :href="route('trainings.create')">
        <span>Létrehozás</span>
      </inertia-link>
    </div>
    <div class="bg-white rounded shadow overflow-x-auto scrolling-touch">
      <table class="w-full whitespace-no-wrap">
        <tr class="text-left font-bold">
          <th class="px-6 pt-6 pb-4">Edzés</th>
          <th class="px-6 pt-6 pb-4">Kezdés</th>
          <th class="px-6 pt-6 pb-4">Időtartam</th>
          <th class="px-6 pt-6 pb-4" colspan="2">Létszám</th>
        </tr>
        <tr v-for="training in trainings.data" :key="training.id" class="hover:bg-gray-100 focus-within:bg-gray-100">
          <td class="border-t truncate" :class="{ 'italic': limitExceeded(training.attendees, training.max_attendees), 'text-red-600': limitExceeded(training.attendees, training.max_attendees) }">
            <inertia-link class="px-6 py-4 block items-center focus:text-indigo" :href="route($page.props.auth.user.owner ? 'trainings.edit' : 'trainings.view', training.id)">
              <div class="leading-normal text-lg flex items-center">
                <icon :name="training.type" class="h-6 w-6 fill-current mr-2" />
                {{ training.name }}
              </div>
              <div class="mt-1 ml-3 text-gray-600 text-sm italic flex items-center"><icon name="location" class="flex w-4 h-4 fill-current text-gray-500 mr-1" /> {{ training.place }}</div>
            </inertia-link>
          </td>
          <td class="border-t truncate" :class="{ 'italic': limitExceeded(training.attendees, training.max_attendees), 'text-red-600': limitExceeded(training.attendees, training.max_attendees) }">
            <inertia-link class="px-6 py-4 block items-center" :href="route($page.props.auth.user.owner ? 'trainings.edit' : 'trainings.view', training.id)" tabindex="-1">
              <div class="leading-normal">{{ training.start_at }}</div>
              <div class="text-sm italic text-gray-600">{{ training.diff }}</div>
            </inertia-link>
          </td>
          <td class="border-t truncate" :class="{ 'italic': limitExceeded(training.attendees, training.max_attendees), 'text-red-600': limitExceeded(training.attendees, training.max_attendees) }">
            <inertia-link class="px-6 py-4 flex items-center" :href="route($page.props.auth.user.owner ? 'trainings.edit' : 'trainings.view', training.id)" tabindex="-1">
              {{ training.length }} perc
            </inertia-link>
          </td>
          <td class="border-t truncate" :class="{ 'italic': limitExceeded(training.attendees, training.max_attendees), 'text-red-600': limitExceeded(training.attendees, training.max_attendees) }">
            <inertia-link class="px-6 py-4 flex items-center" :href="route($page.props.auth.user.owner ? 'trainings.edit' : 'trainings.view', training.id)" tabindex="-1">
              {{ training.attendees }} / {{ training.max_attendees || '&infin;' }}
            </inertia-link>
          </td>
          <td class="border-t w-px" :class="{ 'italic': limitExceeded(training.attendees, training.max_attendees), 'text-red-600': limitExceeded(training.attendees, training.max_attendees) }">
            <inertia-link class="px-4 flex items-center" :href="route('trainings.view', training.id)" tabindex="-1">
              <icon name="cheveron-right" class="block w-6 h-6 fill-gray" />
            </inertia-link>
          </td>
        </tr>
        <tr v-if="trainings.data.length === 0">
          <td class="border-t px-6 py-4 text-center" colspan="4">Nincs megjeleníthető adat</td>
        </tr>
      </table>
    </div>
    <div class="flex flex-wrap justify-center mt-4 px-4 py-3 bg-white rounded italic shadow">
      Összesen: {{ trainings.total }}
    </div>
    <pagination class="mt-4" :links="trainings.links" />
  </div>
</template>

<script>
import Icon from '@/Shared/Icon'
import pickBy from 'lodash/pickBy'
import Layout from '@/Shared/Layout'
import throttle from 'lodash/throttle'
import mapValues from 'lodash/mapValues'
import Pagination from '@/Shared/Pagination'
import SearchFilter from '@/Shared/SearchFilter'

export default {
  metaInfo: { title: 'Edzések' },
  components: {
    Icon,
    Pagination,
    SearchFilter,
  },
  layout: Layout,
  props: {
    trainings: Object,
    filters: Object,
  },
  data() {
    return {
      form: {
        search: this.filters.search,
        start_at: this.filters.start_at,
        attendance: this.filters.attendance,
        type: this.filters.type,
      },
    }
  },
  watch: {
    form: {
      handler: throttle(function() {
        let query = pickBy(this.form)
        this.$inertia.replace(this.route('trainings', Object.keys(query).length ? query : { remember: 'forget' }))
      }, 150),
      deep: true,
    },
  },
  methods: {
    reset() {
      this.form = mapValues(this.form, () => null)
    },
    limitExceeded: (attendees, max_attendees) => max_attendees && attendees > max_attendees
  },
}
</script>
