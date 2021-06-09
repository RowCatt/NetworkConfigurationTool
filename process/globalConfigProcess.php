<?php

require("../requires/connect.php");

// ERROR CHECKING

if(isset($_POST["username"]) && $_POST["username"] != "" && isset($_POST["password"]) && $_POST["password"] != "" && isset($_POST["domain_name"]) && $_POST["domain_name"] != ""){
    $username = $_POST["username"];
    $password = $_POST["password"];
    $domain_name = $_POST["domain_name"];
}else{
    header("Location: ../devices.php");
    exit;
}

if(!filter_var($domain_name, FILTER_VALIDATE_DOMAIN)){
    echo "not a domain";
}else{
    echo "is a domain";
}

?>