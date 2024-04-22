<template>
  <draggable
    :list="modelValue"
    item-key="id"
    handle=".card-header-index,.drag-handle"
    class="block"
    @end="dragEnd"
  >
    <template #item="{ element, index }">
      <block
        v-if="layouts.hasOwnProperty(element.layout)"
        :index="index"
        :draggable="true"
        v-model:collapsed="element.collapsed"
        @remove="removeLayout"
        @toggle-all="toggleAll"
      >
        <template #title> {{ layouts[element.layout].label }} </template>

        <template #default>
          <input type="hidden" v-model="element.layout" />

          <component
            :is="layouts[element.layout].component"
            :key="`${element.layout}-${index}-${key}`"
            v-model="element.data"
          />
        </template>
      </block>
    </template>
  </draggable>

  <div class="layout-selection">
    <dropdown
      :auto-true="false"
      :arrow="true"
      class="button is-fullwidth"
      placement="top"
    >
      <template #default> Ajouter un bloc </template>

      <template #dropdown>
        <ul class="layout-selection-options">
          <li
            v-for="(layout, name) in layouts"
            :key="`layout-option-${name}`"
            @click="addLayout(name)"
          >
            {{ layout.label }}
          </li>
        </ul>
      </template>
    </dropdown>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { v4 as uuid } from 'uuid'
import draggable from 'vuedraggable'
import Dropdown from '@/Components/Dropdown.vue'
import Block from '@/Components/Block.vue'

const props = defineProps({
  modelValue: Object,
  layouts: Object,
})

const key = ref(uuid())
const collapseAll = ref(false)

const emit = defineEmits(['update:modelValue'])

const addLayout = (layout) => {
  let blocks = props.modelValue || []

  blocks.push({
    uuid: uuid(),
    layout: layout,
    data: JSON.parse(JSON.stringify(props.layouts[layout].default)),
    collapsed: false,
  })

  emit('update:modelValue', blocks)
}

const removeLayout = (index) => {
  props.modelValue.splice(index, 1)
}

const toggleAll = () => {
  collapseAll.value = !collapseAll.value

  props.modelValue.forEach((block) => {
    block.collapsed = collapseAll
  })
}

const dragEnd = () => {
  // Force subfields to re-render after dragging.
  // This prevents TinyMCE editors to freeze.
  key.value = uuid()
}
</script>
