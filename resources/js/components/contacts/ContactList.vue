<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <Message
      v-if="contactsStore.successMessage"
      type="success"
      :message="contactsStore.successMessage"
      dismissible
      @dismiss="contactsStore.clearMessages"
    />

    <Message
      v-if="contactsStore.errorMessage"
      type="error"
      :message="contactsStore.errorMessage"
      dismissible
      @dismiss="contactsStore.clearMessages"
    />

    <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div class="mb-4 sm:mb-0">
          <h1 class="text-3xl font-bold text-gray-900">Contacts</h1>
          <p class="mt-2 text-sm text-gray-600">
            Manage your contact database with ease
          </p>
        </div>
        <button
          @click="showCreateModal = true"
          class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors"
        >
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
          </svg>
          Add Contact
        </button>
      </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div>
          <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Search</label>
          <div class="relative">
            <input
              id="search"
              v-model="searchQuery"
              type="text"
              placeholder="Search contacts..."
              class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
              @input="handleSearch"
            />
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </div>
          </div>
        </div>

        <div>
          <label for="company" class="block text-sm font-medium text-gray-700 mb-2">Company</label>
          <div class="relative">
            <input
              id="company"
              v-model="companyFilter"
              type="text"
              placeholder="Filter by company..."
              class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
              @input="handleCompanyFilter"
            />
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
              </svg>
            </div>
          </div>
        </div>

        <div>
          <label for="sort" class="block text-sm font-medium text-gray-700 mb-2">Sort By</label>
          <select
            id="sort"
            v-model="sortBy"
            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
            @change="handleSort"
          >
            <option value="created_at">Created Date</option>
            <option value="first_name">First Name</option>
            <option value="last_name">Last Name</option>
            <option value="email">Email</option>
            <option value="company">Company</option>
          </select>
        </div>

        <div>
          <label for="order" class="block text-sm font-medium text-gray-700 mb-2">Order</label>
          <select
            id="order"
            v-model="sortOrder"
            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
            @change="handleSort"
          >
            <option value="desc">Descending</option>
            <option value="asc">Ascending</option>
          </select>
        </div>
      </div>

      <div class="mt-4 flex justify-between items-center">
        <div class="text-sm text-gray-600">
          Showing {{ contactsStore.contacts.length }} of {{ contactsStore.pagination.total }} contacts
        </div>
        <button
          @click="clearFilters"
          class="text-sm text-purple-600 hover:text-purple-700 font-medium transition-colors"
        >
          Clear Filters
        </button>
      </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
      <div v-if="contactsStore.loading" class="p-8 text-center">
        <div class="inline-flex items-center">
          <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-purple-600"></div>
          <span class="ml-3 text-gray-600">Loading contacts...</span>
        </div>
      </div>

      <div v-else-if="!contactsStore.hasContacts" class="p-8 text-center">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">No contacts found</h3>
        <p class="mt-1 text-sm text-gray-500">
          Get started by creating your first contact.
        </p>
        <div class="mt-6">
          <button
            @click="showCreateModal = true"
            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            Add Contact
          </button>
        </div>
      </div>

      <div v-else class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Contact
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Company
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Phone
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Created
              </th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                Actions
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="contact in contactsStore.contacts" :key="contact.id" class="hover:bg-gray-50 transition-colors">
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                  <div class="flex-shrink-0 h-10 w-10">
                    <div class="h-10 w-10 rounded-full bg-purple-100 flex items-center justify-center">
                      <span class="text-sm font-medium text-purple-600">
                        {{ contact.first_name.charAt(0).toUpperCase() }}{{ contact.last_name.charAt(0).toUpperCase() }}
                      </span>
                    </div>
                  </div>
                  <div class="ml-4">
                    <div class="text-sm font-bold text-gray-900">
                      {{ contact.full_name }}
                    </div>
                    <div class="text-sm text-gray-500">
                      {{ contact.email }}
                    </div>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                {{ contact.company || '-' }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                {{ contact.phone || '-' }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ formatDate(contact.created_at) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <div class="flex justify-end space-x-2">
                  <button
                    @click="viewContact(contact)"
                    class="text-purple-600 hover:text-purple-900 transition-colors"
                    title="View Details"
                  >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                  </button>
                  <button
                    @click="editContact(contact)"
                    class="text-blue-600 hover:text-blue-900 transition-colors"
                    title="Edit Contact"
                  >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                  </button>
                  <button
                    @click="deleteContact(contact)"
                    class="text-red-600 hover:text-red-900 transition-colors"
                    title="Delete Contact"
                  >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div v-if="contactsStore.totalPages > 1" class="mt-8 flex items-center justify-between">
      <div class="flex-1 flex justify-between sm:hidden">
        <button
          @click="changePage(contactsStore.pagination.current_page - 1)"
          :disabled="contactsStore.pagination.current_page === 1"
          class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
        >
          Previous
        </button>
        <button
          @click="changePage(contactsStore.pagination.current_page + 1)"
          :disabled="contactsStore.pagination.current_page === contactsStore.totalPages"
          class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
        >
          Next
        </button>
      </div>
      <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
        <div>
          <p class="text-sm text-gray-700">
            Showing
            <span class="font-medium">{{ (contactsStore.pagination.current_page - 1) * contactsStore.pagination.per_page + 1 }}</span>
            to
            <span class="font-medium">{{ Math.min(contactsStore.pagination.current_page * contactsStore.pagination.per_page, contactsStore.pagination.total) }}</span>
            of
            <span class="font-medium">{{ contactsStore.pagination.total }}</span>
            results
          </p>
        </div>
        <div>
          <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
            <button
              @click="changePage(contactsStore.pagination.current_page - 1)"
              :disabled="contactsStore.pagination.current_page === 1"
              class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
            >
              <span class="sr-only">Previous</span>
              <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
              </svg>
            </button>
            <button
              v-for="page in getPageNumbers()"
              :key="page"
              @click="changePage(page)"
              :class="[
                'relative inline-flex items-center px-4 py-2 border text-sm font-medium transition-colors',
                page === contactsStore.pagination.current_page
                  ? 'z-10 bg-purple-50 border-purple-500 text-purple-600'
                  : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'
              ]"
            >
              {{ page }}
            </button>
            <button
              @click="changePage(contactsStore.pagination.current_page + 1)"
              :disabled="contactsStore.pagination.current_page === contactsStore.totalPages"
              class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
            >
              <span class="sr-only">Next</span>
              <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
              </svg>
            </button>
          </nav>
        </div>
      </div>
    </div>

    <ContactForm
      v-if="showCreateModal"
      mode="create"
      @close="showCreateModal = false"
      @saved="handleContactSaved"
    />

    <ContactForm
      v-if="showEditModal"
      :contact="selectedContact"
      mode="edit"
      @close="showEditModal = false"
      @saved="handleContactSaved"
    />

    <ContactDetail
      v-if="showDetailModal"
      :contact="selectedContact"
      @close="showDetailModal = false"
    />

    <DeleteConfirmationModal
      v-if="showDeleteModal"
      :contact="selectedContact"
      @close="showDeleteModal = false"
      @deleted="handleContactDeleted"
    />
  </div>
</template>

<script>
import { ref, computed, onMounted, watch } from 'vue';
import { useContactsStore } from '../../stores/contacts';
import ContactForm from './ContactForm.vue';
import ContactDetail from './ContactDetail.vue';
import DeleteConfirmationModal from './DeleteConfirmationModal.vue';
import { Message } from '../global';
import axios from 'axios';
import { useAuthStore } from '../../stores/auth';

function debounce(func, wait) {
  let timeout;
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout);
      func(...args);
    };
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
  };
}

