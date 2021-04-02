<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link class="text-gray-500 hover:text-gray-800" :href="route('users')">Warriorok</inertia-link>
      <span class="text-gray-400 font-medium">/</span> Létrehozás
    </h1>
    <div class="bg-white rounded-md shadow overflow-hidden max-w-3xl">
      <form @submit.prevent="store">
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
        <div class="px-8 py-4 bg-gray-50 border-t border-gray-100 flex justify-end items-center">
          <loading-button :loading="form.processing" class="btn-indigo" type="submit">Create User</loading-button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import Layout from '@/Shared/Layout'
import FileInput from '@/Shared/FileInput'
import TextInput from '@/Shared/TextInput'
import SelectInput from '@/Shared/SelectInput'
import LoadingButton from '@/Shared/LoadingButton'

export default {
  metaInfo: { title: 'Warrior létrehozása' },
  components: {
    FileInput,
    LoadingButton,
    SelectInput,
    TextInput,
  },
  layout: Layout,
  remember: 'form',
  data() {
    return {
      form: this.$inertia.form({
        name: null,
        email: null,
        password: null,
        owner: null,
        photo: null,
        size: null,
        birth_date: null,
        phone: null,
        address: null,
        safety_person: null,
        safety_phone: null,
      }),
    }
  },
  methods: {
    store() {
      this.form.post(this.route('users.store'))
    },
  },
}
</script>
