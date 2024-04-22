import { createApp, h } from 'vue'
import { createInertiaApp, Link, Head } from '@inertiajs/vue3'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from 'ziggy-js'
import Swal from 'sweetalert2/dist/sweetalert2.js'
import Layout from './Admin/Layout/Base.vue'
import Filters from './Admin/utils/Filters.js'

createInertiaApp({
  resolve: async name => {
    const page = await resolvePageComponent(`./Admin/Pages/${name}.vue`, import.meta.glob("./Admin/Pages/**/*.vue"))

    page.default.layout ??= Layout

    return page
  },
  title: title => title ? `${title} - Laravel` : 'Laravel - Administration',
  setup({ el, App, props, plugin }) {
    const app = createApp({ render: () => h(App, props) })
      .use(plugin)
      .use(ZiggyVue)
      .component('Link', Link)
      .component('Head', Head)
      .provide('$filters', Filters)

    const swalPrimary = Swal.mixin({
      customClass: {
        actions: 'buttons',
        cancelButton: 'button',
        confirmButton: 'button is-primary',
        denyButton: 'button is-danger',
        loader: 'is-loading',
      },
      buttonsStyling: false,
      showCancelButton: true,
      confirmButtonText: 'OK',
      cancelButtonText: 'Annuler',
    })

    const swalDanger = Swal.mixin({
      customClass: {
        actions: 'buttons',
        cancelButton: 'button',
        confirmButton: 'button is-danger',
        denyButton: 'button',
        loader: 'is-loading',
      },
      buttonsStyling: false,
      showCancelButton: true,
      confirmButtonText: 'OK',
      cancelButtonText: 'Annuler',
    })

    const toastSuccess = Swal.mixin({
      customClass: {
        actions: 'buttons',
        cancelButton: 'button',
        confirmButton: 'button is-success',
        denyButton: 'button',
        loader: 'is-loading',
      },
      buttonsStyling: false,
      toast: true,
      position: 'top-end',
      showConfirmButton: false,
      timer: 3000,
      timerProgressBar: true,
      didOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer)
        toast.addEventListener('mouseleave', Swal.resumeTimer)
      }
    })

    app.provide('swalPrimary', swalPrimary)
    app.provide('swalDanger', swalDanger)
    app.provide('toastSuccess', toastSuccess)

    app.mount(el)
  },
})
