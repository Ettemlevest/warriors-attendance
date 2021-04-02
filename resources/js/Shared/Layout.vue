<template>
  <div>
    <portal-target name="dropdown" slim />
    <div class="md:flex md:flex-col">
      <div class="md:h-screen md:flex md:flex-col">
        <div class="md:flex md:flex-shrink-0">
          <div class="bg-indigo-900 md:flex-shrink-0 md:w-56 px-6 py-4 flex items-center justify-between md:justify-center">
            <inertia-link class="text-white font-bold" href="/">
              <!-- <logo class="fill-white" width="120" height="28" /> -->
              KKL Warriors
            </inertia-link>
            <dropdown class="md:hidden" placement="bottom-end">
              <svg class="fill-white w-6 h-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M0 3h20v2H0V3zm0 6h20v2H0V9zm0 6h20v2H0v-2z" /></svg>
              <div slot="dropdown" class="mt-1 px-8 py-4 shadow-lg bg-gray-100 rounded">
                <main-menu />
              </div>
            </dropdown>
          </div>
          <div class="bg-gray-700 w-full p-4 md:py-0 md:px-12 text-sm md:text-md flex justify-between items-center">
            <div class="mt-1 mr-4">
              <a href="https://facebook.com/kklwarriors" class="text-white hover:text-gray-200">
                <svg class="h-5 w-5 fill-current" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 50 50">
                  <path d="M40,0H10C4.486,0,0,4.486,0,10v30c0,5.514,4.486,10,10,10h30c5.514,0,10-4.486,10-10V10C50,4.486,45.514,0,40,0z M39,17h-3 c-2.145,0-3,0.504-3,2v3h6l-1,6h-5v20h-7V28h-3v-6h3v-3c0-4.677,1.581-8,7-8c2.902,0,6,1,6,1V17z"></path>
                </svg>
              </a>
            </div>
            <dropdown class="mt-1" placement="bottom-end">
              <div class="flex items-center cursor-pointer select-none group">
                <img v-if="$page.props.auth.user.photo" class="w-8 h-8 rounded-full mr-4" :src="$page.props.auth.user.photo" />
                <div class="text-white group-hover:text-gray-200 focus:text-gray-200 mr-1 whitespace-nowrap">
                  <span>{{ $page.props.auth.user.name }}</span>
                </div>
                <icon class="w-5 h-5 group-hover:fill-gray-200 fill-white focus:fill-gray-200" name="cheveron-down" />
              </div>
              <div slot="dropdown" class="mt-2 py-2 shadow-xl bg-white rounded text-sm">
                <inertia-link class="block px-6 py-2 hover:bg-indigo-500 hover:text-white" :href="route('users.edit', $page.props.auth.user.id)">Profilom</inertia-link>
                <inertia-link class="block px-6 py-2 hover:bg-indigo-500 hover:text-white" :href="route('users')">Warriorok</inertia-link>
                <inertia-link class="block px-6 py-2 hover:bg-indigo-500 hover:text-white w-full text-left" :href="route('logout')" method="post" as="button">Kilépés</inertia-link>
              </div>
            </dropdown>
          </div>
        </div>
        <div class="md:flex md:flex-grow md:overflow-hidden">
          <main-menu class="hidden md:block bg-gray-100 border-r flex-shrink-0 w-56 p-12 overflow-y-auto" />
          <div class="md:flex-1 px-4 py-8 md:p-12 bg-gray-200 md:overflow-y-auto" scroll-region>
            <flash-messages />
            <slot />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Icon from '@/Shared/Icon'
import Logo from '@/Shared/Logo'
import Dropdown from '@/Shared/Dropdown'
import MainMenu from '@/Shared/MainMenu'
import FlashMessages from '@/Shared/FlashMessages'

export default {
  components: {
    Dropdown,
    FlashMessages,
    Icon,
    Logo,
    MainMenu,
  },
}
</script>
