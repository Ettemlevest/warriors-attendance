<template>
  <layout>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link class="text-indigo-300 hover:text-indigo-600" :href="route('messages')">Üzenetek</inertia-link>
      <span class="text-indigo-300 font-medium">/</span> Üzenet
    </h1>
    <div v-if="$page.auth.user.owner" class="bg-white rounded shadow overflow-hidden max-w-lg">
      <form @submit.prevent="submit">
        <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
          <text-input v-model="form.title" :errors="$page.errors.title" class="pr-6 pb-8 w-full" label="Cím" autofocus />
          <text-input v-model="form.body" :errors="$page.errors.body" class="pr-6 pb-8 w-full" label="Üzenet" />
          <text-input v-model="form.showed_from" :errors="$page.errors.showed_from" class="pr-6 pb-8 w-full lg:w-1/2" label="Dátumtól" type="date" timezone="Europe/Budapest" />
          <text-input v-model="form.showed_to" :errors="$page.errors.showed_to" class="pr-6 pb-8 w-full lg:w-1/2" label="Dátumig" type="date" timezone="Europe/Budapest" />
        </div>
        <div class="px-8 py-4 bg-gray-300 border-t border-gray-300 flex justify-end items-center">
          <button class="text-red-500 hover:underline" tabindex="-1" type="button" @click="destroy">Törlés</button>
          <loading-button :loading="sending" class="btn-indigo ml-auto" type="submit">Üzenet mentése</loading-button>
        </div>
      </form>
    </div>
  </layout>
</template>

<script>
import Layout from '@/Shared/Layout'
import LoadingButton from '@/Shared/LoadingButton'
import SelectInput from '@/Shared/SelectInput'
import TextInput from '@/Shared/TextInput'
import CheckboxInput from '@/Shared/CheckboxInput'

export default {
  metaInfo: { title: 'Üzenet szerkesztése' },
  components: {
    Layout,
    LoadingButton,
    SelectInput,
    TextInput,
    CheckboxInput,
  },
  props: {
    message: Object,
  },
  // remember: 'form',
  data() {
    return {
      sending: false,
      form: {
        title: this.message.title,
        body: this.message.body,
        showed_from: this.message.showed_from,
        showed_to: this.message.showed_to,
      }
    }
  },
  methods: {
    submit() {
      this.sending = true

      var data = new FormData()
      data.append('title', this.form.title || '')
      data.append('body', this.form.body || '')
      data.append('showed_from', this.form.showed_from || '')
      data.append('showed_to', this.form.showed_to || '')
      data.append('_method', 'put')

      this.$inertia.post(this.route('messages.update', this.message.id), data)
        .then(() => {
          this.sending = false
        })
    },
    destroy() {
      if (confirm('Biztosan törlöd az üzenetet?')) {
        this.$inertia.delete(this.route('messages.destroy', this.message.id))
      }
    },
  },
}
</script>
