<?php
require("../requires/connect.php");

if(isset($_POST["id"]) && $_POST["id"] != "" && isset($_POST["name"]) && $_POST["name"] != ""){
    $id = $_POST["id"];
    $name = $_POST["name"];
}else{
    header("Location: ../vlanManagement.php?error=Fields left empty.");
    exit;
}

// Error if id is 1
if($id == 1){
    header("Location: ../vlanManagement.php?error=VLAN Number already in use.");
    exit;
}

// Check to see if this is is already in the db
$query = "SELECT * FROM vlans WHERE vlan_number='$id'";
$query = mysqli_query($connect,$query);
if(mysqli_num_rows($query) > 0){
    // VLAN Number already in use
    // Check if it has been deleted
    while($vlan = mysqli_fetch_assoc($query)){
        $deleted = $vlan["deleted"];
    }

    if($deleted == 0){
        // Has not been deleted, so is actively in use
        header("Location: ../vlanManagement.php?error=VLAN Number already in use.");
        exit; 
    }else{
        // Update the deleted vlan
        $query = "UPDATE vlans SET vlan_name='$name', deleted='0' WHERE id='$id'";
        $query = mysqli_query($connect,$query);
        header("Location: ../vlanManagement.php");
        exit;
    }

                                                                                                                                                                                                                                                                                                                                           
}

// If the ID hasn't been used (new entry), check if the name is already in use
$query = "SELECT * FROM vlans WHERE vlan_name='$name'";
$query = mysqli_query($connect,$query);
if(mysqli_num_rows($query) > 0){
    header("Location: ../vlanManagement.php?error=VLAN Name already in use.");
    exit;                                                                                                                                                                                                                                                                                                                                        
}

// Passed error checking, add to the DB
$query = "INSERT INTO vlans (vlan_number, vlan_name) VALUES ('$id', '$name')";
mysqli_query($connect,$query);

header("Location: ../vlanManagement.php");

?>