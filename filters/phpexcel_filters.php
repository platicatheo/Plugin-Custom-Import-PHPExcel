<?php

class SampleReadFilter implements PHPExcel_Reader_IReadFilter {
    public function readCell($column, $row, $worksheetName = '') {
        // Read rows 1 to 10 and columns A to C only
        if ($row >= 2) {
           if (in_array($column,range('A','C'))) {
           	 // echo '<pre>'; print_r($column); echo '</pre>';
             return true;
           }
        }
        return false;
    }
}