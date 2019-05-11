<?php
session_start();
error_reporting(0);
error_reporting(E_ERROR | E_WARNING | E_PARSE);
error_reporting(E_ALL);
ini_set("error_reporting", E_ALL);
error_reporting(E_ALL & ~E_NOTICE);

include ('database.php');
if (isset($_GET['id'])) {
    $file = $_GET['id'];
    $id = $_GET['user'];
    $fileType = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    $fileName = basename($file, $fileType);
    $xlsx = "xlsx/" . $fileName . "xlsx";
    $xml = "xml/" . $file;



        $sql = "DELETE FROM file WHERE id=$id";
        if ($conn->query($sql) === TRUE) {
            if (unlink($xml)) {
                $_SESSION['message'] = 'File Delete Successfully';
                header("Location:index.php");
                exit();
            } else {
                $_SESSION['message'] = 'File not Delete, Something wrong';
                header("Location:index.php");
                exit();
            }
        } else {
            echo "Error deleting record: " . $conn->error;
        }
} else {
    echo "ok";
}
