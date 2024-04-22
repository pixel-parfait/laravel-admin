<template>
  <div class="field" :class="$attrs.class">
    <label
      v-if="label"
      class="label"
      :for="id"
      :class="{
        'is-required':
          $attrs.required !== undefined && $attrs.required !== false,
      }"
    >
      {{ label }}
    </label>

    <p class="help has-text-grey-dark">
      {{
        help ||
        "Copiez l'URL du contenu embarqué (par exemple : https://www.youtube.com/watch?v=XXXXX)"
      }}
    </p>

    <div class="control">
      <div class="field has-addons" :class="{ 'has-addons': $slots.addons }">
        <div
          class="control is-expanded"
          :class="{ 'is-loading': loading, 'has-icons-right': $slots.icon }"
        >
          <textarea
            :id="`${id}-url`"
            ref="input"
            class="textarea"
            rows="2"
            resize="none"
            :class="{ 'is-danger': error }"
            :value="embedObject.url"
            v-bind="$attrs"
            @input="updateUrl"
          />

          <span class="icon is-right" v-if="$slots.icon">
            <slot name="icon" />
          </span>
        </div>

        <div class="control" v-if="$slots.addons">
          <slot name="addons" />
        </div>
      </div>

      <toggle-input
        v-model="embedObject.auto_discover"
        label="Détection auto"
      />

      <textarea-input
        v-if="!embedObject.auto_discover"
        v-model="embedObject.iframe"
        label="Iframe"
        rows="2"
        resize="none"
      />

      <div
        v-else-if="embedError"
        class="embed-preview embed-preview-placeholder embed-preview-error"
      >
        <p class="help has-text-danger mb-2">{{ embedError }}</p>

        <button
          class="button is-small"
          @click="embedObject.auto_discover = false"
        >
          Saisir manuellement le code d'intégration
        </button>
      </div>

      <div
        v-else-if="embedObject.iframe"
        class="embed-preview"
        :class="{ 'is-loading': loading }"
        v-html="embedObject.iframe || null"
      ></div>

      <div v-else class="embed-preview embed-preview-placeholder">
        Entrez une URL dans le champ ci-dessus pour afficher la prévisualisation
      </div>
    </div>

    <div v-if="error" class="help is-danger">{{ error }}</div>
  </div>
</template>

<script>
import { v4 as uuid } from 'uuid'
import { debounce } from 'lodash-es'
import axios from 'axios'
import TextareaInput from './TextareaInput.vue'
import ToggleInput from './ToggleInput.vue'

export default {
  inheritAttrs: false,
  props: {
    id: {
      type: String,
      default() {
        return `embed-input-${uuid()}`
      },
    },
    help: {
      type: String,
      default: null,
    },
    error: String,
    label: String,
    modelValue: {
      type: Object,
      default: null,
    },
  },
  components: {
    TextareaInput,
    ToggleInput,
  },
  data() {
    return {
      loading: false,
      embedObject: {
        url: this.modelValue ? this.modelValue.url : null,
        iframe: this.modelValue ? this.modelValue.iframe : null,
        auto_discover: this.modelValue
          ? this.modelValue.auto_discover ?? true
          : true,
      },
      embedError: null,
    }
  },
  emits: ['update:modelValue'],
  methods: {
    updateUrl($event) {
      if (this.embedObject.auto_discover) {
        this.loading = true

        this.embedObject.url = $event.target.value

        this.getIframe()
      }
    },
    getIframe: debounce(function () {
      axios
        .post('/admin/oembed', {
          url: this.embedObject.url,
        })
        .then((response) => {
          this.embedObject.iframe = response.data.iframe
        })
        .catch((error) => {
          this.embedObject.iframe = null
          this.embedError = error.response.data.message
        })
        .finally(() => {
          this.loading = false

          this.$emit('update:modelValue', this.embedObject)
        })
    }, 1000),
  },
}
</script>
