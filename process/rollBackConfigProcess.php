<?php

require("../requires/connect.php");

// ERROR CHECKING

if(isset($_POST["id"]) && $_POST["id"] != ""){
    $config_id = $_POST["id"];
}else{
    header("Location: ../devices.php");
    exit;
}

// Send info to python to execute
exec("python3 /var/www/html/NetworkConfigurationTool/process/python/rollBackConfig.py '$id'", $output);

$code = $output[0];
$message = $output[1];

if($code == "ERROR"){
    header("Location: ../devices.php?error=$message");
}else{
    header("Location: ../devices.php");
}

?>