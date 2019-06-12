<?php
error_reporting(0);
error_reporting(E_ERROR | E_WARNING | E_PARSE);
error_reporting(E_ALL);
ini_set("error_reporting", E_ALL);
error_reporting(E_ALL & ~E_NOTICE);
include('database.php');
session_start();
$id = $_COOKIE['id'];
$sql = "select * from user where id='$id'";
$result = mysqli_query($conn, $sql);
$value = mysqli_fetch_object($result);
$_SESSION['carrier_code'] = $value->carrier_code;
if (isset($_POST['upload'])) {
    $target_dir = "xlsx/";
    $target_file = basename($_FILES["excel"]["name"]);
    $FileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    $file_name = uniqid() . time();
    $file_name = $file_name . "." . 'xls';
    $target_fileName = $target_dir . $file_name;
    if ($FileType != 'xls') {
        $_SESSION['message'] = 'Upload Only Excel File';
        header('location: converter/man.php');
    } else {
        if (move_uploaded_file($_FILES["excel"]["tmp_name"], $target_fileName)) {
            if (convert($target_fileName)) {
                $file_name2 = $_SESSION["name"];
                $insert = "INSERT INTO file (user_id,file_name,base_name,converter) VALUES('$id','$file_name2','$target_file',0)";
                mysqli_query($conn, $insert);
                delete($target_fileName);
                $_SESSION['message'] = 'File upload successfully';
                header('location: converter/man.php');
            } else {
                $_SESSION['message'] = 'Error';
                header('location: converter/man.php');
            }
        } else {
            $_SESSION['message'] = 'File upload Error';
            header('location: converter/man.php');
        }
    }
}

if (isset($_POST['uploadDEG'])) {
    $target_dir = "xlsx/";
    $target_file = basename($_FILES["excel"]["name"]);
    $FileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    $file_name = uniqid() . time();
    $file_name = $file_name . "." . 'xls';
    $target_fileName = $target_dir . $file_name;
    if ($FileType != 'xls') {
        $_SESSION['message'] = 'Upload Only Excel File';
        header('location: converter/deg.php');
    } else {
        if (move_uploaded_file($_FILES["excel"]["tmp_name"], $target_fileName)) {
            if (convert2($target_fileName)) {
                $file_name2 = $_SESSION["name"];
                $insert = "INSERT INTO file (user_id,file_name,base_name,converter) VALUES('$id','$file_name2','$target_file',1)";
                mysqli_query($conn, $insert);
                delete($target_fileName);
                $_SESSION['message'] = 'File upload successfully';
                header('location:converter/deg.php');
            } else {
                $_SESSION['message'] = 'Error';
                header('location: converter/deg.php');
            }
        } else {
            $_SESSION['message'] = 'File upload Error';
            header('location: converter/deg.php');
        }
    }
}

