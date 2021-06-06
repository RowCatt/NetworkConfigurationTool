<?php

require("../requires/connect.php");

// ERROR CHECKING

if(isset($_POST["ip"]) && $_POST["ip"] != "" && isset($_POST["username"]) && $_POST["username"] != "" && isset($_POST["password"]) && $_POST["password"] != "" && isset($_POST["model"]) && $_POST["model"] != ""){
    $ip = $_POST["ip"];
    $username = $_POST["username"];
    $password = $_POST["password"];
    $model = $_POST["model"];
}else{
    header("Location: ../add_device.php?error=Fields left empty");
    exit;
}


?>