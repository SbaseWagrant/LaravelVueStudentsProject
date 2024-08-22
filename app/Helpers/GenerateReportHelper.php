<?php

namespace App\Helpers;

use App\Models\StudentSession;
use App\Models\ReportTemplate;

class GenerateReportHelper
{
    public function generateReport($templateId, $studentSessionId)
    {
        // Fetch the template and session data
        $template = ReportTemplate::findOrFail($templateId);
        $session = StudentSession::with('student')->findOrFail($studentSessionId);

        // Prepare data for shortcodes
        $shortcodes = [
            '@student_full_name' => $session->student->full_name,
            '@session_date' => $session->start_time->format('Y-m-d'),
            '@session_minutes' => $session->start_time->diffInMinutes($session->end_time),
            '@session_start_time' => $session->start_time->format('H:i'),
            '@session_end_time' => $session->end_time->format('H:i'),
            '@target_start_date' => $session->target_start_date,
            '@target_end_date' => $session->target_end_date,
            '@target' => $session->target,
        ];

        // Replace shortcodes in the template with actual data
        $reportContent = strtr($template->template, $shortcodes);

        return $reportContent;
    }
}
