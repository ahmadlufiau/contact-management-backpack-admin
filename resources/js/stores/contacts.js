import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import axios from 'axios';

export const useContactsStore = defineStore('contacts', () => {
  const contacts = ref([]);
  const currentContact = ref(null);
  const loading = ref(false);
  const successMessage = ref('');
  const errorMessage = ref('');
  const pagination = ref({
    current_page: 1,
    last_page: 1,
    per_page: 15,
    total: 0,
  });
  const filters = ref({
    search: '',
    company: '',
    sort_by: 'created_at',
    sort_order: 'desc',
    page: 1,
  });

  const hasContacts = computed(() => contacts.value.length > 0);
  const totalPages = computed(() => pagination.value.last_page);

  const setMessage = (type, message, duration = 3000) => {
    if (type === 'success') {
      successMessage.value = message;
      if (duration > 0) {
        setTimeout(() => {
          successMessage.value = '';
        }, duration);
      }
    } else if (type === 'error') {
      errorMessage.value = message;
      if (duration > 0) {
        setTimeout(() => {
          errorMessage.value = '';
        }, duration);
      }
    }
  };

  const fetchContacts = async (params = {}) => {
    loading.value = true;
    try {
      const queryParams = new URLSearchParams({
        ...filters.value,
        ...params,
      }).toString();

      const response = await axios.get(`/api/contacts?${queryParams}`);
      
      if (response.data.success) {
        contacts.value = response.data.data;
        pagination.value = response.data.meta;
        return response.data;
      } else {
        throw new Error(response.data.message || 'Failed to fetch contacts');
      }
    } catch (error) {
      console.error('Error fetching contacts:', error);
      const message = error.response?.data?.message || error.message || 'Failed to fetch contacts';
      setMessage('error', message);
      throw error.response?.data || error.message;
    } finally {
      loading.value = false;
    }
  };

  const fetchContact = async (id) => {
    loading.value = true;
    try {
      const response = await axios.get(`/api/contacts/${id}`);
      
      if (response.data.success) {
        currentContact.value = response.data.data;
        return response.data.data;
      } else {
        throw new Error(response.data.message || 'Failed to fetch contact');
      }
    } catch (error) {
      console.error('Error fetching contact:', error);
      const message = error.response?.data?.message || error.message || 'Failed to fetch contact';
      setMessage('error', message);
      throw error.response?.data || error.message;
    } finally {
      loading.value = false;
    }
  };

  const createContact = async (contactData) => {
    loading.value = true;
    try {
      const response = await axios.post('/api/contacts', contactData);
      
      if (response.data.success) {
        contacts.value.unshift(response.data.data);
        pagination.value.total += 1;
        setMessage('success', 'Contact created successfully!');
        return response.data.data;
      } else {
        throw new Error(response.data.message || 'Failed to create contact');
      }
    } catch (error) {
      console.error('Error creating contact:', error);
      const message = error.response?.data?.message || error.message || 'Failed to create contact';
      setMessage('error', message);
      throw error.response?.data || error.message;
    } finally {
      loading.value = false;
    }
  };

  const updateContact = async (id, contactData) => {
    loading.value = true;
    try {
      const response = await axios.put(`/api/contacts/${id}`, contactData);
      
      if (response.data.success) {
        const index = contacts.value.findIndex(contact => contact.id === id);
        if (index !== -1) {
          contacts.value[index] = response.data.data;
        }
        
        if (currentContact.value && currentContact.value.id === id) {
          currentContact.value = response.data.data;
        }
        
        setMessage('success', 'Contact updated successfully!');
        return response.data.data;
      } else {
        throw new Error(response.data.message || 'Failed to update contact');
      }
    } catch (error) {
      console.error('Error updating contact:', error);
      const message = error.response?.data?.message || error.message || 'Failed to update contact';
      setMessage('error', message);
      throw error.response?.data || error.message;
    } finally {
      loading.value = false;
    }
  };

  const deleteContact = async (id) => {
    loading.value = true;
    try {
      const response = await axios.delete(`/api/contacts/${id}`);
      
      if (response.data.success) {
        const index = contacts.value.findIndex(contact => contact.id === id);
        if (index !== -1) {
          contacts.value.splice(index, 1);
        }
        
        if (currentContact.value && currentContact.value.id === id) {
          currentContact.value = null;
        }
        
        pagination.value.total -= 1;
        setMessage('success', 'Contact deleted successfully!');
      } else {
        throw new Error(response.data.message || 'Failed to delete contact');
      }
    } catch (error) {
      console.error('Error deleting contact:', error);
      const message = error.response?.data?.message || error.message || 'Failed to delete contact';
      setMessage('error', message);
      throw error.response?.data || error.message;
    } finally {
      loading.value = false;
    }
  };

  const setFilters = (newFilters) => {
    filters.value = { ...filters.value, ...newFilters };
  };

  const clearFilters = () => {
    filters.value = {
      search: '',
      company: '',
      sort_by: 'created_at',
      sort_order: 'desc',
      page: 1,
    };
  };

  const setCurrentContact = (contact) => {
    currentContact.value = contact;
  };

  const clearCurrentContact = () => {
    currentContact.value = null;
  };

  const clearMessages = () => {
    successMessage.value = '';
    errorMessage.value = '';
  };

  return {
    // State
    contacts,
    currentContact,
    loading,
    successMessage,
    errorMessage,
    pagination,
    filters,
    
    // Computed
    hasContacts,
    totalPages,
    
    // Actions
    fetchContacts,
    fetchContact,
    createContact,
    updateContact,
    deleteContact,
    setFilters,
    clearFilters,
    setCurrentContact,
    clearCurrentContact,
    clearMessages,
    setMessage,
  };
}); 