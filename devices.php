<?php

$page_name = "Devices"; // Set page name
// Insert header
require("requires/header.php");

// Add databse connection info
require("requires/connect.php");

?>

<body>

    <div class="container">

        <?php

        require("requires/navbar.php"); // insert navbar

        // Add Device Page link
        echo "<a style='margin: 15px;' class='btn btn-primary' href='add_device.php'> Add New Device </a>";

        // Get devices in the devices table
        $query = "SELECT * FROM `devices`";
        $query = mysqli_query($connect,$query);

        // Check to see if there are any entries in the devices table
        if(mysqli_num_rows($query) == 0) // If there's no entries
        {  
            echo "<p> No Devices in the Database. </p>";
        }else
        {

            // Devices table
            echo "<table class='table'>";
                echo "<thead>";
                    echo "<tr>";
                        echo "<th scope='col'> IP Address </th>";
                        echo "<th scope='col'> Model </th>";
                        echo "<th scope='col'> Action </th>";
                    echo "</tr>";
                echo "</thead>";
                echo "<tbody>";

                        // Display all devices
                        while($currentDevice = mysqli_fetch_assoc($query))
                        {
                            $id = $currentDevice["id"];
                            $ip_address = $currentDevice["ip_address"];
                            $model = $currentDevice["model"];
                            // Get model from the models table
                            $query_model = "SELECT * FROM `models` WHERE `id`='$model'";
                            $query_model = mysqli_query($connect,$query_model);
                            while($fetched_model = mysqli_fetch_assoc($query_model)){
                                $model = $fetched_model["name"];
                            }

                            echo "<tr>";
                                echo "<td> $ip_address </td>";
                                echo "<td> $model </td>";
                                echo "<td> <a style='margin: 15px;' class='btn btn-primary' href='edit_device_config.php?id=$id'> Edit Configuration </a> </td>";
                            echo "</tr>";
                            
                        }

                echo "</tbody>";
            echo "</table>";
        }

        ?>

    </div>
</body>

<?php
// Include footer
require("requires/footer.php");
?>