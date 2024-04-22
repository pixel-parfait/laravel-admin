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

    <div v-if="error" class="help is-danger">{{ error }}</div>

    <div class="control">
      <div class="subfields" v-if="items.length > 0">
        <draggable
          v-if="sortable"
          :list="items"
          item-key="uuid"
          handle=".subfield-handle"
        >
          <template #item="{ element, index }">
            <div class="subfield">
              <div class="subfield-handle">
                <span class="icon">
                  <ChevronUpDownIcon />
                </span>
              </div>

              <div class="subfield-content">
                <slot name="subfields" :element="element" :index="index"></slot>
              </div>

              <div class="subfield-actions">
                <div
                  class="icon button-icon is-danger"
                  role="button"
                  @click="removeItem(index)"
                >
                  <XCircleIcon />
                </div>
              </div>
            </div>
          </template>
        </draggable>

        <template v-else>
          <div v-for="(element, index) in items" :key="index" class="subfield">
            <div class="subfield-content">
              <slot name="subfields" :element="element" :index="index"></slot>
            </div>

            <div class="subfield-actions" v-if="editable">
              <div
                class="icon button-icon is-danger"
                role="button"
                @click="removeItem(index)"
              >
                <XCircleIcon />
              </div>
            </div>
          </div>
        </template>
      </div>

      <div class="buttons" v-if="editable">
        <button type="button" class="button is-small" @click="addItem">
          <slot name="add-item"> Ajouter un élément </slot>
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import { v4 as uuid } from 'uuid'
import draggable from 'vuedraggable'
import { ChevronUpDownIcon, XCircleIcon } from '@heroicons/vue/24/outline'
import { map } from 'lodash-es'

export default {
  inheritAttrs: false,
  props: {
    id: {
      type: String,
      default() {
        return `repeater-${uuid()}`
      },
    },
    label: String,
    modelValue: {
      type: Array,
      default: [],
    },
    sortable: {
      type: Boolean,
      default: false,
    },
    editable: {
      type: Boolean,
      default: true,
    },
    help: {
      type: String,
      default: null,
    },
    error: String,
    default: {
      type: Object,
      default: null,
    },
  },
  data() {
    let items = JSON.parse(JSON.stringify(this.modelValue))

    return {
      items: map(items, (item) => {
        return {
          uuid: uuid(),
          ...item,
        }
      }),
    }
  },
  watch: {
    items: {
      handler(items) {
        this.$emit('update:modelValue', items)
      },
      deep: true,
    },
  },
  emits: ['update:modelValue'],
  methods: {
    removeItem(index) {
      this.items.splice(index, 1)
    },
    addItem() {
      let attributes = {
        uuid: uuid(),
      }

      if (this.default) {
        attributes = {
          ...attributes,
          ...this.default,
        }
      }

      this.items.push(attributes)
    },
  },
  components: {
    draggable,
    ChevronUpDownIcon,
    XCircleIcon,
  },
}
</script>
