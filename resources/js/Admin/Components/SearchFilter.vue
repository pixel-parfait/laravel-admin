<template>
  <div class="is-flex is-align-items-center is-justify-space-between">
    <div class="search-filter">
      <div class="field has-addons mb-0">
        <p class="control" v-if="$slots.default">
          <dropdown
            :auto-close="false"
            :same-width="false"
            class="button is-rounded"
            placement="bottom-start"
          >
            <template #default>
              <span>Filtrer</span>
              <span class="icon is-small">
                <ChevronDownIcon />
              </span>
            </template>

            <template #dropdown>
              <div
                class="search-filter-dropdown"
                :style="{ maxWidth: `${maxWidth}px` }"
              >
                <slot />
              </div>
            </template>
          </dropdown>
        </p>

        <p class="control">
          <input
            class="input is-rounded"
            autocomplete="off"
            type="text"
            name="search"
            placeholder="Rechercher…"
            :value="modelValue"
            @input="$emit('update:modelValue', $event.target.value)"
          />
        </p>
      </div>

      <button
        v-if="isFiltered"
        class="button is-text has-text-dark is-small"
        type="button"
        @click="$emit('reset')"
      >
        Réinitialiser
      </button>
    </div>
  </div>
</template>

<script setup>
import Dropdown from '@/Components/Dropdown.vue'
import { ChevronDownIcon } from '@heroicons/vue/24/outline'

defineProps({
  modelValue: String,
  isFiltered: Boolean,
  maxWidth: {
    type: Number,
    default: 300,
  },
})

defineEmits(['update:modelValue', 'reset'])
</script>
