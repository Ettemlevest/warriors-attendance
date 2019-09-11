<template>
  <div>
    <div class="mb-4">
      <inertia-link class="flex items-center group py-3" :href="route('dashboard')">
        <icon name="dashboard" class="w-4 h-4 mr-2" :class="isUrl('') ? 'fill-white' : 'fill-indigo-light group-hover:fill-white'" />
        <div :class="isUrl('') ? 'text-white' : 'text-indigo-lighter group-hover:text-white'">Dashboard</div>
      </inertia-link>
    </div>
    <div class="mb-4">
      <inertia-link class="flex items-center group py-3" :href="route('reports')">
        <icon name="printer" class="w-4 h-4 mr-2" :class="isUrl('reports') ? 'fill-white' : 'fill-indigo-light group-hover:fill-white'" />
        <div :class="isUrl('reports') ? 'text-white' : 'text-indigo-lighter group-hover:text-white'">Reports</div>
      </inertia-link>
    </div>
  </div>
</template>

<script>
import Icon from '@/Shared/Icon'

export default {
  components: {
    Icon,
  },
  methods: {
    isUrl(...urls) {
      if (urls[0] === '') {
        return location.pathname.substr(1) === ''
      }

      return urls.filter(url => location.pathname.substr(1).startsWith(url)).length
    },
  },
}
</script>
