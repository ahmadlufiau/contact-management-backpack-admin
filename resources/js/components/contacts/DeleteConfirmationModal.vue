<template>
  <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-lg bg-white">
      <div class="mt-3">
        <div class="flex items-center justify-between mb-6">
          <h3 class="text-xl font-bold text-gray-900">
            Delete Contact
          </h3>
          <button
            @click="$emit('close')"
            class="text-gray-400 hover:text-gray-600 transition-colors"
          >
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
          <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
          </svg>
        </div>

        <div class="text-center mb-6">
          <h3 class="text-lg font-medium text-gray-900 mb-2">
            Are you sure you want to delete this contact?
          </h3>
          <p class="text-sm text-gray-500 mb-4">
            This action cannot be undone. The contact will be permanently removed from your database.
          </p>
          
          <div class="bg-gray-50 rounded-lg p-4 mb-4">
            <div class="flex items-center justify-center mb-3">
              <div class="h-12 w-12 rounded-full bg-purple-100 flex items-center justify-center">
                <span class="text-lg font-medium text-purple-600">
                  {{ contact.first_name.charAt(0).toUpperCase() }}{{ contact.last_name.charAt(0).toUpperCase() }}
                </span>
              </div>
            </div>
            <div class="text-center">
              <p class="text-sm font-medium text-gray-900">{{ contact.full_name }}</p>
              <p class="text-sm text-gray-500">{{ contact.email }}</p>
              <p v-if="contact.company" class="text-sm text-gray-500">{{ contact.company }}</p>
            </div>
          </div>
        </div>

        <Message
          v-if="error"
          type="error"
          :message="error"
        />

        <div class="flex justify-end space-x-3 pt-6">
          <button
            type="button"
            @click="$emit('close')"
            :disabled="loading"
            class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
          >
            Cancel
          </button>
          <button
            type="button"
            @click="handleDelete"
            :disabled="loading"
            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
          >
            <span v-if="loading" class="mr-2">
              <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white"></div>
            </span>
            {{ loading ? 'Deleting...' : 'Delete Contact' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref } from 'vue';
import { useContactsStore } from '../../stores/contacts';
import { Message } from '../global';

export default {
  name: 'DeleteConfirmationModal',
  components: {
    Message,
  },
  props: {
    contact: {
      type: Object,
      required: true,
    },
  },
  emits: ['close', 'deleted'],
  setup(props, { emit }) {
    const contactsStore = useContactsStore();
    const loading = ref(false);
    const error = ref('');

    const handleDelete = async () => {
      loading.value = true;
      error.value = '';
      
      try {
        await contactsStore.deleteContact(props.contact.id);
        emit('deleted');
      } catch (err) {
        error.value = err.message || 'Failed to delete contact. Please try again.';
      } finally {
        loading.value = false;
      }
    };

    return {
      loading,
      error,
      handleDelete,
    };
  },
};
</script> 