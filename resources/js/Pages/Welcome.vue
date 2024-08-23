<template>
    <nav class="-mx-3 flex flex-1 justify-end space-x-4">
    <!-- Dropdown Menu -->
    <div class="relative group">
        <a
            href="#"
            class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white inline-flex items-center"
        >
            Menu
            <svg
                class="w-5 h-5 ml-2"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M19 9l-7 7-7-7"
                />
            </svg>
        </a>
        <div class="absolute right-0 mt-2 hidden w-48 bg-white shadow-lg rounded-md group-focus-within:block">
            <a
                :href="route('login')"
                class="block px-4 py-2 text-black hover:bg-gray-200"
            >
                Log in
            </a>
            <a
                :href="route('register')"
                class="block px-4 py-2 text-black hover:bg-gray-200"
            >
                Register
            </a>
            <a
                :href="route('dashboard')"
                class="block px-4 py-2 text-black hover:bg-gray-200"
            >
                Manage students
            </a>
            <a
                :href="route('passed-sessions-view')"
                class="block px-4 py-2 text-black hover:bg-gray-200"
            >
                Passed sessions
            </a>
            <a
                :href="route('upload-document')"
                class="block px-4 py-2 text-black hover:bg-gray-200"
            >
                Upload document
            </a>
            <a
                :href="route('templates')"
                class="block px-4 py-2 text-black hover:bg-gray-200"
            >
                Create report
            </a>
            <a
                :href="route('templates-list')"
                class="block px-4 py-2 text-black hover:bg-gray-200"
            >
                View reports
            </a>
            <a
                :href="route('destroy')"
                class="block px-4 py-2 text-black hover:bg-gray-200"
            >
                Logout
            </a>
        </div>
    </div>
