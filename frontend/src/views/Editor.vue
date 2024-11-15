<template>
  <div class="editor">
    <h1>Content Management</h1>
    <div v-if="error" class="error">{{ error }}</div>
    
    <div class="actions">
      <button @click="showAddForm = true" class="btn primary">Add New Post</button>
    </div>

    <div v-if="showAddForm" class="post-form">
      <h2>{{ editingPost ? 'Edit Post' : 'New Post' }}</h2>
      <form @submit.prevent="savePost">
        <div class="form-group">
          <label>Title</label>
          <input v-model="currentPost.title" required>
        </div>
        <div class="form-group">
          <label>Content</label>
          <textarea v-model="currentPost.content" required></textarea>
        </div>
        <div class="form-group">
          <label>Status</label>
          <select v-model="currentPost.status">
            <option value="draft">Draft</option>
            <option value="published">Published</option>
          </select>
        </div>
        <div class="form-actions">
          <button type="submit" class="btn primary" :disabled="saving">
            {{ saving ? 'Saving...' : 'Save' }}
          </button>
          <button type="button" class="btn" @click="cancelEdit">Cancel</button>
        </div>
      </form>
    </div>

    <Loading v-if="loading" message="Loading posts..." />
    <Error v-else-if="error" :message="error" />

    <div v-else class="posts">
      <div v-for="post in posts" :key="post.id" class="post">
        <div class="post-header">
          <h2>{{ post.title }}</h2>
          <span class="status" :class="post.status">{{ post.status }}</span>
        </div>
        <p class="author">By {{ post.author_name }}</p>
        <p class="content">{{ post.content }}</p>
        <div class="post-actions">
          <button @click="editPost(post)" class="btn">Edit</button>
          <button @click="confirmDelete(post)" class="btn danger">Delete</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import api from '@/services/api';
import Loading from '@/components/Loading.vue';
import Error from '@/components/Error.vue';

export default {
  name: 'Editor',
  components: {
    Loading,
    Error
  },
  data() {
    return {
      posts: [],
      showAddForm: false,
      loading: true,
      saving: false,
      error: null,
      currentPost: this.getEmptyPost(),
      editingPost: null
    }
  },
  methods: {
    getEmptyPost() {
      return {
        title: '',
        content: '',
        status: 'draft'
      }
    },
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
    async savePost() {
      try {
        this.saving = true;
        if (this.editingPost) {
          await api.updatePost(this.editingPost.id, this.currentPost);
          this.$toast.success('Post updated successfully');
        } else {
          await api.createPost(this.currentPost);
          this.$toast.success('Post created successfully');
        }
        await this.fetchPosts();
        this.cancelEdit();
      } catch (error) {
        this.$toast.error(error.response?.data?.error || 'Failed to save post');
      } finally {
        this.saving = false;
      }
    },
    editPost(post) {
      this.editingPost = post;
      this.currentPost = { ...post };
      this.showAddForm = true;
    },
    async confirmDelete(post) {
      if (window.confirm('Are you sure you want to delete this post?')) {
          try {
              await api.deletePost(post.id);
              this.$toast.success('Post deleted successfully');
              await this.fetchPosts();
          } catch (error) {
              this.$toast.error(error.response?.data?.error || 'Failed to delete post');
          }
      }
  },
    cancelEdit() {
      this.showAddForm = false;
      this.editingPost = null;
      this.currentPost = this.getEmptyPost();
    }
  },
  mounted() {
    this.fetchPosts();
  }
}
</script>

<style scoped>
.editor {
  padding: 20px;
  max-width: 1200px;
  margin: 0 auto;
}

.post-form {
  background: white;
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  margin: 20px 0;
}

.form-actions {
  display: flex;
  gap: 10px;
  margin-top: 20px;
}

.post {
  background: white;
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  margin-bottom: 20px;
}

.post-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 10px;
}

.status {
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 0.9em;
}

.status.draft {
  background: #f1c40f;
  color: #fff;
}

.status.published {
  background: #2ecc71;
  color: #fff;
}

.post-actions {
  display: flex;
  gap: 10px;
  margin-top: 15px;
}

.btn.danger {
  background-color: #e74c3c;
}

.btn.danger:hover {
  background-color: #c0392b;
}
</style>
