<template>
  <div>
    <Head title="Médiathèque" />

    <section class="section is-title-bar">
      <div class="level">
        <div class="level-left">
          <div class="level-item">
            <ul>
              <li>Médiathèque</li>
            </ul>
          </div>
        </div>
      </div>
    </section>

    <section class="section is-main-section">
      <div class="level">
        <div class="level-left">
          <search-filter
            v-model="form.search"
            :is-filtered="form.search != '' || form.type != null"
            @reset="reset"
          >
            <div class="field">
              <label class="label">Type</label>
              <div class="select">
                <select v-model="form.type">
                  <option :value="null">Tous</option>
                  <option value="image">Images</option>
                  <option value="video">Vidéos</option>
                  <option value="audio">Sons</option>
                  <option value="application/pdf">Documents PDF</option>
                  <option value="image/svg+xml">SVG</option>
                  <option value="other">Autres</option>
                </select>
              </div>
            </div>
          </search-filter>
        </div>

        <div class="level-right">
          <form @submit.prevent="upload">
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
                @change="upload"
              />
            </label>
          </form>
        </div>
      </div>

      <div class="card">
        <div class="card-content">
          <div class="columns is-mobile is-multiline" v-if="medias.data.length">
            <div
              class="column is-6-mobile is-4-tablet is-3-desktop is-2-widescreen"
              v-for="(media, index) in medias.data"
              :key="`media-${index}`"
            >
              <a class="library-file" @click="editMedia(media)">
                <figure class="image is-4by3">
                  <div v-html="media.thumbnail"></div>
                </figure>

                <div class="library-file-name">
                  {{ media.name }}
                </div>

                <div class="library-file-size">
                  {{ media.size }}
                </div>
              </a>
            </div>
          </div>

          <div class="has-text-centered" v-else>
            <p class="mb-4">La médiathèque est vide.</p>

            <form @submit.prevent="upload">
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
                  @change="upload"
                />
              </label>
            </form>
          </div>

          <pagination :links="medias.links" />
        </div>
      </div>
    </section>

    <Teleport to="#modals" v-if="activeMedia">
      <media-modal :media="activeMedia" @close="activeMedia = null" />
    </Teleport>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import axios from 'axios'
import pickBy from 'lodash/pickBy'
import throttle from 'lodash/throttle'
import MediaModal from '@/Components/MediaModal.vue'
import Pagination from '@/Components/Pagination.vue'
import SearchFilter from '@/Components/SearchFilter.vue'

const props = defineProps({
  filters: Object,
  medias: Object,
})

const form = ref({
  search: props.filters.search || '',
  type: props.filters.type,
})

const activeMedia = ref(null)
const uploadInput = ref(null)
const uploadProcessing = ref(false)

watch(
  form,
  throttle(() => {
    router.get('/admin/library', pickBy(form.value), {
      preserveState: true,
    })
  }, 2000),
  {
    deep: true,
  },
)

const reset = () => {
  form.value.search = ''
  form.value.type = null
}

const editMedia = (media) => {
  axios.get(route('admin.media.edit', { media: media.id })).then((response) => {
    activeMedia.value = response.data
  })
}

const upload = (event) => {
  uploadProcessing.value = true

  axios
    .post(
      '/admin/upload',
      {
        files: event.target.files,
      },
      {
        headers: {
          'Content-Type': 'multipart/form-data',
        },
      },
    )
    .then(() => router.reload())
    .finally(() => {
      uploadInput.value = null
      uploadProcessing.value = false
    })
}
</script>
