<template>
  <div class="home">
    <header class="header">
      <h1>Blog Posts</h1>
      <div class="auth-buttons" v-if="!isLoggedIn">
        <button @click="$router.push('/login')" class="btn">Login</button>
      </div>
      <div class="user-menu" v-else>
        <span>Welcome, {{ user.name }}</span>
        <template v-if="user.role === 'admin'">
          <button @click="$router.push('/admin')" class="btn">Admin Panel</button>
        </template>
        <template v-if="user.role === 'editor'">
          <button @click="$router.push('/editor')" class="btn">Content Management</button>
        </template>
        <button @click="logout" class="btn">Logout</button>
      </div>
    </header>

    <div class="content">
      <Loading v-if="loading" message="Loading posts..." />
      <Error v-else-if="error" :message="error" />
      <div v-else class="posts">
        <article v-for="post in publishedPosts" :key="post.id" class="post-card">
          <header class="post-header">
            <h2>{{ post.title }}</h2>
            <div class="post-meta">
              <span class="author">By {{ post.author_name }}</span>
              <span class="date">{{ formatDate(post.created_at) }}</span>
            </div>
          </header>
          <div class="post-content">
            <p>{{ post.content }}</p>
          </div>
        </article>
      </div>
    </div>
  </div>
</template>

<script>
import { mapState } from 'vuex';
import api from '@/services/api';
import Loading from '@/components/Loading.vue';
import Error from '@/components/Error.vue';

export default {
  name: 'Home',
  components: {                           
    Loading,
    Error
  },
  data() {
    return {
      posts: [],
      loading: true,
      error: null
    }
  },
  computed: {
    ...mapState({
      user: state => state.auth.user,
      isLoggedIn: state => state.auth.isLoggedIn
    }),
    publishedPosts() {
      return this.posts.filter(post => post.status === 'published');
    }
  },
  methods: {
    async fetchPosts() {
      try {
        this.loading = true;
        const response = await api.getPosts();
        this.posts = response.data.data;
      } catch (error) {
        this.error = error.response?.data?.error || 'Failed to fetch posts';
      } finally {
        this.loading = false;
      }
    },
    formatDate(dateString) {
      return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      });
    },
    async logout() {
      await this.$store.dispatch('auth/logout');
      this.$router.push('/');
    }
  },
  mounted() {
    this.fetchPosts();
  }
}
</script>

<style scoped>
.home {
  max-width: 1200px;
  margin: 0 auto;
  padding: 20px;
}

.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 40px;
  padding-bottom: 20px;
  border-bottom: 1px solid #eee;
}

.user-menu {
  display: flex;
  gap: 10px;
  align-items: center;
}

.user-menu span {
  margin-right: 15px;
  font-weight: 500;
}

.posts {
  display: grid;
  gap: 30px;
}

.post-card {
  background: #fff;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  padding: 20px;
  transition: transform 0.2s;
}

.post-card:hover {
  transform: translateY(-2px);
}

.post-header {
  margin-bottom: 15px;
}

.post-header h2 {
  margin: 0 0 10px 0;
  color: #2c3e50;
}

.post-meta {
  display: flex;
  gap: 15px;
  color: #666;
  font-size: 0.9em;
}

.post-content {
  color: #2c3e50;
  line-height: 1.6;
}

.btn {
  padding: 8px 16px;
  border-radius: 4px;
  border: none;
  background-color: #3498db;
  color: white;
  cursor: pointer;
  transition: background-color 0.2s;
}

.btn:hover {
  background-color: #2980b9;
}

.loading {
  text-align: center;
  padding: 40px;
  color: #666;
}

.error {
  text-align: center;
  padding: 40px;
  color: #e74c3c;
}
</style>
