<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full">
      <div class="text-center mb-8">
        <h1 class="text-4xl font-bold">
          <span class="text-gray-800">Contact Management</span>
        </h1>
      </div>

      <div class="bg-white rounded-lg shadow-sm p-8">
        <h2 class="text-2xl font-bold text-gray-900 text-center mb-8">
          Login
        </h2>
        
        <form @submit.prevent="handleLogin" class="space-y-6">
          <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
              Email
            </label>
            <div class="relative">
              <input
                id="email"
                v-model="form.email"
                name="email"
                type="email"
                autocomplete="email"
                :class="[
                  'w-full px-3 py-2 border rounded-md text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 sm:text-sm',
                  errors.email ? 'border-red-300' : 'border-gray-300'
                ]"
                placeholder="Enter your email"
              />
              <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                <button type="button" class="w-6 h-6 bg-red-700 rounded flex items-center justify-center">
                  <span class="text-white text-xs">⋯</span>
                </button>
              </div>
            </div>
            <p v-if="errors.email" class="mt-1 text-sm text-red-600">
              {{ errors.email }}
            </p>
          </div>
          <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
              Password
            </label>
            <div class="relative">
              <input
                id="password"
                v-model="form.password"
                name="password"
                type="password"
                autocomplete="current-password"
                :class="[
                  'w-full px-3 py-2 border rounded-md text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 sm:text-sm',
                  errors.password ? 'border-red-300' : 'border-gray-300'
                ]"
                placeholder="Enter your password"
              />
              <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                <button type="button" class="w-6 h-6 bg-red-700 rounded flex items-center justify-center">
                  <span class="text-white text-xs">⋯</span>
                </button>
              </div>
            </div>
            <p v-if="errors.password" class="mt-1 text-sm text-red-600">
              {{ errors.password }}
            </p>
          </div>
          <div>
            <button
              type="submit"
              :disabled="loading"
              class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
            >
              <span v-if="loading" class="mr-2">
                <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white"></div>
              </span>
              {{ loading ? 'Signing in...' : 'Login' }}
            </button>
          </div>
          <div v-if="error" class="rounded-md bg-red-50 p-4">
            <div class="flex">
              <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
              </div>
              <div class="ml-3">
                <h3 class="text-sm font-medium text-red-800">
                  {{ error }}
                </h3>
              </div>
            </div>
          </div>
        </form>
      </div>

      <div class="mt-6 bg-blue-50 border border-blue-200 rounded-md p-4">
        <h4 class="text-sm font-medium text-blue-800 mb-2">Demo Credentials:</h4>
        <div class="text-sm text-blue-700">
          <p><strong>Email:</strong> test@example.com</p>
          <p><strong>Password:</strong> password</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, reactive } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../../stores/auth';

export default {
  name: 'Login',
  setup() {
    const router = useRouter();
    const authStore = useAuthStore();
    
    const loading = ref(false);
    const error = ref('');
    const errors = reactive({});
    
    const form = reactive({
      email: '',
      password: '',
    });

    const validateForm = () => {
      errors.email = '';
      errors.password = '';
      
      if (!form.email) {
        errors.email = 'Email is required';
      } else if (!/\S+@\S+\.\S+/.test(form.email)) {
        errors.email = 'Please enter a valid email address';
      }
      
      if (!form.password) {
        errors.password = 'Password is required';
      }
      
      return !errors.email && !errors.password;
    };

    const handleLogin = async () => {
      if (!validateForm()) {
        return;
      }

      loading.value = true;
      error.value = '';
      
      try {
        await authStore.login(form);
        router.push('/');
      } catch (err) {
        if (err.errors) {
          Object.keys(err.errors).forEach(key => {
            errors[key] = err.errors[key][0];
          });
        } else {
          error.value = err.message || 'Login failed. Please try again.';
        }
      } finally {
        loading.value = false;
      }
    };

    return {
      form,
      loading,
      error,
      errors,
      handleLogin,
    };
  },
};
</script> 