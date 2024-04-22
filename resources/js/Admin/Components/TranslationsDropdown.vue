<template>
  <div
    class="dropdown"
    :class="{ 'is-active': isActive }"
    @click="isActive = !isActive"
    v-click-outside="handleClickOutside"
  >
    <div class="dropdown-trigger">
      <button class="button is-outlined" type="button">
        <span class="icon">
          <TranslateIcon />
        </span>

        {{ supportedLocales[currentLocale].name }}

        <span class="icon is-small">
          <ChevronDownIcon />
        </span>
      </button>
    </div>

    <div class="dropdown-menu">
      <div class="dropdown-content">
        <Link
          v-for="(locale, code) in supportedLocales"
          :key="code"
          class="dropdown-item is-flex is-align-items-center is-justify-content-space-between"
          :class="{ 'is-active': currentLocale == code }"
          :href="route(route().current(), { ...route().params, locale: code })"
        >
          <span>{{ locale.name }}</span>

          <span
            class="icon is-small ml-3"
            :class="{ 'has-text-success': currentLocale != code }"
            v-if="translations.includes(code)"
          >
            <CheckCircleIcon />
          </span>
          <span
            class="icon is-small ml-3"
            :class="{ 'has-text-danger': currentLocale != code }"
            v-else
          >
            <XCircleIcon />
          </span>
        </Link>
      </div>
    </div>
  </div>
</template>

<script>
import { usePage } from '@inertiajs/vue3'
import {
  TranslateIcon,
  ChevronDownIcon,
  CheckCircleIcon,
  XCircleIcon,
} from '@heroicons/vue/24/outline'
import { computed } from 'vue'
import vClickOutside from 'click-outside-vue3'

export default {
  data() {
    return {
      isActive: false,
    }
  },
  setup() {
    const supportedLocales = computed(
      () => usePage().props.value.supportedLocales
    )

    return { supportedLocales }
  },
  props: {
    currentLocale: {
      type: String,
      default: 'fr',
    },
    translations: {
      type: Array,
      default: [],
    },
  },
  components: {
    Link,
    TranslateIcon,
    ChevronDownIcon,
    CheckCircleIcon,
    XCircleIcon,
  },
  directives: {
    clickOutside: vClickOutside.directive,
  },
  methods: {
    handleClickOutside() {
      this.isActive = false
    },
  },
}
</script>
