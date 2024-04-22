<template>
  <div id="main" :class="{ 'has-aside-left has-aside-expanded': auth }">
    <div id="dropdown" />

    <nav id="navbar-main" class="navbar">
      <div class="navbar-brand">
        <a class="navbar-item is-hidden-desktop" @click="toggleSidebar">
          <span class="icon">
            <Bars3Icon />
          </span>
        </a>
      </div>

      <div class="navbar-brand is-right">
        <a class="navbar-item is-hidden-desktop" @click="toggleUserMenu">
          <span class="icon">
            <EllipsisVerticalIcon />
          </span>
        </a>
      </div>

      <div
        class="navbar-menu"
        id="navbar-menu"
        :class="{ 'is-active': userMenuOpen }"
      >
        <div class="navbar-end">
          <div class="navbar-item">
            <div class="is-user-name">
              <span>{{ auth.user.full_name }}</span>
            </div>

            <Link
              class="button is-text is-small"
              :href="route('admin.logout')"
              method="delete"
              as="button"
            >
              <span class="is-hidden-desktop">Déconnexion</span>

              <span class="icon">
                <PowerIcon />
              </span>
            </Link>
          </div>
        </div>
      </div>
    </nav>

    <sidebar :current-route="route().current()" :user="auth.user" />

    <div scroll-region>
      <flash-messages />
      <slot />
    </div>
  </div>
</template>

<script>
import Sidebar from './Sidebar.vue'
import FlashMessages from '@/Components/FlashMessages.vue'
import {
  ChevronDownIcon,
  PowerIcon,
  Bars3Icon,
  EllipsisVerticalIcon,
  UserIcon,
} from '@heroicons/vue/24/outline'

export default {
  components: {
    FlashMessages,
    Sidebar,
    ChevronDownIcon,
    PowerIcon,
    Bars3Icon,
    EllipsisVerticalIcon,
    UserIcon,
  },
  props: {
    auth: Object,
  },
  data() {
    return {
      userMenuOpen: false,
    }
  },
  methods: {
    toggleSidebar() {
      document.documentElement.classList.toggle('has-aside-mobile-expanded')
    },
    toggleUserMenu() {
      this.userMenuOpen = !this.userMenuOpen
    },
  },
}
</script>
