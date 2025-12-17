// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
    compatibilityDate: '2025-07-15',
    runtimeConfig: {
		public: {
			apiUrl: process.env.API_URL ?? 'http://localhost',
		}
    },
    app: {
		baseURL: '/simple-task/',
		head: {
			title: 'Simple Task',
			meta: [
				{ charset: 'utf-8' },
				{ name: 'viewport', content: 'width=device-width, initial-scale=1' },
				{ name: 'description', content: 'Simple Task - A simple app for managing tasks efficiently' },
				{ name: 'author', content: 'William Jay Inclino' },
			],
		}
    },
    modules: [
		'@nuxtjs/tailwindcss',
		'@pinia/nuxt',
		'@pinia-plugin-persistedstate/nuxt',
    ],
    ssr: true,
})