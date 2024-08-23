<template>
  <div class="student-sessions-container">
    <h1 class="text-2xl font-bold mb-4 text-center">Schedule a Student Session</h1>

    <form @submit.prevent="scheduleSession" class="max-w-md mx-auto bg-white p-6 rounded-lg shadow-md">
      <!-- Scheduling Form -->
      <div class="mb-4">
        <label for="student" class="block text-sm font-medium text-gray-700 mb-1">Select a Student</label>
        <select 
          v-model="selectedStudent" 
          id="student"
          @change="onStudentChange" 
          required 
          class="block w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5"
        >
          <option disabled value="">Select a Student</option>
          <option v-for="student in students" :key="student.id" :value="student.id">
            {{ student.first_name }} {{ student.last_name }}
          </option>
        </select>
      </div>

      <div v-if="selectedStudent" class="mb-4">
        <p class="text-sm font-medium text-gray-700">Available Days:</p>
        <ul class="list-disc pl-5">
          <li v-for="day in availableDays" :key="day" class="text-green-600">
            {{ day }}
          </li>
        </ul>
      </div>

      <div class="mb-4">
        <label for="sessionDate" class="block text-sm font-medium text-gray-700 mb-1">Select Date</label>
        <input 
          v-model="sessionDate" 
          type="date" 
          id="sessionDate"
          required 
          class="block w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5" 
        />
      </div>

      <div class="mb-4">
        <label for="sessionStartTime" class="block text-sm font-medium text-gray-700 mb-1">Start Time</label>
        <input 
          v-model="sessionStartTime" 
          type="time" 
          id="sessionStartTime" 
          required 
          class="block w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5" 
          @change="updateEndTimeLimits"
        />
      </div>

      <div class="mb-4">
        <label for="sessionEndTime" class="block text-sm font-medium text-gray-700 mb-1">End Time</label>
        <input 
          v-model="sessionEndTime" 
          type="time" 
          id="sessionEndTime" 
          required 
          class="block w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5" 
          :min="minEndTime"
          :max="maxEndTime"
          @change="validateEndTime"
        />
      </div>

      <div class="mb-6">
        <label for="sessionType" class="block text-sm font-medium text-gray-700 mb-1">Session Type</label>
        <select 
          v-model="sessionType" 
          id="sessionType" 
          required 
          class="block w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5"
        >
          <option disabled value="">Select Session Type</option>
          <option value="one-time">One-Time</option>
          <option value="recurring">Recurring</option>
        </select>
      </div>

      <button 
        type="submit" 
        :disabled="!isAvailable || loading" 
        class="w-full text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-500 font-medium rounded-lg text-sm px-5 py-2.5 text-center"
      >
        Schedule Session
      </button>

      <div v-if="errorMessage" class="text-red-500 mt-4">{{ errorMessage }}</div>
      <div v-if="successMessage" class="text-green-500 mt-4">{{ successMessage }}</div>
    </form>

    <!-- Loading Spinner -->
    <div v-if="loading" class="loading-spinner-overlay">
      <div class="loading-spinner"></div>
    </div>

    <!-- Rating Form -->
    <h1 class="text-2xl font-bold mb-4 text-center">Rate Student</h1>

    <form @submit.prevent="submitRatings" class="max-w-md mx-auto bg-white p-6 rounded-lg shadow-md">
      <div v-if="passedStudents.length === 0" class="text-center text-gray-500">
        No sessions to rate.
      </div>

      <div v-else>
        <div v-for="session in passedStudents" :key="session.id" class="mb-4">
          <p class="text-lg font-medium text-gray-700">
            {{ session.student.first_name }} {{ session.student.last_name }} 
            ({{ session.start_time }} - {{ session.end_time }})
          </p>
          <input 
            v-model.number="session.rating" 
            type="number" 
            min="0" 
            max="10" 
            step="1" 
            required 
            class="block w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5" 
            placeholder="Rate out of 10"
          />
        </div>
      </div>

      <button 
        type="submit" 
        class="w-full text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-500 font-medium rounded-lg text-sm px-5 py-2.5 text-center"
        :disabled="passedStudents.length === 0"
      >
        Submit Ratings
      </button>
    </form>

    <!-- Loading Spinner -->
    <div v-if="loading" class="loading-spinner-overlay">
      <div class="loading-spinner"></div>
    </div>

    <div v-if="successMessage" class="text-green-500 mt-4 text-center">{{ successMessage }}</div>
    <div v-if="errorMessage" class="text-red-500 mt-4 text-center">{{ errorMessage }}</div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      selectedStudent: '',
      sessionDate: '',
      sessionStartTime: '',
      sessionEndTime: '',
      sessionType: '',
      students: [],
      passedStudents: [],
      minEndTime: '',
      maxEndTime: '',
      errorMessage: '',
      successMessage: '',
      isAvailable: true,
      availableDays: [],
      loading: false,
    };
  },
  watch: {
    sessionDate() {
      if (this.selectedStudent && this.sessionDate) {
        this.fetchAvailability();
      }
    },
  },
  methods: {
    async onStudentChange() {
      if (this.selectedStudent) {
        this.loading = true;
        await this.fetchAvailableDays();
        this.loading = false;
      }
    },
    async fetchAvailableDays() {
      try {
        const response = await axios.post('/public/student-available-days', {
          student_id: this.selectedStudent
        });
        this.availableDays = response.data.available_days || [];
        this.isAvailable = this.availableDays.length > 0;
        if (!this.isAvailable) {
          this.errorMessage = `The student is not available on the selected date.`;
        } else {
          this.errorMessage = '';
        }
      } catch (error) {
        console.error('Failed to load available days:', error);
        this.errorMessage = 'Failed to load available days.';
      }
    },
    async fetchStudents() {
      try {
        const response = await axios.get('/public/students');
        this.students = response.data;
      } catch (error) {
        this.errorMessage = 'Failed to load students';
        console.error('Failed to load students:', error);
      }
    },
    updateEndTimeLimits() {
      if (this.sessionStartTime) {
        const startTimeInMinutes = this.convertToMinutes(this.sessionStartTime);
        const maxEndTimeInMinutes = startTimeInMinutes + 15;

        const maxHours = Math.floor(maxEndTimeInMinutes / 60);
        const maxMinutes = maxEndTimeInMinutes % 60;

        this.minEndTime = this.formatTime(startTimeInMinutes + 5);
        this.maxEndTime = `${this.formatTime(maxHours)}:${this.formatTime(maxMinutes)}`;

        const endTimeInMinutes = this.convertToMinutes(this.sessionEndTime);
        if (endTimeInMinutes > maxEndTimeInMinutes || !this.sessionEndTime) {
          this.sessionEndTime = this.formatTime(maxHours) + ':' + this.formatTime(maxMinutes);
        } else if (endTimeInMinutes < startTimeInMinutes + 5) {
          this.sessionEndTime = this.formatTime(startTimeInMinutes + 5);
        }
      }
    },
    validateEndTime() {
    if (this.sessionStartTime && this.sessionEndTime) {
        const startTimeInMinutes = this.convertToMinutes(this.sessionStartTime);
        const endTimeInMinutes = this.convertToMinutes(this.sessionEndTime);

        const maxEndTimeInMinutes = startTimeInMinutes + 15;
        const minEndTimeInMinutes = startTimeInMinutes + 5;

        if (endTimeInMinutes > maxEndTimeInMinutes) {
            this.sessionEndTime = this.formatTime(maxEndTimeInMinutes);
            this.errorMessage = 'End time adjusted to within 15 minutes of the start time';
        } else if (endTimeInMinutes < minEndTimeInMinutes) {
            this.sessionEndTime = this.formatTime(minEndTimeInMinutes);
            this.errorMessage = 'End time adjusted to at least 5 minutes after start time';
        } else {
            this.errorMessage = '';
        }
    }
},
    convertToMinutes(time) {
      const [hours, minutes] = time.split(':').map(Number);
      return hours * 60 + minutes;
    },
    formatTime(minutes) {
      return String(minutes).padStart(2, '0');
    },
    async scheduleSession() {
      this.loading = true;
      try {
        const response = await axios.post('/public/student-sessions', {
          student_id: this.selectedStudent,
          date: this.sessionDate,
          start_time: this.sessionStartTime,
          end_time: this.sessionEndTime,
          session_type: this.sessionType
        });

        this.successMessage = 'Session scheduled successfully';
        this.clearForm();
      } catch (error) {
        this.errorMessage = error;
      } finally {
        this.loading = false;
        this.fetchPassedSessions();
      }
    },
    async fetchPassedSessions() {
      this.loading = true;
      try {
        const response = await axios.get('/public/passed-sessions');
        this.passedStudents = response.data || [];
      } catch (error) {
        console.error('Failed to load passed sessions:', error);
        this.errorMessage = 'Failed to load passed sessions';
      } finally {
        this.loading = false;
      }
    },
    async submitRatings() {
      this.loading = true;
      try {
        const response = await axios.post('/public/rate-student', {
          ratings: this.passedStudents.map(session => ({
            session_id: session.id,
            rating: session.rating,
          })),
        });

        this.successMessage = 'Ratings submitted successfully';
        this.fetchPassedSessions();
      } catch (error) {
        console.error('Failed to submit ratings:', error);
        this.errorMessage = 'Failed to submit ratings';
      } finally {
        this.loading = false;
      }
    },
    clearForm() {
      this.selectedStudent = '';
      this.sessionDate = '';
      this.sessionStartTime = '';
      this.sessionEndTime = '';
      this.sessionType = '';
      this.isAvailable = true;
      this.availableDays = [];
    },
    async fetchAvailability() {
      if (!this.selectedStudent || !this.sessionDate) {
          return;
      }

      this.loading = true;
      this.errorMessage = '';
      this.isAvailableSt = false;

      try {
          const response = await axios.get(`/public/students/${this.selectedStudent}/availabilities`, {
              params: { date: this.sessionDate }
          });

          if (response.data && response.data.length > 0) {
              const weekday = new Date(this.sessionDate).toLocaleString('en-US', { weekday: 'long' });
              this.isAvailableSt = response.data.some(day => day.weekday === weekday);

              if (!this.isAvailableSt) {
                  this.errorMessage = 'No available times on this selected date.';
              }
          } else {
              this.errorMessage = 'No availability data found for the selected student.';
          }
      } catch (error) {
          console.error('Error fetching availability:', error);
          this.errorMessage = 'Failed to fetch availability.';
      } finally {
          this.loading = false;
      }
  },

  },
  mounted() {
    this.fetchStudents();
    this.fetchPassedSessions();
  },
};
</script>

<style scoped>
.loading-spinner-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  display: flex;
  justify-content: center;
  align-items: center;
  background-color: rgba(255, 255, 255, 0.8);
  z-index: 1000;
}

.loading-spinner {
  border: 8px solid rgba(0, 0, 0, 0.1);
  border-top-color: #3498db;
  border-radius: 50%;
  width: 60px;
  height: 60px;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}
</style>
