<template>
  <div>
    <Head title="Utilisateurs" />

    <section class="section is-title-bar">
      <div class="level">
        <div class="level-left">
          <div class="level-item">
            <ul>
              <li>Utilisateurs</li>
            </ul>
          </div>
        </div>

        <div class="level-right">
          <div class="level-item">
            <Link class="button is-primary" :href="route('admin.users.create')">
              <span class="is-hidden-mobile">Nouvel utilisateur</span>
              <span class="is-hidden-tablet">Ajouter</span>
            </Link>
          </div>
        </div>
      </div>
    </section>

    <section class="section is-main-section">
      <div class="level">
        <div class="level-left">
          <search-filter
            v-model="form.search"
            :is-filtered="form.search.length > 0"
            @reset="reset"
          />
        </div>
      </div>

      <div class="card has-table">
        <div class="card-content">
          <div class="has-pagination">
            <div class="table-wrapper">
              <table class="is-fullwidth is-sortable is-hoverable table">
                <thead>
                  <tr>
                    <th width="50">#</th>
                    <th>Nom</th>
                    <th width="280">Email</th>
                    <th width="280">Rôle</th>
                    <th width="200">Dernière connexion</th>
                    <th width="80"></th>
                  </tr>
                </thead>

                <tbody>
                  <tr
                    v-for="user in users.data"
                    :key="user.id"
                    class="is-selectable"
                  >
                    <td>
                      <Link :href="route('admin.users.edit', { user: user })">
                        {{ user.id }}
                      </Link>
                    </td>
                    <td>
                      <Link :href="route('admin.users.edit', { user: user })">
                        {{ user.name }}
                      </Link>
                    </td>
                    <td>
                      <Link :href="route('admin.users.edit', { user: user })">
                        {{ user.email }}
                      </Link>
                    </td>
                    <td>
                      <Link :href="route('admin.users.edit', { user: user })">
                        {{ user.roles }}
                      </Link>
                    </td>
                    <td>
                      <Link :href="route('admin.users.edit', { user: user })">
                        {{ $filters.formatDateTime(user.last_login_at) }}
                      </Link>
                    </td>
                    <td class="is-actions-cell">
                      <Link :href="route('admin.users.edit', { user: user })">
                        <span class="icon is-small">
                          <ChevronRightIcon />
                        </span>
                      </Link>
                    </td>
                  </tr>
                  <tr v-if="users.data.length === 0">
                    <td colspan="6">Aucun utilisateur trouvé.</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <pagination :links="users.links" />
    </section>
  </div>
</template>

<script setup>
import { ref, inject, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import pickBy from 'lodash/pickBy'
import throttle from 'lodash/throttle'
import Pagination from '@/Components/Pagination.vue'
import SearchFilter from '@/Components/SearchFilter.vue'
import { ChevronRightIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
  filters: Object,
  users: Object,
})

const form = ref({
  search: props.filters.search || '',
})

const $filters = inject('$filters')

const reset = () => {
  form.value.search = ''
}

watch(
  form,
  throttle(() => {
    router.get('/admin/users', pickBy(form.value), {
      preserveState: true,
    })
  }, 150),
  {
    deep: true,
  }
)
</script>
