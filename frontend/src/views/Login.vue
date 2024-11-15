<template>
  <div class="login-container">
    <div class="login-card">
      <h2>Login</h2>
      <form @submit.prevent="handleLogin">
        <div class="form-group">
          <label>Email</label>
          <input 
            type="email" 
            v-model="credentials.email" 
            required
            autocomplete="email"
          >
        </div>
        <div class="form-group">
          <label>Password</label>
          <input 
            type="password" 
            v-model="credentials.password" 
            required
            autocomplete="current-password"
          >
        </div>
        <div v-if="error" class="error">
          {{ error }}
        </div>
        <button type="submit" class="btn" :disabled="loading">
          {{ loading ? 'Logging in...' : 'Login' }}
        </button>
      </form>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Login',
  data() {
    return {
      credentials: {
        email: '',
        password: ''
      },
      loading: false,
      error: null
    }
  },
  methods: {
    async handleLogin() {
      try {
        this.loading = true;
        this.error = null;
        await this.$store.dispatch('auth/login', this.credentials);
        this.$router.push(this.$route.query.redirect || '/');
      } catch (error) {
        this.error = error.response?.data?.error || 'Login failed';
      } finally {
        this.loading = false;
      }
    }
  }
}
</script>

<style scoped>
.login-container {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  background-color: #f5f6fa;
}

.login-card {
  background: white;
  padding: 40px;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  width: 100%;
  max-width: 400px;
}

.form-group {
  margin-bottom: 20px;
}

label {
  display: block;
  margin-bottom: 8px;
  color: #2c3e50;
}

input {
  width: 100%;
  padding: 8px;
  border: 1px solid #ddd;
  border-radius: 4px;
}

.error {
  color: #e74c3c;
  margin-bottom: 15px;
}

.btn {
  width: 100%;
  padding: 12px;
}
</style>
