<?php

require("../requires/connect.php");

if(isset($_POST["id"]) && $_POST["id"] != ""){
    $config_id = $_POST["id"];
}else{
    header("Location: ../devices.php");
    exit;
}

// Send info to python to execute
exec("python3 /var/www/html/NetworkConfigurationTool/process/python/rollBackConfig.py '$id'", $output);

header("Location: ../devices.php");

?>