<template>
  <aside class="aside is-placed-left is-expanded">
    <a class="aside-logo has-text-black" :href="route('home')" target="_blank">
      <Logo width="180" />
    </a>

    <div class="menu is-menu-main">
      <p class="menu-label">Administration</p>

      <ul class="menu-list">
        <li>
          <Link
            class="has-icon"
            :class="{ 'is-active': route().current('admin.dashboard') }"
            :href="route('admin.dashboard')"
          >
            <span class="icon">
              <HomeIcon />
            </span>
            <span class="menu-item-label">Tableau de bord</span>
          </Link>
        </li>

        <li>
          <Link
            class="has-icon"
            :class="{ 'is-active': route().current('admin.users.*') }"
            :href="route('admin.users.index')"
          >
            <span class="icon">
              <UsersIcon />
            </span>
            <span class="menu-item-label">Utilisateurs</span>
          </Link>
        </li>
      </ul>

      <p class="menu-label">Contenus</p>

      <ul class="menu-list">
        <li>
          <Link
            class="has-icon"
            :class="{ 'is-active': route().current('admin.library.*') }"
            :href="route('admin.library.index')"
          >
            <span class="icon">
              <PhotoIcon />
            </span>
            <span class="menu-item-label">Médiathèque</span>
          </Link>
        </li>
      </ul>
    </div>
  </aside>
</template>

<script setup>
import Logo from '@/Components/Logo.vue'
import { HomeIcon, PhotoIcon, UsersIcon } from '@heroicons/vue/24/outline'

defineProps({
  currentRoute: String,
  user: Object,
})
</script>
