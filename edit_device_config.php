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

if($online == 0){
    $online_str = "No";
}else{
    $online_str = "Yes";
}

?>

<body>

    <div class="container">

        <?php require("requires/navbar.php"); ?>

        <div class='container'>

            <form action='process/editDeviceConfigProcess.php' method='post' style='width: 30%; margin: 20px;'>

                <h3> Edit Configuration </h3>

                <div class="form-group">
                    <p>Model: <?php echo $model_name; ?></p>
                </div>

                <div class="form-group">
                    <p>Currently online? <?php echo $online_str; ?></p>
                </div>

                <div class="form-group">
                    <p>Last online: <?php echo $last_online; ?></p>
                </div>

                <div class="form-group">
                    <label for="use_global_conf"> Use Global Configuration? Note: This will bypass the information entered below. </label>
                    <input type="checkbox" id="use_global_conf" name="use_global_conf" value="yes">
                </div>

                <div class="form-group">
                    <label for="ip_address">IP Address</label>
                    <input type="text" class="form-control" name="ip_address" id="ip_address" value="<?php echo $ip_address; ?>">
                </div>

                
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" class="form-control" name="username" id="username" value="<?php echo $username; ?>">
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="text" class="form-control" name="password" id="password" value="<?php echo $password; ?>">
                </div>

                <div class="form-group">
                    <label for="hostname">Hostname</label>
                    <input type="text" class="form-control" name="hostname" id="hostname" value="<?php echo $hostname; ?>">
                </div>

                <div class="form-group">
                    <label for="domain_name">Domain Name</label>
                    <input type="text" class="form-control" name="domain_name" id="domain_name" value="<?php echo $domain_name; ?>">
                </div>

                <button type="submit" class="btn btn-primary"> Save </button>
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