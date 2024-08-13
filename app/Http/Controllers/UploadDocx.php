<?php

use Illuminate\Http\Request;
use PhpOffice\PhpWord\IOFactory;
use App\Models\StudentImprovement;

class ParseMSDocxFiles extends Controller
{
    public function parseAndStoreDocx(Request $request)
    {
        $file = $request->file('docx_file');
        $phpWord = IOFactory::load($file->getPathname());

        $parsedData = [];

        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                if (method_exists($element, 'getElements')) {
                    foreach ($element->getElements() as $childElement) {
                        if ($childElement instanceof \PhpOffice\PhpWord\Element\TextRun) {
                            foreach ($childElement->getElements() as $textElement) {
                            }
                        }
                    }
                }
            }
        }

        foreach ($parsedData as $data) {
            StudentImprovement::create([
                'student_id' => $data['student_id'],
                'subject_name' => $data['subject_name'],
                'area_of_weakness' => $data['area_of_weakness'],
                'session_start_date' => $data['session_start_date'],
                'session_end_date' => $data['session_end_date'],
                'improvement_target' => $data['improvement_target'],
            ]);
        }

        return response()->json(['message' => 'Data parsed and stored successfully']);
    }
}