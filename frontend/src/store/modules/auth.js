import api from '@/services/api';
import jwt_decode from 'jwt-decode';

const state = {
  token: localStorage.getItem('token') || null,
  user: null,
  isLoggedIn: false
};

const getters = {
  isAdmin: (state) => state.user?.role === 'admin',
  isEditor: (state) => ['admin', 'editor'].includes(state.user?.role),
  userRole: (state) => state.user?.role
};

const actions = {
  async login({ commit }, credentials) {
    try {
      const response = await api.login(credentials);
      const { token } = response.data;
      
      // Decode token to get user info
      const user = jwt_decode(token);
      
      // Store token in localStorage
      localStorage.setItem('token', token);
      
      // Update state
      commit('SET_TOKEN', token);
      commit('SET_USER', user);
      commit('SET_LOGGED_IN', true);
      
      return user;
    } catch (error) {
      commit('CLEAR_AUTH');
      throw error;
    }
  },

  async logout({ commit }) {
    // Clear localStorage
    localStorage.removeItem('token');
    
    // Clear state
    commit('CLEAR_AUTH');
  },

  // Initialize auth state from stored token
  async initAuth({ commit }) {
    const token = localStorage.getItem('token');
    if (!token) {
      commit('CLEAR_AUTH');
      return;
    }

    try {
      // Verify token is still valid
      const user = jwt_decode(token);
      const currentTime = Date.now() / 1000;
      
      if (user.exp < currentTime) {
        throw new Error('Token expired');
      }

      // Token is valid, restore auth state
      commit('SET_TOKEN', token);
      commit('SET_USER', user);
      commit('SET_LOGGED_IN', true);
    } catch (error) {
      localStorage.removeItem('token');
      commit('CLEAR_AUTH');
    }
  }
};

const mutations = {
  SET_TOKEN(state, token) {
    state.token = token;
  },
  SET_USER(state, user) {
    state.user = user;
  },
  SET_LOGGED_IN(state, value) {
    state.isLoggedIn = value;
  },
  CLEAR_AUTH(state) {
    state.token = null;
    state.user = null;
    state.isLoggedIn = false;
  }
};

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations
};
