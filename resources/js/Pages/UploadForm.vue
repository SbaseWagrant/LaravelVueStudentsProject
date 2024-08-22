<template>
    <div class="container mx-auto">
        <h1 class="text-2xl font-bold mb-4">Upload MS-Docx File</h1>

        <form @submit.prevent="uploadDocument" enctype="multipart/form-data">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Choose MS-Docx File</label>
                <input type="file" @change="handleFileUpload" accept=".docx" class="mt-1 block w-full">
            </div>

            <div class="mb-4">
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Upload
                </button>
            </div>
        </form>

        <div v-if="successMessage" class="text-green-500 mt-4">
            {{ successMessage }}
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            docxFile: null,
            successMessage: ''
        };
    },
    methods: {
        handleFileUpload(event) {
            this.docxFile = event.target.files[0];
        },
        async uploadDocument() {
            if (!this.docxFile) {
                alert('Please select a file first.');
                return;
            }

            const formData = new FormData();
            formData.append('docx_file', this.docxFile);

            try {
                const response = await axios.post('/public/upload-docx', formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });

                this.successMessage = response.data.message || 'File uploaded and data parsed successfully.';
                this.docxFile = null;
            } catch (error) {
                console.error('An error occurred:', error);
            }
        }
    }
};
</script>

<style scoped>
/* Add your custom styles here if needed */
</style>
