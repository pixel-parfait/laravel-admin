<template>
  <div class="modal is-active">
    <div class="modal-background" @click.self="$emit('close')"></div>

    <div class="modal-content" style="overflow: visible">
      <div class="box">
        <form @submit.prevent="submit">
          <relationship-input
            v-model="form.replacement"
            :error="form.errors.replacement"
            :label="label"
            :models="[model]"
            :multiple="false"
            :exclude="[original]"
            :query-params="queryParams"
          />

          <div class="buttons is-right">
            <button type="button" class="button" @click="$emit('close')">
              Annuler
            </button>

            <button
              :disabled="form.processing"
              class="button is-primary"
              :class="{ 'is-loading': form.processing }"
            >
              <slot name="submit">Valider</slot>
            </button>
          </div>
        </form>
      </div>
    </div>

    <button
      class="modal-close is-large"
      aria-label="close"
      @click="$emit('close')"
    ></button>
  </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import RelationshipInput from '@/Components/Forms/RelationshipInput.vue'

const props = defineProps({
  model: String,
  original: Number,
  action: String,
  label: String,
  queryParams: {
    type: Object,
    default: null,
  },
})

const form = useForm({
  original: props.original,
  replacement: null,
})

const submit = () => {
  form.post(action, {
    preserveState: false,
  })
}
</script>
