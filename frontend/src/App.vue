<template>
  <div id="app">
    <Navigation />
    <router-view v-slot="{ Component }">
      <transition name="fade" mode="out-in">
        <component :is="Component" />
      </transition>
    </router-view>
  </div>
</template>

<script>
import Navigation from '@/components/Navigation.vue';

export default {
  name: 'App',
  components: {
    Navigation
  }
}
</script>

<style>
#app {
  font-family: 'Arial', sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  color: #2c3e50;
}

/* Transition animations */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

/* Global styles */
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

.btn:disabled {
  background-color: #95a5a6;
  cursor: not-allowed;
}

.form-group {
  margin-bottom: 1rem;
}

.form-group label {
  display: block;
  margin-bottom: 0.5rem;
}

.form-group input,
.form-group textarea,
.form-group select {
  width: 100%;
  padding: 8px;
  border: 1px solid #ddd;
  border-radius: 4px;
}

.error {
  color: #e74c3c;
  margin: 8px 0;
}

.loading {
  text-align: center;
  padding: 20px;
  color: #666;
}
</style>
