<?php
if(isset($_GET['id'])){
     $file = 'xml/'.$_GET['id'];

     if (file_exists($file)){
         header('Content-Description: File Transfer');
         header('Content-Type: application/octet-stream');
         header('Content-Disposition: attachment; filename="'.basename($file).'"');
         header('Expires: 0');
         header('Cache-Control: must-revalidate');
         header('Pragma: public');
         header('Content-Length: ' . filesize($file));
         readfile($file);
         exit;
     }else{
         echo "Not found";
     }
}else{
    echo "ok";
}
