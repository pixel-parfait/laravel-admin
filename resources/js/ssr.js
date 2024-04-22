import { createSSRApp, h } from 'vue'
import { renderToString } from '@vue/server-renderer'
import { createInertiaApp, Link, Head } from '@inertiajs/vue3'
import createServer from '@inertiajs/vue3/server'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import { ZiggyVue } from 'ziggy-js'
import MetaTags from './Components/MetaTags.vue'
import Layout from './Layouts/Base.vue'
import { gsap } from 'gsap'

import '../css/app.css';

createServer(page =>
  createInertiaApp({
    page,
    render: renderToString,
    resolve: async name => {
      const page = await resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob("./Pages/**/*.vue"))

      page.default.layout ??= Layout

      return page
    },
    title: title => title ? `${title} - Laravel` : "Laravel",
    setup({ App, props, plugin }) {
      return createSSRApp({ render: () => h(App, props) })
        .use(plugin)
        .use(ZiggyVue)
        .component('Link', Link)
        .component('Head', Head)
        .component('MetaTags', MetaTags)
        .provide('gsap', gsap)
    },
  }),
)
