<template>
  <div
    class="badge"
    :class="{
      'is-success': availability === 2,
      'is-warning': availability === 1,
      'is-danger': availability === 0,
      'is-light': availability === null,
    }"
  ></div>
</template>

<script setup>
defineProps({
  availability: {
    type: Number,
    default: null,
  },
})
</script>
