<?php
require("../requires/connect.php");

if(isset($_POST["id"]) && $_POST["id"] != ""){
    $id = $_POST["id"];
}else{
    header("Location: ../vlanManagement.php?error=Fields left empty.");
    exit;
}

// Update the entry with no name deleted=0
$query = "UPDATE vlans SET vlan_name='', deleted='1' WHERE id='$id'";
mysqli_query($connect,$query);
header("Location: ../vlanManagement.php");

?>