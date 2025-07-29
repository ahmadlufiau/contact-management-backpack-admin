import { describe, it, expect, beforeEach, vi } from 'vitest';
import { mount } from '@vue/test-utils';
import Login from '@/components/auth/Login.vue';

// Mock the auth store
const mockAuthStore = {
  login: vi.fn(),
};

vi.mock('@/stores/auth', () => ({
  useAuthStore: () => mockAuthStore,
}));

// Mock Vue Router
const mockRouter = {
  push: vi.fn(),
};

vi.mock('vue-router', () => ({
  useRouter: () => mockRouter,
}));

describe('Login Component', () => {
  let wrapper;

  beforeEach(() => {
    // Reset mocks
    vi.clearAllMocks();
    mockAuthStore.login.mockResolvedValue({ success: true });
    
    wrapper = mount(Login);
  });

  it('renders login form correctly', () => {
    expect(wrapper.find('h2').text()).toBe('Login');
    expect(wrapper.find('input[type="email"]').exists()).toBe(true);
    expect(wrapper.find('input[type="password"]').exists()).toBe(true);
    expect(wrapper.find('button[type="submit"]').exists()).toBe(true);
  });

  it('has correct form structure', () => {
    const emailInput = wrapper.find('input[type="email"]');
    const passwordInput = wrapper.find('input[type="password"]');
    const submitButton = wrapper.find('button[type="submit"]');

    expect(emailInput.attributes('placeholder')).toBe('Enter your email');
    expect(passwordInput.attributes('placeholder')).toBe('Enter your password');
    expect(submitButton.text()).toBe('Login');
  });

  it('renders demo credentials section', () => {
    expect(wrapper.text()).toContain('Demo Credentials:');
    expect(wrapper.text()).toContain('test@example.com');
    expect(wrapper.text()).toContain('password');
  });

  it('submit button has correct attributes', () => {
    const submitButton = wrapper.find('button[type="submit"]');

    expect(submitButton.attributes('type')).toBe('submit');
    expect(submitButton.classes()).toContain('bg-purple-600');
    expect(submitButton.classes()).toContain('hover:bg-purple-700');
  });
}); 