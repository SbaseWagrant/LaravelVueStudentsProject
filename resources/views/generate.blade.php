<div class="container">
    <h2>Generate Report</h2>
    <form id="generate-report-form">
        <div class="mb-3">
            <label for="template" class="form-label">Select Template</label>
            <select class="form-select" id="template" v-model="selectedTemplate">
                <option v-for="template in templates" :value="template.id">@{{ template.name }}</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="session" class="form-label">Select Student Session</label>
            <select class="form-select" id="session" v-model="selectedSession">
                <option v-for="session in sessions" :value="session.id">@{{ session.student.full_name }} - @{{ session.start_time }}</option>
            </select>
        </div>
        <button type="button" class="btn btn-primary" @click="generateReport">Generate Report</button>
    </form>
</div>

@section('scripts')
<script>
    new Vue({
        el: '#generate-report-form',
        data: {
            templates: [],
            sessions: [],
            selectedTemplate: null,
            selectedSession: null
        },
        created() {
            this.fetchTemplates();
            this.fetchSessions();
        },
        methods: {
            fetchTemplates() {
                axios.get('/reports/templates')
                    .then(response => {
                        this.templates = response.data;
                    })
                    .catch(error => {
                        console.error('There was an error fetching templates:', error);
                    });
            },
            fetchSessions() {
                axios.get('/reports/sessions')
                    .then(response => {
                        this.sessions = response.data;
                    })
                    .catch(error => {
                        console.error('There was an error fetching sessions:', error);
                    });
            },
            generateReport() {
                const url = `/reports/generate/${this.selectedTemplate}/${this.selectedSession}`;
                window.open(url, '_blank');
            }
        }
    });
</script>
@endsection