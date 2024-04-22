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

    <div
      ref="control"
      class="control has-datalist"
      :class="{ 'is-loading': loading, 'is-multiple': multiple }"
    >
      <input type="hidden" v-model="selected" />
      <input
        :id="id"
        type="text"
        ref="input"
        class="input"
        :class="{ 'is-danger': error }"
        :readonly="isReadonly"
        placeholder="Rechercher"
        v-bind="{ ...$attrs, class: null }"
        v-model="search"
        @keyup="!isReadonly ? getResults() : null"
        @keydown.esc="hideDatalist"
        @keydown.down="arrowDown"
        @keydown.up="arrowUp"
        @keydown.enter.prevent="
          !isReadonly && show_datalist
            ? addItem(datalist[datalist_counter])
            : null
        "
        autocomplete="off"
      />

      <button
        class="delete"
        type="button"
        v-if="search && !loading"
        @click="reset(true)"
      ></button>

      <ul class="datalist" v-if="show_datalist" :class="datalist_class">
        <template v-if="datalist.length">
          <li
            class="datalist-item"
            v-for="(item, index) in datalist"
            :key="index"
          >
            <a
              @click="addItem(item)"
              :class="{ 'is-active': index == datalist_counter }"
            >
              <span class="datalist-item-id">#{{ item.id }}</span>
              <span class="is-flex-grow-1">{{ item.title }}</span>
              <span class="tag is-small is-light" v-if="item.season">
                {{ item.season }}
              </span>
              <span class="tag is-small is-dark" v-if="models.length > 1">
                {{ item.model }}
              </span>
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

    <div class="datalist-selection" v-if="multiple">
      <template v-if="selected && selected.length">
        <!-- Sortable items -->
        <draggable
          v-if="sortable"
          :list="selected"
          tag="ul"
          item-key="id"
          handle=".tag-value"
          class="tags is-sortable"
        >
          <template #item="{ element, index }">
            <li class="tag is-rounded">
              <span class="tag-value">
                <template v-if="map[element]">
                  {{ map[element].title }}
                </template>
                <template v-else>
                  {{ element }}
                </template>
              </span>
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
            <span class="tag-value">
              <template v-if="map[element]">
                {{ map[element].escaped_title || map[element].title }}
              </template>
              <template v-else>
                {{ element }}
              </template>
            </span>
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
import throttle from 'lodash/throttle'
import axios from 'axios'
import draggable from 'vuedraggable'

export default {
  inheritAttrs: false,
  props: {
    id: {
      type: String,
      default() {
        return `relationship-input-${uuid()}`
      },
    },
    models: Array,
    error: String,
    label: String,
    modelValue: {
      type: [Array, String, Number],
      default: null,
    },
    help: {
      type: String,
      default: null,
    },
    sortable: {
      type: Boolean,
      default: false,
    },
    multiple: {
      type: Boolean,
      default: true,
    },
    showId: {
      type: Boolean,
      default: false,
    },
    queryParams: {
      type: Object,
      default: null,
    },
    exclude: {
      type: Array,
      default: [],
    },
  },
  emits: ['update:modelValue'],
  data() {
    let selected = this.modelValue

    if (!selected) {
      selected = this.multiple ? [] : {}
    }

    return {
      search: '',
      selected: selected,
      datalist: [],
      datalist_counter: 0,
      datalist_class: null,
      map: {},
      show_datalist: false,
      loading: false,
      polymorphic: this.models.length > 1,
    }
  },
  mounted() {
    this.initialize()
  },
  components: {
    draggable,
  },
  watch: {
    modelValue() {
      this.initialize()
    },
    show_datalist(shown) {
      if (shown) {
        window.addEventListener('scroll', this.handleScroll)
      } else {
        window.removeEventListener('scroll', this.handleScroll)
      }

      this.$nextTick(this.handleScroll)
    },
    selected: {
      handler(selected) {
        this.$emit('update:modelValue', selected)
      },
      deep: true,
    },
  },
  computed: {
    isReadonly() {
      if (this.multiple) {
        return false
      }

      return this.modelValue
    },
  },
  methods: {
    initialize() {
      // Resolve initial model value
      if (this.modelValue) {
        if (this.multiple) {
          this.modelValue.forEach((item, index) => {
            this.resolveModel(item, index)
          })
        } else {
          this.resolveModel(this.modelValue, 0)
        }
      }
    },
    getResults() {
      if (this.search == '') {
        this.datalist = []
        this.hideDatalist()
      } else {
        this.showDatalist()
        this.loading = true

        let exclude = this.exclude

        if (this.selected && this.selected.length) {
          if (this.multiple) {
            exclude = exclude.concat(this.selected)
          } else {
            exclude.push(this.selected)
          }
        }

        this.getThrottledResults(this.search, exclude)
      }
    },
    getThrottledResults: throttle(function (search, exclude) {
      axios
        .post('/admin/search', {
          search: search,
          models: this.models,
          exclude: exclude,
          params: this.queryParams,
        })
        .then((response) => {
          this.datalist = response.data
          this.loading = false
        })
    }, 1000),
    async resolveModel(id, index) {
      await axios
        .post('/admin/resolve', {
          id: id,
          model: this.polymorphic ? null : this.models[0],
        })
        .then((response) => {
          if (response.data) {
            this.map[id] = response.data

            // Set initial search value
            if (!this.multiple && this.selected !== undefined) {
              this.search = response.data.title

              if (this.showId) {
                this.search = `#${response.data.id} - ${this.search}`
              }
            }
          } else {
            // Remove from selection
            this.removeItem(index)
          }
        })
    },
    reset(removeSelection = false) {
      if (this.multiple) {
        this.search = ''
      } else {
        if (removeSelection) {
          this.selected = null
          this.search = ''
        }
      }

      this.datalist = []
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
      let id = this.polymorphic ? `${item.model}:${item.id}` : item.id

      if (this.multiple) {
        this.selected.push(id)
        this.map[id] = item
      } else {
        this.selected = id
        this.map[id] = item
        this.search = item.escaped_title || item.title
      }

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
