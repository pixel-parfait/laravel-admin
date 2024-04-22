<template>
  <text-input
    v-model="placeholder"
    :error="error"
    :label="label"
    :help="help"
    v-bind="$attrs"
    disabled
  >
    <template #addons>
      <a class="button is-outlined" @click="editLink = true">Modifier</a>
    </template>
  </text-input>

  <Teleport to="#modals" v-if="editLink">
    <div class="modal is-active">
      <div class="modal-background" @click="editLink = false"></div>

      <div class="modal-card">
        <header class="modal-card-head is-block">
          <div class="level">
            <div class="level-left">
              <h2 class="title is-5">Éditer le lien</h2>
            </div>
          </div>
        </header>

        <div class="modal-card-body">
          <text-input v-model="link.url" type="url" label="URL" />
          <text-input v-model="link.title" label="Texte du lien" />
          <toggle-input
            v-model="link.external"
            label="Ouvrir dans un nouvel onglet"
          />

          <div class="field">
            <button class="button is-primary" type="button" @click="updateLink">
              Valider
            </button>
          </div>
        </div>
      </div>

      <button
        class="modal-close is-large"
        aria-label="close"
        @click="editLink = false"
      ></button>
    </div>
  </Teleport>
</template>

<script>
import { v4 as uuid } from 'uuid'
import TextInput from './TextInput.vue'
import ToggleInput from './ToggleInput.vue'

export default {
  inheritAttrs: false,
  props: {
    id: {
      type: String,
      default() {
        return `link-input-${uuid()}`
      },
    },
    help: {
      type: String,
      default: null,
    },
    error: String,
    label: String,
    modelValue: Object,
  },
  emits: ['update:modelValue'],
  components: {
    TextInput,
    ToggleInput,
  },
  data() {
    const link = this.modelValue || {
      url: null,
      title: null,
      external: false,
    }

    return {
      link: link,
      editLink: false,
    }
  },
  computed: {
    placeholder() {
      if (!this.link.url) {
        return null
      }

      if (!this.link.title) {
        return this.link.url
      }

      return `${this.link.title} (${this.link.url})`
    },
  },
  methods: {
    updateLink() {
      this.$emit('update:modelValue', this.link)
      this.editLink = false
    },
  },
}
</script>
