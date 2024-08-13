<template>
    <div class="student-sessions-container">
      <h1>Student Sessions</h1>
      <!-- Content for Student Sessions -->
      <form @submit.prevent="scheduleSession">
        <select v-model="selectedStudent" required>
          <option disabled value="">Select a Student</option>
          <option v-for="student in students" :key="student.id" :value="student.id">
            {{ student.first_name }} {{ student.last_name }}
          </option>
        </select>
  
        <input v-model="sessionDate" type="date" required />
        <input v-model="sessionTime" type="time" required />
        <button type="submit">Schedule Session</button>
      </form>
      
      <div v-if="sessions.length">
        <h2>Scheduled Sessions</h2>
        <ul>
          <li v-for="session in sessions" :key="session.id">
            {{ session.student_name }} - {{ session.date }} {{ session.time }}
          </li>
        </ul>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    data() {
      return {
        students: [],
        sessions: [],
        selectedStudent: '',
        sessionDate: '',
        sessionTime: '',
      };
    },
    methods: {
      async fetchStudents() {
        try {
          const response = await axios.get('/public/student-students');
          this.students = response.data;
        } catch (error) {
          console.error('Failed to fetch students:', error);
        }
      },
      async fetchSessions() {
        try {
          const response = await axios.get('/public/student-sessions');
          this.sessions = response.data;
        } catch (error) {
          console.error('Failed to fetch sessions:', error);
        }
      },
      async scheduleSession() {
        try {
          const response = await axios.post('/public/student-sessions', {
            student_id: this.selectedStudent,
            date: this.sessionDate,
            time: this.sessionTime,
          });
          this.sessions.push(response.data);
          this.sessionDate = '';
          this.sessionTime = '';
        } catch (error) {
          console.error('Failed to schedule session:', error);
        }
      },
    },
    mounted() {
      this.fetchStudents();
      this.fetchSessions();
    },
  };
  </script>
  
  <style scoped>
  .student-sessions-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
  }
  </style>
  