<template>
  <nav class="nav">
    <router-link to="/" class="nav-link">Home</router-link>
    <template v-if="isLoggedIn">
      <template v-if="user.role === 'admin'">
        <router-link to="/admin" class="nav-link">Admin</router-link>
      </template>
      <router-link 
        v-if="user.role === 'editor'"
        to="/editor" 
        class="nav-link"
      >Editor</router-link>
      <a @click="logout" class="nav-link">Logout</a>
    </template>
    <router-link v-else to="/login" class="nav-link">Login</router-link>
  </nav>
</template>

<script>
import { mapState } from 'vuex';

export default {
  name: 'Navigation',
  computed: {
    ...mapState({
      isLoggedIn: state => state.auth.isLoggedIn,
      user: state => state.auth.user
    })
  },
  methods: {
    async logout() {
      await this.$store.dispatch('auth/logout');
      this.$router.push('/');
    }
  }
}
</script>

<style scoped>
.nav {
  padding: 15px;
  background: #2c3e50;
  display: flex;
  align-items: center;
  gap: 5px;
}

.nav-link {
  color: white;
  text-decoration: none;
  padding: 5px 10px;
  margin: 0 5px;
  border-radius: 4px;
  cursor: pointer;
}

.nav-link:hover {
  background: rgba(255,255,255,0.1);
}

.router-link-active {
  background: rgba(255,255,255,0.2);
}
</style>
