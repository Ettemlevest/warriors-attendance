<template>
  <layout>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link class="text-indigo-500 hover:text-indigo-600" :href="route('users')">Warriorok</inertia-link>
      <span class="text-indigo-500 font-medium">/</span> Létrehozás
    </h1>
    <div v-if="$page.auth.user.owner === false">Nincs jogosultságod Warrior hozzáadásához!</div>
    <div v-if="$page.auth.user.owner" class="bg-white rounded shadow overflow-hidden max-w">
      <form @submit.prevent="submit">
        <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
          <text-input v-model="form.name" :errors="$page.errors.name" class="pr-6 pb-8 w-full lg:w-1/2" label="Név" autofocus />
          <text-input v-model="form.email" :errors="$page.errors.email" class="pr-6 pb-8 w-full lg:w-1/2" label="E-mail" />
          <text-input v-model="form.password" :errors="$page.errors.password" class="pr-6 pb-8 w-full lg:w-1/2" type="password" autocomplete="new-password" label="Jelszó" />
          <select-input v-if="$page.auth.user.owner" v-model="form.owner" :errors="$page.errors.owner" class="pr-6 pb-8 w-full lg:w-1/2" label="Edző">
            <option :value="true">Igen</option>
            <option :value="false">Nem</option>
          </select-input>
          <h3 class="w-full mb-4 mr-6 p-2 border-b border-gray-400 tracking-wider text-lg italic pl-0">Információk versenyre jelentkezéshez</h3>
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
          <h3 class="w-full mb-4 mr-6 p-2 border-b border-gray-400 tracking-wider text-lg italic pl-0">Baleset esetén értesítendő</h3>
          <text-input v-model="form.safety_person" :errors="$page.errors.safety_person" class="pr-6 pb-8 w-full lg:w-1/2" label="Név" />
          <text-input v-model="form.safety_phone" :errors="$page.errors.safety_phone" class="pr-6 pb-8 w-full lg:w-1/2" label="Telefonszám" />
        </div>
        <div class="px-8 py-4 bg-gray-100 border-t border-gray-300 flex justify-end items-center">
          <loading-button :loading="sending" class="btn-indigo" type="submit">Warrior mentése</loading-button>
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

export default {
  metaInfo: { title: 'Warrior hozzáadása' },
  components: {
    Layout,
    LoadingButton,
    SelectInput,
    TextInput,
    FileInput,
  },
  remember: 'form',
  data() {
    return {
      sending: false,
      form: {
        name: null,
        email: null,
        password: null,
        owner: false,
        photo: null,
        size: null,
        birth_date: null,
        phone: null,
        address: null,
        safety_person: null,
        safety_phone: null,
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
      data.append('safety_person', this.form.safety_person || '')
      data.append('safety_phone', this.form.safety_phone || '')

      this.$inertia.post(this.route('users.store'), data)
        .then(() => this.sending = false)
    },
  },
}
</script>
