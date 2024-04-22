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

    <p class="help has-text-grey-dark" v-if="help" v-html="help"></p>

    <div class="control">
      <div class="field has-addons">
        <div class="control is-expanded">
          <div class="file has-name">
            <label class="file-label">
              <input
                :id="id"
                ref="input"
                class="file-input"
                :class="{ 'is-danger': error }"
                type="file"
                v-bind="$attrs"
                :multiple="multiple"
                @change="
                  $emit(
                    'update:modelValue',
                    multiple ? $event.target.files : $event.target.files[0]
                  )
                "
              />
              <span class="file-cta">
                <span class="file-icon">
                  <ArrowUpOnSquareIcon />
                </span>
                <span class="file-label"> Parcourir </span>
              </span>
              <span class="file-name"> {{ selected }} </span>
            </label>
          </div>
        </div>
      </div>
    </div>

    <div v-if="error" class="help is-danger">{{ error }}</div>
  </div>
</template>

<script>
import { v4 as uuid } from 'uuid'
import { ArrowUpOnSquareIcon } from '@heroicons/vue/24/outline'

export default {
  inheritAttrs: false,
  props: {
    id: {
      type: String,
      default() {
        return `file-input-${uuid()}`
      },
    },
    help: {
      type: String,
      default: null,
    },
    multiple: {
      type: Boolean,
      default: false,
    },
    error: String,
    label: String,
    modelValue: {
      type: [File, FileList],
      default: null,
    },
  },
  emits: ['update:modelValue'],
  components: {
    ArrowUpOnSquareIcon,
  },
  computed: {
    selected() {
      const file = this.modelValue

      if (this.multiple) {
        if (!(file && file.length)) {
          return 'Aucun fichier sélectionné'
        }

        return file.length > 1 ? `${file.length} fichiers` : file[0].name
      }

      return file ? file.name : 'Aucun fichier sélectionné'
    },
  },
}
</script>
