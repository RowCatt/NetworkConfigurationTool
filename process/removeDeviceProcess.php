<?php

require("../requires/connect.php");

// ERROR CHECKING

if(isset($_POST["device_id"]) && $_POST["device_id"] != ""){
    $device_id = $_POST["device_id"];
}else{
    header("Location: ../devices.php");
    exit;
}

// Delete all configtuations with this device_id
$query = "DELETE FROM configurations WHERE device_id='$device_id'";
$query = mysqli_query($connect,$query);

// Delete device
$query = "DELETE FROM devices WHERE id='$device_id'";
$query = mysqli_query($connect,$query);

header("Location: ../devices.php");

?>