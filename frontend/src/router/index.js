import { createRouter, createWebHistory } from 'vue-router';
import store from '../store';

const routes = [
  {
    path: '/',
    name: 'Home',
    component: () => import('../views/Home.vue')
  },
  {
    path: '/login',
    name: 'Login',
    component: () => import('../views/Login.vue')
  },
  {
    path: '/admin',
    name: 'Admin',
    component: () => import('../views/Admin.vue'),
    meta: { requiresAuth: true, roles: ['admin'] }
  },
  {
    path: '/editor',
    name: 'Editor',
    component: () => import('../views/Editor.vue'),
    meta: { requiresAuth: true, roles: ['admin', 'editor'] }
  }
];

const router = createRouter({
  history: createWebHistory(),
  routes
});

router.beforeEach((to, from, next) => {
  if (to.meta.requiresAuth && !store.state.auth.isLoggedIn) {
    next('/login');
    return;
  }

  if (to.meta.roles && !to.meta.roles.includes(store.state.auth.user?.role)) {
    next('/');
    return;
  }

  next();
});

export default router;
