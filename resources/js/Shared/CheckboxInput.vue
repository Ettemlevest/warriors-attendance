<template>
  <div>
    <label>
      <input type="checkbox" :id="id" v-bind="$attrs" :class="{ error: errors.length }" :value="value" @change="change" />
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
    change() {
      this.checked = !this.checked
      this.$emit('input', this.checked)
    },
  },
}
</script>