</nav>


    <div class="dashboard-container">
      <h2>Add Student</h2>
      <form @submit.prevent="addStudent" class="form-container">
        <input v-model="student.first_name" placeholder="First Name" required class="form-input" />
        <input v-model="student.middle_name" placeholder="Middle Name" class="form-input" />
        <input v-model="student.last_name" placeholder="Last Name" required class="form-input" />
        <input v-model="student.date_of_birth" type="date" required class="form-input" />
        <button type="submit" class="submit-button" :disabled="loading">Add Student</button>
        <div class="link-container">
        <a
                :href="route('dashboard')"
            >
            View Student Sessions
            </a>
        </div>
      </form>
      <div v-if="errorMessage" class="error-message">{{ errorMessage }}</div>
    
      <h2>Student List</h2>
      <div v-if="students.length === 0" class="no-data-message">No students available.</div>
      <ul class="student-list">
        <li v-for="student in students" :key="student.id" class="student-item">
          <div class="student-info">
            <span>{{ student.first_name }} {{ student.middle_name }} {{ student.last_name }} - {{ student.date_of_birth }}</span>
            <button @click="openPopup(student)" class="info-button">→</button>
          </div>
        </li>
      </ul>
    
      <!-- Availability Popup -->
      <div v-if="selectedStudent" class="popup-overlay" @click="closePopup">
        <div class="popup-content" @click.stop>
          <h3>{{ selectedStudent.first_name }} {{ selectedStudent.last_name }}'s Availability</h3>
          <ul class="availability-list">
            <li v-for="avail in selectedStudent.availabilities" :key="avail.id" class="availability-item">
              {{ avail.weekday }}
            </li>
          </ul>
          <form @submit.prevent="addAvailability(selectedStudent)" class="form-container">
            <div class="checkbox-container">
              <div v-for="day in weekdays" :key="day" class="checkbox-item">
                <input
                  type="checkbox"
                  :id="day"
                  :value="day"
                  v-model="selectedDays"
                  class="form-checkbox"
                />
                <label :for="day">{{ day }}</label>
              </div>
            </div>
            <button type="submit" class="submit-button" :disabled="loadingAvailability">Add Availability</button>
          </form>
          <button @click="closePopup" class="close-button">Close</button>
          <!-- Spinner in Popup -->
          <div v-if="loadingAvailability" class="loading-spinner popup-spinner">
            <div class="spinner"></div>
            Adding...
          </div>
        </div>
      </div>
  
      <!-- Global Loading Spinner -->
      <div v-if="loading" class="loading-spinner global-spinner">
        <div class="spinner"></div>
        Loading...
      </div>
    </div>
  </template>
  
  <script>
  import axios from 'axios';
  import AuthenticatedLayout from '../Layouts/AuthenticatedLayout.vue';
  
  export default {
    data() {
      return {
        student: {
          first_name: '',
          middle_name: '',
          last_name: '',
          date_of_birth: '',
        },
        selectedDays: [],
        students: [],
        weekdays: ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'],
        errorMessage: '',
        selectedStudent: null,
        loading: false,
        loadingAvailability: false,
        layout: AuthenticatedLayout,
      };
    },
    methods: {
      async addStudent() {
        this.loading = true;
        try {
          this.errorMessage = '';
          const response = await axios.post('/public/students', this.student);
          this.students.push(response.data);
          this.student = { first_name: '', middle_name: '', last_name: '', date_of_birth: '' };
        } catch (error) {
          this.errorMessage = error.response?.data?.message || 'An error occurred while adding the student.';
        } finally {
          this.loading = false;
        }
      },
      async addAvailability(student) {
        this.loadingAvailability = true;
        try {
          const response = await axios.post(`/public/students/${student.id}/availability`, { weekdays: this.selectedDays });
          if (!student.availabilities) {
            this.$set(student, 'availabilities', []);
          }
          student.availabilities.push(...response.data);
          this.selectedDays = [];
        } catch (error) {
          console.error(error);
        } finally {
          this.fetchStudents();
          this.closePopup();
          this.loadingAvailability = false;
        }
      },
      async fetchStudents() {
        this.loading = true;
        try {
          const response = await axios.get('/public/students');
          this.students = response.data;
          this.students.forEach(student => {
            this.fetchAvailabilities(student);
          });
        } catch (error) {
          console.error(error);
        } finally {
          this.loading = false;
        }
      },
      async fetchAvailabilities(student) {
            this.loading = true;
            try {
                const response = await axios.get(`/public/students/${student.id}/availabilities`);
                student.availabilities = response.data;
            } catch (error) {
                console.error(error);
            } finally {
                this.loading = false;
            }
        },
      openPopup(student) {
        this.selectedDays = [];
        this.selectedStudent = student;
      },
      closePopup() {
        this.selectedDays = [];
        this.selectedStudent = null;
      }
    },
    mounted() {
      this.fetchStudents();
    }
  };
  </script>
  
  <style scoped>
  .dashboard-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
    font-family: Arial, sans-serif;
    text-align: center;
  }
  
  h2, h3, h4 {
    color: #333;
    margin-bottom: 10px;
  }
  
  .form-container {
    margin-bottom: 20px;
    display: flex;
    flex-direction: column;
    align-items: center;
  }
  
  .form-input, .form-select, .form-checkbox {
    margin-bottom: 10px;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
  }
  
  .submit-button {
    background-color: #007bff;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 4px;
    cursor: pointer;
    font-size: 16px;
  }
  
  .submit-button:disabled {
    background-color: #007bff;
    cursor: not-allowed;
  }
  
  .submit-button:hover:not(:disabled) {
    background-color: #0056b3;
  }
  
  .info-button {
    background-color: transparent;
    border: none;
    color: #007bff;
    font-size: 20px;
    cursor: pointer;
  }
  
  .error-message {
    color: red;
    margin-bottom: 20px;
  }
  
  .no-data-message {
    color: #666;
  }
  
  .student-list {
    list-style-type: none;
    padding: 0;
  }
  
  .student-item {
    padding: 10px;
    border-bottom: 1px solid #ddd;
  }
  
  .student-info {
    display: flex;
    align-items: center;
    justify-content: space-between;
  }
  
  .popup-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
  }
  
  .popup-content {
    background: white;
    padding: 20px;
    border-radius: 8px;
    max-width: 500px;
    width: 100%;
  }
  
  .close-button {
    background-color: #dc3545;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 4px;
    cursor: pointer;
    font-size: 16px;
    margin-top: 10px;
  }
  
  .close-button:hover {
    background-color: #c82333;
  }
  
  .checkbox-container {
    display: flex;
    flex-direction: column;
    margin-bottom: 20px;
    align-items: flex-start;
  }
  
  .checkbox-item {
    display: flex;
    align-items: center;
    margin-bottom: 5px;
  }
  
  .form-checkbox {
    margin-right: 5px;
  }
  
  .availability-list {
    list-style-type: none;
    padding: 0;
  }
  
  .availability-item {
    background-color: #f8f9fa;
    padding: 5px;
    border: 1px solid #ddd;
    border-radius: 4px;
    margin-bottom: 5px;
  }
  
  .loading-spinner {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    display: flex;
    align-items: center;
    justify-content: center;
  }
  
  .global-spinner {
    background: rgba(0, 0, 0, 0.5);
    width: 100%;
    height: 100%;
    z-index: 999;
  }
  
  .popup-spinner {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  background: rgba(0, 0, 0, 0.5);
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
}

.spinner {
  border: 4px solid rgba(0, 0, 0, 0.1);
  border-radius: 50%;
  border-top: 4px solid #007bff;
  width: 30px;
  height: 30px;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
.link-button {
    display: inline-block;
    margin-top: 20px;
    padding: 10px 20px;
    background-color: #007bff;
    color: white;
    border-radius: 4px;
    text-decoration: none;
}

.link-button:hover {
    cursor:pointer;
    background-color: #0056b3;
}
  </style>
  