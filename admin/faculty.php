<?php
session_start();
include '../../config.php';
$conn = mysqli_connect($host, $username, $password, $database);
if (isset($_GET['id'])) {
    if ($conn) {
        $id = $_GET['id'];
        $sql = "SELECT * FROM users WHERE id = '$id' AND role = 'faculty'";

        $user_res = mysqli_query($conn, $sql);
        $user = mysqli_fetch_assoc($user_res);
    } else {
        echo "Couldn't connect to database.";
    }
} else if (isset($_POST['id'])) {
    if ($conn) {
        $id = $_POST['id'];
        $sql = "DELETE FROM users WHERE id = '$id'";

        mysqli_query($conn, $sql);
        header("location: $rootURL/admin/faculty_management.php");
    } else {
        echo "Couldn't connect to database.";
    }
} else {
    header("location: $rootURL/admin/faculty_management.php");
}
include('../logout.php');

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Intelli.fied | Admin Dashboard</title>
    <?php include_once "../components/header.php"; ?>
</head>

<body>
    <div class="container-fluid">
        <div class="row flex-nowrap">
            <div class="col-auto col-md-3 col-xl-2 px-sm-2 px-0 bg-dark shadow">
                <?php include_once "components/new_panel.php" ?>
            </div>

            <div class="col">
                <div class="mt-4">
                    <h1>Faculty Profile</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?= $rootURL ?>/admin/faculty_management.php">Faculty Management</a></li>
                            <li class=" breadcrumb-item active" aria-current="page"><?php echo $user['id']; ?></li>
                        </ol>
                    </nav>
                    <table class="table">
                        <tr>
                            <td>First Name</td>
                            <td><?php echo $user['first_name']; ?></td>
                        </tr>
                        <tr>
                            <td>Last Name</td>
                            <td><?php echo $user['last_name']; ?></td>
                        </tr>
                        <tr>
                            <td>TUPV ID</td>
                            <td><?php echo $user['username']; ?></td>
                        </tr>
                        <tr>
                            <td>Age</td>
                            <td><?php echo $user['age']; ?></td>
                        </tr>
                        <tr>
                            <td>Gender</td>
                            <td><?php echo $user['gender']; ?></td>
                        </tr>
                        <tr>
                            <td>Address</td>
                            <td><?php echo $user['address']; ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>

</html>