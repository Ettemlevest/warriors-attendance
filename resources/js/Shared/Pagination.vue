<template>
  <div v-if="links.length > 3">
    <div class="flex flex-wrap justify-center -mb-1">
      <template v-for="(link, key) in links">
        <div v-if="link.url === null" :key="key" class="mr-1 mb-1 px-4 py-3 text-sm leading-4 text-gray-400 border rounded" v-html="link.label" />
        <inertia-link v-else :key="key" class="mr-1 mb-1 px-4 py-3 text-sm leading-4 border border-gray-300 rounded hover:bg-white hover:border-gray-400 focus:border-gray-400 focus:text-gray-400" :class="{ 'bg-white': link.active }" :href="link.url" v-html="link.label" />
      </template>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    links: Array,
  },
}
</script>
