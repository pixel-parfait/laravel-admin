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

    <p class="help has-text-grey-dark" v-if="help">
      {{ help }}
    </p>

    <div class="control">
      <editor
        ref="editor"
        v-bind="{ ...$attrs, class: null }"
        :class="{ 'is-danger': error }"
        :id="id"
        v-model="value"
        :init="initOptions"
      />
    </div>

    <div v-if="error" class="help is-danger">{{ error }}</div>

    <media-library
      v-if="libraryOpen"
      :upload-enabled="true"
      mimes="image/jpeg, image/png, image/svg+xml"
      @close="libraryOpen = false"
      @saveSelection="saveLibrarySelection"
    />
  </div>
</template>

<script>
import { v4 as uuid } from 'uuid'
import tinymce from 'tinymce'
import 'tinymce/themes/silver'
import 'tinymce/icons/default'
import 'tinymce/models/dom'
import 'tinymce/plugins/advlist'
import 'tinymce/plugins/anchor'
import 'tinymce/plugins/autolink'
import 'tinymce/plugins/code'
import 'tinymce/plugins/fullscreen'
import 'tinymce/plugins/image'
import 'tinymce/plugins/lists'
import 'tinymce/plugins/link'
import 'tinymce/plugins/media'
// import 'tinymce/plugins/quickbars'
import 'tinymce/plugins/table'
import 'tinymce/plugins/wordcount'
import 'tinymce/plugins/searchreplace'
import 'tinymce/skins/ui/oxide/skin.css'
import Editor from '@tinymce/tinymce-vue'
import MediaLibrary from '@/Components/MediaLibrary.vue'
import contentCSS from '/resources/css/editor.css?inline'

export default {
  inheritAttrs: false,
  components: {
    Editor,
    MediaLibrary,
  },
  props: {
    id: {
      type: String,
      default() {
        return `editor-${uuid()}`
      },
    },
    help: {
      type: String,
      default: null,
    },
    error: String,
    label: String,
    modelValue: String,
    toolbar: {
      type: String,
      default: 'undo redo | bold italic underline | link | removeformat code',
    },
    plugins: {
      type: String,
      default: 'autolink link code',
    },
    styleFormats: {
      type: Array,
      default: [
        {
          title: 'Titre',
          items: [
            {
              title: 'Titre 1',
              format: 'h2',
            },
            {
              title: 'Titre 2',
              format: 'h3',
            },
            {
              title: 'Titre 3',
              format: 'h4',
            },
            {
              title: 'Titre 4',
              format: 'h5',
            },
            {
              title: 'Sur-titre',
              block: 'p',
              classes: ['suptitle'],
            },
          ],
        },
        { title: 'Paragraphe', block: 'p' },
        { title: 'Introduction', block: 'p', attributes: { class: 'intro' } },
        {
          title: 'Liste avec bordures',
          selector: 'ul',
          attributes: { class: 'bordered' },
        },
        {
          title: 'Tag',
          block: 'div',
          attributes: { class: 'tag' },
        },
      ],
    },
    height: {
      type: Number,
      default: 400,
    },
  },
  emits: ['update:modelValue'],
  computed: {
    value: {
      get() {
        return this.modelValue
      },
      set(value) {
        this.$emit('update:modelValue', value)
      },
    },
  },
  methods: {
    focus() {
      this.$refs.editor.focus()
    },
    select() {
      this.$refs.editor.select()
    },
    openLibrary() {
      this.libraryOpen = true
    },
    saveLibrarySelection(file) {
      tinymce.activeEditor.insertContent(file.thumbnail)

      this.libraryOpen = false
    },
  },
  data() {
    const openLibrary = this.openLibrary

    let toolbar = ''
    let plugins = ''

    if (this.toolbar == 'full') {
      toolbar =
        'undo redo | styles | layouts | bold italic underline forecolor | link anchor | alignleft aligncenter alignright | bullist numlist blockquote | library media | table | removeformat | code | fullscreen'
      plugins = 'autolink lists link anchor media image table code fullscreen'
    } else {
      toolbar = this.toolbar
      plugins = this.plugins
    }

    return {
      libraryOpen: false,
      libraryContext: null,
      initOptions: {
        height: this.height,
        menubar: false,
        skin: false,
        content_css: false,
        content_style: contentCSS,
        body_class: 'tinymce-editor content',
        plugins: plugins,
        toolbar: toolbar,
        paste_as_text: true,
        paste_block_drop: false,
        relative_urls: false,
        remove_script_host: false,
        convert_urls: true,
        style_formats: this.styleFormats,
        formats: {
          alignleft: {
            selector:
              'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,audio,video,image',
            classes: 'align-left',
          },
          aligncenter: {
            selector:
              'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,audio,video,image',
            classes: 'align-center',
          },
          alignright: {
            selector:
              'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,audio,video,image',
            classes: 'align-right',
          },
        },
        link_class_list: [
          { title: 'Normal', value: '' },
          { title: 'Bouton', value: 'button' },
        ],
        preview_styles:
          'font-family font-size font-weight font-style text-decoration text-transform color background-color border-top border-bottom border-radius padding display outline text-shadow',
        color_map: ['000000', 'Noir', 'ff614e', 'Rouge', '0065bd', 'Bleu'],
        custom_colors: false,
        image_dimensions: false,
        image_caption: true,
        image_class_list: [
          { title: 'Pleine largeur', value: '' },
          { title: 'Moyenne', value: 'medium' },
          { title: 'Petite', value: 'small' },
        ],
        // quickbars_insert_toolbar: false,
        oninit: 'setPlainText',
        setup: (editor) => {
          editor.ui.registry.addButton('library', {
            icon: 'image',
            tooltip: 'Insérer une image',
            onAction: () => {
              openLibrary()
            },
          })

          editor.on('focus', () => {
            editor.getContainer().classList.add('is-focused')
          })

          editor.on('blur', () => {
            editor.getContainer().classList.remove('is-focused')
          })
        },
      },
    }
  },
}
</script>
