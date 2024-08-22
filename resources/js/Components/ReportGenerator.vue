<template>
  <div>
      <h2>Generate Report</h2>
      <form @submit.prevent="generateReport">
          <div>
              <label for="student_id">Select Student:</label>
              <select v-model="form.student_id" id="student_id" required>
                  <option v-for="student in students" :key="student.id" :value="student.id">
                      {{ student.name }}
                  </option>
              </select>
          </div>

          <div>
              <label for="template">Report Template:</label>
              <textarea v-model="form.template" id="template" required></textarea>
              <small>Use shortcodes like @student_full_name, @session_date, etc.</small>
          </div>

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
          form: {
              student_id: null,
              template: '',
          },
          students: [],
          report: null,
      };
  },
  methods: {
      fetchStudents() {
          axios.get('/api/students')
              .then(response => {
                  this.students = response.data;
              });
      },
      generateReport() {
          axios.post('/reports/generate', this.form)
              .then(response => {
                  this.report = response.data.report;
              })
              .catch(error => {
                  console.error(error);
              });
      }
  },
  mounted() {
      this.fetchStudents();
  }
};
</script>

<style scoped>

</style>
