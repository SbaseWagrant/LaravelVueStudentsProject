<template>
    <div class="report-generator-container">
      <h2>Generate Report</h2>
      <form @submit.prevent="generateReport" class="form-container">
        <div>
          <label for="start-date">Start Date:</label>
          <input v-model="reportParams.startDate" type="date" id="start-date" required class="form-input"/>
        </div>
        <div>
          <label for="end-date">End Date:</label>
          <input v-model="reportParams.endDate" type="date" id="end-date" required class="form-input"/>
        </div>
        <div>
          <label for="template">Report Template:</label>
          <textarea v-model="reportParams.template" id="template" rows="4" required class="form-input"></textarea>
        </div>
        <button type="submit" class="submit-button">Generate Report</button>
      </form>
      <div v-if="reportUrl">
        <h3>Generated Report</h3>
        <a :href="reportUrl" target="_blank" class="report-link">Download Report</a>
      </div>
    </div>
  </template>
  
  <script>
  import axios from 'axios';
  
  export default {
    data() {
      return {
        reportParams: {
          startDate: '',
          endDate: '',
          template: '',
        },
        reportUrl: null,
      };
    },
    methods: {
      async generateReport() {
        try {
          const response = await axios.post('/generate-report', this.reportParams, {
            responseType: 'blob',
          });
          const url = window.URL.createObjectURL(new Blob([response.data]));
          this.reportUrl = url;
        } catch (error) {
          console.error('An error occurred while generating the report:', error);
        }
      },
    },
  };
  </script>
  
  <style scoped>
  .report-generator-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
    font-family: Arial, sans-serif;
  }
  
  .form-container {
    display: flex;
    flex-direction: column;
    align-items: center;
  }
  
  .form-input {
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
  
  .submit-button:hover {
    background-color: #0056b3;
  }
  
  .report-link {
    color: #007bff;
  }
  </style>
  