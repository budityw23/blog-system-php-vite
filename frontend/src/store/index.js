import { createStore } from 'vuex';
import auth from './modules/auth.js';
import users from './modules/users.js';

export default createStore({
  modules: {
    auth,
    users
  },
  strict: process.env.NODE_ENV !== 'production'
});