function convert($target_fileName)
{
    include 'excel_reader.php';
    $excel = new PhpExcelReader;
    $excel->read($target_fileName);


    function sheetData($sheet)
    {
        $row2 = isset($sheet['cells'][2][15]) ? $sheet['cells'][2][15] : '';
        if ($row2 != $_SESSION['carrier_code']){
            $_SESSION['message'] = 'Carrier code not match.';
            header('location: converter/man.php');
            die();
        }

        //echo "<pre>";
        //print_r($sheet);
        //die();
        $re = '<?xml version="1.0" encoding="UTF-8" standalone="yes" ?>' . "\n";
        $re2 = '';
        $x = 2;
        $code = "";

        $y = 1;
        while ($y <= $sheet['numCols']) {
            $cell = isset($sheet['cells'][1][$y]) ? $sheet['cells'][1][$y] : '';
            if ($y == 1) {
                $re .= "<$cell>\n";
                $re2 .= "</$cell>\n";
            } else if ($y == 2) {
                $re .= "\t<$cell>\n";
            } else if ($y == 3) {
                $re .= "\t\t<$cell>\n";
                $x = 4;
                while ($x <= 7) {
                    $cell2 = isset($sheet['cells'][1][$x]) ? $sheet['cells'][1][$x] : '';
                    $row2 = isset($sheet['cells'][2][$x]) ? $sheet['cells'][2][$x] : '';
                    if ($x == 4) {
                        $re .= "\t\t\t<$cell2>" . filter($cell2, $row2, 5, 1, 'string') . "</$cell2>\n";
                    } else if ($x == 5) {
                        $re .= "\t\t\t<$cell2>" . filter($cell2, $row2, 17, 1, 'string') . "</$cell2>\n";
                    } else if ($x == 6) {
                        $re .= "\t\t\t<$cell2>" . filter($cell2, $row2, false, false, 'date') . "</$cell2>\n";
                    } else {
                        $re .= "\t\t\t<$cell2>" . filter($cell2, $row2, false, false, 'date') . "</$cell2>\n";
                    }
                    $x++;
                }
                $re .= "\t\t</$cell>\n";
            } else if ($y == 8) {
                $re .= "\t\t<$cell>\n";
                $x = 9;
                while ($x <= 12) {
                    $cell2 = isset($sheet['cells'][1][$x]) ? $sheet['cells'][1][$x] : '';
                    $row2 = isset($sheet['cells'][2][$x]) ? $sheet['cells'][2][$x] : '';
                    //Total Number of bol
                    if ($x == 9) {
                        $k = 2;
                        $total_num_bol = 0;
                        while ($k <= $sheet['numRows']) {
                            $row2 = isset($sheet['cells'][$k][29]) ? $sheet['cells'][$k][29] : '';
                            if ($row2 != '') {
                                $total_num_bol++;
                            }
                            $k++;
                        }
                        $re .= "\t\t\t<$cell2>" . filter($cell2, $total_num_bol, false, false, 'integer') . "</$cell2>\n";
                        //Total number of packages
                    } else if ($x == 10) {
                        $k = 2;
                        $total_num_pack = 0;
                        while ($k <= $sheet['numRows']) {
                            $row2 = isset($sheet['cells'][$k][59]) ? $sheet['cells'][$k][59] : '';
                            if ($row2 != '') {
                                $total_num_pack+=$row2;
                            }
                            $k++;
                        }
                        $re .= "\t\t\t<$cell2>" . filter($cell2, $total_num_pack, false, false, 'double') . "</$cell2>\n";
                    } else if ($x == 11) {
                        $re .= "\t\t\t<$cell2>" . filter($cell2, $row2, false, false, 'integer') . "</$cell2>\n";
                        //Total gross mass
                    } else {
                        $k = 2;
                        $total_gross_mass = 0;
                        while ($k <= $sheet['numRows']) {
                            $row2 = isset($sheet['cells'][$k][61]) ? $sheet['cells'][$k][61] : '';
                            if ($row2 != '') {
                                $total_gross_mass+=$row2;
                            }
                            $k++;
                        }
                        $re .= "\t\t\t<$cell2>" . filter($cell2, $total_gross_mass, false, false, 'double') . "</$cell2>\n";
                    }
                    $x++;
                }
                $re .= "\t\t</$cell>\n";
            } else if ($y == 13) {
                $re .= "\t\t<$cell>\n";
                $cell3 = isset($sheet['cells'][1][14]) ? $sheet['cells'][1][14] : '';
                $re .= "\t\t\t<$cell3>\n";
                $x = 15;
                while ($x <= 17) {
                    $cell2 = isset($sheet['cells'][1][$x]) ? $sheet['cells'][1][$x] : '';
                    $row2 = isset($sheet['cells'][2][$x]) ? $sheet['cells'][2][$x] : '';

                    if ($x == 15) {
                        $re .= "\t\t\t\t<$cell2>" . filter($cell2, $row2, 17, 1, 'string') . "</$cell2>\n";
                    } else if ($x == 16) {
                        $re .= "\t\t\t\t<$cell2>" . filter($cell2, $row2, 35, 1, 'string', true) . "</$cell2>\n";
                    } else {
                        $re .= "\t\t\t\t<$cell2>" . filter($cell2, $row2, 175, 1, 'string', true) . "</$cell2>\n";
                    }

                    $x++;
                }
                $re .= "\t\t\t</$cell3>\n";

                $cell3 = isset($sheet['cells'][1][18]) ? $sheet['cells'][1][18] : '';
                $re .= "\t\t\t<$cell3>\n";
                $x = 19;
                while ($x <= 20) {
                    $cell2 = isset($sheet['cells'][1][$x]) ? $sheet['cells'][1][$x] : '';
                    $row2 = isset($sheet['cells'][2][$x]) ? $sheet['cells'][2][$x] : '';
                    $row2 = preg_replace('/&(?!#?[a-z0-9]+;)/', '&amp;', $row2);
                    if ($x == 19) {
                        //Shipping_Agent_code
                        $re .= "\t\t\t\t".tag($cell2,$row2)."\n";
                    } else {
                        //Shipping_Agent_name
                        $value = filter($cell2, $row2, 35, 0, 'string');
                        $value = $value==0? '':$value;
                        $re .= "\t\t\t\t<$cell2>" . $value . "</$cell2>\n";
                    }
                    $x++;
                }
                $re .= "\t\t\t</$cell3>\n";

                $cell2 = isset($sheet['cells'][1][21]) ? $sheet['cells'][1][21] : '';
                $row2 = isset($sheet['cells'][2][21]) ? $sheet['cells'][2][21] : '';
                $re .= "\t\t\t<$cell2>" . filter($cell2, $row2, 3, 1, 'string') . "</$cell2>\n";

                $cell2 = isset($sheet['cells'][1][22]) ? $sheet['cells'][1][22] : '';
                $row2 = isset($sheet['cells'][2][22]) ? $sheet['cells'][2][22] : '';
                $re .= "\t\t\t<$cell2>" . filter($cell2, $row2, 27, 0, 'string') . "</$cell2>\n";

                $cell2 = isset($sheet['cells'][1][23]) ? $sheet['cells'][1][23] : '';
                $row2 = isset($sheet['cells'][2][23]) ? $sheet['cells'][2][23] : '';
                $re .= "\t\t\t<$cell2>" . filter($cell2, $row2, 3, 1, 'string') . "</$cell2>\n";

                $re .= "\t\t</$cell>\n";

                $cell3 = isset($sheet['cells'][1][24]) ? $sheet['cells'][1][24] : '';
                $re .= "\t\t<$cell3>\n";
                $x = 25;
                while ($x <= 26) {
                    $cell2 = isset($sheet['cells'][1][$x]) ? $sheet['cells'][1][$x] : '';
                    $row2 = isset($sheet['cells'][2][$x]) ? $sheet['cells'][2][$x] : '';
                    $row2 = preg_replace('/&(?!#?[a-z0-9]+;)/', '&amp;', $row2);
                    if ($x == 25) {
                        $re .= "\t\t\t<$cell2>" . filter($cell2, $row2, 5, 1, 'string') . "</$cell2>\n";
                    } else {
                        $re .= "\t\t\t<$cell2>" . filter($cell2, $row2, 5, 1, 'string') . "</$cell2>\n";
                    }
                    $x++;
                }
                $re .= "\t\t</$cell3>\n";


                $cell2 = isset($sheet['cells'][1][2]) ? $sheet['cells'][1][2] : '';
                $re .= "\t</$cell2>\n";
            } else if ($y == 27) {
                $k = 2;
                while ($k <= $sheet['numRows']) {
                    $re .= "\t<$cell>\n";

                    $cell3 = isset($sheet['cells'][1][28]) ? $sheet['cells'][1][28] : '';
                    $re .= "\t\t<$cell3>\n";
                    $x = 29;
                    while ($x <= 34) {
                        $cell2 = isset($sheet['cells'][1][$x]) ? $sheet['cells'][1][$x] : '';
                        $row2 = isset($sheet['cells'][$k][$x]) ? $sheet['cells'][$k][$x] : '';

                        if ($x == 29) {
                            $re .= "\t\t\t<$cell2>" . filter($cell2, $row2, 17, 1, 'string') . "</$cell2>\n";
                        } else if ($x == 30) {
                            $re .= "\t\t\t<$cell2>" . filter($cell2, $row2, false, false, 'integer') . "</$cell2>\n";
                        } else if ($x == 31) {
                            $re .= "\t\t\t<$cell2>" . filter($cell2, $row2, 2, 1, 'string') . "</$cell2>\n";
                        } else if ($x == 32) {
                            $re .= "\t\t\t<$cell2>" . filter($cell2, $row2, 3, 1, 'string') . "</$cell2>\n";
                        } else if ($x == 33) {
                            $row3 = isset($sheet['cells'][$k][$x - 1]) ? $sheet['cells'][$k][$x - 1] : '';
                            if ($row3 != 'MAB') {
                                if ($row2 != "") {
                                    $re .= "\t\t\t<$cell2>" . filter($cell2, $row2, 17, 0, 'string') . "</$cell2>\n";
                                }
                            }
                        } else {
                            //DG_status
                            $value = filter($cell2, $row2, 35, 0, 'string');
                            $value = $value==0?'':$value;
                            $re .= "\t\t\t<$cell2>" .$value. "</$cell2>\n";
                        }


                        $x++;
                    }
                    $re .= "\t\t</$cell3>\n";

                    $cell2 = isset($sheet['cells'][1][35]) ? $sheet['cells'][1][35] : '';
                    $row2 = isset($sheet['cells'][$k][35]) ? $sheet['cells'][$k][35] : '';
                    $re .= "\t\t<$cell2>" . filter($cell2, $row2, 1, false, 'string') . "</$cell2>\n";

                    $cell3 = isset($sheet['cells'][1][36]) ? $sheet['cells'][1][36] : '';
                    $re .= "\t\t<$cell3>\n";
                    $x = 37;
                    while ($x <= 38) {
                        $cell2 = isset($sheet['cells'][1][$x]) ? $sheet['cells'][1][$x] : '';
                        $row2 = isset($sheet['cells'][$k][$x]) ? $sheet['cells'][$k][$x] : '';
                        if ($x == 37) {
                            $re .= "\t\t\t\t<$cell2>" . filter($cell2, $row2, 5, 1, 'string') . "</$cell2>\n";
                        } else {
                            $re .= "\t\t\t\t<$cell2>" . filter($cell2, $row2, 5, 1, 'string') . "</$cell2>\n";
                        }
                        $x++;
                    }
                    $re .= "\t\t</$cell3>\n";


                    $cell4 = isset($sheet['cells'][1][39]) ? $sheet['cells'][1][39] : '';
                    $re .= "\t\t<$cell4>\n";

                    $cell3 = isset($sheet['cells'][1][40]) ? $sheet['cells'][1][40] : '';
                    $re .= "\t\t\t<$cell3>\n";
                    $x = 41;
                    while ($x <= 43) {
                        $cell2 = isset($sheet['cells'][1][$x]) ? $sheet['cells'][1][$x] : '';
                        $row2 = isset($sheet['cells'][$k][$x]) ? $sheet['cells'][$k][$x] : '';
                        if ($x == 41) {
                            $re .= "\t\t\t\t<$cell2>" . filter($cell2, $row2, 17, 1, 'string') . "</$cell2>\n";
                        } else if ($x == 42) {
                            $re .= "\t\t\t\t<$cell2>" . filter($cell2, $row2, 35, 1, 'string',true) . "</$cell2>\n";
                        } else {
                            $re .= "\t\t\t\t<$cell2>" . filter($cell2, $row2, 175, 1, 'string',true) . "</$cell2>\n";
                        }
                        $x++;
                    }
                    $re .= "\t\t\t</$cell3>\n";

                    $cell3 = isset($sheet['cells'][1][44]) ? $sheet['cells'][1][44] : '';
                    $re .= "\t\t\t<$cell3>\n";
                    $x = 45;
                    while ($x <= 46) {
                        $cell2 = isset($sheet['cells'][1][$x]) ? $sheet['cells'][1][$x] : '';
                        $row2 = isset($sheet['cells'][$k][$x]) ? $sheet['cells'][$k][$x] : '';
                        if ($x == 45) {
                            $re .= "\t\t\t\t".tag($cell2,$row2)."\n";
                        } else {
                            $re .= "\t\t\t\t<$cell2>" . filter($cell2, $row2, 35, 0, 'string') . "</$cell2>\n";
                        }
                        $x++;
                    }
                    $re .= "\t\t\t</$cell3>\n";

                    $cell3 = isset($sheet['cells'][1][47]) ? $sheet['cells'][1][47] : '';
                    $re .= "\t\t\t<$cell3>\n";
                    $x = 48;
                    while ($x <= 49) {
                        $cell2 = isset($sheet['cells'][1][$x]) ? $sheet['cells'][1][$x] : '';
                        $row2 = isset($sheet['cells'][$k][$x]) ? $sheet['cells'][$k][$x] : '';
                        if ($x == 48) {
                            $re .= "\t\t\t\t<$cell2>" . filter($cell2, $row2, 35, 1, 'string',true) . "</$cell2>\n";
                        } else {
                            $re .= "\t\t\t\t<$cell2>" . filter($cell2, $row2, 175, 1, 'string',true) . "</$cell2>\n";
                        }
                        $x++;
                    }
                    $re .= "\t\t\t</$cell3>\n";

                    $cell3 = isset($sheet['cells'][1][50]) ? $sheet['cells'][1][50] : '';
                    $re .= "\t\t\t<$cell3>\n";
                    $x = 51;
                    while ($x <= 53) {
                        $cell2 = isset($sheet['cells'][1][$x]) ? $sheet['cells'][1][$x] : '';
                        $row2 = isset($sheet['cells'][$k][$x]) ? $sheet['cells'][$k][$x] : '';
                        if ($x == 51) {
                            $re .= "\t\t\t\t".tag($cell2,$row2)."\n";
                        } else if ($x == 52) {
                            $re .= "\t\t\t\t<$cell2>" . filter($cell2, $row2, 35, 1, 'string',true) . "</$cell2>\n";
                        } else {
                            $re .= "\t\t\t\t<$cell2>" . filter($cell2, $row2, 175, 1, 'string',true) . "</$cell2>\n";
                        }
                        $x++;
                    }
                    $re .= "\t\t\t</$cell3>\n";

                    $cell3 = isset($sheet['cells'][1][54]) ? $sheet['cells'][1][54] : '';
                    $re .= "\t\t\t<$cell3>\n";
                    $x = 55;
                    while ($x <= 57) {
                        $cell2 = isset($sheet['cells'][1][$x]) ? $sheet['cells'][1][$x] : '';
                        $row2 = isset($sheet['cells'][$k][$x]) ? $sheet['cells'][$k][$x] : '';
                        if ($x == 55) {
                                $re .= "\t\t\t\t".tag($cell2,$row2)."\n";
                        } else if ($x == 56) {
                            $re .= "\t\t\t\t<$cell2>" . filter($cell2, $row2, 35, 1, 'string',true) . "</$cell2>\n";
                        } else {
                            $re .= "\t\t\t\t<$cell2>" . filter($cell2, $row2, 175, 1, 'string',true) . "</$cell2>\n";
                        }
                        $x++;
                    }
                    $re .= "\t\t\t</$cell3>\n";
                    $re .= "\t\t</$cell4>\n";

                    $cell3 = isset($sheet['cells'][1][58]) ? $sheet['cells'][1][58] : '';
                    $re .= "\t\t<$cell3>\n";
                    $x = 59;
                    while ($x <= 66) {
                        $cell2 = isset($sheet['cells'][1][$x]) ? $sheet['cells'][1][$x] : '';
                        $row2 = isset($sheet['cells'][$k][$x]) ? $sheet['cells'][$k][$x] : '';
                        if ($x == 59) {
                            $re .= "\t\t\t<$cell2>" . filter($cell2, $row2, false, false, 'double') . "</$cell2>\n";
                        } else if ($x == 60) {
                            $re .= "\t\t\t<$cell2>" . filter($cell2, $row2, 17, 1, 'string') . "</$cell2>\n";
                        } else if ($x == 61) {
                            $re .= "\t\t\t<$cell2>" . filter($cell2, $row2, false, false, 'double') . "</$cell2>\n";
                        } else if ($x == 62) {
                            $re .= "\t\t\t<$cell2>" . filter($cell2, $row2, 512, 0, 'string') . "</$cell2>\n";
                        } else if ($x == 63) {
                            //Goods_description
                            $re .= "\t\t\t<$cell2>" . filter($cell2, $row2, 512, 1, 'string',true) . "</$cell2>\n";
                        } else if ($x == 64) {
                            $re .= "\t\t\t<$cell2>" . filter($cell2, $row2, false, false, 'double') . "</$cell2>\n";
                        } else if ($x == 65) {
                            $re .= "\t\t\t<$cell2>" . filter($cell2, $row2, false, false, 'integer') . "</$cell2>\n";
                        } else {
                            //Remarks
                            $value = filter($cell2, $row2, 70, 0, 'string');
                            $value = $value==0?'':$value;
                            $re .= "\t\t\t<$cell2>" .$value. "</$cell2>\n";
                        }
                        $x++;
                    }
                    $re .= "\t\t</$cell3>\n";

                    $cell4 = isset($sheet['cells'][1][67]) ? $sheet['cells'][1][67] : '';
                    $re .= "\t\t<$cell4>\n";

                    $cell3 = isset($sheet['cells'][1][68]) ? $sheet['cells'][1][68] : '';
                    $re .= "\t\t\t<$cell3>\n";
                    $x = 69;
                    while ($x <= 70) {
                        $cell2 = isset($sheet['cells'][1][$x]) ? $sheet['cells'][1][$x] : '';
                        $row2 = isset($sheet['cells'][$k][$x]) ? $sheet['cells'][$k][$x] : '';
                        if ($x == 69) {
                            $re .= "\t\t\t\t<$cell2>" . filter($cell2, $row2, false, false, 'double') . "</$cell2>\n";
                        } else {
                            $re .= "\t\t\t\t<$cell2>" . filter($cell2, $row2, 3, 0, 'string') . "</$cell2>\n";
                        }
                        $x++;
                    }
                    $re .= "\t\t\t</$cell3>\n";

                    $cell3 = isset($sheet['cells'][1][71]) ? $sheet['cells'][1][71] : '';
                    $re .= "\t\t\t<$cell3>\n";
                    $x = 72;
                    while ($x <= 73) {
                        $cell2 = isset($sheet['cells'][1][$x]) ? $sheet['cells'][1][$x] : '';
                        $row2 = isset($sheet['cells'][$k][$x]) ? $sheet['cells'][$k][$x] : '';
                        if ($x == 72) {
                            //Customs_value
                            $re .= "\t\t\t\t<$cell2>" . filter($cell2, $row2, false, false, 'double') . "</$cell2>\n";
                        } else {
                            $re .= "\t\t\t\t<$cell2>" . filter($cell2, $row2, 3, 0, 'string') . "</$cell2>\n";
                        }
                        $x++;
                    }
                    $re .= "\t\t\t</$cell3>\n";


                    $re .= "\t\t</$cell4>\n";

                    $re .= "\t</$cell>\n";
                    $k++;
                }
            }

            $y++;
        }
        $re .= $re2;
        $codeCell = isset($sheet['cells'][1][16]) ? $sheet['cells'][1][16] : '';
        $code = isset($sheet['cells'][2][15]) ? $sheet['cells'][2][15] : '';
        $code = filter($codeCell, $code, 17, 1, 'string');

        if (isset($_SESSION["count"])) {
            for ($i = 1; $i <= $_SESSION["count"]; $i++) {
                echo $i . ". " . $_SESSION["alert" . $i] . "<br>";
                unset($_SESSION["alert" . $i]);
            }
            unset($_SESSION['count']);
            die();
        }

        //echo htmlspecialchars($re);
        //echo die();


        $file_name2 = "MAN" . $code . '_' . date("dmyhms");
        $file_name2 = $file_name2 . "." . 'xml';
        $_SESSION["name"] = $file_name2;
        $myfile = fopen("xml/$file_name2", "w") or die("Unable to open file!");
        fwrite($myfile, $re);
        fclose($myfile);
        return true;
    }

    $nr_sheets = count($excel->sheets);
    $excel_data = '';

    for ($i = 0; $i < $nr_sheets; $i++) {
        $excel_data .= '<h4>Sheet ' . ($i + 1) . ' (<em>' . $excel->boundsheets[$i]['name'] . '</em>)</h4>' . sheetData($excel->sheets[$i]) . '<br/>';
    }
    return true;
}

