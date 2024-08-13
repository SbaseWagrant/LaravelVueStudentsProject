<!DOCTYPE html>
<html>
<head>
    <title>Session Report</title>
</head>
<body>
    <h1>Session Report</h1>
    <table border="1">
        <thead>
            <tr>
                <th>Student Name</th>
                <th>Session Date</th>
                <th>Start Time</th>
                <th>End Time</th>
                <th>Rating</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sessions as $session)
            <tr>
                <td>{{ $session->student->first_name }} {{ $session->student->last_name }}</td>
                <td>{{ $session->start_time->format('Y-m-d') }}</td>
                <td>{{ $session->start_time->format('H:i:s') }}</td>
                <td>{{ $session->end_time->format('H:i:s') }}</td>
                <td>{{ $session->rating }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>