<?php
include 'excel_reader.php';
$excel = new PhpExcelReader;
$excel->read('5cb18a1a28cc21555139098.xls');

function sheetData($sheet)
{
    echo "<pre>";
    print_r($sheet);
    die();
    $re = '<?xml version="1.0" encoding="UTF-8" ?>';
    $x = 2;
    while ($x <= $sheet['numRows']) {
        $y = 1;
        $re .= "<node>";
        while ($y <= $sheet['numCols']) {
            $cell = isset($sheet['cells'][1][$y]) ? $sheet['cells'][1][$y] : '';
            $cell2 = isset($sheet['cells'][$x][$y]) ? $sheet['cells'][$x][$y] : '';
            $re .= "<$cell>$cell2</$cell>";
            $y++;
        }
        $x++;
        $re .= "</node>";
    }
    $name = uniqid();
    $myfile = fopen("$name.xml", "w") or die("Unable to open file!");
    fwrite($myfile, $re);
    fclose($myfile);
    echo $name . ".xml Create successfully";
}

// main retrive data:

$nr_sheets = count($excel->sheets);
$excel_data = '';

for ($i = 0; $i < $nr_sheets; $i++) {
    $excel_data .= '<h4>Sheet ' . ($i + 1) . ' (<em>' . $excel->boundsheets[$i]['name'] . '</em>)</h4>' . sheetData($excel->sheets[$i]) . '<br/>';
}

?>

