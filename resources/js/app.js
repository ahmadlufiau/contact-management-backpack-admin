import { createApp } from 'vue';
import { createPinia } from 'pinia';
import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from './stores/auth';
import { useContactsStore } from './stores/contacts';
import App from './views/App.vue';
import { Login, ContactList, ContactDetail } from './components';

const pinia = createPinia();

const router = createRouter({
  history: createWebHistory(),
  routes: [
    {
      path: '/login',
      name: 'login',
      component: Login,
      meta: { requiresGuest: true }
    },
    {
      path: '/',
      name: 'contacts',
      component: ContactList,
      meta: { requiresAuth: true }
    },
    {
      path: '/contacts/:id',
      name: 'contact-detail',
      component: ContactDetail,
      meta: { requiresAuth: true }
    }
  ]
});

// Navigation guard
router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore();
  
  if (to.meta.requiresAuth) {
    if (!authStore.token) {
      next('/login');
      return;
    }
    
    if (!authStore.user) {
      try {
        await authStore.getUser();
      } catch (error) {
        console.error('Failed to get user:', error);
        authStore.logout();
        next('/login');
        return;
      }
    }
    
    next();
  } else if (to.meta.requiresGuest && authStore.isAuthenticated) {
    next('/');
  } else {
    next();
  }
});

const app = createApp(App);

app.use(pinia);
app.use(router);

const authStore = useAuthStore();
authStore.initializeAuth();

app.mount('#app');
