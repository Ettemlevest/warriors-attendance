<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link class="text-gray-500 hover:text-gray-800" :href="route('messages')">Üzenetek</inertia-link>
      <span class="text-gray-400 font-medium">/</span> Létrehozás
    </h1>
    <div v-if="$page.props.auth.user.owner" class="bg-white rounded shadow overflow-hidden max-w">
      <form @submit.prevent="store">
        <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
          {{ form }}
          <text-input v-model="form.title" :error="form.errors.title" class="pr-6 pb-8 w-full" label="Cím" autofocus />
          <textarea-input v-model="form.body" :error="form.errors.body" class="pr-6 pb-8 w-full" label="Üzenet" />
          <text-input v-model="form.showed_from" :error="form.errors.showed_from" class="pr-6 pb-8 w-full lg:w-1/2" label="Dátumtól" type="date" timezone="Europe/Budapest" />
          <text-input v-model="form.showed_to" :error="form.errors.showed_to" class="pr-6 pb-8 w-full lg:w-1/2" label="Dátumig" type="date" timezone="Europe/Budapest" />
        </div>
        <div class="px-8 py-4 bg-gray-100 border-t border-gray-300 flex justify-end items-center">
          <loading-button :loading="form.processing" class="btn-indigo" type="submit">Üzenet mentése</loading-button>
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
import TextareaInput from '@/Shared/TextareaInput'
import CheckboxInput from '@/Shared/CheckboxInput'

export default {
  metaInfo: { title: 'Üzenet hozzáadása' },
  components: {
    LoadingButton,
    SelectInput,
    TextInput,
    TextareaInput,
    CheckboxInput,
  },
  layout: Layout,
  remember: 'form',
  data() {
    return {
      form: this.$inertia.form({
        title: null,
        body: null,
        showed_from: null,
        showed_to: null,
      }),
    }
  },
  methods: {
    store() {
      this.form.post(this.route('messages.store'))
    }
  },
}
</script>
