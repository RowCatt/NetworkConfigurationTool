<?php

$page_name = "Add Device"; // Set page name
// Insert header
require("requires/header.php");

// Add databse connection info
require("requires/connect.php");

?>

<body>

    <div class="container">

        <?php require("requires/navbar.php"); // insert navbar ?>

        <div class='container'>

            <form action='process/addDeviceProcess.php' method='post' style='width: 30%; margin: 20px;'>

                <h3> Add Device </h3>

                <div class="form-group">
                    <label for="ip">IP Address</label>
                    <input type="text" class="form-control" name="ip" id="ip">
                </div>

                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" class="form-control" name="username" id="username">
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="text" class="form-control" name="password" id="password">
                </div>

                <div class="form-group">
                    <label for="model">Model</label>
                    <select class="form-control" name="model" id="model">
                        <?php

                        // Get all models from DB
                        $query = "SELECT * FROM `models`";
                        $query = mysqli_query($connect,$query);
                        while($model = mysqli_fetch_assoc($query))
                        {
                            $model = $model["name"];
                            echo "<option value='$model'>$model</option>";
                        }

                        ?>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">Add Device</button>

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