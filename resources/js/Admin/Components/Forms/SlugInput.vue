<template>
  <text-input
    v-model="slug"
    :error="error"
    :label="label"
    v-bind="$attrs"
    :disabled="!edit"
    required
    @keydown="handleKeydown"
  >
    <template #addons>
      <a
        class="button is-success is-outlined"
        v-if="edit"
        @click="validateSlug"
      >
        <span class="icon is-small">
          <CheckIcon />
        </span>
      </a>
      <a class="button is-outlined" v-else @click="edit = true">Modifier</a>
    </template>
  </text-input>
</template>

<script>
import TextInput from './TextInput.vue'
import { CheckIcon } from '@heroicons/vue/24/outline'
import { slug } from '@/utils/slug'

export default {
  inheritAttrs: false,
  props: {
    label: {
      type: String,
      default: 'Slug',
    },
    help: {
      type: String,
      default: null,
    },
    text: String,
    error: String,
    modelValue: String,
    locked: Boolean,
  },
  components: {
    TextInput,
    CheckIcon,
  },
  emits: ['update:modelValue'],
  watch: {
    text(text) {
      if (!this.lock && !this.edit) {
        this.slug = slug(text)

        this.$emit('update:modelValue', this.slug)
      }
    },
  },
  data() {
    return {
      edit: false,
      lock: this.locked,
      slug: this.modelValue,
    }
  },
  methods: {
    handleKeydown(ev) {
      if (!/[a-z0-9\-]+/.test(ev.key)) {
        ev.preventDefault()
      }

      if (ev.key == 'Enter') {
        ev.preventDefault()
        this.validateSlug()
      }
    },
    validateSlug() {
      this.$emit('update:modelValue', this.slug)

      this.edit = false
      this.lock = true
    },
  },
}
</script>
