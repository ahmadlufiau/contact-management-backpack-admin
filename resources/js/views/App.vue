<template>
  <div id="app" class="min-h-screen bg-gray-100">
    <nav v-if="isAuthenticated" class="bg-white shadow-sm border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <div class="flex items-center">
            <h1 class="text-2xl font-bold">
              <span class="text-gray-800">Contact</span> <span class="text-gray-600">Management</span>
            </h1>
          </div>
          <div class="flex items-center space-x-4">
            <span class="text-sm text-gray-700">
              Welcome, {{ user?.name || 'User' }}
            </span>
            <button
              @click="logout"
              class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors"
            >
              Logout
            </button>
          </div>
        </div>
      </div>
    </nav>

    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
      <router-view />
    </main>

    <div
      v-if="loading"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
    >
      <div class="bg-white p-6 rounded-lg shadow-xl">
        <div class="flex items-center space-x-3">
          <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-purple-600"></div>
          <span class="text-gray-700">Loading...</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';

export default {
  name: 'App',
  setup() {
    const router = useRouter();
    const authStore = useAuthStore();
    const loading = ref(false);

    const isAuthenticated = computed(() => authStore.isAuthenticated);
    const user = computed(() => authStore.user);

    const logout = async () => {
      loading.value = true;
      try {
        await authStore.logout();
        router.push('/login');
      } catch (error) {
        console.error('Logout error:', error);
      } finally {
        loading.value = false;
      }
    };

    onMounted(async () => {
      const token = localStorage.getItem('token');
      if (token) {
        loading.value = true;
        try {
          await authStore.getUser();
        } catch (error) {
          console.error('Failed to get user:', error);
          localStorage.removeItem('token');
          router.push('/login');
        } finally {
          loading.value = false;
        }
      }
    });

    return {
      loading,
      isAuthenticated,
      user,
      logout,
    };
  },
};
</script>

<style>
/* Global styles */
body {
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

/* Custom scrollbar */
::-webkit-scrollbar {
  width: 6px;
}

::-webkit-scrollbar-track {
  background: #f1f1f1;
}

::-webkit-scrollbar-thumb {
  background: #c1c1c1;
  border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
  background: #a8a8a8;
}
</style> 