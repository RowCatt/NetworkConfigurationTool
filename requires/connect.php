<?php

$connect = mysqli_connect(
    "localhost", // DB Host
    "pi", // Username
    "%pa55w0rd", // Placeholder Password
    "NetworkConfigurationTool" // DB Name
    ) or die("Unable to connect to the database");

?>