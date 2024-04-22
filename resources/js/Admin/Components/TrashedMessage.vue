<template>
  <div class="notification is-warning">
    <div class="level">
      <div class="level-left">
        <span class="icon mr-2">
          <TrashIcon />
        </span>

        <p>
          <slot />
        </p>
      </div>

      <div class="level-right">
        <div class="buttons">
          <button
            class="button is-black is-outlined is-small"
            tabindex="-1"
            type="button"
            @click="$emit('restore')"
          >
            Restaurer
          </button>

          <button
            class="button is-danger is-small"
            tabindex="-1"
            type="button"
            @click="$emit('forceDelete')"
          >
            Supprimer définitivement
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { TrashIcon } from '@heroicons/vue/24/outline'

export default {
  components: {
    TrashIcon,
  },
  emits: ['restore', 'forceDelete'],
}
</script>
