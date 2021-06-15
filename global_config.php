<?php

$page_name = "Global Configuration"; // Set page name
// Insert header
require("requires/header.php");

// Add databse connection info
require("requires/connect.php");

// Get global conf from DB
$query = "SELECT * FROM global_configuration";
$query = mysqli_query($connect,$query);
while($config = mysqli_fetch_assoc($query)){
    $global_username = $config["username"];
    $global_password = $config["password"];
    $global_domain_name = $config["domain_name"];
}

?>

<body>

    <div class="container">

        <?php require("requires/navbar.php"); // insert navbar ?>

        <div class='container'>

            <form action='process/globalConfigProcess.php' method='post' style='width: 30%; margin: 20px;'>

                <h3> Edit Global Configuration </h3>

                <div class="form-group">
                    <a class='btn btn-primary' href='vlanManagement.php'> VLAN Management </a>
                </div>

                <div class="form-group">
                    <label for="username">Username</label>
                    <?php echo "<input type='text' class='form-control' name='username' id='username' value='$global_username'>"; ?>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <?php echo "<input type='text' class='form-control' name='password' id='password' value='$global_password'>"; ?>
                </div>

                <div class="form-group">
                    <label for="domain_name">Domain Name</label>
                    <?php echo "<input type='text' class='form-control' name='domain_name' id='domain_name' value='$global_domain_name'>"; ?>
                </div>

                <button type="submit" class="btn btn-primary">Save</button>
                <a style='margin-left: 15px;' class='btn btn-danger' href='devices.php'> Back </a>

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