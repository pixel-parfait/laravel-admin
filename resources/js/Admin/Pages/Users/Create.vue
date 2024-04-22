<template>
  <div>
    <Head title="Nouvel utilisateur" />

    <section class="section is-title-bar">
      <div class="level">
        <div class="level-left">
          <div class="level-item">
            <ul>
              <li>
                <Link :href="route('admin.users.index')"> Utilisateurs </Link>
              </li>
              <li>Nouvel utilisateur</li>
            </ul>
          </div>
        </div>
      </div>
    </section>

    <section class="section is-main-section">
      <form @submit.prevent="store" novalidate>
        <user-form :form="form" :options="options" />
      </form>
    </section>
  </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import { useCrud } from '@/composables/crud'
import UserForm from './Form.vue'

const props = defineProps({
  options: Object,
})

const form = useForm('CreateUser', {
  name: null,
  email: null,
  password: null,
  role: null,
})

const { store } = useCrud(form, {
  route: {
    prefix: 'admin.users',
  },
})
</script>
