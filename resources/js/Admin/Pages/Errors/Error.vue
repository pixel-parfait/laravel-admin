<template>
  <Head title="Erreur 500" />

  <section class="hero is-fullheight">
    <div class="hero-body">
      <div class="container">
        <div class="card auth-card">
          <div class="card-content has-text-centered">
            <div class="block">
              <a :href="route('home')">
                <Logo width="280" height="33" />
              </a>
            </div>

            <h1 class="title is-5">Erreur 500</h1>

            <p>Une erreur est survenue.</p>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
import Logo from '@/Components/Logo.vue'
import Layout from '@/Layout/Auth.vue'

export default {
  components: {
    Logo,
  },
  layout: Layout,
}
</script>
