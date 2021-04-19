<template>
  <div>
    <div class="mb-8 flex justify-start max-w">
      <h1 class="font-bold text-3xl">
        <inertia-link class="text-gray-500 hover:text-gray-800" :href="route('trainings')">Edzések</inertia-link>
        <span class="text-gray-400 font-medium">/</span>
        {{ form.name }}
      </h1>
    </div>
    <div class="bg-white rounded-md shadow overflow-hidden max-w">
      <form @submit.prevent="update">
        <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
          <select-input v-model="form.type" :error="form.errors.type" class="pr-6 pb-8 w-full" label="Típus">
            <option value="easy">Felzárkóztató edzés</option>
            <option value="running">Futó edzés</option>
            <option value="hard">Haladó edzés</option>
            <option value="other">Egyéb</option>
          </select-input>

          <text-input v-model="form.name" :error="form.errors.name" class="pr-6 pb-8 w-full lg:w-1/2" label="Edzés neve" />
          <text-input v-model="form.place" :error="form.errors.place" class="pr-6 pb-8 w-full lg:w-1/2" label="Helyszín" />
          <text-input v-model="form.start_at_day" :error="form.errors.start_at_day" class="pr-6 pb-8 w-full lg:w-1/2" label="Kezdés (dátum)" type="date" timezone="Europe/Budapest" />
          <text-input v-model="form.start_at_time" :error="form.errors.start_at_time" class="pr-6 pb-8 w-full lg:w-1/2" label="Kezdés (időpont)" type="time" timezone="Europe/Budapest" />
          <text-input v-model="form.length" :error="form.errors.length" class="pr-6 pb-8 w-full lg:w-1/2" label="Időtartam (perc)" type="number" />
          <text-input v-model="form.max_attendees" :error="form.errors.max_attendees" class="pr-6 pb-8 w-full lg:w-1/2" label="Max. létszám" type="number" />
          <checkbox-input v-model="form.can_attend_more" :error="form.errors.can_attend_more" class="pr-6 pb-8 w-full lg:w-1/2" label="Maximális létszám túlléphető" :checked="form.can_attend_more" />

          <!-- <div class="pr-6 pb-8 w-full">
            <label class="form-label" for="url-helper">Link a jelentkezéshez:</label>
            <input id="url-helper" ref="input" class="form-input italic" type="text" :value="attendURL" readonly="readonly" />
          </div> -->
        </div>
        <div class="px-8 py-4 bg-gray-100 border-t border-gray-300 flex items-center">
          <button v-if="training.start_at > new Date().toISOString()" class="text-red-500 hover:underline tracking-widest" tabindex="-1" type="button" @click="destroy">Törlés</button>
          <loading-button :loading="form.processing" class="btn-indigo ml-auto" type="submit">Edzés mentése</loading-button>
        </div>
      </form>
    </div>
    <div class="bg-white rounded shadow overflow-x-auto scrolling-touch mt-8">
      <table class="w-full whitespace-no-wrap">
        <tr class="text-left font-bold truncate">
          <th class="px-6 pt-6 pb-4 flex justify-between">
            <div>Jelentkezve</div>
            <div class="italic text-gray-500 font-normal">{{ training.attendees.data.length }} fő</div>
          </th>
          <th class="px-6 pt-6 pb-4 text-center">Időpont</th>
          <th class="px-6 pt-6 pb-4 text-center">Részvétel</th>
        </tr>
        <tr v-for="attendee in training.attendees.data" :key="attendee.user_id" class="hover:bg-gray-100 focus-within:bg-gray-100 truncate">
          <td :class="{ 'italic': attendee.extra, 'text-red-600': attendee.extra }" class="border-t px-6 py-4 flex items-center truncate">
            <img v-if="attendee.photo" class="block w-8 h-8 rounded-full mr-2 -my-2" :src="attendee.photo">
            {{ attendee.name }}
            <icon v-if="attendee.extra" name="users" class="flex-shrink-0 w-3 h-3 fill-current text-gray-500 ml-4" />
            <icon v-if="attendee.attended" name="thumbs-up" class="flex-shrink-0 w-3 h-3 fill-current text-gray-500 ml-4" />
          </td>
          <td :class="{ 'italic': attendee.extra, 'text-red-600': attendee.extra }" class="border-t text-center truncate">
            {{ attendee.created_at }}
          </td>
          <td class="border-t px-4">
            <button v-if="!attendee.attended" class="btn-green ml-auto hover:text-green-600 w-full flex justify-center" :class="{ 'opacity-50': training.start_at > new Date().toISOString(), 'cursor-not-allowed': training.start_at > new Date().toISOString() }" @click="confirmAttendance(attendee.id)" title="Megjelent" type="button">
              <icon name="thumbs-up" class="flex-shrink-0 w-4 h-4 fill-current" />
            </button>
            <button v-if="attendee.attended" class="btn-red ml-auto hover:text-red-600 w-full flex justify-center" :class="{ 'opacity-50': training.start_at > new Date().toISOString(), 'cursor-not-allowed': training.start_at > new Date().toISOString() }" @click="rejectAttendance(attendee.id)" title="Nem jelent meg" type="button">
              <icon name="thumbs-down" class="flex-shrink-0 w-4 h-4 fill-current" />
            </button>
          </td>
        </tr>
        <tr v-if="training.attendees.length === 0">
          <td class="border-t px-6 py-4 text-center" colspan="4">Nem jelentkeztek még az edzésre</td>
        </tr>
      </table>
    </div>
  </div>
</template>

<script>
import Icon from '@/Shared/Icon'
import Layout from '@/Shared/Layout'
import TextInput from '@/Shared/TextInput'
import SelectInput from '@/Shared/SelectInput'
import CheckboxInput from '@/Shared/CheckboxInput'
import LoadingButton from '@/Shared/LoadingButton'

export default {
  metaInfo() {
    return { title: this.form.name }
  },
  components: {
    LoadingButton,
    SelectInput,
    TextInput,
    CheckboxInput,
    Icon,
  },
  layout: Layout,
  props: {
    training: Object,
  },
  remember: 'form',
  data() {
    return {
      form: this.$inertia.form({
        name: this.training.name,
        place: this.training.place,
        start_at_day: this.training.start_at_day,
        start_at_time: this.training.start_at_time,
        length: this.training.length.toString(),
        attendees: this.training.attendees,
        max_attendees: this.training.max_attendees.toString(),
        can_attend_more: this.training.can_attend_more,
        type: this.training.type,
      }),
    }
  },
  computed: {
    attendURL: function () {
      return this.route('trainings.attend', this.training.id)
    }
  },
  methods: {
    update() {
      this.form.put(this.route('trainings.update', this.training.id))
    },
    destroy() {
      if (confirm('Biztosan törlöd az edzést?')) {
        this.$inertia.delete(this.route('trainings.destroy', this.training.id))
      }
    },
    confirmAttendance(user_id) {
      if (this.training.start_at < new Date().toISOString()) {
        this.$inertia.post(this.route('trainings.attendance.confirm', [this.training.id, user_id]), {}, {
          preserveScroll: true,
        })
      }
    },
    rejectAttendance(user_id) {
      if (this.training.start_at < new Date().toISOString()) {
        this.$inertia.post(this.route('trainings.attendance.reject', [this.training.id, user_id]), {}, {
          preserveScroll: true
        })
      }
    }
  },
}
</script>
