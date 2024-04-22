<template>
  <nav
    class="pagination is-right"
    role="navigation"
    aria-label="pagination"
    v-if="links.length > 3"
  >
    <ul class="pagination-list">
      <template v-for="(link, key) in links">
        <span
          v-if="link.url === null"
          :key="key"
          class="pagination-ellipsis"
          v-html="link.label"
        />
        <Link
          v-else
          :key="`link-${key}`"
          class="pagination-link"
          :class="{ 'is-current': link.active }"
          :href="link.url"
          :preserve-scroll="preserveScroll"
          v-html="link.label"
        />
      </template>
    </ul>
  </nav>
</template>

<script>
export default {
  props: {
    links: Array,
    preserveScroll: {
      type: Boolean,
      default: false,
    },
  },
}
</script>
