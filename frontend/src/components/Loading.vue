<template>
  <div class="loading-wrapper">
    <div class="loading-spinner"></div>
    <div class="loading-text">{{ message }}</div>
  </div>
</template>

<script>
export default {
  name: 'Loading',
  props: {
    message: {
      type: String,
      default: 'Loading...'
    }
  }
}
</script>

<style scoped>
.loading-wrapper {
  text-align: center;
  padding: 20px;
}

.loading-spinner {
  border: 4px solid #f3f3f3;
  border-top: 4px solid #3498db;
  border-radius: 50%;
  width: 40px;
  height: 40px;
  animation: spin 1s linear infinite;
  margin: 0 auto 10px;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.loading-text {
  color: #666;
}
</style>
