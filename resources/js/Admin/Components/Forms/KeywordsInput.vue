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

    <div class="control has-datalist is-multiple">
      <input type="hidden" v-model="selected" />
      <input
        :id="id"
        type="text"
        ref="input"
        class="input"
        :class="{ 'is-danger': error }"
        placeholder="Rechercher ou ajouter un tag"
        v-bind="{ ...$attrs, class: null }"
        v-model="search"
        @keydown.enter.prevent="addTag"
      />

      <button
        class="delete"
        type="button"
        v-if="search"
        @click="reset(true)"
      ></button>
    </div>

    <!-- <div class="datalist-selection">
      <template v-if="selected.length">
        <ul class="tags">
          <li v-for="(tag, index) in selected" :key="index" class="tag is-rounded">
            <span class="tag-value">{{ tag }}</span>
            <button class="delete" type="button" @click="removeTag(index)"></button>
          </li>
        </ul>
      </template>

      <div class="is-italic is-size-7" v-else>Aucune sélection</div>
    </div> -->

    <div class="datalist-selection">
      <template v-if="selected.length">
        <!-- Sortable items -->
        <draggable
          v-if="sortable"
          v-model="selected"
          tag="ul"
          item-key="id"
          handle=".tag-value"
          class="tags is-sortable"
        >
          <template #item="{ element, index }">
            <li class="tag is-rounded">
              <span class="tag-value">{{ element }}</span>
              <button
                class="delete"
                type="button"
                @click="removeTag(index)"
              ></button>
            </li>
          </template>
        </draggable>

        <!-- Non-sortable items -->
        <ul class="tags" v-else>
          <li
            v-for="(element, index) in selected"
            :key="index"
            class="tag is-rounded"
          >
            <span class="tag-value">{{ element }}</span>
            <button
              class="delete"
              type="button"
              @click="removeTag(index)"
            ></button>
          </li>
        </ul>
      </template>

      <div class="is-italic is-size-7" v-else>Aucune sélection</div>
    </div>

    <div v-if="error" class="help is-danger">{{ error }}</div>
  </div>
</template>

<script>
import { v4 as uuid } from 'uuid'
import draggable from 'vuedraggable'

export default {
  inheritAttrs: false,
  props: {
    id: {
      type: String,
      default() {
        return `keywords-input-${uuid()}`
      },
    },
    error: String,
    label: String,
    modelValue: {
      type: Array,
      default: null,
    },
    sortable: {
      type: Boolean,
      default: true,
    },
  },
  emits: ['update:modelValue'],
  data() {
    let selected = this.modelValue ? this.modelValue : []

    return {
      search: '',
      selected: selected,
    }
  },
  components: {
    draggable,
  },
  watch: {
    selected: {
      handler(selected) {
        this.$emit('update:modelValue', selected)
      },
      deep: true,
    },
  },
  methods: {
    reset() {
      this.search = ''
    },
    addTag() {
      this.selected.push(this.search)
      this.reset()
    },
    removeTag(index) {
      this.selected.splice(index, 1)
    },
  },
}
</script>
