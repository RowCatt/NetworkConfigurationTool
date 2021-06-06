<?php

require("../requires/connect.php");

// Check if data has been entered

if(isset($_POST["ip"]) && $_POST["ip"] != ""){
    $ip = $_POST["ip"];
}else{
    header("Location: ../add_device.php?error=IP Address field empty");
    exit;
}


?>