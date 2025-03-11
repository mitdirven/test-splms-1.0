import open from "open";
import { colors } from "./config/tw";

// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  compatibilityDate: "2024-04-03",
  devtools: {
    enabled: false, //process.env.NODE_ENV == "development",
    timeline: {
      enabled: true,
    },
  },
  ssr: false,
  spaLoadingTemplate: "spa-loading-template.html",

  nitro: {
    preset: "static",
    prerender: {
      crawlLinks: false,
      ignore: [],
      routes: [],
    },
  },
  modules: [
    "@nuxt/ui",
    ["@pinia/nuxt", { autoImports: ["defineStore"] }],
    "@vite-pwa/nuxt",
    "@vueuse/nuxt",
    "@nuxt/image",
    "dayjs-nuxt",
  ],

  imports: {
    dirs: ["./stores"],
  },

  components: [
    { path: "~/components/layout", pathPrefix: false },
    { path: "~/components/sidebar", pathPrefix: false },
    { path: "~/components/nav", pathPrefix: false },
    { path: "~/components/pwa", pathPrefix: false },
    "~/components",
  ],

  app: {
    buildAssetsDir: "assets",
    head: {
      charset: "utf-8",
      viewport: "width=device-width, initial-scale=1",
      // viewport: "width=device-width, initial-scale=1, user-scalable=yes", // Use this to prevent scaling with touch devices
      link: [
        {
          rel: "apple-touch-icon",
          sizes: "180x180",
          href: "/favicons/apple-touch-icon.png",
          type: "image/png",
        },
        {
          rel: "icon",
          sizes: "32x32",
          href: "/favicons/favicon-32x32.png",
          type: "image/png",
        },
        {
          rel: "icon",
          sizes: "16x16",
          href: "/favicons/favicon-16x16.png",
          type: "image/png",
        },
      ],
    },
  },

  hooks: {
    listen(server, e) {
      open(e.url);
    },
  },

  runtimeConfig: {
    public: {
      baseUrl: process.env.NUXT_PUBLIC_APP_SERVER || "http://127.0.0.1:8000",
      product_name: process.env.NUXT_PUBLIC_PRODUCT_NAME || "",
      short_name: process.env.NUXT_PUBLIC_SHORT_NAME || "",
      description: process.env.NUXT_PUBLIC_DESCRIPTION || "",
      office_name: process.env.NUXT_PUBLIC_OFFICE_NAME || "",
      openweather_api_key: process.env.NUXT_OPENWEATHER_API_KEY || "",
      store: {
        encrypt: process.env.NUXT_STORE_ENCRYPT || "auto",
        salt: process.env.NUXT_STORE_SALT || "",
        prefix: process.env.NUXT_STORE_PREFIX || "",
      },
    },
  },

  postcss: {
    plugins: {
      tailwindcss: {},
      autoprefixer: {},
    },
  },

  devServer: {
    host: process.env.NUXT_DEV_HOST || "127.0.0.1",
    port: parseInt(process.env.NUXT_DEV_PORT || "8080"),
  },

  tailwindcss: {
    cssPath: ["~/assets/css/tailwind.scss", { injectPosition: "last" }],
    viewer: false,
  },

  pwa: {
    devOptions: {
      enabled: false, // Enabling devOptions breaks NUXT Devtools and tailwindcss viewer, Enable only if you want to test PWA in dev mode
      type: "module",
    },
    client: {
      installPrompt: true,
      periodicSyncForUpdates: 60,
    },
    workbox: {
      disableDevLogs: true,
      sourcemap: false,
      cleanupOutdatedCaches: true,
      globPatterns: [
        "**/*.{js,css,html,png,svg,ico,jpg,jpeg,webp,woff,woff2,ttf,otf,eot}",
      ],
      maximumFileSizeToCacheInBytes: 5 * 1024 ** 2,
      runtimeCaching: [
        {
          urlPattern: ({ url }) => {
            return url.pathname.startsWith("/api/maps/geojson");
          },
          handler: "CacheFirst",
          method: "GET",
          options: {
            cacheName: "api-n-cache",
            cacheableResponse: {
              statuses: [0, 200],
            },
          },
        },
      ],
    },
    includeAssets: [
      "favicons/favicon.ico",
      "favicons/apple-touch-icon.png",
      "favicons/masked-icon.svg",
    ],
    manifest: {
      name: process.env.NUXT_PUBLIC_PRODUCT_NAME,
      short_name: process.env.NUXT_PUBLIC_SHORT_NAME,
      description: process.env.NUXT_PUBLIC_DESCRIPTION,
      theme_color: "#ffffff",
      icons: [
        {
          src: "favicons/android-chrome-192x192.png",
          sizes: "192x192",
          type: "image/png",
        },
        {
          src: "favicons/android-chrome-512x512.png",
          sizes: "512x512",
          type: "image/png",
        },
      ],
      // screenshots: [
      //   {
      //     src: "source/image1.png",
      //     sizes: "640x320",
      //     type: "image/png",
      //     form_factor: "wide",
      //     label: "Wonder Widgets",
      //   },
      // ],
    },
  },

  vite: {
    css: {
      preprocessorOptions: {
        scss: {
          api: "modern-compiler", // or "modern"
        },
      },
    },

    optimizeDeps: {
      esbuildOptions: {
        target: "ESNext",
        supported: {
          "top-level-await": true,
        },
      },
      include: [],
    },

    build: {
      target: "ESNext",
      commonjsOptions: {
        include: ["node_modules/**"],
      },
      rollupOptions: {
        output: {
          entryFileNames: `assets/c/[hash].js`,
          chunkFileNames: `assets/e/[hash].js`,
          assetFileNames: `assets/[ext]/[name].[ext]`,
        },
      },
    },
  },

  ui: {
    prefix: "t",
    safelistColors: [...Object.keys(colors)],
  },

  icon: {
    // serverBundle: "local",
    // provider: "server",
    // localApiEndpoint: "/_icons",
    // clientBundle: {
    //   // list of icons to include in the client bundle
    //   icons: [],
    //   // scan all components in the project and include icons
    //   scan: true,
    //   // include all custom collections in the client bundle
    //   includeCustomCollections: true,
    // },
  },

  router: {
    options: {
      scrollBehaviorType: "smooth",
    },
  },

  /**
   * @see https://nuxt.com/modules/dayjs
   */
  dayjs: {
    plugins: ["utc", "timezone", "weekday", "isoWeek"],
    defaultTimezone: "Asia/Manila",
  },
});
