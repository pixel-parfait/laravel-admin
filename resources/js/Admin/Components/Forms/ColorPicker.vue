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

    <div ref="control" class="control color-picker has-datalist">
      <input type="hidden" v-model="selected" />
      <div
        class="input select"
        :class="{ 'is-danger': error }"
        v-bind="{ ...$attrs, class: null }"
        tabindex="1"
        @focus="showDatalist"
        @blur="hideDatalist"
        @keydown.esc="hideDatalist"
        @keydown.down="arrowDown"
        @keydown.up="arrowUp"
        @keydown.enter.prevent="show_datalist ? selectItem(active_index) : null"
      >
        <template v-if="selected && options && options[selected] !== undefined">
          <span
            class="color-picker-color"
            :style="{ 'background-color': options[selected].hex }"
          ></span>
          <span>{{ options[selected].name }}</span>
        </template>

        <template v-else>
          <span class="color-picker-color color-picker-none"></span>
          <span>Aucune</span>
        </template>
      </div>

      <ul class="datalist" v-if="show_datalist" :class="datalist_class">
        <li
          class="datalist-item"
          v-if="$attrs.required === undefined"
          :value="null"
        >
          <a
            @mousedown="selectItem(null)"
            :class="{ 'is-active': active_index == null }"
          >
            <span class="color-picker-color color-picker-none"></span>
            <span>Aucune</span>
          </a>
        </li>

        <li class="datalist-item" v-for="(item, index) in options" :key="index">
          <a
            @mousedown="selectItem(index)"
            :class="{ 'is-active': index == active_index }"
          >
            <span
              class="color-picker-color"
              :style="{ 'background-color': item.hex }"
            ></span>
            <span>{{ item.name }}</span>
          </a>
        </li>
      </ul>
    </div>

    <div v-if="error" class="help is-danger">{{ error }}</div>
  </div>
</template>

<script>
import { v4 as uuid } from 'uuid'
import axios from 'axios'

export default {
  inheritAttrs: false,
  props: {
    id: {
      type: String,
      default() {
        return `custom-select-input-${uuid()}`
      },
    },
    error: String,
    label: String,
    modelValue: {
      type: String,
      default: null,
    },
    help: {
      type: String,
      default: null,
    },
  },
  emits: ['update:modelValue'],
  data() {
    return {
      options: {},
      keys: [],
      active_index: null,
      selected: this.modelValue ? this.modelValue : null,
      datalist_counter: 0,
      datalist_class: null,
      show_datalist: false,
    }
  },
  mounted() {
    axios.get('/admin/colors').then((response) => {
      if (response.data) {
        this.options = response.data
        this.keys = Object.keys(response.data)
        this.active_index = this.keys[0]
      }
    })
  },
  watch: {
    show_datalist(shown) {
      if (shown) {
        window.addEventListener('scroll', this.handleScroll)
      } else {
        window.removeEventListener('scroll', this.handleScroll)
      }

      this.$nextTick(this.handleScroll)
    },
    selected(selected) {
      this.$emit('update:modelValue', selected)
    },
  },
  methods: {
    arrowDown() {
      if (this.datalist_counter < Object.keys(this.options).length - 1) {
        this.datalist_counter++
      } else {
        this.datalist_counter = 0
      }

      this.active_index = this.keys[this.datalist_counter]
    },
    arrowUp() {
      if (this.datalist_counter > 0) {
        this.datalist_counter--
      } else {
        this.datalist_counter = Object.keys(this.options).length - 1
      }

      this.active_index = this.keys[this.datalist_counter]
    },
    selectItem(item) {
      this.selected = item
      this.hideDatalist()
    },
    showDatalist() {
      this.show_datalist = true
    },
    hideDatalist() {
      this.show_datalist = false
    },
    handleScroll() {
      const rect = this.$refs.control.getBoundingClientRect()
      const windowHeight =
        window.innerHeight || document.documentElement.clientHeight

      if (rect.bottom > windowHeight - 350) {
        this.datalist_class = 'is-top'
      } else {
        this.datalist_class = null
      }
    },
  },
}
</script>
