<template>
  <div
    class="card"
    :class="{ 'is-collapsed': collapsed, 'is-draggable': draggable }"
  >
    <div class="card-header">
      <div class="card-header-title">
        <span class="card-header-index" v-if="!isNaN(index)">
          {{ index + 1 }}
        </span>
        <span class="is-flex-grow-1">
          <slot name="title" />
        </span>
      </div>

      <div class="card-header-title-end" v-if="draggable">
        <div class="icon button-icon drag-handle" role="button">
          <ChevronUpDownIcon />
        </div>
      </div>

      <div class="card-header-title-end">
        <div
          class="icon button-icon"
          role="button"
          @click.exact="$emit('update:collapsed', !collapsed)"
          @click.ctrl="$emit('toggleAll')"
          @click.meta="$emit('toggleAll')"
        >
          <PlusIcon v-if="collapsed" />
          <MinusIcon v-else />
        </div>
      </div>

      <div class="card-header-title-end" v-if="editable">
        <div
          class="icon button-icon is-danger"
          role="button"
          @click="$emit('remove', index)"
        >
          <XCircleIcon />
        </div>
      </div>
    </div>

    <div class="card-content" v-show="!collapsed">
      <slot />
    </div>
  </div>
</template>

<script setup>
import {
  XCircleIcon,
  PlusIcon,
  MinusIcon,
  ChevronUpDownIcon,
} from '@heroicons/vue/24/outline'

defineProps({
  index: Number,
  collapsed: Boolean,
  editable: {
    type: Boolean,
    default: true,
  },
  draggable: {
    type: Boolean,
    default: false,
  },
})

defineEmits(['update:collapsed', 'toggleAll', 'remove'])
</script>
