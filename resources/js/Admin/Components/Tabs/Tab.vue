<template>
  <div v-show="isActive">
    <slot></slot>
  </div>
</template>

<script>
import { kebabCase } from 'lodash-es'

export default {
  props: {
    title: {
      type: String,
      default: 'Tab',
    },
  },
  data() {
    return {
      isActive: false,
      slug: kebabCase(this.title),
    }
  },
  created() {
    this.$parent.tabs.push(this)
  },
}
</script>
