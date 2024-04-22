<template>
  <Teleport to="#modals" v-if="activeMedia">
    <media-modal :media="activeMedia" @close="activeMedia = null" />
  </Teleport>

  <Teleport to="#modals">
    <div class="modal library-modal is-large is-active">
      <div class="modal-background" @click="$emit('close')"></div>

      <div class="modal-card" v-if="library">
        <header class="modal-card-head is-block">
          <div class="level">
            <div class="level-left">
              <h2 class="title is-5">Médiathèque</h2>
            </div>

            <div class="level-right">
              <form v-if="uploadEnabled" @submit.prevent="upload">
                <label
                  for="upload-input"
                  class="button is-primary has-text-weight-normal"
                  :class="{ 'is-loading': uploadProcessing }"
                >
                  Ajouter des fichiers
                  <input
                    id="upload-input"
                    class="is-hidden"
                    type="file"
                    multiple
                    :accept="mimes"
                    @change="upload"
                  />
                </label>
              </form>

              <p class="control ml-4">
                <input
                  class="input is-rounded"
                  autocomplete="off"
                  type="text"
                  placeholder="Rechercher…"
                  v-model="search"
                />
              </p>
            </div>
          </div>
        </header>

        <div class="modal-card-body" ref="libraryCardBody">
          <div
            class="columns is-mobile is-multiline"
            v-if="library.data.length"
          >
            <div
              class="column is-6-mobile is-4-tablet is-3-desktop is-2-widescreen"
              v-for="(media, index) in library.data"
              :key="`media-${index}`"
            >
              <label :for="`file-${media.id}`" class="library-file">
                <input
                  type="checkbox"
                  v-if="multiple"
                  :id="`file-${media.id}`"
                  :value="media"
                  v-model="selectedFiles"
                  :disabled="mimes && !mimes.includes(media.mime_type)"
                />
                <input
                  type="radio"
                  v-else
                  :id="`file-${media.id}`"
                  :value="media"
                  v-model="selectedFiles"
                  :disabled="mimes && !mimes.includes(media.mime_type)"
                />

                <div>
                  <figure class="image is-4by3">
                    <div v-html="media.thumbnail"></div>

                    <span class="icon is-medium library-file-selected">
                      <CheckIcon />
                    </span>
                  </figure>

                  <div class="library-file-name">
                    {{ media.name }}
                  </div>

                  <div class="library-file-size">
                    {{ media.size }}
                  </div>
                </div>

                <button
                  type="button"
                  class="button is-small library-file-edit"
                  @click="editMedia(media)"
                >
                  <span class="icon is-small">
                    <PencilIcon />
                  </span>
                </button>
              </label>
            </div>
          </div>

          <p class="has-text-centered" v-else>Aucun résultat</p>
        </div>

        <footer class="modal-card-foot is-block">
          <div class="level">
            <div class="level-left">
              <nav
                class="pagination is-right"
                role="navigation"
                aria-label="pagination"
                v-if="library.links.length > 3"
              >
                <ul class="pagination-list">
                  <template v-for="(link, key) in library.links">
                    <span
                      v-if="link.url === null"
                      :key="key"
                      class="pagination-ellipsis"
                      v-html="link.label"
                    />
                    <button
                      v-else
                      type="button"
                      :key="`link-${key}`"
                      class="pagination-link"
                      :class="{ 'is-current': link.active }"
                      @click="loadLibrary(link.url)"
                      v-html="link.label"
                    ></button>
                  </template>
                </ul>
              </nav>
            </div>

            <div class="level-right">
              <button
                type="button"
                class="button is-primary"
                @click="$emit('saveSelection', selectedFiles)"
              >
                Sélectionner
              </button>
              <button type="button" class="button" @click="$emit('close')">
                Annuler
              </button>
            </div>
          </div>
        </footer>
      </div>

      <button
        class="modal-close is-large"
        aria-label="close"
        @click="$emit('close')"
      ></button>
    </div>
  </Teleport>
</template>

<script setup>
import { ref, watch, onMounted, nextTick } from 'vue'
import axios from 'axios'
import throttle from 'lodash/throttle'
import MediaModal from '@/Components/MediaModal.vue'
import { CheckIcon, PencilIcon } from '@heroicons/vue/24/outline'

defineProps({
  multiple: {
    type: Boolean,
    default: false,
  },
  uploadEnabled: {
    type: Boolean,
    default: false,
  },
  mimes: {
    type: String,
    default: null,
  },
})

const library = ref(null)
const selectedFiles = ref([])
const search = ref(null)
const uploadProcessing = ref(false)
const activeMedia = ref(null)
const libraryCardBody = ref(null)

const emit = defineEmits(['close', 'saveSelection'])

const loadLibrary = (url) => {
  if (url === undefined) {
    url = route('admin.library.index')
  }

  axios
    .get(url, {
      params: { search: search.value },
    })
    .then((response) => {
      library.value = response.data

      nextTick(() => {
        libraryCardBody.scrollTop = 0
      })
    })
}

const upload = (event) => {
  uploadProcessing.value = true

  axios
    .post(
      '/admin/upload',
      {
        files: event.target.files,
        sync: true,
      },
      {
        headers: {
          'Content-Type': 'multipart/form-data',
        },
      }
    )
    .then(() => loadLibrary())
    .finally(() => (uploadProcessing.value = false))
}

const editMedia = (media) => {
  axios.get(route('admin.media.edit', { media: media.id })).then((response) => {
    activeMedia.value = response.data
  })
}

onMounted(() => {
  loadLibrary()
})

watch(
  search,
  throttle(() => {
    loadLibrary()
  }, 500)
)
</script>
