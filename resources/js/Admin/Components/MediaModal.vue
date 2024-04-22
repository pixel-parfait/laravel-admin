<template>
  <div class="modal media-modal is-large is-active">
    <div class="modal-background" @click="$emit('close')"></div>

    <div class="modal-card">
      <header class="modal-card-head is-block">
        <h2 class="title is-5">{{ media.name }}</h2>
      </header>

      <div class="modal-card-body">
        <div class="columns">
          <div class="column is-4">
            <figure class="image">
              <div v-html="media.thumbnail"></div>
            </figure>
          </div>

          <div class="column">
            <form @submit.prevent="updateMedia" novalidate>
              <div class="field">
                <label class="label">URL</label>

                <div class="notification px-3 py-2">
                  <p>
                    <a :href="media.url" target="_blank">
                      {{ media.url }}
                    </a>
                  </p>
                </div>
              </div>

              <textarea-input v-model="form.caption" label="Légende" rows="2" />

              <text-input
                v-if="media.type == 'image'"
                v-model="form.alt"
                label="Texte alternatif"
              />
            </form>
          </div>
        </div>
      </div>

      <footer class="modal-card-foot level is-justify-content-space-between">
        <div class="level-left">
          <button
            type="button"
            class="button is-primary"
            :class="{ 'is-loading': form.processing }"
            @click="updateMedia"
          >
            Enregistrer
          </button>

          <button type="button" class="button" @click="$emit('close')">
            Annuler
          </button>
        </div>

        <div class="level-right">
          <button
            type="button"
            class="button is-danger"
            :class="{ 'is-loading': form.processing }"
            @click="deleteMedia"
          >
            Supprimer
          </button>
        </div>
      </footer>
    </div>

    <button
      class="modal-close is-large"
      aria-label="Fermer"
      @click="$emit('close')"
    ></button>
  </div>
</template>

<script setup>
import { inject } from 'vue'
import { useForm } from '@inertiajs/vue3'
import TextInput from '@/Components/Forms/TextInput.vue'
import TextareaInput from '@/Components/Forms/TextareaInput.vue'

const props = defineProps({
  media: Object,
})

const emit = defineEmits(['close'])

const form = useForm(`EditMedia-${props.media.id}`, props.media)

const swalDanger = inject('swalDanger')

const updateMedia = () => {
  form.put(route('admin.media.update', { media: props.media.id }), {
    onSuccess: () => emit('close'),
  })
}

const deleteMedia = () => {
  swalDanger
    .fire({
      title: 'Suppression définitive',
      text: 'Voulez-vous vraiment supprimer définitivement ce fichier ? Cette action est irréversible.',
      confirmButtonText: 'Supprimer',
      focusCancel: true,
    })
    .then((result) => {
      if (result.isConfirmed) {
        form.delete(route('admin.media.destroy', { media: props.media.id }), {
          preserveState: false,
          onSuccess: () => {
            console.log('success')
          },
        })
      }
    })
}
</script>
