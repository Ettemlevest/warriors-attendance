<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link class="text-gray-500 hover:text-gray-800" :href="route('trainings')">Edzések</inertia-link>
      <span class="text-gray-400 font-medium">/</span> Létrehozás
    </h1>
    <div v-if="$page.props.auth.user.owner" class="bg-white rounded-md shadow overflow-hidden max-w">
      <div class="px-8 py-4">
        <button
          v-for="template in templates"
          :key="template.template"
          class="btn-indigo inline-block rounded-full px-4 py-2 text-sm font-semibold m-2"
          @click="setTemplate(template)"
        >
          {{ template.template }}
        </button>
      </div>

      <form @submit.prevent="store">
        <div class="px-8 py-4 -mr-6 -mb-8 flex flex-wrap">
          <select-input v-model="form.type" :error="form.errors.type" class="pr-6 pb-8 w-full lg:w-1/2" label="Típus">
            <option value="easy">Felzárkóztató edzés</option>
            <option value="running">Futó edzés</option>
            <option value="hard">Haladó edzés</option>
            <option value="other">Egyéb</option>
          </select-input>
          <div class="pr-6 pb-8 w-full lg:w-1/2">
            <label class="form-label">Minta:</label>
            <div class="flex items-center border rounded-md shadow italic px-4 py-1" :class="overalStyling">
              <icon :name="form.type" class="w-8 h-8 mr-4 fill-current" />
              {{ form.name || 'Minta edzés' }}
            </div>
          </div>

          <text-input v-model="form.name" :error="form.errors.name" class="pr-6 pb-8 w-full lg:w-1/2" label="Edzés neve" autofocus />
          <text-input v-model="form.place" :error="form.errors.place" class="pr-6 pb-8 w-full lg:w-1/2" label="Helyszín" />
          <text-input v-model="form.start_at_day" :error="form.errors.start_at_day" class="pr-6 pb-8 w-full lg:w-1/2" label="Kezdés (dátum)" type="date" timezone="Europe/Budapest" />
          <text-input v-model="form.start_at_time" :error="form.errors.start_at_time" class="pr-6 pb-8 w-full lg:w-1/2" label="Kezdés (időpont)" type="time" timezone="Europe/Budapest" />
          <text-input v-model="form.length" :error="form.errors.length" class="pr-6 pb-8 w-full lg:w-1/2" label="Időtartam (perc)" type="number" />
          <text-input v-model="form.max_attendees" :error="form.errors.max_attendees" class="pr-6 pb-8 w-full lg:w-1/2" label="Max. létszám" type="number" />
          <checkbox-input v-model="form.can_attend_more" :error="form.errors.can_attend_more" class="pr-6 pb-8 w-full lg:w-1/2" label="Maximális létszám túlléphető" :checked="form.can_attend_more" />

          <textarea-input v-model="form.description" :error="form.errors.description" class="w-full pb-8" label="Leírás" />
        </div>
        <div class="px-8 py-4 bg-gray-100 border-t border-gray-300 flex justify-end items-center">
          <loading-button :loading="form.processing" class="btn-indigo" type="submit">Edzés mentése</loading-button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import Layout from '@/Shared/Layout'
import LoadingButton from '@/Shared/LoadingButton'
import SelectInput from '@/Shared/SelectInput'
import TextInput from '@/Shared/TextInput'
import CheckboxInput from '@/Shared/CheckboxInput'
import Icon from '@/Shared/Icon'
import TextareaInput from '../../Shared/TextareaInput.vue'

export default {
  metaInfo: { title: 'Edzés hozzáadása' },
  components: {
    TextareaInput,
    LoadingButton,
    SelectInput,
    TextInput,
    CheckboxInput,
    Icon,
  },
  layout: Layout,
  remember: 'form',
  data() {
    return {
      templates: [
        {
          template: 'Köredzés 18:45',
          name: 'Köredzés korai',
          place: 'Sportcsarnok',
          start_at_time: '18:45',
          length: '60',
          max_attendees: '36',
          can_attend_more: true,
          type: 'easy',
          description: '',
        },
        {
          template: 'Köredzés 20:00',
          name: 'Köredzés késői',
          place: 'Sportcsarnok',
          start_at_time: '20:00',
          length: '60',
          max_attendees: '36',
          can_attend_more: true,
          type: 'running',
          description: '',
        },
        {
          template: 'Köredzés 19:45',
          name: 'Köredzés',
          place: 'Sportcsarnok',
          start_at_time: '19:45',
          length: '60',
          max_attendees: '36',
          can_attend_more: true,
          type: 'hard',
          description: '',
        },
      ],
      form: this.$inertia.form({
        name: null,
        place: null,
        start_at_day: null,
        start_at_time: null,
        length: '60',
        attendees: null,
        max_attendees: '32',
        can_attend_more: true,
        type: 'easy',
        description: '',
      }),
    }
  },
  computed: {
    overalStyling: function () {
      return {
        'bg-green-100': this.form.type === 'easy',
        'border-green-500': this.form.type === 'easy',
        'text-green-600': this.form.type === 'easy',

        'bg-orange-100': this.form.type === 'running',
        'border-orange-500': this.form.type === 'running',
        'text-orange-600': this.form.type === 'running',

        'bg-red-100': this.form.type === 'hard',
        'border-red-500': this.form.type === 'hard',
        'text-red-600': this.form.type === 'hard',

        'bg-indigo-100': this.form.type === 'other',
        'border-indigo-500': this.form.type === 'other',
        'text-indigo-600': this.form.type === 'other',
      }
    },
  },
  methods: {
    store() {
      this.form.post(this.route('trainings.store'))
    },
    setTemplate(template) {
      this.form.name = template.name
      this.form.place = template.place
      this.form.start_at_time = template.start_at_time
      this.form.length = template.length
      this.form.max_attendees = template.max_attendees
      this.form.can_attend_more = template.can_attend_more
      this.form.type = template.type
      this.form.description = template.description
    },
  },
}
</script>
