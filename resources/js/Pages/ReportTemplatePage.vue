<template>
    <div>
        <h1>Generate Report Template</h1>
        <form @submit.prevent="generateReport">
            <div class="form-group">
                <label for="template">Report Template:</label>
                <textarea id="template" v-model="templateContent" class="form-control" rows="10" placeholder="Enter your report template with shortcodes"></textarea>
            </div>

            <div class="form-group mt-3">
                <button type="submit" class="btn btn-primary">Generate Report</button>
            </div>
        </form>

        <div v-if="generatedReport" class="mt-5">
            <h2>Generated Report</h2>
            <pre>{{ generatedReport }}</pre>
        </div>
    </div>
</template>

<script>
import { Inertia } from '@inertiajs/inertia';

export default {
    data() {
        return {
            templateContent: '',
            generatedReport: null,
        };
    },
    methods: {
        async generateReport() {
            try {
                const response = await Inertia.post('/public/reports/generate', {
                    template: this.templateContent
                });

                this.generatedReport = response.props.generatedReport;
            } catch (error) {
                console.error("Error generating report:", error);
            }
        }
    }
};
</script>

<style scoped>
.form-group {
    margin-bottom: 15px;
}

textarea {
    width: 100%;
    resize: none;
    padding: 10px;
}

button {
    padding: 10px 15px;
}

pre {
    background-color: #f8f9fa;
    padding: 15px;
    border-radius: 5px;
    white-space: pre-wrap;
}
</style>
