<?php

require("../requires/connect.php");

// ERROR CHECKING

if(isset($_POST["ip"]) && $_POST["ip"] != "" && isset($_POST["username"]) && $_POST["username"] != "" && isset($_POST["password"]) && $_POST["password"] != "" && isset($_POST["model"]) && $_POST["model"] != ""){
    $ip = $_POST["ip"];
    $username = $_POST["username"];
    $password = $_POST["password"];
    $model = $_POST["model"];
}else{
    header("Location: ../add_device.php?error=Fields left empty.");
    exit;
}

// Check each field to see if it's legitimate

// Check if IP is an IP
if(!filter_var($ip, FILTER_VALIDATE_IP)){
    header("Location: ../add_device.php?error=Entered IP Address is not valid.");
    exit;
}

// Send this data to python to add the device
exec("python3 /var/www/html/NetworkConfigurationTool/process/python/addDevice.py '$ip' '$username' '$password' '$model'", $output);
// print_r($output);
// var_dump($output);
$code = $output[0];
$message = $output[1];
echo "Code: $code";
echo "<br>";
echo "Message: $message";

?>