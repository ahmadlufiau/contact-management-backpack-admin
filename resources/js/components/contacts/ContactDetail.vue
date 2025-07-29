<template>
  <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-lg bg-white">
      <div class="mt-3">
        <div class="flex items-center justify-between mb-8">
          <h3 class="text-2xl font-bold text-gray-900">Contact Details</h3>
          <button
            @click="$emit('close')"
            class="text-gray-400 hover:text-gray-600 transition-colors"
          >
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <div class="flex items-center mb-8 p-6 bg-gray-50 rounded-lg">
          <div class="flex-shrink-0 h-20 w-20">
            <div class="h-20 w-20 rounded-full bg-purple-100 flex items-center justify-center">
              <span class="text-2xl font-bold text-purple-600">
                {{ contact.first_name.charAt(0) }}{{ contact.last_name.charAt(0) }}
              </span>
            </div>
          </div>
          <div class="ml-6">
            <h2 class="text-2xl font-bold text-gray-900">{{ contact.full_name }}</h2>
            <p class="text-lg text-gray-600">{{ contact.company || 'No company' }}</p>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div class="bg-white p-4 rounded-lg border border-gray-200">
            <div class="flex items-center mb-2">
              <svg class="h-5 w-5 text-purple-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
              </svg>
              <p class="text-sm font-bold text-gray-900">Email</p>
            </div>
            <p class="text-sm text-gray-600 break-all">{{ contact.email }}</p>
          </div>

          <div v-if="contact.phone" class="bg-white p-4 rounded-lg border border-gray-200">
            <div class="flex items-center mb-2">
              <svg class="h-5 w-5 text-purple-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
              </svg>
              <p class="text-sm font-bold text-gray-900">Phone</p>
            </div>
            <p class="text-sm text-gray-600">{{ contact.phone }}</p>
          </div>

          <div v-if="contact.address" class="bg-white p-4 rounded-lg border border-gray-200 md:col-span-2">
            <div class="flex items-center mb-2">
              <svg class="h-5 w-5 text-purple-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
              <p class="text-sm font-bold text-gray-900">Address</p>
            </div>
            <p class="text-sm text-gray-600 whitespace-pre-line">{{ contact.address }}</p>
          </div>

          <div v-if="contact.birth_date" class="bg-white p-4 rounded-lg border border-gray-200">
            <div class="flex items-center mb-2">
              <svg class="h-5 w-5 text-purple-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
              <p class="text-sm font-bold text-gray-900">Birth Date</p>
            </div>
            <p class="text-sm text-gray-600">{{ formatDate(contact.birth_date) }}</p>
          </div>

          <div v-if="contact.notes" class="bg-white p-4 rounded-lg border border-gray-200 md:col-span-2">
            <div class="flex items-center mb-2">
              <svg class="h-5 w-5 text-purple-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
              <p class="text-sm font-bold text-gray-900">Notes</p>
            </div>
            <p class="text-sm text-gray-600 whitespace-pre-line">{{ contact.notes }}</p>
          </div>

          <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 md:col-span-2">
            <div class="flex items-center mb-2">
              <svg class="h-5 w-5 text-purple-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <p class="text-sm font-bold text-gray-900">Timeline</p>
            </div>
            <p class="text-sm text-gray-600">Created: {{ formatDateTime(contact.created_at) }}</p>
            <p v-if="contact.updated_at !== contact.created_at" class="text-sm text-gray-600 mt-1">
              Updated: {{ formatDateTime(contact.updated_at) }}
            </p>
          </div>
        </div>

        <div class="flex justify-end space-x-3 pt-8">
          <button
            @click="$emit('close')"
            class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors"
          >
            Close
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ContactDetail',
  props: {
    contact: {
      type: Object,
      required: true,
    },
  },
  emits: ['close'],
  setup() {
    const formatDate = (dateString) => {
      if (!dateString) return '';
      const date = new Date(dateString);
      return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
      });
    };

    const formatDateTime = (dateString) => {
      if (!dateString) return '';
      const date = new Date(dateString);
      return date.toLocaleString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
      });
    };

    return {
      formatDate,
      formatDateTime,
    };
  },
};
</script> 