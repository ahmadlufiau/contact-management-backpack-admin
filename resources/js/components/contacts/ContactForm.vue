<template>
  <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-lg bg-white">
      <div class="mt-3">
        <div class="flex items-center justify-between mb-8">
          <h3 class="text-2xl font-bold text-gray-900">
            {{ mode === 'create' ? 'Add New Contact' : 'Edit Contact' }}
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

        <Message
          v-if="successMessage"
          type="success"
          :message="successMessage"
        />

        <form @submit.prevent="handleSubmit" class="space-y-6">
          <div class="bg-gray-50 p-6 rounded-lg border border-gray-200">
            <h4 class="text-lg font-bold text-gray-900 mb-4">Personal Information</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label for="first_name" class="block text-sm font-bold text-gray-700 mb-2">
                  First Name *
                </label>
                <input
                  id="first_name"
                  v-model="form.first_name"
                  type="text"
                  :class="[
                    'w-full px-3 py-2 border rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm',
                    errors.first_name ? 'border-red-300' : 'border-gray-300'
                  ]"
                  placeholder="Enter first name"
                />
                <p v-if="errors.first_name" class="mt-1 text-sm text-red-600">
                  {{ errors.first_name }}
                </p>
              </div>

              <div>
                <label for="last_name" class="block text-sm font-bold text-gray-700 mb-2">
                  Last Name *
                </label>
                <input
                  id="last_name"
                  v-model="form.last_name"
                  type="text"
                  :class="[
                    'w-full px-3 py-2 border rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm',
                    errors.last_name ? 'border-red-300' : 'border-gray-300'
                  ]"
                  placeholder="Enter last name"
                />
                <p v-if="errors.last_name" class="mt-1 text-sm text-red-600">
                  {{ errors.last_name }}
                </p>
              </div>

              <div class="md:col-span-2">
                <label for="email" class="block text-sm font-bold text-gray-700 mb-2">
                  Email *
                </label>
                <input
                  id="email"
                  v-model="form.email"
                  type="email"
                  :class="[
                    'w-full px-3 py-2 border rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm',
                    errors.email ? 'border-red-300' : 'border-gray-300'
                  ]"
                  placeholder="Enter email address"
                />
                <p v-if="errors.email" class="mt-1 text-sm text-red-600">
                  {{ errors.email }}
                </p>
              </div>
            </div>
          </div>

          <div class="bg-gray-50 p-6 rounded-lg border border-gray-200">
            <h4 class="text-lg font-bold text-gray-900 mb-4">Contact Information</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label for="phone" class="block text-sm font-bold text-gray-700 mb-2">
                  Phone
                </label>
                <input
                  id="phone"
                  v-model="form.phone"
                  type="tel"
                  :class="[
                    'w-full px-3 py-2 border rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm',
                    errors.phone ? 'border-red-300' : 'border-gray-300'
                  ]"
                  placeholder="Enter phone number"
                />
                <p v-if="errors.phone" class="mt-1 text-sm text-red-600">
                  {{ errors.phone }}
                </p>
              </div>

              <div>
                <label for="company" class="block text-sm font-bold text-gray-700 mb-2">
                  Company
                </label>
                <input
                  id="company"
                  v-model="form.company"
                  type="text"
                  :class="[
                    'w-full px-3 py-2 border rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm',
                    errors.company ? 'border-red-300' : 'border-gray-300'
                  ]"
                  placeholder="Enter company name"
                />
                <p v-if="errors.company" class="mt-1 text-sm text-red-600">
                  {{ errors.company }}
                </p>
              </div>
            </div>
          </div>

          <div class="bg-gray-50 p-6 rounded-lg border border-gray-200">
            <h4 class="text-lg font-bold text-gray-900 mb-4">Additional Information</h4>
            <div class="space-y-4">
              <div>
                <label for="address" class="block text-sm font-bold text-gray-700 mb-2">
                  Address
                </label>
                <textarea
                  id="address"
                  v-model="form.address"
                  rows="3"
                  :class="[
                    'w-full px-3 py-2 border rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm',
                    errors.address ? 'border-red-300' : 'border-gray-300'
                  ]"
                  placeholder="Enter address"
                ></textarea>
                <p v-if="errors.address" class="mt-1 text-sm text-red-600">
                  {{ errors.address }}
                </p>
              </div>

              <div>
                <label for="birth_date" class="block text-sm font-bold text-gray-700 mb-2">
                  Birth Date
                </label>
                <input
                  id="birth_date"
                  v-model="form.birth_date"
                  type="date"
                  :class="[
                    'w-full px-3 py-2 border rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm',
                    errors.birth_date ? 'border-red-300' : 'border-gray-300'
                  ]"
                />
                <p v-if="errors.birth_date" class="mt-1 text-sm text-red-600">
                  {{ errors.birth_date }}
                </p>
              </div>

              <div>
                <label for="notes" class="block text-sm font-bold text-gray-700 mb-2">
                  Notes
                </label>
                <textarea
                  id="notes"
                  v-model="form.notes"
                  rows="3"
                  :class="[
                    'w-full px-3 py-2 border rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm',
                    errors.notes ? 'border-red-300' : 'border-gray-300'
                  ]"
                  placeholder="Enter notes"
                ></textarea>
                <p v-if="errors.notes" class="mt-1 text-sm text-red-600">
                  {{ errors.notes }}
                </p>
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
              class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors"
            >
              Cancel
            </button>
            <button
              type="submit"
              :disabled="loading"
              class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
            >
              <span v-if="loading" class="mr-2">
                <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white"></div>
              </span>
              {{ loading ? 'Saving...' : (mode === 'create' ? 'Create Contact' : 'Update Contact') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, reactive, onMounted, watch } from 'vue';
import { useContactsStore } from '../../stores/contacts';
import { Message } from '../global';

export default {
  name: 'ContactForm',
  components: {
    Message,
  },
  props: {
    contact: {
      type: Object,
      default: null,
    },
    mode: {
      type: String,
      required: true,
      validator: (value) => ['create', 'edit'].includes(value),
    },
  },
  emits: ['close', 'saved'],
  setup(props, { emit }) {
    const contactsStore = useContactsStore();
    
    const loading = ref(false);
    const error = ref('');
    const successMessage = ref('');
    const errors = reactive({});
    
    const form = reactive({
      first_name: '',
      last_name: '',
      email: '',
      phone: '',
      company: '',
      address: '',
      birth_date: '',
      notes: '',
    });

    const initializeForm = () => {
      if (props.contact && props.mode === 'edit') {
        Object.keys(form).forEach(key => {
          if (props.contact[key] !== null && props.contact[key] !== undefined) {
            form[key] = props.contact[key];
          }
        });
      }
    };

    const clearMessages = () => {
      error.value = '';
      successMessage.value = '';
      Object.keys(errors).forEach(key => {
        errors[key] = '';
      });
    };

    const validateForm = () => {
      clearMessages();
      
      let isValid = true;
      
      if (!form.first_name.trim()) {
        errors.first_name = 'First name is required';
        isValid = false;
      }
      
      if (!form.last_name.trim()) {
        errors.last_name = 'Last name is required';
        isValid = false;
      }
      
      if (!form.email.trim()) {
        errors.email = 'Email is required';
        isValid = false;
      } else if (!/\S+@\S+\.\S+/.test(form.email)) {
        errors.email = 'Please enter a valid email address';
        isValid = false;
      }
      
      if (form.phone && !/^[\+]?[1-9][\d]{0,15}$/.test(form.phone.replace(/\s/g, ''))) {
        errors.phone = 'Please enter a valid phone number';
        isValid = false;
      }
      
      if (form.address && form.address.length > 500) {
        errors.address = 'Address must be less than 500 characters';
        isValid = false;
      }
      
      if (form.notes && form.notes.length > 1000) {
        errors.notes = 'Notes must be less than 1000 characters';
        isValid = false;
      }
      
      return isValid;
    };

    const handleSubmit = async () => {
      if (!validateForm()) {
        return;
      }

      loading.value = true;
      clearMessages();
      
      try {
        const contactData = { ...form };
        
        if (props.mode === 'create') {
          await contactsStore.createContact(contactData);
          successMessage.value = 'Contact created successfully!';
        } else {
          await contactsStore.updateContact(props.contact.id, contactData);
          successMessage.value = 'Contact updated successfully!';
        }
        
        setTimeout(() => {
          emit('saved');
        }, 1500);
        
      } catch (err) {
        if (err.errors) {
          Object.keys(err.errors).forEach(key => {
            errors[key] = err.errors[key][0];
          });
        } else {
          error.value = err.message || 'Failed to save contact. Please try again.';
        }
      } finally {
        loading.value = false;
      }
    };

    onMounted(() => {
      initializeForm();
    });

    watch(() => props.contact, () => {
      initializeForm();
      clearMessages();
    });

    return {
      form,
      loading,
      error,
      successMessage,
      errors,
      handleSubmit,
    };
  },
};
</script> 