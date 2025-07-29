import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import axios from 'axios';

export const useAuthStore = defineStore('auth', () => {
  const user = ref(null);
  const token = ref(localStorage.getItem('token') || null);

  const isAuthenticated = computed(() => !!token.value);

  const login = async (credentials) => {
    try {
      const response = await axios.post('/api/login', credentials);
      
      if (response.data.success && response.data.data) {
        const { token: newToken, user: userData } = response.data.data;
        
        token.value = newToken;
        user.value = userData;
        localStorage.setItem('token', newToken);
        
        axios.defaults.headers.common['Authorization'] = `Bearer ${newToken}`;
        
        return response.data;
      } else {
        throw new Error(response.data.message || 'Login failed');
      }
    } catch (error) {
      throw error.response?.data || error.message;
    }
  };

  const logout = async () => {
    try {
      if (token.value) {
        const response = await axios.post('/api/logout');
        
        if (!response.data.success) {
          console.warn('Logout response indicates failure:', response.data.message);
        }
      }
    } catch (error) {
      console.error('Logout error:', error);
      if (error.response?.status === 401) {
        console.log('Token expired or invalid, clearing local state');
      }
    } finally {
      token.value = null;
      user.value = null;
      localStorage.removeItem('token');
      delete axios.defaults.headers.common['Authorization'];
    }
  };

  const getUser = async () => {
    try {
      const response = await axios.get('/api/user');
      
      if (response.data.success && response.data.data) {
        user.value = response.data.data.user;
        return response.data.data.user;
      } else {
        throw new Error(response.data.message || 'Failed to get user');
      }
    } catch (error) {
      throw error.response?.data || error.message;
    }
  };

  const initializeAuth = () => {
    const storedToken = localStorage.getItem('token');
    if (storedToken) {
      token.value = storedToken;
      axios.defaults.headers.common['Authorization'] = `Bearer ${storedToken}`;
    }
  };

  return {
    user,
    token,
    isAuthenticated,
    login,
    logout,
    getUser,
    initializeAuth,
  };
}); 