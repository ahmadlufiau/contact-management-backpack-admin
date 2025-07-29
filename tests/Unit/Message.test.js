import { describe, it, expect, beforeEach } from 'vitest';
import { mount } from '@vue/test-utils';
import Message from '@/components/global/Message.vue';

describe('Message Component', () => {
  let wrapper;

  beforeEach(() => {
    wrapper = null;
  });

  it('renders success message correctly', () => {
    wrapper = mount(Message, {
      props: {
        type: 'success',
        message: 'Operation completed successfully!',
      },
    });

    expect(wrapper.find('.bg-green-50').exists()).toBe(true);
    expect(wrapper.text()).toContain('Operation completed successfully!');
    expect(wrapper.find('svg').exists()).toBe(true);
  });

  it('renders error message correctly', () => {
    wrapper = mount(Message, {
      props: {
        type: 'error',
        message: 'Something went wrong!',
      },
    });

    expect(wrapper.find('.bg-red-50').exists()).toBe(true);
    expect(wrapper.text()).toContain('Something went wrong!');
  });

  it('renders warning message correctly', () => {
    wrapper = mount(Message, {
      props: {
        type: 'warning',
        message: 'Please review your input',
      },
    });

    expect(wrapper.find('.bg-yellow-50').exists()).toBe(true);
    expect(wrapper.text()).toContain('Please review your input');
  });

  it('renders info message correctly', () => {
    wrapper = mount(Message, {
      props: {
        type: 'info',
        message: 'Processing your request...',
      },
    });

    expect(wrapper.find('.bg-blue-50').exists()).toBe(true);
    expect(wrapper.text()).toContain('Processing your request...');
  });

  it('renders description when provided', () => {
    wrapper = mount(Message, {
      props: {
        type: 'success',
        message: 'Success!',
        description: 'Your changes have been saved.',
      },
    });

    expect(wrapper.text()).toContain('Success!');
    expect(wrapper.text()).toContain('Your changes have been saved.');
  });

  it('does not show dismiss button when dismissible is false', () => {
    wrapper = mount(Message, {
      props: {
        type: 'success',
        message: 'Success!',
        dismissible: false,
      },
    });

    const dismissButton = wrapper.find('button');
    expect(dismissButton.exists()).toBe(false);
  });

  it('emits dismiss event when dismiss button is clicked', async () => {
    wrapper = mount(Message, {
      props: {
        type: 'success',
        message: 'Success!',
        dismissible: true,
      },
    });

    const dismissButton = wrapper.find('button');
    await dismissButton.trigger('click');

    expect(wrapper.emitted('dismiss')).toBeTruthy();
    expect(wrapper.emitted('dismiss')).toHaveLength(1);
  });

  it('does not render when message is empty', () => {
    wrapper = mount(Message, {
      props: {
        type: 'success',
        message: '',
      },
    });

    expect(wrapper.find('.bg-green-50').exists()).toBe(false);
  });

  it('has correct default props', () => {
    wrapper = mount(Message, {
      props: {
        message: 'Test message',
      },
    });

    expect(wrapper.props('type')).toBe('success');
    expect(wrapper.props('description')).toBe('');
    expect(wrapper.props('dismissible')).toBe(false);
  });

  it('renders with all props correctly', () => {
    wrapper = mount(Message, {
      props: {
        type: 'error',
        message: 'Error occurred',
        description: 'Please try again later',
        dismissible: true,
      },
    });

    expect(wrapper.find('.bg-red-50').exists()).toBe(true);
    expect(wrapper.text()).toContain('Error occurred');
    expect(wrapper.text()).toContain('Please try again later');
    expect(wrapper.find('button').exists()).toBe(true);
  });
}); 