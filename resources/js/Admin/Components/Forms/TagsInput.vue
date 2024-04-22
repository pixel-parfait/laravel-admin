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

    <div class="control has-datalist is-multiple">
      <input type="hidden" v-model="selected" />
      <input
        :id="id"
        type="text"
        ref="input"
        class="input"
        :class="{ 'is-danger': Object.keys(errors).length }"
        :placeholder="placeholder"
        v-bind="{ ...$attrs, class: null }"
        v-model="search"
        @keydown.enter.prevent="search ? addTag(search) : null"
      />

      <button
        class="delete"
        type="button"
        v-if="search"
        @click="reset(true)"
      ></button>
    </div>

    <div class="datalist-selection">
      <template v-if="selected.length">
        <ul class="tags">
          <li
            v-for="(tag, index) in selected"
            :key="`tag-${index}`"
            class="tag is-rounded"
            :class="{ 'is-danger': errors[`tag-${index}`] }"
          >
            <span class="tag-value">{{ tag }}</span>
            <button
              class="delete"
              type="button"
              @click="removeTag(index)"
            ></button>
          </li>
        </ul>
      </template>

      <div class="is-italic is-size-7" v-else>Aucune sélection</div>
    </div>

    <ul v-if="Object.keys(errors).length" class="help is-danger">
      <li v-for="(error, index) in errors" :key="`error-${index}`">
        {{ error }}
      </li>
    </ul>
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
        return `tags-input-${uuid()}`
      },
    },
    errors: Object,
    label: String,
    placeholder: {
      type: String,
      default: null,
    },
    modelValue: {
      type: Array,
      default: null,
    },
  },
  emits: ['update:modelValue'],
  data() {
    let selected = this.modelValue ? this.modelValue : []

    return {
      search: '',
      selected: selected,
    }
  },
  watch: {
    selected: {
      handler(selected) {
        this.$emit('update:modelValue', selected)
      },
      deep: true,
    },
  },
  methods: {
    reset() {
      this.search = ''
    },
    addTag(tag) {
      if (tag === undefined) {
        this.selected.push(this.search)
      } else {
        this.selected.push(tag)
      }

      this.reset()
    },
    removeTag(index) {
      this.selected.splice(index, 1)
    },
  },
}
</script>
