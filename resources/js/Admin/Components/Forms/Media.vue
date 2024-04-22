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
      <input type="hidden" ref="fileIds" :value="modelValue" />

      <file-pond
        name="file"
        ref="pond"
        credits=""
        class="has-library-button"
        :class="{ 'is-danger': error }"
        :server="server"
        :label-idle="
          multiple
            ? 'Sélectionnez ou déposez des fichiers'
            : 'Sélectionnez ou déposez un fichier'
        "
        label-invalid-field="Le champ contient des fichiers non valides"
        label-file-waiting-for-size="Calcul de la taille"
        label-size-not-available="Taille non disponible"
        label-file-loading="Chargement"
        label-file-load-error="Erreur lors du chargement"
        label-file-processing="Chargement en cours"
        label-file-processing-complete="Chargement terminé"
        label-file-processing-aborted="Chargement annulé"
        label-file-processing-error="Erreur lors du chargement"
        label-file-processing-revert-error="Erreur lors de la réinitialisation"
        label-file-remove-error="Erreur lors de la suppression"
        label-tap-to-cancel="Cliquez pour annuler"
        label-tap-to-retry="Cliquez pour réessayer"
        label-tap-to-undo="Cliquez pour annuler"
        label-button-remove-item="Supprimer"
        label-button-abort-item-load="Annuler"
        label-button-retry-item-load="Réessayer"
        label-button-abort-item-processing="Annuler"
        label-button-undo-item-processing="Annuler"
        label-button-retry-item-processing="Réessayer"
        label-button-process-item="Charger"
        :allow-multiple="multiple"
        :allow-reorder="multiple"
        :accepted-file-types="mimes"
        :files="files"
        :required="$attrs.required !== undefined"
        :image-edit-editor="imageEditor"
        :max-files="maxFiles"
        @updatefiles="handleFilesUpdated"
        @reorderfiles="handleFilesUpdated"
        @processfile="handleFileProcessed"
      />

      <button
        v-if="library"
        type="button"
        class="button is-fullwidth"
        @click="libraryOpen = true"
      >
        Ouvrir la médiathèque
      </button>
    </div>

    <div v-if="error" class="help is-danger">{{ error }}</div>

    <media-library
      v-if="library && libraryOpen"
      :multiple="multiple"
      :mimes="mimes"
      @close="libraryOpen = false"
      @saveSelection="saveLibrarySelection"
    />
  </div>
</template>

<script>
import { v4 as uuid } from 'uuid'
import vueFilePond from 'vue-filepond'
import MediaLibrary from '@/Components/MediaLibrary.vue'

import 'filepond/dist/filepond.min.css'
import 'filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css'
import 'filepond-plugin-media-preview/dist/filepond-plugin-media-preview.min.css'
import 'filepond-plugin-image-edit/dist/filepond-plugin-image-edit.css'

import FilePondPluginFileValidateType from 'filepond-plugin-file-validate-type'
import FilePondPluginImagePreview from 'filepond-plugin-image-preview'
import FilePondPluginMediaPreview from 'filepond-plugin-media-preview'
import FilePondPluginImageEdit from 'filepond-plugin-image-edit'

const FilePond = vueFilePond(
  FilePondPluginFileValidateType,
  FilePondPluginImagePreview,
  FilePondPluginMediaPreview,
  FilePondPluginImageEdit
)

export default {
  inheritAttrs: false,
  props: {
    id: {
      type: String,
      default() {
        return `media-input-${uuid()}`
      },
    },
    multiple: {
      type: Boolean,
      default: false,
    },
    help: {
      type: String,
      default: null,
    },
    mimes: {
      type: String,
      default: 'image/jpeg, image/png, image/svg+xml',
    },
    maxFiles: {
      type: Number,
      default: null,
    },
    library: {
      type: Boolean,
      default: true,
    },
    disk: {
      type: String,
      default: null,
    },
    path: {
      type: String,
      default: null,
    },
    error: String,
    label: String,
    modelValue: [Array, Number, String],
  },
  emits: ['update:modelValue'],
  data() {
    let files = []

    if (this.modelValue) {
      if (this.multiple) {
        files = this.modelValue.map((id) => {
          return {
            source: id,
            options: {
              type: 'local',
            },
          }
        })
      } else {
        files = [
          {
            source: this.modelValue,
            options: {
              type: 'local',
            },
          },
        ]
      }
    }

    let url = '/media'
    let args = []

    if (this.disk) {
      args.push(`disk=${this.disk}`)
    }

    if (this.path) {
      args.push(`path=${this.path}`)
    }

    if (args.length > 0) {
      url += '?' + args.join('&')
    }

    return {
      libraryOpen: false,
      files: files,
      server: {
        url: '/admin',
        process: {
          url: url,
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
              .content,
          },
          onload: (response) => response,
        },
        load: '/media/',
      },
      imageEditor: null,
      // imageEditor: {
      //   // Called by FilePond to edit the image
      //   // - should open your image editor
      //   // - receives file object and image edit instructions
      //   open: (file, instructions) => {
      //     // Open editor
      //     console.log(file, instructions)

      //     // this.$refs.imageEditor

      //     // const image = this.$refs.imageEditorImg
      //     // const cropper = new Cropper(image, {
      //     //   autoCrop: false,
      //     //   dragMode: 'move',
      //     //   aspectRatio: 16 / 9,
      //     //   crop(event) {
      //     //     console.log(event.detail.x)
      //     //     console.log(event.detail.y)
      //     //     console.log(event.detail.width)
      //     //     console.log(event.detail.height)
      //     //     console.log(event.detail.rotate)
      //     //     console.log(event.detail.scaleX)
      //     //     console.log(event.detail.scaleY)
      //     //   },
      //     // })
      //   },

      //   // Callback set by FilePond
      //   // - should be called by the editor when user confirms editing
      //   // - should receive output object, resulting edit information
      //   onconfirm: (output) => {},

      //   // Callback set by FilePond
      //   // - should be called by the editor when user cancels editing
      //   oncancel: () => {},

      //   // Callback set by FilePond
      //   // - should be called by the editor when user closes the editor
      //   onclose: () => {},
      // },
    }
  },
  components: {
    FilePond,
    MediaLibrary,
  },
  methods: {
    saveLibrarySelection(files) {
      if (this.multiple) {
        files.forEach((file) => {
          this.$refs.pond.addFile(file.id, {
            type: 'local',
          })
        })
      } else {
        this.$refs.pond.addFile(files.id, {
          type: 'local',
        })
      }

      this.libraryOpen = false
    },
    handleFilesUpdated(files) {
      let newValue = this.multiple ? [] : null

      files.forEach((file) => {
        if (file && file.serverId) {
          if (this.multiple) {
            newValue.push(parseInt(file.serverId))
          } else {
            newValue = parseInt(file.serverId)
          }
        }
      })

      this.$emit('update:modelValue', newValue)
    },
    handleFileProcessed(error, file) {
      let newValue = JSON.parse(JSON.stringify(this.modelValue))

      if (this.multiple) {
        newValue.push(parseInt(file.serverId))
      } else {
        newValue = parseInt(file.serverId)
      }

      this.$emit('update:modelValue', newValue)
    },
  },
}
</script>
