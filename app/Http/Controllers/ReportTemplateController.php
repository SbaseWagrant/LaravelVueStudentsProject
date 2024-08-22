<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ReportTemplate;
use App\Models\Student;
use App\Models\StudentSession;
use Illuminate\Support\Facades\Storage;

class ReportTemplateController extends Controller
{
    public function create()
    {
        return inertia('CreateReportTemplate');
    }

    public function index()
    {
        $templates = ReportTemplate::all();
        return response()->json($templates);
    }

    public function store(Request $request)
    {
        $template = new ReportTemplate();
        $template->name = $request->name;
        $template->content = $request->content;
        $template->save();

        return response()->json(['message' => 'Template saved successfully!'], 201);
    }

    public function listView()
    {
        $templates = ReportTemplate::all();
        return view('list', compact('templates'));
    }

    public function list()
    {
        $templates = ReportTemplate::all();
        return response()->json(['templates' => $templates]);
    }

    public function generate(Request $request, $id)
    {
        $template = ReportTemplate::findOrFail($id);
        $student = Student::findOrFail($request->student_id);
        $session = StudentSession::findOrFail($request->session_id);

        $report = $template->content;
        $report = str_replace('@student_full_name', $student->first_name, $report);
        $report = str_replace('@session_date', $session->created_at, $report);
        $report = str_replace('@session_minutes', $session->session_minutes, $report);
        $report = str_replace('@session_start_time', $session->start_time, $report);
        $report = str_replace('@session_end_time', $session->end_time, $report);
        $report = str_replace('@target_start_date', $session->created_at, $report);
        $report = str_replace('@target_end_date', $session->updated_at, $report);
        $report = str_replace('@target', $session->rating, $report);

        $fileName = 'report_' . $student->id . '_' . now()->format('YmdHis') . '.txt';
        $filePath = 'reports/' . $fileName;
        Storage::put($filePath, $report);

        return response()->json([
            'report' => $report,
            'file_url' => Storage::url($filePath)
        ]);
    }
}

