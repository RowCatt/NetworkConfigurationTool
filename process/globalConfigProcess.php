<?php

require("../requires/connect.php");

// ERROR CHECKING

if(isset($_POST["username"]) && $_POST["username"] != "" && isset($_POST["password"]) && $_POST["password"] != "" && isset($_POST["domain_name"]) && $_POST["domain_name"] != ""){
    $username = $_POST["username"];
    $password = $_POST["password"];
    $domain_name = $_POST["domain_name"];
}else{
    header("Location: ../global_config.php?error=Fields left empty.");
    exit;
}

if(!filter_var($domain_name, FILTER_VALIDATE_DOMAIN)){
    // Is not a domain, send back with error
    header("Location: ../global_config.php?error=Domain name not valid.");
    exit;
}

// Update the global config table
$query = "UPDATE `global_configuration` SET `username`='$username', `password`='$password', `domain_name`='$domain_name' WHERE id='1'";
$query = mysqli_query($connect,$query);

header("Location: ../global_config.php");

?>