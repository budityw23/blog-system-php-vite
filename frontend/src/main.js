import { createApp } from 'vue';
import App from './App.vue'; 
import router from './router';
import store from './store';
import Toast from 'vue-toastification';
import 'vue-toastification/dist/index.css';

const app = createApp(App);

// Initialize auth state before mounting the app
store.dispatch('auth/initAuth').finally(() => {
  app
    .use(store)
    .use(router)
    .use(Toast, {
      position: 'top-right',
      timeout: 3000,
      closeOnClick: true,
      pauseOnHover: true
    })
    .mount('#app');
});
