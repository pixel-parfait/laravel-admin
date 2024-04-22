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

    <p class="help has-text-grey-dark" v-if="help" v-html="help"></p>

    <div class="control">
      <div class="field has-addons" :class="{ 'has-addons': $slots.addons }">
        <div
          class="control is-expanded"
          :class="{ 'has-icons-right': $slots.icon }"
        >
          <input
            :id="id"
            ref="input"
            class="input"
            :class="{ 'is-danger': error }"
            :type="type"
            :value="modelValue"
            :list="datalist ? `${id}-datalist` : null"
            v-bind="$attrs"
            @input="$emit('update:modelValue', $event.target.value)"
          />

          <datalist v-if="datalist" :id="`${id}-datalist`">
            <option v-for="item in datalist" :value="item">
              {{ item }}
            </option>
          </datalist>

          <span class="icon is-right" v-if="$slots.icon">
            <slot name="icon" />
          </span>
        </div>

        <div class="control" v-if="$slots.addons">
          <slot name="addons" />
        </div>
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
        return `text-input-${uuid()}`
      },
    },
    type: {
      type: String,
      default: 'text',
    },
    help: {
      type: String,
      default: null,
    },
    datalist: {
      type: Array,
      default: null,
    },
    error: String,
    label: String,
    modelValue: [String, Number, Date],
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