function convert2($target_fileName)
{
    include 'excel_reader.php';
    $excel = new PhpExcelReader;
    $excel->read($target_fileName);


    function sheetData($sheet)
    {
        $row2 = isset($sheet['cells'][2][21]) ? $sheet['cells'][2][21] : '';
        if ($row2 != $_SESSION['carrier_code']){
            $_SESSION['message'] = 'Carrier code not match.';
            header('location: converter/deg.php');
            die();
        }

        //echo "<pre>";
        //print_r($sheet);
        //die();
        $re = '<?xml version="1.0" encoding="UTF-8" standalone="yes" ?>' . "\n";
        $re2 = '';
        $x = 2;

        $y = 1;
        while ($y <= $sheet['numCols']) {
            $cell = isset($sheet['cells'][1][$y]) ? $sheet['cells'][1][$y] : '';
            if ($y == 1) {
                $re .= "<$cell>\n";
                $re2 .= "</$cell>\n";
            } else if ($y == 2) {
                $re .= "\t<$cell>\n";
                $x = 3;
                while ($x <= 6) {
                    $cell2 = isset($sheet['cells'][1][$x]) ? $sheet['cells'][1][$x] : '';
                    $row2 = isset($sheet['cells'][2][$x]) ? $sheet['cells'][2][$x] : '';
                    if ($x == 3) {
                        $re .= "\t\t<$cell2>" . filter($cell2, $row2, 5, 1, 'string') . "</$cell2>\n";
                    } else if ($x == 4) {
                        $re .= "\t\t<$cell2>" . filter($cell2, $row2, 17, 1, 'string') . "</$cell2>\n";
                    } else if ($x == 5) {
                        $re .= "\t\t<$cell2>" . filter($cell2, $row2, false, false, 'date') . "</$cell2>\n";
                    } else {
                        $re .= "\t\t<$cell2>$row2</$cell2>\n";
                    }

                    $x++;
                }
                $re .= "\t</$cell>\n";
            } else if ($y == 7) {
                $k = 2;
                while ($k <= $sheet['numRows']) {
                    $re .= "\t<$cell>\n";

                    $cell3 = isset($sheet['cells'][1][8]) ? $sheet['cells'][1][8] : '';
                    $re .= "\t\t<$cell3>\n";
                    $x = 9;
                    while ($x <= 14) {
                        $cell2 = isset($sheet['cells'][1][$x]) ? $sheet['cells'][1][$x] : '';
                        $row2 = isset($sheet['cells'][$k][$x]) ? $sheet['cells'][$k][$x] : '';
                        if ($x == 9) {
                            $re .= "\t\t\t<$cell2>" . filter($cell2, $row2, 17, 1, 'string') . "</$cell2>\n";
                        } else if ($x == 10) {
                            $re .= "\t\t\t<$cell2>" . filter($cell2, $row2, false, false, 'integer') . "</$cell2>\n";
                        } else if ($x == 11) {
                            $re .= "\t\t\t<$cell2>" . filter($cell2, $row2, 2, 1, 'string') . "</$cell2>\n";
                        } else if ($x == 12) {
                            $re .= "\t\t\t<$cell2>" . filter($cell2, $row2, 3, 1, 'string') . "</$cell2>\n";
                        } else if ($x == 13) {
                            $row3 = isset($sheet['cells'][$k][$x - 1]) ? $sheet['cells'][$k][$x - 1] : '';
                            if ($row3 != 'MAB') {
                                if ($row2 != "") {
                                    $re .= "\t\t\t<$cell2>" . filter($cell2, $row2, 17, 0, 'string') . "</$cell2>\n";
                                }
                            }
                        } else {
                            //DG_status
                            $value = filter($cell2, $row2, 35, 0, 'string');
                            $value=$value==0?'':$value;
                            $re .= "\t\t\t<$cell2>" . $value . "</$cell2>\n";
                        }
                        $x++;
                    }
                    $re .= "\t\t</$cell3>\n";

                    $cell2 = isset($sheet['cells'][1][15]) ? $sheet['cells'][1][15] : '';
                    $row2 = isset($sheet['cells'][$k][15]) ? $sheet['cells'][$k][15] : '';
                    $re .= "\t\t<$cell2>" . filter($cell2, $row2, 1, 0, 'string') . "</$cell2>\n";

                    $cell3 = isset($sheet['cells'][1][16]) ? $sheet['cells'][1][16] : '';
                    $re .= "\t\t<$cell3>\n";
                    $x = 17;
                    while ($x <= 18) {
                        $cell2 = isset($sheet['cells'][1][$x]) ? $sheet['cells'][1][$x] : '';
                        $row2 = isset($sheet['cells'][$k][$x]) ? $sheet['cells'][$k][$x] : '';
                        if ($x == 17) {
                            $re .= "\t\t\t<$cell2>" . filter($cell2, $row2, 5, 1, 'string') . "</$cell2>\n";
                        } else {
                            $re .= "\t\t\t<$cell2>" . filter($cell2, $row2, 5, 1, 'string') . "</$cell2>\n";
                        }
                        $x++;
                    }
                    $re .= "\t\t</$cell3>\n";


                    $cell4 = isset($sheet['cells'][1][19]) ? $sheet['cells'][1][19] : '';
                    $re .= "\t\t<$cell4>\n";

                    $cell3 = isset($sheet['cells'][1][20]) ? $sheet['cells'][1][20] : '';
                    $re .= "\t\t\t<$cell3>\n";
                    $x = 21;
                    while ($x <= 23) {
                        $cell2 = isset($sheet['cells'][1][$x]) ? $sheet['cells'][1][$x] : '';
                        $row2 = isset($sheet['cells'][$k][$x]) ? $sheet['cells'][$k][$x] : '';
                        if ($x == 21) {
                            $re .= "\t\t\t\t<$cell2>" . filter($cell2, $row2, 17, 1, 'string') . "</$cell2>\n";
                        } else if ($x == 22) {
                            $re .= "\t\t\t\t<$cell2>" . filter($cell2, $row2, 35, 1, 'string',true) . "</$cell2>\n";
                        } else {
                            $re .= "\t\t\t\t<$cell2>" . filter($cell2, $row2, 175, 1, 'string',true) . "</$cell2>\n";
                        }
                        $x++;
                    }
                    $re .= "\t\t\t</$cell3>\n";

                    $cell3 = isset($sheet['cells'][1][24]) ? $sheet['cells'][1][24] : '';
                    $re .= "\t\t\t<$cell3>\n";
                    $x = 25;
                    while ($x <= 26) {
                        $cell2 = isset($sheet['cells'][1][$x]) ? $sheet['cells'][1][$x] : '';
                        $row2 = isset($sheet['cells'][$k][$x]) ? $sheet['cells'][$k][$x] : '';
                        if ($x == 25) {
                            $re .= "\t\t\t\t".tag($cell2,$row2)."\n";
                        } else {
                            //Shipping_Agent_name
                            $value =filter($cell2, $row2, 35, 0, 'string',true);
                            $value=$value==0?'':$value;
                            $re .= "\t\t\t\t<$cell2>" . $value . "</$cell2>\n";
                        }
                        $x++;
                    }
                    $re .= "\t\t\t</$cell3>\n";

                    $cell3 = isset($sheet['cells'][1][27]) ? $sheet['cells'][1][27] : '';
                    $re .= "\t\t\t<$cell3>\n";
                    $x = 28;
                    while ($x <= 29) {
                        $cell2 = isset($sheet['cells'][1][$x]) ? $sheet['cells'][1][$x] : '';
                        $row2 = isset($sheet['cells'][$k][$x]) ? $sheet['cells'][$k][$x] : '';
                        if ($x == 28) {
                            $re .= "\t\t\t\t<$cell2>" . filter($cell2, $row2, 35, 1, 'string',true) . "</$cell2>\n";
                        } else {
                            $re .= "\t\t\t\t<$cell2>" . filter($cell2, $row2, 175, 1, 'string',true) . "</$cell2>\n";
                        }
                        $x++;
                    }
                    $re .= "\t\t\t</$cell3>\n";

                    $cell3 = isset($sheet['cells'][1][30]) ? $sheet['cells'][1][30] : '';
                    $re .= "\t\t\t<$cell3>\n";
                    $x = 31;
                    while ($x <= 33) {
                        $cell2 = isset($sheet['cells'][1][$x]) ? $sheet['cells'][1][$x] : '';
                        $row2 = isset($sheet['cells'][$k][$x]) ? $sheet['cells'][$k][$x] : '';
                        if ($x == 31) {
                            $re .= "\t\t\t\t".tag($cell2,$row2)."\n";
                        } else if ($x == 32) {
                            $re .= "\t\t\t\t<$cell2>" . filter($cell2, $row2, 35, 1, 'string',true) . "</$cell2>\n";
                        } else {
                            $re .= "\t\t\t\t<$cell2>" . filter($cell2, $row2, 175, 1, 'string',true) . "</$cell2>\n";
                        }
                        $x++;
                    }
                    $re .= "\t\t\t</$cell3>\n";

                    $cell3 = isset($sheet['cells'][1][34]) ? $sheet['cells'][1][34] : '';
                    $re .= "\t\t\t<$cell3>\n";
                    $x = 35;
                    while ($x <= 37) {
                        $cell2 = isset($sheet['cells'][1][$x]) ? $sheet['cells'][1][$x] : '';
                        $row2 = isset($sheet['cells'][$k][$x]) ? $sheet['cells'][$k][$x] : '';
                        if ($x == 35) {
                            $re .= "\t\t\t\t".tag($cell2,$row2)."\n";
                        } else if ($x == 36) {
                            $re .= "\t\t\t\t<$cell2>" . filter($cell2, $row2, 35, 1, 'string',true) . "</$cell2>\n";
                        } else {
                            $re .= "\t\t\t\t<$cell2>" . filter($cell2, $row2, 175, 1, 'string',true) . "</$cell2>\n";
                        }
                        $x++;
                    }
                    $re .= "\t\t\t</$cell3>\n";
                    $re .= "\t\t</$cell4>\n";

                    $cell3 = isset($sheet['cells'][1][38]) ? $sheet['cells'][1][38] : '';
                    $re .= "\t\t<$cell3>\n";
                    $x = 39;
                    while ($x <= 46) {
                        $cell2 = isset($sheet['cells'][1][$x]) ? $sheet['cells'][1][$x] : '';
                        $row2 = isset($sheet['cells'][$k][$x]) ? $sheet['cells'][$k][$x] : '';
                        if ($x == 39) {
                            $re .= "\t\t\t<$cell2>" . filter($cell2, $row2, false, false, 'double') . "</$cell2>\n";
                        } else if ($x == 40) {
                            $re .= "\t\t\t<$cell2>" . filter($cell2, $row2, 17, 1, 'string') . "</$cell2>\n";
                        } else if ($x == 41) {
                            $re .= "\t\t\t<$cell2>" . filter($cell2, $row2, false, false, 'double') . "</$cell2>\n";
                        } else if ($x == 42) {
                            $re .= "\t\t\t<$cell2>" . filter($cell2, $row2, 512, 0, 'string') . "</$cell2>\n";
                        } else if ($x == 43) {
                            //Goods_description
                            $re .= "\t\t\t<$cell2>" . filter($cell2, $row2, 512, 1, 'string',true) . "</$cell2>\n";
                        } else if ($x == 44) {
                            $re .= "\t\t\t<$cell2>" . filter($cell2, $row2, false, false, 'double') . "</$cell2>\n";
                        } else if ($x == 45) {
                            $re .= "\t\t\t<$cell2>" . filter($cell2, $row2, false, false, 'integer') . "</$cell2>\n";
                        } else {
                            //Remarks
                            $value = filter($cell2, $row2, 70, 0, 'string');
                            $value=$value==0?'':$value;
                            $re .= "\t\t\t<$cell2>" . $value . "</$cell2>\n";
                        }
                        $x++;
                    }
                    $re .= "\t\t</$cell3>\n";

                    $cell4 = isset($sheet['cells'][1][47]) ? $sheet['cells'][1][47] : '';
                    $re .= "\t\t<$cell4>\n";

                    $cell3 = isset($sheet['cells'][1][48]) ? $sheet['cells'][1][48] : '';
                    $re .= "\t\t\t<$cell3>\n";
                    $x = 49;
                    while ($x <= 50) {
                        $cell2 = isset($sheet['cells'][1][$x]) ? $sheet['cells'][1][$x] : '';
                        $row2 = isset($sheet['cells'][$k][$x]) ? $sheet['cells'][$k][$x] : '';
                        $row2 = preg_replace('/&(?!#?[a-z0-9]+;)/', '&amp;', $row2);
                        if ($x == 49) {
                            $re .= "\t\t\t\t<$cell2>" . filter($cell2, $row2, false, false, 'double') . "</$cell2>\n";
                        } else {
                            $re .= "\t\t\t\t<$cell2>" . filter($cell2, $row2, 3, 0, 'string') . "</$cell2>\n";
                        }
                        $x++;
                    }
                    $re .= "\t\t\t</$cell3>\n";

                    $cell3 = isset($sheet['cells'][1][51]) ? $sheet['cells'][1][51] : '';
                    $re .= "\t\t\t<$cell3>\n";
                    $x = 52;
                    while ($x <= 53) {
                        $cell2 = isset($sheet['cells'][1][$x]) ? $sheet['cells'][1][$x] : '';
                        $row2 = isset($sheet['cells'][$k][$x]) ? $sheet['cells'][$k][$x] : '';
                        $row2 = preg_replace('/&(?!#?[a-z0-9]+;)/', '&amp;', $row2);
                        if ($x == 52) {
                            $re .= "\t\t\t\t<$cell2>" . filter($cell2, $row2, false, false, 'double') . "</$cell2>\n";
                        } else {
                            $re .= "\t\t\t\t<$cell2>" . filter($cell2, $row2, 3, 0, 'string') . "</$cell2>\n";
                        }
                        $x++;
                    }
                    $re .= "\t\t\t</$cell3>\n";

                    $re .= "\t\t</$cell4>\n";

                    $re .= "\t</$cell>\n";
                    $k++;
                }
            }

            $y++;
        }
        $re .= $re2;

        $codeCell = isset($sheet['cells'][1][21]) ? $sheet['cells'][1][21] : '';
        $code = isset($sheet['cells'][2][21]) ? $sheet['cells'][2][21] : '';
        $code = filter($codeCell, $code, 17, 1, 'string');

        if (isset($_SESSION["count"])) {
            for ($i = 1; $i <= $_SESSION["count"]; $i++) {
                echo $i . ". " . $_SESSION["alert" . $i] . "<br>";
                unset($_SESSION["alert" . $i]);
            }
            unset($_SESSION["count"]);
            die();
        }

        //echo htmlspecialchars($re);
        //echo die();

        $file_name2 = "DEG" . $code . '_' . date("dmyhms");
        $file_name2 = $file_name2 . "." . 'xml';
        $_SESSION["name"] = $file_name2;
        $myfile = fopen("xml/$file_name2", "w") or die("Unable to open file!");
        fwrite($myfile, $re);
        fclose($myfile);
        return true;
    }

    $nr_sheets = count($excel->sheets);
    $excel_data = '';

    for ($i = 0; $i < $nr_sheets; $i++) {
        $excel_data .= '<h4>Sheet ' . ($i + 1) . ' (<em>' . $excel->boundsheets[$i]['name'] . '</em>)</h4>' . sheetData($excel->sheets[$i]) . '<br/>';
    }
    return true;
}

