<?php
include ('database.php');
if (isset($_COOKIE['email']) && isset($_COOKIE['password'])) {
    $email = $_COOKIE['email'];
    $password = $_COOKIE['password'];
    $sql = "SELECT * FROM user WHERE email='$email' and password='$password'";
    $query = mysqli_query($conn, $sql);
    $rowcount = mysqli_num_rows($query);
    if ($rowcount == 1) {

    } else {
        header("login.php");
        exit();
    }
} else {
    header("Location:login.php");
    exit();
}