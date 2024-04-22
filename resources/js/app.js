import { createSSRApp, h } from 'vue'
import { createInertiaApp, Link, Head } from '@inertiajs/vue3'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import { ZiggyVue } from 'ziggy-js'
import MetaTags from './Components/MetaTags.vue'
import Layout from './Layouts/Base.vue'
import { gsap } from 'gsap'
import { ScrollTrigger } from 'gsap/ScrollTrigger'

gsap.registerPlugin(ScrollTrigger)

import '../css/app.css';

// Uncomment if using Matomo
// router.on('navigate', (event) => {
//   if (typeof _paq !== 'undefined') {
//     _paq.push(['setCustomUrl', event.detail.page.url])
//     _paq.push(['trackPageView'])
//   }
// })

createInertiaApp({
  resolve: async name => {
    const page = await resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob("./Pages/**/*.vue"))

    page.default.layout = page.default.layout || Layout

    return page
  },
  title: title => title ? `${title} - Laravel` : "Laravel",
  setup({ el, App, props, plugin }) {
    const app = createSSRApp({ render: () => h(App, props) })
      .use(plugin)
      .use(ZiggyVue)
      .component('Link', Link)
      .component('Head', Head)
      .component('MetaTags', MetaTags)
      .provide('gsap', gsap)
      .provide('ScrollTrigger', ScrollTrigger)

    app.mount(el)
  },
})
