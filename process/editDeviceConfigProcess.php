<?php

require("../requires/connect.php");

// ERROR CHECKING

if(isset($_POST["username"]) && $_POST["username"] != "" && isset($_POST["password"]) && $_POST["password"] != "" && isset($_POST["hostname"]) && $_POST["hostname"] != "" && isset($_POST["domain_name"]) && $_POST["domain_name"] != "" && isset($_POST["id"]) && $_POST["id"] != ""){
    $username = $_POST["username"];
    $password = $_POST["password"];
    $hostname = $_POST["hostname"];
    $domain_name = $_POST["domain_name"];
    $id = $_POST["id"];
    if(isset($_POST["use_global_conf"])){
        $use_global_conf = 1;
    }else{
        $use_global_conf = 0;
    }
}else{
    header("Location: ../devices.php");
    exit;
}

// Update the database
$query = "UPDATE devices SET username='$username', `password`='$password', use_global_conf='$use_global_conf', hostname='$hostname', domain_name='$domain_name' WHERE id='$id'";
$query = mysqli_query($connect,$query);

// Return to devices
header("Location: ../devices.php");

?>