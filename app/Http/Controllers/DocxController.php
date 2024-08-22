<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpWord\IOFactory;
use App\Models\StudentImprovement;
use App\Helpers\DocxParser;

class DocxController extends Controller
{
    public function uploadDocx(Request $request)
    {
        $request->validate([
            'docx_file' => 'required|mimes:docx|max:2048',
        ]);

        $file = $request->file('docx_file');
        $content = DocxParser::parse($file->getPathname());
        $this->saveImprovements($content);

        return response()->json(['message' => 'File uploaded and data parsed successfully.']);
    }

    private function saveImprovements($content)
    {
        foreach ($content['Practical'] as $item) {
            $start_date = $this->correctDate($item['start_date']);
            $end_date = $this->correctDate($item['end_date']);
            StudentImprovement::create([
                'type' => 'Practical Knowledge',
                'start_date' => $start_date,
                'end_date' => $end_date,
                'target' => $item['target'],
            ]);
        }

        foreach ($content['Theoretical'] as $item) {
            $start_date = $this->correctDate($item['start_date']);
            $end_date = $this->correctDate($item['end_date']);
            StudentImprovement::create([
                'type' => 'Theoretical Knowledge',
                'start_date' => $start_date,
                'end_date' => $end_date,
                'target' => $item['target'],
            ]);
        }
    }

    private function correctDate($date)
    {
        $dateParts = explode('-', $date);
        $year = $dateParts[0];
        $month = $dateParts[1];
        $day = $dateParts[2];

        $maxDay = date('t', mktime(0, 0, 0, $month, 1,  $year));

        if ($day > $maxDay) {
            $day = $maxDay;
        }

        return sprintf('%04d-%02d-%02d', $year, $month, $day);
    }
}
