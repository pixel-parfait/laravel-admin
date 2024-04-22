<template>
  <div>
    <div class="columns">
      <div class="column is-8">
        <div class="card">
          <div class="card-content">
            <text-input
              v-model="form.email"
              :error="form.errors.email"
              label="Email"
              type="email"
              required
            />

            <text-input
              v-model="form.name"
              :error="form.errors.name"
              label="Nom"
              required
            />

            <div class="columns is-fullhd is-align-items-flex-end">
              <div class="column">
                <text-input
                  v-model="form.password"
                  :error="form.errors.password"
                  label="Mot de passe"
                  :type="password_visible ? 'text' : 'password'"
                  autocomplete="new-password"
                  :required="!form.id"
                  :help="
                    form.id
                      ? 'Laissez vide pour conserver le mot de passe actuel'
                      : null
                  "
                >
                  <template #addons>
                    <a
                      class="button is-outlined"
                      @click="password_visible = !password_visible"
                    >
                      <span class="icon is-small">
                        <EyeSlashIcon v-if="password_visible" />
                        <EyeIcon v-else />
                      </span>
                    </a>
                  </template>
                </text-input>
              </div>

              <div class="column">
                <button type="button" class="button" @click="generatePassword">
                  <span class="icon is-small">
                    <KeyIcon />
                  </span>
                  <span>Générer un mot de passe</span>
                </button>
              </div>
            </div>

            <select-input
              v-if="form.id !== auth_user.id"
              v-model="form.role"
              :error="form.errors.role"
              label="Rôle"
              default="super_user"
              required
            >
              <option v-for="(name, value) in options.roles" :value="value">
                {{ name }}
              </option>
            </select-input>
          </div>
        </div>
      </div>

      <div class="column">
        <div class="is-sticky">
          <div class="card">
            <div class="card-content">
              <button
                :disabled="form.processing"
                class="button is-primary is-fullwidth"
                :class="{ 'is-loading': form.processing }"
              >
                <slot title="submit">Enregistrer</slot>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { usePage } from '@inertiajs/vue3'
import TextInput from '@/Components/Forms/TextInput.vue'
import SelectInput from '@/Components/Forms/SelectInput.vue'
import { KeyIcon, EyeIcon, EyeSlashIcon } from '@heroicons/vue/24/outline'

const auth_user = usePage().props.auth.user
const password_visible = ref(false)

defineProps({
  key: {
    type: Number,
    default: null,
  },
  form: Object,
  options: Object,
})

const generatePassword = () => {
  const charSet =
    'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789![]{}()%&*$#^<>~@|'

  let password = ''

  for (let i = 0; i < 16; i++) {
    password += charSet.charAt(Math.floor(Math.random() * charSet.length))
  }

  this.form.password = password
  this.password_visible.value = true
}
</script>
