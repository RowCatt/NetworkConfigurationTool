<?php

$page_name = "Devices"; // Set page name
// Insert header
require("requires/header.php");

// Add databse connection info
require("requires/connect.php");

?>

<div class="container">

    <?php

    // List devices in the devices table
    $query = "SELECT * FROM `devices`";
    $query = mysqli_query($connect,$query);

    // Check to see if there are any entries in the devices table
    if(mysqli_num_rows($query) == 0) // If there's no entries
    {  
        echo "<p> No Devices in the Database. </p>";
    }else
    {

        // Display all devices
        while($currentDevice = mysqli_fetch_assoc($query))
        {
            $ip_address = $currentDevice["ip_address"];
            $model = $currentDevice["model"];
            // Get model from the models table
            $query_model = "SELECT * FROM `models` WHERE `id`=`$model`";
            $query_model = mysqli_query($connect,$query_model)
            $model = $query_model["name"];

            echo "<p> $ip_address - $model</p>";
        }
    }

    ?>

</div>

<?php
// Include footer
require("requires/footer.php");
?>