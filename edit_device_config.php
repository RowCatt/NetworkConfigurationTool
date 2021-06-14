<?php

$page_name = "Edit Device Configuration"; // Set page name
// Insert header
require("requires/header.php");

// Add databse connection info
require("requires/connect.php");

// Get device from id
if(isset($_GET["id"]) || $_GET["id"] != ""){
    $id = $_GET["id"];
}

$query = "SELECT * FROM devices WHERE id='$id' LIMIT 1";
$query = mysqli_query($connect,$query);
while($device = mysqli_fetch_assoc($query)){
    $model_id = $device["model"];
    $last_online = $device["last_online"];
    $online = $device["online"];
    $ip_address = $device["ip_address"];
    $username = $device["username"];
    $password = $device["password"];
    $use_global_conf = $device["use_global_conf"];
    $hostname = $device["hostname"];
    $domain_name = $device["domain_name"];
}

// Get model name
$query = "SELECT * FROM models WHERE id='$model_id' LIMIT 1";
$query = mysqli_query($connect,$query);
while($result = mysqli_fetch_assoc($query)){
    $model_name = $result["name"];
}

?>

<body>

    <div class="container">

        <?php require("requires/navbar.php"); ?>

        <div class='container'>

            <form action='process/editDeviceConfigProcess.php' method='post' style='width: 30%; margin: 20px;'>

                <h3> Edit Configuration </h3>

                <div class="form-group">
                    <p>Model: <?php echo $model_name ?></p>
                </div>

                <!-- <div class="form-group">
                    <label for="ip">IP Address</label>
                    <input type="text" class="form-control" name="ip" id="ip">
                </div> -->

                <!--
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" class="form-control" name="username" id="username">
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="text" class="form-control" name="password" id="password">
                </div> -->

                <button type="submit" class="btn btn-primary">Add Device</button>
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

<?php
// Include footer
require("requires/footer.php");
?>