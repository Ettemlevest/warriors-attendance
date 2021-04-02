<template>
  <div>
    <div class="mb-8 flex justify-start max-w-3xl">
      <h1 class="font-bold text-3xl">
        <inertia-link class="text-gray-500 hover:text-gray-800" :href="route('users')">Warriorok</inertia-link>
        <span class="text-gray-400 font-medium">/</span>
        {{ form.name }}
      </h1>
      <img v-if="form.photo" class="block w-8 h-8 rounded-full ml-4" :src="form.photo">
    </div>
    <trashed-message v-if="user.deleted_at" class="mb-6" @restore="restore">
      A Warrior törölve van.
    </trashed-message>
    <div class="bg-white rounded-md shadow overflow-hidden max-w-3xl">
      <form @submit.prevent="update">
        <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
          <text-input v-model="form.name" :error="form.errors.name" class="pr-6 pb-8 w-full lg:w-1/2" label="Név" />
          <text-input v-model="form.email" :error="form.errors.email" class="pr-6 pb-8 w-full lg:w-1/2" label="E-mail" type="email" />
          <text-input v-model="form.password" :error="form.errors.password" class="pr-6 pb-8 w-full lg:w-1/2" label="Jelszó" type="password" />
          <select-input v-if="$page.props.auth.user.owner" v-model="form.owner" :error="form.errors.owner" class="pr-6 pb-8 w-full lg:w-1/2" label="Edző">
            <option :value="true">Igen</option>
            <option :value="false">Nem</option>
          </select-input>

          <h3 class="w-full mb-4 mr-6 p-2 border-b border-gray-400 tracking-widest text-lg italic pl-0">Információk versenyre jelentkezéshez</h3>
          <file-input v-model="form.photo" :error="form.errors.photo" class="pr-6 pb-8 w-full lg:w-1/2" type="file" accept="image/*" label="Fénykép" />
          <select-input v-model="form.size" :error="form.errors.size" class="pr-6 pb-8 w-full lg:w-1/2" label="Póló méret">
            <option :value="null" />
            <option value="S">S</option>
            <option value="M">M</option>
            <option value="L">L</option>
            <option value="XL">XL</option>
            <option value="XXL">XXL</option>
          </select-input>
          <text-input v-model="form.birth_date" :error="form.errors.birth_date" class="pr-6 pb-8 w-full lg:w-1/2" label="Születési dátum" type="date" />
          <text-input v-model="form.phone" :error="form.errors.phone" class="pr-6 pb-8 w-full lg:w-1/2" label="Telefonszám" />
          <text-input v-model="form.address" :error="form.errors.address" class="pr-6 pb-8 w-full" label="Lakcím" />

          <h3 class="w-full mb-4 mr-6 p-2 border-b border-gray-400 tracking-widest text-lg italic pl-0">Baleset esetén értesítendő</h3>
          <text-input v-model="form.safety_person" :error="form.errors.safety_person" class="pr-6 pb-8 w-full lg:w-1/2" label="Név" />
          <text-input v-model="form.safety_phone" :error="form.errors.safety_phone" class="pr-6 pb-8 w-full lg:w-1/2" label="Telefonszám" />
        </div>
        <div class="px-8 py-4 bg-gray-50 border-t border-gray-100 flex items-center">
          <button v-if="!user.deleted_at" class="text-red-600 hover:underline" tabindex="-1" type="button" @click="destroy">Törlés</button>
          <loading-button :loading="form.processing" class="btn-indigo ml-auto" type="submit">Warrior mentése</loading-button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import Icon from '@/Shared/Icon'
import Layout from '@/Shared/Layout'
import FileInput from '@/Shared/FileInput'
import TextInput from '@/Shared/TextInput'
import SelectInput from '@/Shared/SelectInput'
import LoadingButton from '@/Shared/LoadingButton'
import TrashedMessage from '@/Shared/TrashedMessage'

export default {
  metaInfo() {
    return { title: this.form.name }
  },
  components: {
    Icon,
    LoadingButton,
    SelectInput,
    TextInput,
    FileInput,
    TrashedMessage,
  },
  layout: Layout,
  props: {
    user: Object,
  },
  remember: 'form',
  data() {
    return {
      form: this.$inertia.form({
        name: this.user.name,
        email: this.user.email,
        password: this.user.password,
        owner: this.user.owner,
        photo: this.user.photo,
        size: this.user.size,
        birth_date: this.user.birth_date,
        phone: this.user.phone,
        address: this.user.address,
        safety_person: this.user.safety_person,
        safety_phone: this.user.safety_phone,
      }),
    }
  },
  methods: {
    update() {
      this.form.put(this.route('users.update', this.user.id))
    },
    destroy() {
      if (confirm('Biztosan törlöd a Warriort?')) {
        this.$inertia.delete(this.route('users.destroy', this.user.id))
      }
    },
    restore() {
      if (confirm('Biztosan visszaállítod a Warriort?')) {
        this.$inertia.put(this.route('users.restore', this.user.id))
      }
    },
  },
}
</script>
