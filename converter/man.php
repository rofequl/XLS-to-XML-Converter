<?php
include '../login_check.php';
session_start();
$id = $_COOKIE['id'];
?>

<?php include '../header/header.php'; ?>

<div class="container mt-5">

    <div class="row justify-content-md-center">
        <div class="col-md-7">
            <h2 class="text-center my-1">Excel to xml converter (MAN)</h2>
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
        <div class="col-md-8 bg">
            <div class="card">
                <div class="card-header">
                    <p class="text-center my-1 mb-3">Upload your Excel (.xls) File (MAN)</p>
                    <p class="text-center my-1 mb-3">Example of Excel Format file is <a href="../MAN.xls"
                                                                                        title="Example file">HERE</a>
                    Download and make your file like it .</p>
                </div>
                <div class="card-body">
                    <form class="form-inline" method="post" enctype="multipart/form-data" action="../upload.php">
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
                    <td><a href="../download.php?id=' . $result['file_name'] . '" class="btn btn-sm px-2 btn-primary">Convert</a>
                    <a href="../delete.php?id=' . $result['file_name'] . '&user=' . $result['id'] . '" class="btn btn-sm px-2 btn-danger">Delete</a>
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