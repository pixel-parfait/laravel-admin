import { defineConfig, loadEnv } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import { VitePWA } from 'vite-plugin-pwa';
import { run } from 'vite-plugin-run';

export default defineConfig(({ mode }) => {
  process.env = { ...process.env, ...loadEnv(mode, process.cwd()) };

  return {
    plugins: [
      laravel({
        input: ['resources/js/app.js'],
        ssr: 'resources/js/ssr.js',
        refresh: true,
      }),
      vue({
        template: {
          transformAssetUrls: {
            // The Vue plugin will re-write asset URLs, when referenced
            // in Single File Components, to point to the Laravel web
            // server. Setting this to `null` allows the Laravel plugin
            // to instead re-write asset URLs to point to the Vite
            // server instead.
            base: `http://${process.env.VITE_LOCAL_IP_ADDRESS}:8000`,

            // The Vue plugin will parse absolute URLs and treat them
            // as absolute paths to files on disk. Setting this to
            // `false` will leave absolute URLs un-touched so they can
            // reference assets in the public directory as expected.
            includeAbsolute: false,
          },
        },
      }),
      VitePWA({
        registerType: 'autoUpdate',
      }),
      run([
        {
          name: 'generate routes manifests',
          run: ['php', 'artisan', 'app:routes'],
          condition: (file) => file.includes('/routes/'),
        }
      ]),
    ],
    resolve: {
      alias: {
        '@': '/resources/js',
        'ziggy': '/vendor/tightenco/ziggy/dist/vue.es.js',
      },
    },
    server: {
      host: process.env.VITE_LOCAL_IP_ADDRESS,
      hmr: {
        host: process.env.VITE_LOCAL_IP_ADDRESS
      },
    }
  }
});
