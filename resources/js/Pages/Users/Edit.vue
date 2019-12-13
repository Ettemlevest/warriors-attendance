<template>
  <layout>
    <div class="mb-8 flex justify-start max-w">
      <h1 class="font-bold text-3xl">
        <inertia-link class="text-indigo-500 hover:text-indigo-600" :href="route('users')">Warriorok</inertia-link>
        <span class="text-indigo-500 font-medium">/</span>
        {{ form.name }}
      </h1>
      <img v-if="user.photo" class="block w-8 h-8 rounded-full ml-4" :src="user.photo">
    </div>
    <trashed-message v-if="user.deleted_at" class="mb-6" @restore="restore">
      A Warrior törölve van.
    </trashed-message>
    <div class="bg-white rounded shadow overflow-hidden max-w">
      <form @submit.prevent="submit">
        <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
          <text-input v-model="form.name" :errors="$page.errors.name" class="pr-6 pb-8 w-full lg:w-1/2" label="Név" />
          <text-input v-model="form.email" :errors="$page.errors.email" class="pr-6 pb-8 w-full lg:w-1/2" label="E-mail" />
          <text-input v-model="form.password" :errors="$page.errors.password" class="pr-6 pb-8 w-full lg:w-1/2" type="password" autocomplete="new-password" label="Jelszó" />
          <select-input v-if="$page.auth.user.owner" v-model="form.owner" :errors="$page.errors.owner" class="pr-6 pb-8 w-full lg:w-1/2" label="Edző">
            <option :value="true">Igen</option>
            <option :value="false">Nem</option>
          </select-input>
          <h3 class="w-full mb-4">Hasznos adatok</h3>
          <file-input v-model="form.photo" :errors="$page.errors.photo" class="pr-6 pb-8 w-full lg:w-1/2" type="file" accept="image/*" label="Fénykép" />
          <select-input v-model="form.size" :errors="$page.errors.size" class="pr-6 pb-8 w-full lg:w-1/2" label="Póló méret">
            <option :value="null" />
            <option value="S">S</option>
            <option value="M">M</option>
            <option value="L">L</option>
            <option value="XL">XL</option>
            <option value="XXL">XXL</option>
          </select-input>
          <text-input v-model="form.birth_date" :errors="$page.errors.birth_date" class="pr-6 pb-8 w-full lg:w-1/2" type="date" label="Szül. dátum" />
          <text-input v-model="form.phone" :errors="$page.errors.phone" class="pr-6 pb-8 w-full lg:w-1/2" label="Telefonszám" />
          <text-input v-model="form.address" :errors="$page.errors.address" class="pr-6 pb-8 w-full" label="Lakcím" />
        </div>
        <div class="px-8 py-4 bg-gray-100 border-t border-gray-300 flex items-center">
          <button v-if="!user.deleted_at && $page.auth.user.id != this.user.id" class="text-red-500 hover:underline tracking-widest" tabindex="-1" type="button" @click="destroy">Törlés</button>
          <loading-button :loading="sending" class="btn-indigo ml-auto" type="submit">Warrior mentése</loading-button>
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
import FileInput from '@/Shared/FileInput'
import TrashedMessage from '@/Shared/TrashedMessage'

export default {
  metaInfo() {
    return { title: this.form.name }
  },
  components: {
    Layout,
    LoadingButton,
    SelectInput,
    TextInput,
    FileInput,
    TrashedMessage,
  },
  props: {
    user: Object,
  },
  remember: 'form',
  data() {
    return {
      sending: false,
      form: {
        name: this.user.name,
        email: this.user.email,
        password: this.user.password,
        owner: this.user.owner,
        photo: null,
        size: this.user.size,
        birth_date: this.user.birth_date,
        phone: this.user.phone,
        address: this.user.address,
      },
    }
  },
  methods: {
    submit() {
      this.sending = true

      var data = new FormData()
      data.append('name', this.form.name || '')
      data.append('email', this.form.email || '')
      data.append('password', this.form.password || '')
      data.append('owner', this.form.owner ? '1' : '0')
      data.append('photo', this.form.photo || '')
      data.append('size', this.form.size || '')
      data.append('birth_date', this.form.birth_date || '')
      data.append('phone', this.form.phone || '')
      data.append('address', this.form.address || '')
      data.append('_method', 'put')

      this.$inertia.post(this.route('users.update', this.user.id), data)
        .then(() => {
          this.sending = false
          if (Object.keys(this.$page.errors).length === 0) {
            this.form.photo = null
            this.form.password = null
          }
        })
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
