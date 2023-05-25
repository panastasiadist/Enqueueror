import { defineConfig } from 'vitepress'

// https://vitepress.dev/reference/site-config
export default defineConfig({
  title: "Enqueueror",
  description: "Supercharged WordPress CSS & JS Coding",
  base: '/Enqueueror/',
  themeConfig: {
    // https://vitepress.dev/reference/default-theme-config
    nav: [
      { text: 'Home', link: '/' },
      { text: 'Guide', link: '/guide/introduction' },
      { text: 'Examples', link: '/examples' },
      { text: 'Changelog', link: 'https://github.com/panastasiadist/Enqueueror/blob/main/CHANGELOG.md' },
    ],

    sidebar: [
      {
        text: 'Guide',
        items: [
          { text: 'Introduction', link: '/guide/introduction' },
          { text: 'Getting Started', link: '/guide/getting-started' },
          { text: 'Core Concepts', link: '/guide/core-concepts' },
          { text: 'Asset Naming', link: '/guide/asset-naming' },
          { text: 'Asset Preprocessing', link: '/guide/asset-preprocessing' },
          { text: 'Asset Header', link: '/guide/asset-header' },
          { text: 'Asset Dependencies', link: '/guide/asset-dependencies' },
          { text: 'Asset Loading', link: '/guide/asset-loading' }
        ]
      }
    ],

    socialLinks: [
      { icon: 'github', link: 'https://github.com/panastasiadist/Enqueueror' }
    ],

    search: {
      provider: 'local'
    },

    footer: {
      message: 'Released under the GPL v2+ License.',
      copyright: 'Copyright © 2023-present <a href="https://anastasiadis.me" title="Panagiotis (Panos) Anastasiadis" target="_blank">Panagiotis Anastasiadis</a>'
    },
  },
})
