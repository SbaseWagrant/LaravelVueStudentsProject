<!-- resources/js/components/CreateReportTemplate.vue -->
<template>
    <div class="container mt-5">
      <div class="row justify-content-center">
        <div class="col-md-8">
          <div class="card">
            <div class="card-header">
              <h2>Create Report Template</h2>
            </div>
            <div class="card-body">
              <form @submit.prevent="submitTemplate">
                <div class="mb-3">
                  <label for="name" class="form-label">Template Name</label>
                  <input type="text" class="form-control" id="name" v-model="template.name">
                </div>
                <div class="mb-3">
                  <label for="template" class="form-label">Template Content</label>
                  <textarea class="form-control" id="template" rows="6" v-model="template.content"></textarea>
                  <small class="form-text text-muted">
                    Use the following shortcodes in your template:<br>
                    @student_full_name, @session_date, @session_minutes, @session_start_time, @session_end_time, @target_start_date, @target_end_date, @target
                  </small>
                </div>
                <button type="submit" class="btn btn-primary">Save Template</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  import axios from 'axios';
  
  export default {
    data() {
      return {
        template: {
          name: '',
          content: ''
        }
      };
    },
    methods: {
      submitTemplate() {
        axios.post('/api/reports/templates', this.template)
          .then(response => {
            alert('Template saved successfully!');
          })
          .catch(error => {
            console.error('There was an error saving the template:', error);
          });
      }
    }
  };
  </script>
  
  <style scoped>
  .container {
    margin-top: 2rem;
  }
  .card-header h2 {
    margin-bottom: 0;
  }
  .btn-primary {
    margin-top: 1rem;
  }
  </style>
  