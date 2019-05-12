<?php
include 'login_check.php';
session_start();
$id = $_COOKIE['id'];
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    unset($_SESSION['email']);
    unset($_SESSION['password']);
    setcookie('email', '', 0, "/");
    setcookie('password', '', 0, "/");
    header('location: login.php');
}
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
    <style>
        /*.bg {*/
        /*    background-image: url("img/biman.jpg");*/
        /*    background-position: center;*/
        /*    background-repeat: no-repeat;*/
        /*    background-size: cover;*/
        /*}*/
        /*.bg2 {*/
        /*    background-image: url("img/madhur.jpg");*/
        /*    background-position: center;*/
        /*    background-repeat: no-repeat;*/
        /*    background-size: cover;*/
        /*}*/
        body {
            background-image: url("img/1920x1080-white-solid-color-background.jpg");
            height: 100vh;
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
        }
    </style>

</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #1e90ff!important;">
    <a class="navbar-brand" href="#">XLS to XML</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item active">
                <a class="nav-link" href="index.php">Home <span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="index.php">Guide for XML <span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item float-right">
                <a class="nav-link" href="index.php?action=logout">Log out</a>
            </li>
        </ul>
    </div>
</nav>

<div class="container mt-5">

    <div class="row justify-content-md-center">
        <div class="col-md-7">
            <h2 class="text-center my-1">Excel to xml converter</h2>
            <p class="text-center my-1 mb-3">Only .xls file upload </p>
            <?php if (isset($_SESSION['message'])) { ?>
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <?php echo $_SESSION['message'];
                    unset($_SESSION['message']); ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php } ?>
        </div>
        <div class="col-md-6 bg">
            <div class="card">
                <div class="card-header">
                    <p class="text-center my-1 mb-3">Upload your Excel (.xls) File (MAN)</p>
                    <p class="text-center my-1 mb-3">Example of Excel Format file is <a href="MAN.xls"
                                                                                        title="Example file">HERE</a>
                    </p>
                    <p class="text-center my-1 mb-3"> Download and make your file like it .</p>
                </div>
                <div class="card-body">
                    <form class="form-inline" method="post" enctype="multipart/form-data" action="upload.php">
                        <div class="input-group mb-2 mx-2">
                            <input type="file" name="excel" class="">
                        </div>
                        <button type="submit" name="upload" class="btn btn-primary mb-2">Upload</button>
                    </form>
                </div>
            </div>
            <table class="table border mt-5">
                <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Base Name</th>
                    <th scope="col">Action</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $data = "select * from file where user_id='$id' and converter=0";
                $data_connect = mysqli_query($conn, $data);
                $listnum = 1;
                while ($result = mysqli_fetch_array($data_connect)) {
                    echo '<tr>
                    <th scope="row">' . $listnum . '</th>
                    <td>' . $result['base_name'] . '</td>
                    <td><a href="download.php?id=' . $result['file_name'] . '" class="btn btn-sm px-2 btn-primary">Convert</a>
                    <a href="delete.php?id=' . $result['file_name'] . '&user=' . $result['id'] . '" class="btn btn-sm px-2 btn-danger">Delete</a>
                    </td>
                </tr>';
                    $listnum++;
                }
                ?>
                </tbody>
            </table>
        </div>
        <div class="col-md-6">
            <div class="card bg2">
                <div class="card-header">
                    <p class="text-center my-1 mb-3">Upload your Excel File (DEG)</p>
                    <p class="text-center my-1 mb-3">Example of Excel Format file is <a href="DEG.xls"
                                                                                        title="Example file">HERE</a>
                    </p>
                    <p class="text-center my-1 mb-3"> Download and make your file like it .</p>
                </div>
                <div class="card-body">
                    <form class="form-inline" method="post" id="theForm" enctype="multipart/form-data" action="upload.php">
                        <div class="input-group mb-2 mx-2">
                            <input type="file" name="excel" class="">
                            <input type="hidden" name="uploadDEG" class="">
                        </div>
                        <input type="submit"  class="btn btn-primary mb-2 upload" value="Upload">
                    </form>
                </div>
            </div>
            <table class="table border mt-5">
                <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Base Name</th>
                    <th scope="col">Action</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $data = "select * from file where user_id='$id' and converter=1";
                $data_connect = mysqli_query($conn, $data);
                $listnum = 1;
                while ($result = mysqli_fetch_array($data_connect)) {
                    echo '<tr>
                    <th scope="row">' . $listnum . '</th>
                    <td>' . $result['base_name'] . '</td>
                    <td><a href="download.php?id=' . $result['file_name'] . '" class="btn btn-sm px-2 btn-primary">Convert</a>
                    <a href="delete.php?id=' . $result['file_name'] . '&user=' . $result['id'] . '" class="btn btn-sm px-2 btn-danger">Delete</a>
                    </td>
                </tr>';
                    $listnum++;
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
<script>

    $('.upload').on('click',function(e){
        e.preventDefault();
        var form = $(this).parents('form');
        swal({
            title: "Please check",
            text: "\"Total number of  packages and Total Gross_mass of DEG file  will be equal to that of  the Master_bol_reference which you used\" , If Ok please proceed , otherwise your xml will not be uploaded into Customs Asycuda System.",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, confirm it!",
            closeOnConfirm: false
        }, function(isConfirm){
            if (isConfirm) form.submit();
        });
    });

</script>
</body>
</html>