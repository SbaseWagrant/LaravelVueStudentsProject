<template>
  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-8">
        <!-- Card for Creating Report Template -->
        <div class="card mb-4">
          <div class="card-header">
            <h2>Create Report Template</h2>
          </div>
          <div class="card-body">
            <form @submit.prevent="submitTemplate">
              <div class="mb-3">
                <label for="name" class="form-label">Template Name</label>
                <input
                  type="text"
                  class="form-control"
                  id="name"
                  v-model="template.name"
                  required
                />
              </div>
              <div class="mb-3">
                <label for="template" class="form-label">Template Content</label>
                <textarea
                  class="form-control"
                  id="template"
                  rows="6"
                  v-model="template.content"
                  required
                ></textarea>
                <small class="form-text text-muted">
                  Use the following shortcodes in your template:<br />
                  @student_full_name, @session_date, @session_minutes, @session_start_time, @session_end_time, @target_start_date, @target_end_date, @target
                </small>
              </div>
              <button type="submit" class="btn btn-primary">Save Template</button>
            </form>
          </div>
        </div>

        <!-- Card for Generating Report -->
        <div class="card">
          <div class="card-header">
            <h2>Generate Report</h2>
          </div>
          <div class="card-body">
            <form @submit.prevent="generateReport">
              <div class="mb-3">
                <label for="templateSelect" class="form-label">Select Template</label>
                <select v-model="selectedTemplate" class="form-control" id="templateSelect" required>
                  <option v-for="template in templates" :key="template.id" :value="template.id">
                    {{ template.name }}
                  </option>
                </select>
              </div>
              <div class="mb-3">
                <label for="studentSelect" class="form-label">Select Student</label>
                <select v-model="selectedStudent" class="form-control" id="studentSelect" required>
                  <option v-for="student in students" :key="student.id" :value="student.id">
                    {{ student.first_name }} {{ student.last_name }}
                  </option>
                </select>
              </div>
              <div class="mb-3">
                <label for="sessionSelect" class="form-label">Select Session</label>
                <select v-model="selectedSession" class="form-control" id="sessionSelect" required>
                  <option v-for="session in sessions" :key="session.id" :value="session.id">
                    {{ session.created_at }}
                  </option>
                </select>
              </div>
              <button type="submit" class="btn btn-primary">Generate Report</button>
            </form>

            <div v-if="report" class="mt-4">
              <h3>Generated Report</h3>
              <pre>{{ report }}</pre>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import axios from 'axios';

export default {
  setup() {
    const template = ref({
      name: '',
      content: '',
    });

    const templates = ref([]);
    const students = ref([]);
    const sessions = ref([]);
    const selectedTemplate = ref(null);
    const selectedStudent = ref(null);
    const selectedSession = ref(null);
    const report = ref('');

    const submitTemplate = () => {
      axios.post('/public/reports/templates', template.value)
        .then(() => {
          alert('Template saved successfully!');
          fetchTemplates();
        })
        .catch(error => {
          console.error('There was an error saving the template:', error);
        });
    };

    const fetchTemplates = async () => {
      try {
        const response = await axios.get('/public/reports/templates/list');
        templates.value = response.data.templates;
      } catch (error) {
        console.error('Failed to fetch templates:', error);
      }
    };

    const fetchStudents = async () => {
      try {
        const response = await axios.get('/public/students');
        students.value = response.data;
      } catch (error) {
        console.error('Failed to fetch students:', error);
      }
    };

    const fetchSessions = async () => {
      try {
        const response = await axios.get('/public/passed-sessions');
        sessions.value = response.data;
      } catch (error) {
        console.error('Failed to fetch sessions:', error);
      }
    };

    const generateReport = async () => {
      try {
        const response = await axios.post(`/public/report-templates/${selectedTemplate.value}/generate`, {
          student_id: selectedStudent.value,
          session_id: selectedSession.value,
        });
        report.value = response.data.report;
      } catch (error) {
        console.error('Failed to generate report:', error);
      }
    };
    onMounted(() => {
      fetchTemplates();
      fetchStudents();
      fetchSessions();
    });

    return {
      template,
      templates,
      students,
      sessions,
      selectedTemplate,
      selectedStudent,
      selectedSession,
      report,
      submitTemplate,
      generateReport,
    };
  },
};
</script>

<style scoped>
.container {
  max-width: 800px;
}

.card-header {
  background-color: #f8f9fa;
  border-bottom: 1px solid #dee2e6;
}

.btn-primary {
  background-color: #007bff;
  border-color: #007bff;
}

.btn-primary:hover {
  background-color: #0056b3;
  border-color: #004085;
}

.form-label {
  font-weight: bold;
}
</style>
