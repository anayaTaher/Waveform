<?php
session_start();

if(isset($_COOKIE['rememberMe'])){
    setcookie("rememberMe", "", time() - 3600);
}
session_destroy();
header("location:index.php");