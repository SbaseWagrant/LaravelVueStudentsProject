<?php

namespace App\Helpers;

use SimpleXMLElement;
use ZipArchive;

class DocxParser
{
    public static function parse($filePath)
    {
        $zip = new ZipArchive;
        if ($zip->open($filePath) === true) {
            if (($index = $zip->locateName('word/document.xml')) !== false) {
                $data = $zip->getFromIndex($index);
                $zip->close();

                $xml = new SimpleXMLElement($data);
                $xml->registerXPathNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');

                $practicalTextNodes = $xml->xpath('//w:p//w:r//w:t');
                $practicalText = '';
                foreach ($practicalTextNodes as $textNode) {
                    $practicalText .= (string)$textNode . ' ';
                }

                $theoreticalTextNodes = $xml->xpath('//w:tbl//w:tr//w:tc//w:p//w:r//w:t');
                $theoreticalText = '';
                foreach ($theoreticalTextNodes as $textNode) {
                    $theoreticalText .= (string)$textNode . ' ';
                }

                $practicalPattern = '/(?:Session\s+start\s+date|Start\s+Date)\s*(\d{4}-\s*\d{2}-\s*\d{2})\s*(?:Session\s+end\s+date|End\s+Date)\s*(\d{4}-\s*\d{2}-\s*\d{2})\s*(?:Improvement\s+target|target)\s*(\d+)/is';

                $theoreticalPattern = '/(?:Start\s+From\s+|)\s*(\d{4}-\d{2}-\d{2})\s*(?:End\s+To\s+|)\s*(\d{4}-\d{2}-\d{2})\s*(\d+)\s*(?:per\s+session|)/is';
                $results = [];
                preg_match_all($practicalPattern, $practicalText, $practicalMatches, PREG_SET_ORDER);
                foreach ($practicalMatches as $match) {
                    $results['Practical'][] = [
                        'start_date'   => trim(preg_replace('/\s+/', '', $match[1])),
                        'end_date'     => trim(preg_replace('/\s+/', '', $match[2])),
                        'target'       => trim($match[3])
                    ];
                }

                preg_match_all($theoreticalPattern, $theoreticalText, $theoreticalMatches, PREG_SET_ORDER);
                foreach ($theoreticalMatches as $match) {
                    $results['Theoretical'][] = [
                        'start_date'   => trim($match[1]),
                        'end_date'     => trim($match[2]),
                        'target'       => trim($match[3])
                    ];
                }
                
                if (count($results['Practical']) <= 1) {
                    $results['Practical'] = self::extractPracticalKnowledge($practicalText);
                    $results['Theoretical'] = self::extractTheoreticalKnowledge(strip_tags($data));
                }
                return $results;
            }
        }
        return null;
    }

    public static function extractPracticalKnowledge($practicalText)
    {
        // Adjusted regex pattern to account for irregular spaces and different date formats
        $pattern = '/(?:Session start date|Start Date)\s*([0-9]{4})[\s\-]*([0-9\s]+)[\s\-]*([0-9\s]+).*?(?:Session end date|End Date)\s*([0-9]{4})[\s\-]*([0-9\s]+)[\s\-]*([0-9\s]+).*?(?:Improvement target|target)\s*(\d+)/is';
    
        preg_match_all($pattern, $practicalText, $matches, PREG_SET_ORDER);
    
        $results = [];
    
        foreach ($matches as $match) {
            // Remove any spaces within the date components
            $month_start = trim(str_replace(' ', '', $match[2]));
            $day_start = trim(str_replace(' ', '', $match[3]));
            $month_end = trim(str_replace(' ', '', $match[5]));
            $day_end = trim(str_replace(' ', '', $match[6]));
    
            // Format the start and end dates
            $start_date = sprintf("%04d-%02d-%02d", $match[1], $month_start, $day_start);
            $end_date = sprintf("%04d-%02d-%02d", $match[4], $month_end, $day_end);
            $target = $match[7];
    
            // Add the extracted data to the results array
            $results[] = [
                'start_date' => $start_date,
                'end_date' => $end_date,
                'target' => $target
            ];
        }
        return $results;
    }
    


    public static function extractTheoreticalKnowledge($content)
    {
        $pattern = '/(\d{4}-\d{2}-\d{2})\s*to\s*(\d{4}-\d{2}-\d{2})\s*(\d+)/i';

        preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);

        $data = [];

        foreach ($matches as $match) {
            $start_date = $match[1] ?? null;
            $end_date = $match[2] ?? null;
            $target = $match[3] ?? null;


            $data[] = [
                'start_date' => $start_date,
                'end_date' => $end_date,
                'target' => $target,
            ];
        }

        return $data;
    } 
}