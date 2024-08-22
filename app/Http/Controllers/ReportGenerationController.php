<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ReportTemplate;

class ReportGenerationController extends Controller
{
    public function showTemplate()
    {
        logger('ReportTemplatePage view loaded');
        return inertia('ReportTemplatePage');
    }
    public function generate(Request $request)
    {
        $request->validate([
            'template_id' => 'required|exists:report_templates,id',
            'data' => 'required|json',
        ]);

        $template = ReportTemplate::findOrFail($request->template_id);
        $data = json_decode($request->data, true);

        $reportContent = $this->replaceShortcodes($template->content, $data);

        return response()->json(['report' => $reportContent]);
    }

    private function replaceShortcodes($templateContent, $data)
    {
        $shortcodes = [
            '@student_full_name' => $data['student_full_name'] ?? '',
            '@session_date' => $data['session_date'] ?? '',
            '@session_minutes' => $data['session_minutes'] ?? '',
            '@session_start_time' => $data['session_start_time'] ?? '',
            '@session_end_time' => $data['session_end_time'] ?? '',
            '@target_start_date' => $data['target_start_date'] ?? '',
            '@target_end_date' => $data['target_end_date'] ?? '',
            '@target' => $data['target'] ?? '',
        ];

        return str_replace(array_keys($shortcodes), array_values($shortcodes), $templateContent);
    }

    public function template_generate(Request $request, $id)
    {
        $template = ReportTemplate::findOrFail($id);
        $student = Student::findOrFail($request->student_id);
        $session = StudentSession::findOrFail($request->session_id);

        $report = $template->template;
        $report = str_replace('@student_full_name', $student->full_name, $report);
        $report = str_replace('@session_date', $session->session_date, $report);
        $report = str_replace('@session_minutes', $session->session_minutes, $report);
        $report = str_replace('@session_start_time', $session->start_time, $report);
        $report = str_replace('@session_end_time', $session->end_time, $report);
        $report = str_replace('@target_start_date', $session->target_start_date, $report);
        $report = str_replace('@target_end_date', $session->target_end_date, $report);
        $report = str_replace('@target', $session->target, $report);

        return response()->json(['report' => $report]);
    }
}
