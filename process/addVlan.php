<?php
require("../requires/connect.php");

if(isset($_POST["id"]) && $_POST["id"] != "" && isset($_POST["name"]) && $_POST["name"] != ""){
    $id = $_POST["id"];
    $name = $_POST["name"];
}else{
    header("Location: ../vlanMangement.php?error=Fields left empty.");
    exit;
}

// Error if id is 1
if($id == 1){
    header("Location: ../vlanMangement.php?error=VLAN Number already in use.");
    exit;
}

// Check to see if this is is already in the db
$query = "SELECT * FROM vlans WHERE vlan_number='$id'";
$query = mysqli_query($connect,$query);
if(mysqli_num_rows($query) > 0){
    // VLAN Number already in use
    header("Location: ../vlanMangement.php?error=VLAN Number already in use.");
    exit;                                                                                                                                                                                                                                                                                                                                        
}
$query = "SELECT * FROM vlans WHERE vlan_name='$name'";
$query = mysqli_query($connect,$query);
if(mysqli_num_rows($query) > 0){
    header("Location: ../vlanMangement.php?error=VLAN Name already in use.");
    exit;                                                                                                                                                                                                                                                                                                                                        
}

// Passed error checking, add to the DB
$query = "INSERT INTO vlans (vlan_number, vlan_name) VALUES ('$id', '$name')";
mysqli_query($connect,$query);

header("Location: ../vlanMangement.php");

?>