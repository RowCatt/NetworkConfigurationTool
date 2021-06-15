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

        <div class='container' style="margin-top: 20px;">

            <h3> VLAN Management </h3>

            <!-- <a class='btn btn-primary' href='addVlan.php'> Add VLAN </a> -->
            <form action='process/addVlan.php' method='post' style='width: 20%;'>
                <p> Add new VLAN </p>
                <div class="form-group">
                    <label for="id">ID</label>
                    <input type="text" class="form-control" name="id" id="id">
                </div>
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" class="form-control" name="name" name="name">
                </div>
                <button type="submit" class="btn btn-primary">Add VLAN</button>
            </form>

            <table class="table" style="margin-top: 20px;">
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
                        $vlan_name = $vlan["vlan_name"];
						$vlan_deleted = $vlan["deleted"];

						if($vlan_deleted != 1){
							echo "<tr>";
								echo "<td> $vlan_number </td>";
								echo "<td> $vlan_name </td>";
								// echo "<td> <a class='btn btn-primary' href='renameVlan.php?id=$vlan_id'> Rename </a> </td>";
								echo "<td>";
									echo "<form action='process/renameVlan.php' method='post'>";
										echo "<input style='width: 30%;' type='text' name='name' id='name'>";
										echo "<input type='hidden' name='id' id='id' value='$vlan_id'>";
										echo "<button style='margin-left: 10px;' type='submit' class='btn btn-primary'>Rename</button>";
									echo "</form>";
								echo "</td>";
								echo "<td> <a class='btn btn-danger' href='process/deleteVlan.php?id=$vlan_id'> Delete </a> </td>";
							echo "</tr>";
						}
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