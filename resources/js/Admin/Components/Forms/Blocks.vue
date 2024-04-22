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

    <draggable
      :list="items"
      item-key="uuid"
      handle=".card-header-index,.drag-handle"
      class="block"
    >
      <template #item="{ element, index }">
        <block
          :index="index"
          :draggable="true"
          v-model:collapsed="element.collapsed"
          @remove="removeItem(index)"
          @toggle-all="toggleAll"
        >
          <template #title>
            <slot name="title" :element="element" :index="index"></slot>
          </template>

          <template #default>
            <slot name="subfields" :element="element" :index="index"></slot>
          </template>
        </block>
      </template>
    </draggable>

    <button type="button" class="button" @click="addItem">
      <slot name="add-item"> Ajouter un élément </slot>
    </button>
  </div>
</template>

<script>
import { v4 as uuid } from 'uuid'
import draggable from 'vuedraggable'
import Block from '@/Components/Block.vue'
import { map } from 'lodash-es'

export default {
  inheritAttrs: false,
  props: {
    id: {
      type: String,
      default() {
        return `blocks-${uuid()}`
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
    help: {
      type: String,
      default: null,
    },
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
          collapsed: false,
          ...item,
        }
      }),
      collapseAll: false,
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
        collapsed: false,
      }

      if (this.default) {
        attributes = {
          ...attributes,
          ...this.default,
        }
      }

      this.items.push(attributes)
    },
    toggleAll() {
      this.collapseAll = !this.collapseAll

      this.items.forEach((item) => {
        item.collapsed = this.collapseAll
      })
    },
  },
  components: {
    draggable,
    Block,
  },
}
</script>
