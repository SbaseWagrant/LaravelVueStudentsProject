<template>
    <div class="upload-container">
      <h2>Upload Student Improvement Data</h2>
      <form @submit.prevent="uploadDocx" enctype="multipart/form-data">
        <input type="file" @change="handleFileUpload" accept=".docx" required />
        <button type="submit">Upload and Parse</button>
      </form>
      <div v-if="uploadSuccessMessage" class="text-green-500">{{ uploadSuccessMessage }}</div>
      <div v-if="uploadErrorMessage" class="text-red-500">{{ uploadErrorMessage }}</div>
    </div>
  </template>
  
  <script>
  export default {
    data() {
      return {
        file: null,
        uploadSuccessMessage: '',
        uploadErrorMessage: ''
      };
    },
    methods: {
      handleFileUpload(event) {
        this.file = event.target.files[0];
      },
      async uploadDocx() {
        const formData = new FormData();
        formData.append('file', this.file);
  
        try {
          const response = await axios.post('/public/upload-docx', formData, {
            headers: {
              'Content-Type': 'multipart/form-data'
            }
          });
  
          this.uploadSuccessMessage = 'File uploaded and parsed successfully!';
          this.uploadErrorMessage = '';
        } catch (error) {
          this.uploadSuccessMessage = '';
          this.uploadErrorMessage = 'Error uploading or parsing the file.';
        }
      }
    }
  };
  </script>  