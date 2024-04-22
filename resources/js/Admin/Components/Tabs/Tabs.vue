<template>
  <div class="tabs card-content-tabs">
    <ul>
      <li
        v-for="tab in tabs"
        :key="tab.title"
        :class="{ 'is-active': tab.isActive }"
        @click="selectTab(tab)"
      >
        <a>{{ tab.title }}</a>
      </li>
    </ul>
  </div>

  <slot />
</template>

<script>
import { find } from 'lodash-es'

export default {
  data() {
    return {
      activeTab: 0,
      tabs: [],
    }
  },
  mounted() {
    let currentTab = null

    if (location.hash) {
      currentTab = find(this.tabs, ['slug', location.hash.replace('#', '')])
    }

    if (!currentTab) {
      currentTab = this.tabs[0]
    }

    this.selectTab(currentTab, false)
  },
  emits: ['change'],
  methods: {
    selectTab(selectedTab, replaceState) {
      this.activeTab = selectedTab

      if (replaceState !== false) {
        history.replaceState(history.state, '', `#${selectedTab.slug}`)
      }

      this.tabs.forEach((tab) => {
        tab.isActive = tab.slug == selectedTab.slug
      })

      this.$emit('change', selectedTab.slug)
    },
  },
}
</script>
