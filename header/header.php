<?php
$pagename = basename($_SERVER['PHP_SELF']);
$url = $_SERVER['PHP_SELF'];
$path_count = count(explode('/', (parse_url($url, PHP_URL_PATH)))) - 2;
if ($path_count == 1){$path = '';}
elseif ($path_count == 2){$path = '../';}
else{$path='../../';}
?>
<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    <meta name="robots" content="index, follow"/>
    <meta name="keywords"
          content="B.M. SYEDUR RAHMAN,Coverter excel to xml converter, IGM Submission, Manifest Guide, Bangladesh Customs,Airlines , Courier Guide for IGM &amp; Cargo IGM."/>
    <meta name="description" content="B.M. SYEDUR RAHMAN, NATIONAL BOARD OF REVENUE, NBR, BANGLADESH DHAKA."/>


    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
          integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css" rel="stylesheet"/>
    <title>Converter : EXCEL (XLS) TO XML </title>
</head>
<body>

<style>
    ul.navbar-nav li.dropdown:hover > div.dropdown-menu {
        display: block;
    }
</style>
<nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #1e90ff!important;">
    <a class="navbar-brand" href="#">XLS to XML</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item <?php if ($pagename == 'index.php') {echo 'active';} ?>">
                <a class="nav-link" href="<?= $path ?>index.php">Home <span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item <?php if ($pagename == 'man.php') {echo 'active';} ?>">
                <a class="nav-link" href="<?= $path ?>converter/man.php">MAN</a>
            </li>
            <li class="nav-item <?php if ($pagename == 'deg.php') {echo 'active';} ?>">
                <a class="nav-link" href="<?= $path ?>converter/deg.php">DEG</a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true"
                   aria-expanded="false">Guide for XML</a>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="<?= $path ?>guide/page1.php">Guide for MAN</a>
                    <a class="dropdown-item" href="<?= $path ?>guide/page2.php">Guide for DEG</a>
                </div>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true"
                   aria-expanded="false">Profile</a>
                <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item" href="<?= $path ?>profile/setting.php">Setting</a>
                    <a class="dropdown-item" href="<?= $path ?>index.php?action=logout">Logout</a>
                </div>
            </li>
        </ul>
    </div>
</nav>