    <div class="container">
        <h2>Report Templates</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Content</th>
                    <th>Created At</th>
                    <th>Updated At</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($templates as $template)
                    <tr>
                        <td>{{ $template->id }}</td>
                        <td>{{ $template->name }}</td>
                        <td>{{ $template->content }}</td>
                        <td>{{ $template->created_at }}</td>
                        <td>{{ $template->updated_at }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>