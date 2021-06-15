<?php
require("../requires/connect.php");

if(isset($_POST["id"]) && $_POST["id"] != "" && isset($_POST["name"]) && $_POST["name"] != ""){
    $id = $_POST["id"];
    $name = $_POST["name"];
}else{
    header("Location: ../vlanManagement.php?error=Fields left empty.");
    exit;
}

$query = "SELECT * FROM vlans WHERE vlan_name='$name'";
$query = mysqli_query($connect,$query);
if(mysqli_num_rows($query) > 0){
    header("Location: ../vlanManagement.php?error=VLAN Name already in use.");
    exit;                                                                                                                                                                                                                                                                                                                                        
}

// Passed error checking, update the DB
$query = "UPDATE vlans SET vlan_name='$name' WHERE id='$id'";
mysqli_query($connect,$query);

header("Location: ../vlanManagement.php");

?>