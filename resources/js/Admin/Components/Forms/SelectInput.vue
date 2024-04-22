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

    <p class="help has-text-grey-dark" v-if="help">
      {{ help }}
    </p>

    <div class="control">
      <div class="select is-fullwidth" :class="{ 'is-danger': error }">
        <select :id="id" ref="input" v-model="selected" v-bind="$attrs">
          <option v-if="$attrs.required === undefined" :value="null">
            Sélectionnez une valeur
          </option>
          <slot />
        </select>
      </div>
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
        return `select-input-${uuid()}`
      },
    },
    help: {
      type: String,
      default: null,
    },
    error: String,
    label: String,
    modelValue: [String, Number, Boolean],
    default: {
      type: [String, Number, Boolean],
      default: null,
    },
  },
  emits: ['update:modelValue'],
  data() {
    return {
      selected: this.modelValue,
    }
  },
  mounted() {
    if (this.selected === null || this.selected === undefined) {
      this.selected = this.default || null
    }
  },
  watch: {
    modelValue(value) {
      this.selected = value
    },
    selected(selected) {
      this.$emit('update:modelValue', selected)
    },
  },
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
