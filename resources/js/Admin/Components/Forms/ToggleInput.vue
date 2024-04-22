<template>
  <div class="field" :class="$attrs.class">
    <label
      v-if="label && !inline"
      class="label"
      :for="id"
      :class="{
        'is-required':
          $attrs.required !== undefined && $attrs.required !== false,
      }"
    >
      {{ label }}
    </label>

    <p class="help has-text-grey-dark" v-if="help">
      {{ help }}
    </p>

    <label
      class="switch is-rounded"
      :for="id"
      :class="{
        'is-required':
          $attrs.required !== undefined && $attrs.required !== false,
        'is-disabled':
          $attrs.disabled !== undefined && $attrs.disabled !== false,
      }"
    >
      <input
        :id="id"
        ref="input"
        type="checkbox"
        :disabled="$attrs.disabled !== undefined && $attrs.disabled !== false"
        :checked="modelValue"
        @input="$emit('update:modelValue', $event.target.checked)"
      />
      <span class="check"></span>
      <span v-if="label && inline" class="control-label">{{ label }}</span>
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
        return `toggle-input-${uuid()}`
      },
    },
    help: {
      type: String,
      default: null,
    },
    error: String,
    label: String,
    modelValue: Boolean,
    inline: {
      type: Boolean,
      default: true,
    },
  },
  emits: ['update:modelValue'],
  methods: {
    focus() {
      this.$refs.input.focus()
    },
    select() {
      this.$refs.input.select()
    },
  },
}
</script>
