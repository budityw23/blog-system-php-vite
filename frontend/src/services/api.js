import axios from 'axios';

const API_URL = import.meta.env.VITE_API_URL || 'http://localhost:8000/api';

const api = axios.create({
  baseURL: API_URL,
  headers: {
    'Content-Type': 'application/json'
  }
});

// Add token to requests
api.interceptors.request.use(config => {
  const token = localStorage.getItem('token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

// Add error interceptor
api.interceptors.response.use(
    response => response,
    error => {
      if (error.response?.status === 401) {
        store.dispatch('auth/logout');
        router.push('/login');
      }
      return Promise.reject(error);
    }
  );

export default {
  // Auth
  login: (credentials) => api.post('/login', credentials),

  // Users
  getUsers: () => api.get('/admin/users'),
  updateUserRole: (userId, role) => api.put(`/admin/users/${userId}`, { role }),
  createUser: (userData) => api.post('/admin/users', userData),
  deleteUser: (userId) => api.delete(`admin/users/${userId}`),

  // Posts
  getPosts: () => api.get('/posts'),
  createPost: (post) => api.post('/content/posts', post),
  updatePost: (id, post) => api.put(`/content/${id}`, post),
  deletePost: (id) => api.delete(`/content/${id}`)
};
