<template>
  <div>
    <Head :title="`Utilisateurs / ${user.name}`" />

    <section class="section is-title-bar">
      <div class="level">
        <div class="level-left">
          <div class="level-item">
            <ul>
              <li>
                <Link :href="route('admin.users.index')"> Utilisateurs </Link>
              </li>
              <li>{{ user.name }}</li>
            </ul>
          </div>
        </div>

        <div class="level-right">
          <div class="level-item">
            <div class="buttons">
              <button
                v-if="auth_user.id != user.id"
                class="button is-danger"
                tabindex="-1"
                type="button"
                @click="destroy"
              >
                Supprimer
              </button>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="section is-main-section">
      <form class="block" @submit.prevent="update" novalidate>
        <user-form :key="user.id" :form="form" :options="options">
          <template #submit> Mettre à jour </template>
        </user-form>
      </form>
    </section>
  </div>
</template>

<script>
import { computed } from 'vue'
import { usePage, useForm } from '@inertiajs/vue3'
import { useCrud } from '@/composables/crud'
import UserForm from './Form.vue'
import { ExclamationCircleIcon } from '@heroicons/vue/24/outline'

export default {
  components: {
    UserForm,
    ExclamationCircleIcon,
  },
  props: {
    user: Object,
    options: Object,
  },
  remember: 'form',
  inject: ['swalDanger'],
  setup(props) {
    const form = useForm(`EditUser-${props.user.id}`, {
      ...props.user,
    })

    const auth_user = computed(() => usePage().props.auth.user)

    const { update, destroy } = useCrud(form, {
      route: {
        prefix: 'admin.users',
        params: { user: props.user },
      },
      messages: {
        destroy: 'Voulez-vous vraiment supprimer cet utilisateur ?',
      },
    })

    return { form, auth_user, update, destroy }
  },
}
</script>
