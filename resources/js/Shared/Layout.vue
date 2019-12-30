<template>
  <div class="h-screen flex flex-col">
    <portal-target name="dropdown" slim />
    <header class="flex flex-shrink-0">
      <div class="flex-shrink-0 px-4 py-3 bg-gray-700 lg:w-64 lg:bg-gray-800">
        <button @click="menubarOpen = true" class="block text-gray-400 hover:text-gray-200 sm:hidden">
          <svg class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor">
            <path d="M3 6a1 1 0 011-1h16a1 1 0 110 2H4a1 1 0 01-1-1zM3 12a1 1 0 011-1h16a1 1 0 110 2H4a1 1 0 01-1-1zM4 17a1 1 0 100 2h7a1 1 0 100-2H4z" />
          </svg>
        </button>

        <inertia-link class="hidden sm:flex sm:items-center sm:w-full" :href="route('users.edit', $page.auth.user.id)">
          <img class="h-6 w-6 rounded-full object-cover" :src="$page.auth.user.photo" alt="" />
          <span class="hidden lg:inline ml-4 mr-2 text-sm font-medium text-white">{{ $page.auth.user.name }}</span>
          <svg class="ml-2 h-6 w-6 fill-current text-gray-400 lg:ml-auto" viewBox="0 0 24 24">
            <path d="M7.293 9.293a1 1 0 011.414 0L12 12.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/>
          </svg>
        </inertia-link>
      </div>
      <div class="flex-1 flex items-center justify-between pl-2 pr-6 bg-gray-700 lg:px-6">
        <div class="text-white font-bold tracking-wider flex items-center">
          <img class="h-8 w-8 mr-4" src="logo.png" />
          Kiskunlacháza WARRIORS
        </div>
        <!-- <nav class="hidden sm:flex">
          <a href="#" class="inline-block px-3 py-2 bg-gray-800 rounded-lg leading-none text-sm font-medium text-white">
            item-1
          </a>
          <svg class="mt-2 h-4 w-4 fill-current text-gray-200" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M12.95 10.707l.707-.707L8 4.343 6.586 5.757 10.828 10l-4.242 4.243L8 15.657l4.95-4.95z"/></svg>
          <a href="#" class="inline-block px-3 py-2 rounded-lg leading-none text-sm font-medium text-white hover:bg-gray-600">
            item-2
          </a>
          <svg class="mt-2 h-4 w-4 fill-current text-gray-200" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M12.95 10.707l.707-.707L8 4.343 6.586 5.757 10.828 10l-4.242 4.243L8 15.657l4.95-4.95z"/></svg>
          <a href="#" class="inline-block px-3 py-2 rounded-lg leading-none text-sm font-medium text-white hover:bg-gray-600">
            item-3
          </a>
        </nav> -->
        <div class="ml-auto flex items-center">
          <a href="https://facebook.com/kklwarriors" class="ml-5 text-gray-400 hover:text-gray-200">
            <svg class="h-5 w-5 fill-current" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 50 50">
              <path d="M40,0H10C4.486,0,0,4.486,0,10v30c0,5.514,4.486,10,10,10h30c5.514,0,10-4.486,10-10V10C50,4.486,45.514,0,40,0z M39,17h-3 c-2.145,0-3,0.504-3,2v3h6l-1,6h-5v20h-7V28h-3v-6h3v-3c0-4.677,1.581-8,7-8c2.902,0,6,1,6,1V17z"></path>
            </svg>
          </a>
        </div>
      </div>
    </header>
    <div class="flex-1 flex overflow-hidden">
      <div
        :class="menubarOpen ? 'translate-x-0 ease-out transition-slow' : '-translate-x-full ease-in transition-medium'"
        class="z-30 fixed inset-y-0 left-0 w-64 bg-gray-100 border-r overflow-y-auto sm:static sm:block sm:translate-x-0 sm:transition-none"
      >
        <div class="absolute top-0 left-0 pl-4 pt-3 sm:hidden">
          <button
            @click="menubarOpen = false"
            class="block text-gray-600 hover:text-gray-800"
          >
            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor">
              <path
                d="M17.293 18.707a1 1 0 001.414-1.414L13.414 12l5.293-5.293a1 1 0 00-1.414-1.414L12 10.586 6.707 5.293a1 1 0 00-1.414 1.414L10.586 12l-5.293 5.293a1 1 0 101.414 1.414L12 13.414l5.293 5.293z"
              />
            </svg>
          </button>
        </div>
        <nav class="mt-16 sm:mt-0">
          <div class="mt-8 px-6">
            <inertia-link :href="route('dashboard')" class="mt-2 -mx-3 px-3 py-2 flex items-center text-sm font-medium hover:bg-gray-200 rounded-lg text-gray-700">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" class="w-4 h-4 mr-2 fill-current"><path d="M10 20a10 10 0 1 1 0-20 10 10 0 0 1 0 20zm-5.6-4.29a9.95 9.95 0 0 1 11.2 0 8 8 0 1 0-11.2 0zm6.12-7.64l3.02-3.02 1.41 1.41-3.02 3.02a2 2 0 1 1-1.41-1.41z"></path></svg>
              Kezdőlap
            </inertia-link>
            <inertia-link :href="route('trainings')" class="mt-2 -mx-3 px-3 py-2 flex items-center text-sm font-medium hover:bg-gray-200 rounded-lg text-gray-700">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" class="w-4 h-4 mr-2 fill-current"><path d="M6 4H5a1 1 0 1 1 0-2h11V1a1 1 0 0 0-1-1H4a2 2 0 0 0-2 2v16c0 1.1.9 2 2 2h12a2 2 0 0 0 2-2V5a1 1 0 0 0-1-1h-7v8l-2-2-2 2V4z"></path></svg>
              Edzések
            </inertia-link>
            <inertia-link :href="route('albums')" class="mt-2 -mx-3 px-3 py-2 flex items-center text-sm font-medium hover:bg-gray-200 rounded-lg text-gray-700">
              <svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 100 100" class="w-4 h-4 mr-2 fill-current"><path fill-rule="evenodd" d="M7 0h86v100H57.108V88.418H42.892V100H7V0zm9 64h11v15H16V64zm57 0h11v15H73V64zm-19 0h11v15H54V64zm-19 0h11v15H35V64zM16 37h11v15H16V37zm57 0h11v15H73V37zm-19 0h11v15H54V37zm-19 0h11v15H35V37zM16 11h11v15H16V11zm57 0h11v15H73V11zm-19 0h11v15H54V11zm-19 0h11v15H35V11z"></path></svg>
              Képek
            </inertia-link>
            <inertia-link
              :href="route('messages')"
              v-if="$page.auth.user.owner"
              class="mt-2 -mx-3 px-3 py-2 flex items-center text-sm font-medium hover:bg-gray-200 rounded-lg text-gray-700"
            >
              <icon name="chat-bubble-dots" class="w-4 h-4 mr-2 fill-current" />
              Üzenetek
            </inertia-link>



            <inertia-link
              :href="route('users')"
              v-if="$page.auth.user.owner"
              class="mt-2 -mx-3 px-3 py-2 flex items-center text-sm font-medium hover:bg-gray-200 rounded-lg text-gray-700"
            >
              <svg class="h-4 w-4 mr-2 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M7 8a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm0 1c2.15 0 4.2.4 6.1 1.09L12 16h-1.25L10 20H4l-.75-4H2L.9 10.09A17.93 17.93 0 0 1 7 9zm8.31.17c1.32.18 2.59.48 3.8.92L18 16h-1.25L16 20h-3.96l.37-2h1.25l1.65-8.83zM13 0a4 4 0 1 1-1.33 7.76 5.96 5.96 0 0 0 0-7.52C12.1.1 12.53 0 13 0z"/></svg>
              Warriorok
            </inertia-link>
            <inertia-link
              :href="route('logout')"
              method="post"
              class="mt-2 -mx-3 px-3 py-2 flex items-center text-sm font-medium hover:bg-gray-200 rounded-lg text-gray-700 md:flex hidden"
            >
              <svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 100 100" class="w-4 h-4 mr-2 invisible"><path fill-rule="evenodd" d="M7 0h86v100H57.108V88.418H42.892V100H7V0zm9 64h11v15H16V64zm57 0h11v15H73V64zm-19 0h11v15H54V64zm-19 0h11v15H35V64zM16 37h11v15H16V37zm57 0h11v15H73V37zm-19 0h11v15H54V37zm-19 0h11v15H35V37zM16 11h11v15H16V11zm57 0h11v15H73V11zm-19 0h11v15H54V11zm-19 0h11v15H35V11z"></path></svg>
              Kilépés
            </inertia-link>
          </div>
          <div class="mt-8 p-6 border-t sm:hidden">
            <div class="flex items-center">
              <inertia-link :href="route('users.edit', $page.auth.user.id)" class="flex items-center">
                <img
                  class="h-8 w-8 rounded-full object-cover"
                  :src="$page.auth.user.photo"
                  alt=""
                />
                <span class="ml-4 mr-2 text-sm font-medium text-gray-800">{{ $page.auth.user.name }}</span>
              </inertia-link>
            </div>
            <div class="mt-4">
              <inertia-link :href="route('logout')" method="post" class="mt-4 block text-sm font-medium text-gray-700">
                Kilépés
              </inertia-link>
            </div>
          </div>
        </nav>
      </div>
      <main class="flex-1 flex bg-gray-200">
        <div class="flex-1 flex flex-col w-0 overflow-hidden">
          <div class="p-3 flex-1 overflow-y-auto">
            <div class="container mx-auto px-4 sm:px-8">
              <div class="py-8">
                <flash-messages />
                <slot />
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>
</template>

<script>
import Dropdown from '@/Shared/Dropdown'
import FlashMessages from '@/Shared/FlashMessages'
import Icon from '@/Shared/Icon'
import Logo from '@/Shared/Logo'

export default {
  components: {
    Dropdown,
    FlashMessages,
    Icon,
    Logo,
  },
  data() {
    return {
      menubarOpen: false,
    }
  },
  methods: {
    hideDropdownMenus() {
      this.menuBarOpen = false
    },
  },
}
</script>
