<?php
include '../login_check.php';
include ('../database.php');
if (isset($_POST['submit'])) {
    if (empty($_POST["oldpass"])) {
        $comment = "Please Enter Your old psaaword";
    } else if (empty($_POST["newpass"])) {
        $comment = "Please Enter Your new password";
    } else {
        $oldpass = test_input($_POST["oldpass"]);
        $newpass = test_input($_POST["newpass"]);
        $email = $_COOKIE['email'];
        $sql = "SELECT * FROM user WHERE email='$email' AND password='$oldpass'";
        $connect = mysqli_query($conn, $sql);
        $rowcount = mysqli_num_rows($connect);
        if ($rowcount > 0) {

            $sql = "UPDATE user SET password='$newpass' WHERE email='$email' AND password='$oldpass'";
            if (mysqli_query($conn, $sql)) {
                setcookie('password', $newpass,time() + (86400 * 30), "/");
                $comment = "Password change successfully";
            } else {
                $comment = "Error updating record: " . mysqli_error($conn);
            }
        } else {
            $comment = "Failed! Invalid Password";
        }
    }
}

function test_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}


include '../header/header.php';
?>

<div class="container mt-5">

    <div class="row justify-content-md-center">
        <div class="col-md-10">

            <div class="row">
                <div class="col-4">
                    <div class="list-group" id="list-tab" role="tablist">
                        <a class="list-group-item list-group-item-action active" id="list-home-list" data-toggle="list"
                           href="#list-home" role="tab" aria-controls="home">Change Password</a>
                    </div>
                </div>
                <div class="col-8">
                    <div class="tab-content" id="nav-tabContent">
                        <div class="tab-pane fade show active" id="list-home" role="tabpanel"
                             aria-labelledby="list-home-list">
                            <div class="card card-body">
                                <form method="post" action="">
                                    <?php if (isset($comment)) { ?>
                                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                            <?php echo $comment; ?>
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                    <?php } ?>
                                    <div class="form-group row">
                                        <label for="staticEmail" class="col-sm-3 col-form-label">Old Password</label>
                                        <div class="col-sm-9">
                                            <input type="password" class="form-control" id="staticEmail" name="oldpass"
                                                   placeholder="Old Password">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="inputPassword" class="col-sm-3 col-form-label">New Password</label>
                                        <div class="col-sm-9">
                                            <input type="password" class="form-control" id="inputPassword" name="newpass"
                                                   placeholder="New Password">
                                        </div>
                                    </div>
                                    <div class="form-group w-100">
                                        <input type="submit" name="submit" class="btn btn-primary float-right" value="Submit">
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>


<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
</body>
</html>