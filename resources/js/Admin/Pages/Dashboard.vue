<template>
  <Head title="Tableau de bord" />

  <section class="section is-title-bar">
    <div class="level">
      <div class="level-left">
        <div class="level-item">
          <ul>
            <li>Tableau de bord</li>
          </ul>
        </div>
      </div>
    </div>
  </section>

  <section class="section is-main-section">
    <div class="columns">
      <div class="column">
        <div class="card">
          <div class="card-header">
            <div class="card-header-title">
              <h2 class="title is-6">Cache</h2>
            </div>
          </div>

          <div class="card-content">
            <div class="buttons">
              <Link
                method="delete"
                :href="route('admin.cache.clear')"
                class="button"
                as="button"
              >
                Vider le cache
              </Link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>
