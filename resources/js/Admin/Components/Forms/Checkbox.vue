<template>
  <div class="field" :class="$attrs.class">
    <label
      :for="id"
      class="b-checkbox checkbox"
      :class="{
        'is-required':
          $attrs.required !== undefined && $attrs.required !== false,
      }"
    >
      <input :id="id" type="checkbox" ref="checkbox" v-model="checked" />
      <span class="check"></span>
      <span v-if="label" class="control-label">{{ label }}</span>
    </label>

    <div v-if="error" class="help is-danger">{{ error }}</div>
  </div>
</template>


<script>
import { v4 as uuid } from 'uuid'

export default {
  inheritAttrs: false,
  props: {
    id: {
      type: String,
      default() {
        return `checkbox-${uuid()}`
      },
    },
    error: String,
    label: String,
    modelValue: Boolean,
  },
  emits: ['update:modelValue'],
  data() {
    return {
      checked: this.modelValue,
    }
  },
  watch: {
    checked(checked) {
      this.$emit('update:modelValue', checked)
    },
  },
}
</script>
