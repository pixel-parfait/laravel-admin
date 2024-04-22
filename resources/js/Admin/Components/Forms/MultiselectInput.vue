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

    <div
      ref="control"
      class="control has-datalist is-multiple"
      :class="{ 'is-loading': loading }"
    >
      <input type="hidden" v-model="selected" />
      <input
        :id="id"
        type="text"
        ref="input"
        class="input"
        :class="{ 'is-danger': error }"
        placeholder="Rechercher"
        v-bind="{ ...$attrs, class: null }"
        v-model="search"
        @focus="showDatalist"
        @blur="hideDatalist"
        @keydown.esc="hideDatalist"
        @keydown.down="arrowDown"
        @keydown.up="arrowUp"
        @keydown.enter.prevent="
          show_datalist ? addItem(datalist[datalist_counter].index) : null
        "
      />

      <button
        class="delete"
        type="button"
        v-if="search != '' && !loading"
        @click="reset"
      ></button>

      <ul class="datalist" v-if="show_datalist" :class="datalist_class">
        <template v-if="datalist.length">
          <li
            class="datalist-item"
            v-for="(item, index) in datalist"
            :key="index"
          >
            <a
              @mousedown="addItem(item.index)"
              :class="{ 'is-active': index == datalist_counter }"
            >
              {{ item.label }}
            </a>
          </li>
        </template>

        <template v-else>
          <li class="datalist-item is-italic has-text-grey">
            <span>Aucun résultat</span>
          </li>
        </template>
      </ul>
    </div>

    <div class="datalist-selection">
      <template v-if="selected.length">
        <!-- Sortable items -->
        <draggable
          v-if="sortable"
          v-model="selected"
          tag="ul"
          item-key="id"
          handle=".tag-value"
          class="tags is-sortable"
        >
          <template #item="{ element, index }">
            <li class="tag is-rounded">
              <span class="tag-value">{{ options[element] }}</span>
              <button
                class="delete"
                type="button"
                @click="removeItem(index)"
              ></button>
            </li>
          </template>
        </draggable>

        <!-- Non-sortable items -->
        <ul class="tags" v-else>
          <li
            v-for="(element, index) in selected"
            :key="index"
            class="tag is-rounded"
          >
            <span class="tag-value">{{ options[element] }}</span>
            <button
              class="delete"
              type="button"
              @click="removeItem(index)"
            ></button>
          </li>
        </ul>
      </template>

      <div class="is-italic is-size-7" v-else>Aucune sélection</div>
    </div>

    <div v-if="error" class="help is-danger">{{ error }}</div>
  </div>
</template>

<script>
import { v4 as uuid } from 'uuid'
import draggable from 'vuedraggable'
import { filter, map, kebabCase } from 'lodash-es'

export default {
  inheritAttrs: false,
  props: {
    id: {
      type: String,
      default() {
        return `multiselect-input-${uuid()}`
      },
    },
    options: Object,
    error: String,
    label: String,
    modelValue: {
      type: Array,
      default: [],
    },
    sortable: {
      type: Boolean,
      default: false,
    },
    help: {
      type: String,
      default: null,
    },
  },
  emits: ['update:modelValue'],
  data() {
    return {
      search: '',
      selected: this.modelValue ? this.modelValue : [],
      datalist_counter: 0,
      datalist_class: null,
      show_datalist: false,
      loading: false,
    }
  },
  components: {
    draggable,
  },
  computed: {
    datalist() {
      let optionsArray = map(this.options, (item, key) => ({
        index: key,
        label: item,
      }))

      return filter(optionsArray, (item) => {
        if (this.selected.map((item) => item.toString()).includes(item.index)) {
          return false
        }

        if (this.search) {
          return kebabCase(item.label).match(kebabCase(this.search))
        }

        return true
      })
    },
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
    reset() {
      this.search = ''
      this.datalist_counter = 0
      this.hideDatalist()
    },
    arrowDown() {
      if (this.datalist_counter < this.datalist.length - 1) {
        this.datalist_counter++
      } else {
        this.datalist_counter = 0
      }
    },
    arrowUp() {
      if (this.datalist_counter > 0) {
        this.datalist_counter--
      } else {
        this.datalist_counter = this.datalist.length - 1
      }
    },
    addItem(item) {
      this.selected.push(item)
      this.reset()
    },
    removeItem(index) {
      this.selected.splice(index, 1)
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
