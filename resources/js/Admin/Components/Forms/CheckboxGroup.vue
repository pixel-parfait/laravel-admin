<template>
  <div class="field" :class="$attrs.class">
    <label
      v-if="label"
      class="label"
      :for="id"
      :class="{
        'is-required':
          $attrs.required !== undefined && $attrs.required !== false,
      }"
    >
      {{ label }}
    </label>

    <div :class="`grid grid-cols-${columns} gap-4`">
      <label
        v-for="(name, value) in options"
        :for="`${id}-${value}`"
        class="b-checkbox checkbox"
        :class="{
          'is-required':
            $attrs.required !== undefined && $attrs.required !== false,
        }"
      >
        <input
          :id="`${id}-${value}`"
          type="checkbox"
          v-model="selected"
          :value="value"
        />
        <span class="check"></span>
        <span class="control-label">{{ name }}</span>
      </label>
    </div>

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
        return `checkbox-group-${uuid()}`
      },
    },
    options: Object,
    error: String,
    label: String,
    modelValue: {
      type: Array,
      default: [],
    },
    help: {
      type: String,
      default: null,
    },
    columns: {
      type: Number,
      default: 3,
    },
  },
  emits: ['update:modelValue'],
  data() {
    return {
      selected: this.modelValue ? this.modelValue : [],
    }
  },
  watch: {
    selected(selected) {
      this.$emit('update:modelValue', selected)
    },
  },
}
</script>
