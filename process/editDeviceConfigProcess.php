<?php

require("../requires/connect.php");

// ERROR CHECKING

if(isset($_POST["ip_address"]) && $_POST["ip_address"] != "" && isset($_POST["username"]) && $_POST["username"] != "" && isset($_POST["password"]) && $_POST["password"] != "" && isset($_POST["hostname"]) && $_POST["hostname"] != "" && isset($_POST["domain_name"]) && $_POST["domain_name"] != "" && isset($_POST["id"]) && $_POST["id"] != ""){
    $ip_address = $_POST["ip_address"];
    $username = $_POST["username"];
    $password = $_POST["password"];
    $hostname = $_POST["hostname"];
    $domain_name = $_POST["domain_name"];
    $id = $_POST["id"];
    if(isset($_POST["use_global_conf"])){
        $use_global_conf = $_POST["use_global_conf"];
        echo "using global conf: $use_global_conf";
    }else{
        $use_global_conf = "No";
        echo "Using global conf: $use_global_conf";
    }
}else{
    header("Location: ../devices.php");
    exit;
}



?>