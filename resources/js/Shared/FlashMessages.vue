<template>
  <div>
    <div v-if="$page.flash.success && show" class="mb-8 flex items-center justify-between bg-green-500 rounded max-w">
      <div class="flex items-center">
        <svg class="ml-4 mr-2 flex-shrink-0 w-4 h-4 fill-current text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><polygon points="0 11 2 9 7 14 18 3 20 5 7 18" /></svg>
        <div class="py-4 text-white text-sm font-medium">{{ $page.flash.success }}</div>
      </div>
      <button type="button" class="group mr-2 p-2" @click="show = false">
        <svg class="block w-2 h-2 fill-current text-green-200 group-hover:text-green-900" xmlns="http://www.w3.org/2000/svg" width="235.908" height="235.908" viewBox="278.046 126.846 235.908 235.908"><path d="M506.784 134.017c-9.56-9.56-25.06-9.56-34.62 0L396 210.18l-76.164-76.164c-9.56-9.56-25.06-9.56-34.62 0-9.56 9.56-9.56 25.06 0 34.62L361.38 244.8l-76.164 76.165c-9.56 9.56-9.56 25.06 0 34.62 9.56 9.56 25.06 9.56 34.62 0L396 279.42l76.164 76.165c9.56 9.56 25.06 9.56 34.62 0 9.56-9.56 9.56-25.06 0-34.62L430.62 244.8l76.164-76.163c9.56-9.56 9.56-25.06 0-34.62z" /></svg>
      </button>
    </div>
    <div v-if="$page.flash.warning && show" class="mb-8 flex items-center justify-between bg-yellow-500 rounded max-w">
      <div class="flex items-center">
        <svg class="ml-4 mr-2 flex-shrink-0 w-4 h-4 fill-current text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M0 10a10 10 0 1 1 20 0 10 10 0 0 1-20 0zm16.32-4.9L5.09 16.31A8 8 0 0 0 16.32 5.09zm-1.41-1.42A8 8 0 0 0 3.68 14.91L14.91 3.68z"/></svg>
        <div class="py-4 text-white text-sm font-medium">{{ $page.flash.warning }}</div>
      </div>
      <button type="button" class="group mr-2 p-2" @click="show = false">
        <svg class="block w-2 h-2 fill-current text-red-200 group-hover:text-red-900" xmlns="http://www.w3.org/2000/svg" width="235.908" height="235.908" viewBox="278.046 126.846 235.908 235.908"><path d="M506.784 134.017c-9.56-9.56-25.06-9.56-34.62 0L396 210.18l-76.164-76.164c-9.56-9.56-25.06-9.56-34.62 0-9.56 9.56-9.56 25.06 0 34.62L361.38 244.8l-76.164 76.165c-9.56 9.56-9.56 25.06 0 34.62 9.56 9.56 25.06 9.56 34.62 0L396 279.42l76.164 76.165c9.56 9.56 25.06 9.56 34.62 0 9.56-9.56 9.56-25.06 0-34.62L430.62 244.8l76.164-76.163c9.56-9.56 9.56-25.06 0-34.62z" /></svg>
      </button>
    </div>
    <div v-if="$page.flash.error && show" class="mb-8 flex items-center justify-between bg-red-600 rounded max-w">
      <div class="flex items-center">
        <svg class="ml-4 mr-2 flex-shrink-0 w-4 h-4 fill-current text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm1.41-1.41A8 8 0 1 0 15.66 4.34 8 8 0 0 0 4.34 15.66zm9.9-8.49L11.41 10l2.83 2.83-1.41 1.41L10 11.41l-2.83 2.83-1.41-1.41L8.59 10 5.76 7.17l1.41-1.41L10 8.59l2.83-2.83 1.41 1.41z" /></svg>
        <div class="py-4 text-white text-sm font-medium">{{ $page.flash.error }}</div>
      </div>
      <button type="button" class="group mr-2 p-2" @click="show = false">
        <svg class="block w-2 h-2 fill-current text-red-200 group-hover:text-red-900" xmlns="http://www.w3.org/2000/svg" width="235.908" height="235.908" viewBox="278.046 126.846 235.908 235.908"><path d="M506.784 134.017c-9.56-9.56-25.06-9.56-34.62 0L396 210.18l-76.164-76.164c-9.56-9.56-25.06-9.56-34.62 0-9.56 9.56-9.56 25.06 0 34.62L361.38 244.8l-76.164 76.165c-9.56 9.56-9.56 25.06 0 34.62 9.56 9.56 25.06 9.56 34.62 0L396 279.42l76.164 76.165c9.56 9.56 25.06 9.56 34.62 0 9.56-9.56 9.56-25.06 0-34.62L430.62 244.8l76.164-76.163c9.56-9.56 9.56-25.06 0-34.62z" /></svg>
      </button>
    </div>
    <div v-if="Object.keys($page.errors).length > 0 && show" class="mb-8 flex items-center justify-between bg-red-600 rounded max-w">
      <div class="flex items-center">
        <svg class="ml-4 mr-2 flex-shrink-0 w-4 h-4 fill-current text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm1.41-1.41A8 8 0 1 0 15.66 4.34 8 8 0 0 0 4.34 15.66zm9.9-8.49L11.41 10l2.83 2.83-1.41 1.41L10 11.41l-2.83 2.83-1.41-1.41L8.59 10 5.76 7.17l1.41-1.41L10 8.59l2.83-2.83 1.41 1.41z" /></svg>
        <div class="py-4 text-white text-sm font-medium">
          <span v-if="Object.keys($page.errors).length === 1">Az adatok között van egy hiba.</span>
          <span v-else>{{ Object.keys($page.errors).length }} hibás adatot adtál meg.</span>
        </div>
      </div>
      <button type="button" class="group mr-2 p-2" @click="show = false">
        <svg class="block w-2 h-2 fill-current text-red-200 group-hover:text-red-900" xmlns="http://www.w3.org/2000/svg" width="235.908" height="235.908" viewBox="278.046 126.846 235.908 235.908"><path d="M506.784 134.017c-9.56-9.56-25.06-9.56-34.62 0L396 210.18l-76.164-76.164c-9.56-9.56-25.06-9.56-34.62 0-9.56 9.56-9.56 25.06 0 34.62L361.38 244.8l-76.164 76.165c-9.56 9.56-9.56 25.06 0 34.62 9.56 9.56 25.06 9.56 34.62 0L396 279.42l76.164 76.165c9.56 9.56 25.06 9.56 34.62 0 9.56-9.56 9.56-25.06 0-34.62L430.62 244.8l76.164-76.163c9.56-9.56 9.56-25.06 0-34.62z" /></svg>
      </button>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      show: true,
    }
  },
  watch: {
    '$page.flash': {
      handler() {
        this.show = true
      },
      deep: true,
    },
  },
}
</script>
