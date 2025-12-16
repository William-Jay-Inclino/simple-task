import Toast, { type PluginOptions } from 'vue-toastification';
import 'vue-toastification/dist/index.css';


export default defineNuxtPlugin( (nuxtApp) => {

    const toastOptions: PluginOptions = {
        // You can set your default options here
    };

    nuxtApp.vueApp.use(Toast, toastOptions)
  })