function delete($xlsx)
{
    if (!unlink($xlsx)) {
        echo("Error deleting $xlsx");
    }
}

function filter($cell, $row, $max = false, $min = false, $type = false, $special_charecter = false)
{

    if (isset($_SESSION["count"])) {
        $count = $_SESSION["count"];
    } else {
        $count = 0;
    }
    if ($max && strlen($row) > $max) {
        $row = substr($row, 0, $max);
    }
    if (($min || $min == 0) && strlen($row) <= $min) {
        if ($min == 1 && strlen($row) != 1) {
            $count++;
            $_SESSION["count"] = $count;
            $_SESSION["alert" . $count] = $cell . ': Is empty value';
        } else if ($min == 0 && strlen($row) == 0) {
            $row = 0;
        }
    }
    if ($type) {
        if ($type == 'integer') {
            if (gettype($row) != 'integer') {
                $row = (int)$row;
            }
        } else if ($type == 'double') {
            if (gettype($row) != 'double') {
                $row = number_format(floatval($row), 2, '.', '');;
            }
        } else if ($type == 'string') {
            if (gettype($row) != 'string') {
                $row = strval($row);
            }
        } else if ($type == 'date') {
            if (!validateDate($row)) {
                $count++;
                $_SESSION["count"] = $count;
                $_SESSION["alert" . $count] = $cell . ': Is not a validate date.Format:Y-m-d';
            }
        } else if ($type == 'time') {
            if (!validateTime($row)) {
                $count++;
                $_SESSION["count"] = $count;
                $_SESSION["alert" . $count] = $cell . ': Is not a validate time.Format:H:i:s';
            }
        }
    }
    if ($special_charecter){
        $row = RemoveSpecialChar($row);
//        $row = str_replace  ("<", "&lt;", $row);
//        $row = str_replace  (">", "&gt;", $row);
//        $row = str_replace  ('"', "&quot;", $row);
//        $row = str_replace  ("''", "&apos;", $row);
    }
    $row = trim($row, " \t\n\r");
    $row = preg_replace('/&(?!#?[a-z0-9]+;)/', '&amp;', $row);

    return $row;
}

function validateDate($date, $format = 'Y-m-d')
{
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
}

function validateTime($date, $format = 'H:i:s')
{
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
}

function RemoveSpecialChar($value){
    return str_replace( array(',' ,'&','!','%','*','?','¤','(',')','{','}','[',']','<','>','"','\'','\\' ), '', $value);
}

function tag($cell, $row)
{
    if ($row == '' || $row == 0) {
        return "<$cell/>";
    } else {
        return "<$cell>".filter($cell, $row, 17, 0, 'string')."</$cell>";
    }
}
