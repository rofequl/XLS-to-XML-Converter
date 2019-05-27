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
        Redirect('http://nayem.bd/new/login.php', false);
    }
} else {
        Redirect('http://nayem.bd/new/login.php', false);
}

function Redirect($url, $permanent = false)
{
    header('Location: ' . $url, true, $permanent ? 301 : 302);
    exit();
}

