<template>
  <div class="block">
    <div class="card has-table">
      <div class="card-content">
        <div class="has-pagination">
          <div class="table-wrapper">
            <table class="is-fullwidth table">
              <thead>
                <tr>
                  <th>{{ labels.title }} ({{ list.total }})</th>
                  <th width="200"></th>
                </tr>
              </thead>

              <tbody>
                <tr
                  v-for="item in list.data"
                  :key="item.id"
                  class="is-selectable"
                >
                  <td>
                    <span v-html="item.title"></span>
                  </td>
                  <td class="is-actions-cell">
                    <span>
                      <a
                        :href="route(routeName, { [routeParam]: item })"
                        target="_blank"
                        class="button is-xs is-transparent is-link"
                      >
                        Voir
                      </a>
                      <button
                        class="button is-xs is-transparent is-danger"
                        @click="$emit('detach', item.id)"
                      >
                        Détacher
                      </button>
                    </span>
                  </td>
                </tr>
                <tr v-if="list.data.length === 0">
                  <td colspan="2">{{ labels.empty }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <pagination class="is-small" :links="list.links" />
  </div>
</template>

<script>
import Pagination from '@/Components/Pagination.vue'
import { ChevronRightIcon } from '@heroicons/vue/24/outline'

export default {
  props: {
    list: Object,
    routeName: String,
    routeParam: String,
    labels: Object,
  },
  components: {
    Pagination,
    ChevronRightIcon,
  },
}
</script>
