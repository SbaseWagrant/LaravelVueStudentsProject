<template>
    <div>
      <h2>Generate Report</h2>
      <form @submit.prevent="generateReport">
        <select v-model="selectedTemplate" required>
          <option v-for="template in templates" :key="template.id" :value="template.id">
            {{ template.name }}
          </option>
        </select>
        <select v-model="selectedStudent" required>
          <option v-for="student in students" :key="student.id" :value="student.id">
            {{ student.full_name }}
          </option>
        </select>
        <select v-model="selectedSession" required>
          <option v-for="session in sessions" :key="session.id" :value="session.id">
            {{ session.session_date }}
          </option>
        </select>
        <button type="submit">Generate Report</button>
      </form>
  
      <div v-if="report">
        <h3>Generated Report</h3>
        <pre>{{ report }}</pre>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    data() {
      return {
        templates: [],
        students: [],
        sessions: [],
        selectedTemplate: null,
        selectedStudent: null,
        selectedSession: null,
        report: '',
      };
    },
    async created() {
      this.templates = await this.fetchTemplates();
      this.students = await this.fetchStudents();
      this.sessions = await this.fetchSessions();
    },
    methods: {
      async fetchTemplates() {
        const response = await axios.get('/public/report-templates');
        return response.data;
      },
      async fetchStudents() {
        const response = await axios.get('/public/students');
        return response.data;
      },
      async fetchSessions() {
        const response = await axios.get('/public/student-sessions');
        return response.data;
      },
      async generateReport() {
        try {
          const response = await axios.post(`/public/report-templates/${this.selectedTemplate}/generate`, {
            student_id: this.selectedStudent,
            session_id: this.selectedSession,
          });
          this.report = response.data.report;
        } catch (error) {
          console.error('Failed to generate report:', error);
        }
      },
    },
  };
  </script>
  