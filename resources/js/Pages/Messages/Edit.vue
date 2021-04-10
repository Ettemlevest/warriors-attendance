<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link class="text-grey-500 hover:text-grey-800" :href="route('messages')">Üzenetek</inertia-link>
      <span class="text-grey-400 font-medium">/</span> Üzenet
    </h1>
    <div v-if="$page.props.auth.user.owner" class="bg-white rounded-md shadow overflow-hidden max-w">
      <form @submit.prevent="update">
        <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
          <text-input v-model="form.title" :errors="form.errors.title" class="pr-6 pb-8 w-full" label="Cím" autofocus />
          <textarea-input v-model="form.body" :errors="form.errors.body" class="pr-6 pb-8 w-full" label="Üzenet" />
          <text-input v-model="form.showed_from" :errors="form.errors.showed_from" class="pr-6 pb-8 w-full lg:w-1/2" label="Dátumtól" type="date" timezone="Europe/Budapest" />
          <text-input v-model="form.showed_to" :errors="form.errors.showed_to" class="pr-6 pb-8 w-full lg:w-1/2" label="Dátumig" type="date" timezone="Europe/Budapest" />
        </div>
        <div class="px-8 py-4 bg-gray-100 border-t border-gray-300 flex items-center">
          <button class="text-red-600 hover:underline tracking-widest" tabindex="-1" type="button" @click="destroy">Törlés</button>
          <loading-button :loading="form.processing" class="btn-indigo ml-auto" type="submit">Üzenet mentése</loading-button>
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
  metaInfo: { title: 'Üzenet szerkesztése' },
  components: {
    LoadingButton,
    SelectInput,
    TextInput,
    CheckboxInput,
    TextareaInput,
  },
  layout: Layout,
  props: {
    message: Object,
  },
  remember: 'form',
  data() {
    return {
      form: this.$inertia.form({
        title: this.message.title,
        body: this.message.body,
        showed_from: this.message.showed_from,
        showed_to: this.message.showed_to,
      }),
    }
  },
  methods: {
    update() {
      this.form.put(this.route('messages.update', this.message.id))
    },
    destroy() {
      if (confirm('Biztosan törlöd az üzenetet?')) {
        this.$inertia.delete(this.route('messages.destroy', this.message.id))
      }
    },
  },
}
</script>
