<?php

$page_name = "VLAN Mangement"; // Set page name
// Insert header
require("requires/header.php");

// Add databse connection info
require("requires/connect.php");

?>

<body>

    <div class="container">

        <?php require("requires/navbar.php"); // insert navbar ?>

        <div class='container'>

            <h3> VLAN Management </h3>

            <table class="table">
                <thead>
                    <tr>
                        <th scope="col>"> ID </th>
                        <th scope="col>"> Name </th>
                        <th scope="col>"> Rename </th>
                        <th scope="col>"> Delete </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td> 1 </td>
                        <td> Native </td>
                        <td>  </td>
                        <td>  </td>
                    </tr>
                    <?php

                    // Get VLANS
                    $query = "SELECT * FROM vlans";
                    $query = mysqli_query($connect,$query);
                    while($vlan = mysqli_fetch_assoc($query)){
                        $vlan_id = $vlan["id"];
                        $vlan_number = $vlan["vlan_number"];
                        $vlan_name = $vlan["name"];

                        echo "<tr>";
                            echo "<td> $vlan_number </td>";
                            echo "<td> $vlan_name </td>";
                            echo "<td> <a class='btn btn-primary' href='renameVlan.php?id=$vlan_id'> Rename </a> </td>";
                            echo "<td> <a class='btn btn-danger' href='deleteVlan.php?id=$vlan_id'> Delete </a> </td>";
                        echo "</tr>";
                    }


                    ?>
                </tbody>
            </table>
        </div>

    </div>
</body>

<?php
// Include footer
require("requires/footer.php");
?>