export default {
  name: 'ContactList',
  components: {
    ContactForm,
    ContactDetail,
    DeleteConfirmationModal,
    Message,
  },
  setup() {
    const contactsStore = useContactsStore();
    
    const showCreateModal = ref(false);
    const showEditModal = ref(false);
    const showDetailModal = ref(false);
    const showDeleteModal = ref(false);
    const selectedContact = ref(null);

    const searchQuery = ref('');
    const companyFilter = ref('');
    const sortBy = ref('created_at');
    const sortOrder = ref('desc');

    const fetchContacts = async () => {
      try {
        await contactsStore.fetchContacts();
      } catch (error) {
        console.error('Error fetching contacts:', error);
      }
    };

    const handleSearch = debounce(() => {
      contactsStore.setFilters({ search: searchQuery.value, page: 1 });
      fetchContacts();
    }, 300);

    const handleCompanyFilter = debounce(() => {
      contactsStore.setFilters({ company: companyFilter.value, page: 1 });
      fetchContacts();
    }, 300);

    const handleSort = () => {
      contactsStore.setFilters({ 
        sort_by: sortBy.value, 
        sort_order: sortOrder.value,
        page: 1
      });
      fetchContacts();
    };

    const clearFilters = () => {
      searchQuery.value = '';
      companyFilter.value = '';
      sortBy.value = 'created_at';
      sortOrder.value = 'desc';
      contactsStore.clearFilters();
      fetchContacts();
    };

    const changePage = (page) => {
      if (page >= 1 && page <= contactsStore.totalPages) {
        contactsStore.setFilters({ page });
        fetchContacts();
      }
    };

    const getPageNumbers = () => {
      const pages = [];
      const current = contactsStore.pagination.current_page;
      const total = contactsStore.totalPages;
      const start = Math.max(1, current - 2);
      const end = Math.min(total, current + 2);
      
      for (let i = start; i <= end; i++) {
        pages.push(i);
      }
      
      return pages;
    };

    const viewContact = (contact) => {
      selectedContact.value = contact;
      showDetailModal.value = true;
    };

    const editContact = (contact) => {
      selectedContact.value = contact;
      showEditModal.value = true;
    };

    const deleteContact = (contact) => {
      selectedContact.value = contact;
      showDeleteModal.value = true;
    };

    const handleContactSaved = () => {
      showCreateModal.value = false;
      showEditModal.value = false;
      selectedContact.value = null;
      contactsStore.setFilters({ page: 1 });
      fetchContacts();
    };

    const handleContactDeleted = () => {
      showDeleteModal.value = false;
      selectedContact.value = null;
      fetchContacts();
    };

    const formatDate = (dateString) => {
      if (!dateString) return '';
      const date = new Date(dateString);
      return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
      });
    };

    onMounted(async () => {
      fetchContacts();
    });

    watch(() => contactsStore.filters, () => {
      searchQuery.value = contactsStore.filters.search;
      companyFilter.value = contactsStore.filters.company;
      sortBy.value = contactsStore.filters.sort_by;
      sortOrder.value = contactsStore.filters.sort_order;
    }, { immediate: true });

    return {
      contactsStore,
      showCreateModal,
      showEditModal,
      showDetailModal,
      showDeleteModal,
      selectedContact,
      searchQuery,
      companyFilter,
      sortBy,
      sortOrder,
      fetchContacts,
      handleSearch,
      handleCompanyFilter,
      handleSort,
      clearFilters,
      changePage,
      getPageNumbers,
      viewContact,
      editContact,
      deleteContact,
      handleContactSaved,
      handleContactDeleted,
      formatDate,
    };
  },
};
</script> 