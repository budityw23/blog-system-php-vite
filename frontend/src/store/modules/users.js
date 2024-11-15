import api from '@/services/api';

const state = {
  users: [],
  loading: false,
  error: null
};

const getters = {
  totalUsers: (state) => state.users.length,
  getUserById: (state) => (id) => state.users.find(user => user.id === id)
};

const actions = {
  async fetchUsers({ commit }) {
    commit('SET_LOADING', true);
    try {
      const response = await api.getUsers();
      const users = response.data.data || response.data;
      commit('SET_USERS', users);
      commit('SET_ERROR', null);
    } catch (error) {
      console.error('Error fetching users:', error);
      commit('SET_ERROR', error.response?.data?.error || 'Failed to fetch users');
    } finally {
      commit('SET_LOADING', false);
    }
  },

  async createUser({ commit }, userData) {
    commit('SET_LOADING', true);
    try {
      const response = await api.createUser(userData);
      const newUser = response.data.data || response.data;
      commit('ADD_USER', newUser);
      commit('SET_ERROR', null);
      return newUser;
    } catch (error) {
      const errorMessage = error.response?.data?.error || 'Failed to create user';
      commit('SET_ERROR', errorMessage);
      throw new Error(errorMessage);
    } finally {
      commit('SET_LOADING', false);
    }
  },

  async updateUserRole({ commit }, { userId, role }) {
    commit('SET_LOADING', true);
    try {
      const response = await api.updateUserRole(userId, role);
      const updatedUser = response.data.data || response.data;
      commit('UPDATE_USER', updatedUser);
      commit('SET_ERROR', null);
      return updatedUser;
    } catch (error) {
      commit('SET_ERROR', error.response?.data?.error || 'Failed to update role');
      throw error;
    } finally {
      commit('SET_LOADING', false);
    }
  },

  async deleteUser({ commit }, userId) {
    commit('SET_LOADING', true);
    try {
      await api.deleteUser(userId);
      commit('REMOVE_USER', userId);
      commit('SET_ERROR', null);
    } catch (error) {
      commit('SET_ERROR', error.response?.data?.error || 'Failed to delete user');
      throw error;
    } finally {
      commit('SET_LOADING', false);
    }
  }
};

const mutations = {
  SET_USERS(state, users) {
    state.users = users;
  },
  ADD_USER(state, user) {
    state.users.push(user);
  },
  UPDATE_USER(state, updatedUser) {
    const index = state.users.findIndex(u => u.id === updatedUser.id);
    if (index !== -1) {
      state.users.splice(index, 1, updatedUser);
    }
  },
  REMOVE_USER(state, userId) {
    state.users = state.users.filter(u => u.id !== userId);
  },
  SET_LOADING(state, loading) {
    state.loading = loading;
  },
  SET_ERROR(state, error) {
    state.error = error;
  },
  CLEAR_USERS(state) {
    state.users = [];
    state.loading = false;
    state.error = null;
  }
};

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations
};
