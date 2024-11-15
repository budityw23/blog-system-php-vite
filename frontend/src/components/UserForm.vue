<template>
  <div class="user-form">
    <h2>{{ isEdit ? 'Edit User' : 'Add New User' }}</h2>
    <form @submit.prevent="handleSubmit" class="form">
      <div class="form-group">
        <label for="name">Name:</label>
        <input 
          type="text" 
          id="name" 
          v-model="form.name" 
          required
          class="form-control"
        >
      </div>

      <div class="form-group">
        <label for="email">Email:</label>
        <input 
          type="email" 
          id="email" 
          v-model="form.email" 
          required
          class="form-control"
        >
      </div>

      <div class="form-group">
        <label for="password">Password:</label>
        <input 
          type="password" 
          id="password" 
          v-model="form.password" 
          :required="!isEdit"
          class="form-control"
        >
      </div>

      <div class="form-group">
        <label for="role">Role:</label>
        <select 
          id="role" 
          v-model="form.role" 
          required
          class="form-control"
        >
          <option value="viewer">Viewer</option>
          <option value="editor">Editor</option>
          <option value="admin">Admin</option>
        </select>
      </div>

      <div class="form-actions">
        <button type="submit" class="btn btn-primary">
          {{ isEdit ? 'Update' : 'Create' }} User
        </button>
        <button type="button" @click="$emit('cancel')" class="btn btn-secondary">
          Cancel
        </button>
      </div>
    </form>
  </div>
</template>

<script>
export default {
  name: 'UserForm',
  props: {
    user: {
      type: Object,
      default: () => ({})
    },
    isEdit: {
      type: Boolean,
      default: false
    }
  },
  data() {
    return {
      form: {
        name: '',
        email: '',
        password: '',
        role: 'viewer'
      }
    }
  },
  created() {
    if (this.isEdit && this.user) {
      this.form = { ...this.user }
      this.form.password = '' // Clear password for edit mode
    }
  },
  methods: {
    handleSubmit() {
      const userData = {
            name: this.form.name,
            email: this.form.email,
            password: this.form.password,
            role: this.form.role
        };
        
        this.$emit('submit', userData);
    }
  }
}
</script>

<style scoped>
.user-form {
  max-width: 500px;
  margin: 0 auto;
  padding: 20px;
}

.form-group {
  margin-bottom: 1rem;
}

.form-group label {
  display: block;
  margin-bottom: 0.5rem;
}

.form-control {
  width: 100%;
  padding: 0.375rem 0.75rem;
  border: 1px solid #ced4da;
  border-radius: 0.25rem;
}

.form-actions {
  display: flex;
  gap: 10px;
  margin-top: 20px;
}

.btn {
  padding: 0.375rem 0.75rem;
  border-radius: 0.25rem;
  border: 1px solid transparent;
  cursor: pointer;
}

.btn-primary {
  background-color: #007bff;
  color: white;
}

.btn-secondary {
  background-color: #6c757d;
  color: white;
}
</style>
