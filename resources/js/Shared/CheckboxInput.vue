<template>
  <div>
    <label>
      <input type="checkbox" :id="id" v-bind="$attrs" :class="{ error: errors.length }" :value="value" @input="$emit('input', $event.target.value)" />
      {{ label }}
    </label>
    <div v-if="errors.length" class="form-error">{{ errors[0] }}</div>
  </div>
</template>

<script>
export default {
  inheritAttrs: false,
  props: {
    id: {
      type: String,
      default() {
        return `checkbox-input-${this._uid}`
      },
    },
    type: {
      type: Boolean,
      default: false,
    },
    value: Boolean,
    label: String,
    errors: {
      type: Array,
      default: () => [],
    },
  },
  methods: {
    focus() {
      this.$refs.input.focus()
    },
    select() {
      this.$refs.input.select()
    },
    setSelectionRange(start, end) {
      this.$refs.input.setSelectionRange(start, end)
    },
  },
}
</script>
