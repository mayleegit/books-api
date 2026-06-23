import { defineStore } from 'pinia';
import axios from 'axios';

const baseURL = import.meta.env.VITE_API_BASE_URL;

export const useAuth = defineStore('auth', {
  state: () => ({
    token: localStorage.getItem('token') || null,
    user: JSON.parse(localStorage.getItem('user') || 'null'),
  }),

  getters: {
    isAuthenticated: (state) => !!state.token,
    isAdmin: (state) => state.user?.role === 'admin',
  },

  actions: {
    async login(email, password) {
      const { data } = await axios.post(`${baseURL}/auth/login`, {
        email,
        password,
      });

      this.token = data.access_token;
      this.user = data.user;

      localStorage.setItem('token', this.token);
      localStorage.setItem('user', JSON.stringify(this.user));
    },

    async register(name, email, password) {
      await axios.post(`${baseURL}/auth/register`, {
        name,
        email,
        password,
      });

      await this.login(email, password);
    },

    logout() {
      this.token = null;
      this.user = null;

      localStorage.removeItem('token');
      localStorage.removeItem('user');
    },
  },
});