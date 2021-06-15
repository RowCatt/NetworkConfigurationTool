<?php
$page_name = "View Configuration"; // Set page name
require("requires/header.php");
require("requires/connect.php");
$config_id = $_GET["id"];
$query = "SELECT * FROM configurations WHERE id='$config_id'";
$query = mysqli_query($connect,$query);
while($config = mysqli_fetch_assoc($query)){
    $running_config = $config["configuration"];
}

echo "<pre> $running_config </pre>";

?>