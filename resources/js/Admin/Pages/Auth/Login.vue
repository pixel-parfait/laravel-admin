<template>
  <Head title="Login" />

  <section class="hero is-fullheight">
    <div class="hero-body">
      <div class="container">
        <div class="card auth-card">
          <div class="card-content">
            <div class="auth-card-logo">
              <a class="is-block has-text-black" :href="route('home')">
                <Logo />
              </a>
            </div>

            <form @submit.prevent="login">
              <text-input
                v-model="form.email"
                :error="form.errors.email"
                label="Email"
                type="email"
                autofocus
                autocapitalize="off"
                autocomplete="username"
                required
              />

              <text-input
                v-model="form.password"
                :error="form.errors.password"
                label="Password"
                type="password"
                autocomplete="current-password"
                required
              />

              <checkbox v-model="form.remember" label="Se souvenir de moi" />

              <button
                class="button is-primary is-fullwidth"
                :class="{ 'is-loading': form.processing }"
              >
                Connexion
              </button>

              <!-- <div class="level">
                <div class="level-left">
                  <Link class="is-size-7" :href="route('password.request')"> Mot de passe oublié ? </Link>
                </div>

                <div class="level-right">
                  <button class="button is-primary" :class="{ 'is-loading': form.processing }">Connexion</button>
                </div>
              </div> -->
            </form>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
import Layout from '@/Layout/Auth.vue'

export default {
  layout: Layout,
}
</script>

<script setup>
import { useForm } from '@inertiajs/vue3'
import Logo from '@/Components/Logo.vue'
import TextInput from '@/Components/Forms/TextInput.vue'
import Checkbox from '@/Components/Forms/Checkbox.vue'

const form = useForm('Login', {
  email: '',
  password: '',
  remember: true,
})

const login = () => {
  form.post('/admin/login')
}
</script>
