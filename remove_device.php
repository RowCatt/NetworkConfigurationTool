<?php

$page_name = "Remove Device";
require("requires/header.php");

require("requires/connect.php");

$device_id = $_GET["id"];

?>

<body>
    <div class="container">
        <?php require("requires/navbar.php"); ?>
        <div class='container'>
            <form action='process/removeDeviceProcess.php' method='post' style='width: 30%; margin: 20px;'>
                <h3> Are you sure you want to remove this device? </h3>
                <p>Note: This will remove all saved configurations associated with the device. Please back these up before removing if you wish to keep them.</p>
                <?php echo"<input type='hidden' name='device_id' id='device_id' value='$device_id'>"; ?>
                <button type="submit" class="btn btn-primary">Remove Device</button>
                <a style='margin-left: 15px;' class='btn btn-danger' href='devices.php'> Cancel </a>
            </form>
            <?php
            // Check to see if there's an error sent by the processing page
            if(isset($_GET["error"]) && $_GET["error"] != ""){
                $error = $_GET["error"];
                echo "<p style='color: red'> $error </p>";
            }
            ?>
        </div>
    </div>
</body>

<?php require("requires/footer.php"); ?>