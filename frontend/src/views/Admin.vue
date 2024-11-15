<template>
  <div class="admin-page">
    <h1>Admin Dashboard</h1>

    <div v-if="error" class="alert alert-danger">
      {{ error }}
    </div>

    <div class="actions">
      <button @click="showForm = true" class="btn btn-primary" v-if="!showForm">
        Add New User
      </button>
    </div>

    <UserForm
      v-if="showForm"
      @submit="handleCreateUser"
      @cancel="showForm = false"
    />

    <div v-else>
      <div v-if="loading" class="loading">Loading...</div>
      <table v-else class="users-table">
        <thead>
          <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="user in users" :key="user.id">
            <td>{{ user.name }}</td>
            <td>{{ user.email }}</td>
            <td>
              <select 
                v-model="user.role" 
                @change="handleRoleChange(user)"
                :disabled="user.id === currentUser.id"
              >
                <option value="viewer">Viewer</option>
                <option value="editor">Editor</option>
                <option value="admin">Admin</option>
              </select>
            </td>
            <td>
              <button 
                @click="handleDeleteUser(user)" 
                class="btn btn-danger btn-sm"
                :disabled="user.id === currentUser.id"
              >
                Delete
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
import { mapState, mapActions } from 'vuex'
import UserForm from '@/components/UserForm.vue'

export default {
  name: 'Admin',
  components: {
    UserForm
  },
  data() {
    return {
      showForm: false
    }
  },
  computed: {
    ...mapState('users', ['users', 'loading', 'error']),
    ...mapState('auth', {
      currentUser: state => state.user
    })
  },
  methods: {
    ...mapActions('users', ['fetchUsers', 'createUser', 'updateUserRole', 'deleteUser']),
    
    formatDate(date) {
      return new Date(date).toLocaleDateString()
    },

    async handleCreateUser(userData) {
      try {
          // Validate the data
          if (!userData.name || !userData.email || !userData.password) {
              throw new Error('Please fill in all required fields');
          }
          
          // Make sure we have all required fields
          const userPayload = {
              name: userData.name,
              email: userData.email,
              password: userData.password,
              role: userData.role || 'viewer' // Default to viewer if not specified
          };
          
          await this.createUser(userPayload);
          this.showForm = false;
          this.$toast.success('User created successfully');
      } catch (error) {
          console.error('Error creating user:', error);
          this.$toast.error(error.message || 'Failed to create user');
      }
    },

    async handleRoleChange(user) {
      try {
        await this.updateUserRole({ 
          userId: user.id, 
          role: user.role 
        })
        this.$toast.success('Role updated successfully')
      } catch (error) {
        this.$toast.error('Failed to update role')
        // Reset the role if update failed
        this.fetchUsers()
      }
    },

    async handleDeleteUser(user) {
      if (window.confirm(`Are you sure you want to delete ${user.name}?`)) {
        try {
          await this.deleteUser(user.id)
          this.$toast.success('User deleted successfully')
        } catch (error) {
          this.$toast.error('Failed to delete user')
        }
      }
    }
  },
  created() {
    this.fetchUsers().then(() => {
    }).catch(error => {
      console.error('Error fetching users:', error)
    })
  }
}
</script>

<style scoped>
.admin-page {
  padding: 20px;
  max-width: 1200px;
  margin: 0 auto;
}

.actions {
  margin: 20px 0;
}

.users-table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 20px;
}

.users-table th,
.users-table td {
  padding: 12px;
  text-align: left;
  border-bottom: 1px solid #ddd;
}

.users-table th {
  background-color: #f5f5f5;
}

.alert {
  padding: 12px;
  margin-bottom: 20px;
  border-radius: 4px;
}

.alert-danger {
  background-color: #f8d7da;
  color: #721c24;
  border: 1px solid #f5c6cb;
}

.loading {
  text-align: center;
  padding: 20px;
  font-style: italic;
}

.btn {
  padding: 0.375rem 0.75rem;
  border-radius: 0.25rem;
  border: 1px solid transparent;
  cursor: pointer;
}

.btn-sm {
  padding: 0.25rem 0.5rem;
  font-size: 0.875rem;
}

.btn-primary {
  background-color: #007bff;
  color: white;
}

.btn-danger {
  background-color: #dc3545;
  color: white;
}

.btn:disabled {
  opacity: 0.65;
  cursor: not-allowed;
}

select {
  padding: 4px 8px;
  border-radius: 4px;
  border: 1px solid #ced4da;
}

select:disabled {
  background-color: #e9ecef;
  cursor: not-allowed;
}
</style>
