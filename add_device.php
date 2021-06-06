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

            <h3> Add Device </h3>

            <form action='process/addDeviceProcess.php' method='post'>

                <label for="ip">IP Address</label>
                <input type="text" name="ip" id="ip">

                <label for="username">Username</label>
                <input type="text" name="username" id="username">

                <label for="password">Password</label>
                <input type="text" name="password" id="password">

                <label for="model">Model</label>
                <select name="model" id="model">
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

                <input type="submit" value="Save">

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