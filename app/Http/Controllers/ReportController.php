<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PDF;

class ReportController extends Controller
{
    public function generateReport(Request $request)
    {
        $validated = $request->validate([
            'startDate' => 'required|date',
            'endDate' => 'required|date',
            'template' => 'required|string',
        ]);

        $startDate = $validated['startDate'];
        $endDate = $validated['endDate'];
        $template = $validated['template'];

        $reportData = [
            'student_name' => 'TEST test test',
            'session_date' => '2024-08-10',
            'session_minutes' => 15,
            'session_start_time' => '10:00 AM',
            'session_end_time' => '10:15 AM',
            'target_start_date' => $startDate,
            'target_end_date' => $endDate,
            'target' => 'Improve math skills',
            'session_rating' => 8,
        ];

        // Generate PDF
        $pdf = PDF::loadView('reports.report', $reportData);

        return $pdf->download('report.pdf');
    }
}
