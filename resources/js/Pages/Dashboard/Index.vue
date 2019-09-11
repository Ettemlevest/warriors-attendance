<template>
  <layout title="Dashboard">
    <h1 class="mb-8 font-bold text-3xl">Kezdőlap</h1>
    <p class="mb-12 leading-normal">Szia Warrior! Itt jelentkezhetsz a közelgő edzésekre.</p>
    <div>
      <inertia-link class="btn-indigo" href="/500">500 error</inertia-link>
      <inertia-link class="btn-indigo" href="/404">404 error</inertia-link>
    </div>
  </layout>
</template>

<script>
import Layout from '@/Shared/Layout'

export default {
  components: {
    Layout,
  },
}
</script>
