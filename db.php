<?php
session_start();

$conn = mysqli_connect(hostname: "Localhost", username: "root", password: "root", database: "rizline");

// if($conn){
//     echo "connected";
// }

function isLoggedIn()
{
    return !empty($_SESSION['user_id']) && is_numeric($_SESSION['user_id']) && $_SESSION['user_id'] > 0;
}
function isUser()
{
    return $_SESSION['user_type'] == 'user' || $_SESSION['user_type'] == 'old_user';
}

function isAdmin()
{
    return $_SESSION['user_type'] == 'admin';
}

function isStaff()
{
    return $_SESSION['user_type'] == 'staff';
}

?>