<?php
session_start();

$conn = mysqli_connect(hostname: "Localhost", username: "root", password: "root", database: "rizline");

// if($conn){
//     echo "connected";
// }

function isLoggedIn()
{
    return isset($_SESSION['user_id']) && $_SESSION['user_id'] > 0;
}

function isUser()
{
    return $_SESSION['user_type'] == 'user';
}

function isAdmin()
{
    return $_SESSION['user_type'] == 'admin';
}

?>