<?php

$page_name = "Index"; // Set page name
// Insert header
require("requires/header.php");

?>
<body>
    <?php require("requires/navbar.php") // insert navbar ?>
    <div class="container">
        <a style="margin: 5px; margin-left: 10px;" class="btn btn-primary" href="devices.php"> Devices </a>
    </div>
</body>
<?php
// Include footer
require("requires/footer.php");
?>