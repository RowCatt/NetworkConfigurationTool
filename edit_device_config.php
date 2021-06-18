<?php
$page_name = "Edit Device Configuration";
require("requires/header.php");

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
            <div class="row">
                <div class="col" style='margin-top: 20px;'>
                    <form action='process/editDeviceConfigProcess.php' method='post'>
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
                            <?php
                            if($use_global_conf == 1){
                                echo "<input type='checkbox' id='use_global_conf' name='use_global_conf' value='Yes' checked>";
                            }else{
                                echo "<input type='checkbox' id='use_global_conf' name='use_global_conf' value='Yes'>";
                            }
                            ?>
                        </div>
                        <div class="form-group">
                            <p>IP Address: <?php echo $ip_address; ?></p>
                            <p>If you wish to change the IP Address, delete and re-add the device.</p>
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
                        <input type="hidden" id="id" name="id" value="<?php echo $id; ?>">
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
                <div class="col" style='margin-top: 20px;'>
                    <h3> Configurations </h3>
                    <p> Choose a Configuration to roll back to </p>
                    <ul class="list-group">
                        <?php
                        $query_config = "SELECT * FROM configurations WHERE device_id='$id' ORDER BY time_saved DESC";
                        $query_config = mysqli_query($connect,$query_config);
                        while($config = mysqli_fetch_assoc($query_config)){
                            $config_time_saved = $config["time_saved"];
                            $config_id = $config["id"];
                            
                            echo "<li class='list-group-item'>";
                                echo $config_time_saved;
                                echo "<a style='margin-left: 15px;' class='btn btn-primary' href='viewConfig.php?id=$config_id'> View </a>";
                                echo "<a style='margin-left: 15px;' class='btn btn-success' href='process/rollBackConfigProcess.php?id=$config_id'> Roll Back </a>";
                            echo "</li>";
                        }
                        ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</body>

<?php require("requires/footer.php"); ?>