<?php

$page_name = "Devices"; // Set page name
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
                <input type="text" name="ip">

                <label for="username">Username</label>
                <input type="text" name="username">

                <label for="password">Password</label>
                <input type="text" name="password">

                <label for="model">Model</label>
                <select name="model">
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

        </div>

    </div>
</body>

<?php
// Include footer
require("requires/footer.php");
?>