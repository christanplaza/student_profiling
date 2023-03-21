<?php
session_start();
include '../../config.php';
$conn = mysqli_connect($host, $username, $password, $database);

if ($conn) {
    if (isset($_GET['sort_by']) && isset($_GET['order'])) {
        $sort_by = $_GET['sort_by'];
        $order = $_GET['order'];
        $sql = "SELECT * FROM users WHERE role = 'student' ORDER BY $sort_by $order";

        $student_res = mysqli_query($conn, $sql);
    } else {
        $sql = "SELECT * FROM users WHERE role = 'student' ORDER BY last_name";

        $student_res = mysqli_query($conn, $sql);
    }
} else {
    echo "Couldn't connect to database.";
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
                    <h1>Masterlist</h1>
                    <div class="row mt-4">
                        <div class="col-12 mb-2">
                            <a href="<?= $rootURL ?>/admin/masterlist.php?sort_by=first_name&order=ASC" class="btn btn-sm btn-primary">Sort By First Name (ASC)</a>
                            <a href="<?= $rootURL ?>/admin/masterlist.php?sort_by=first_name&order=DESC" class="btn btn-sm btn-primary">Sort By First Name (DESC)</a>
                            <a href="<?= $rootURL ?>/admin/masterlist.php?sort_by=last_name&order=ASC" class="btn btn-sm btn-primary">Sort By Last Name (ASC)</a>
                            <a href="<?= $rootURL ?>/admin/masterlist.php?sort_by=last_name&order=DESC" class="btn btn-sm btn-primary">Sort By Last Name (DESC)</a>
                        </div>
                        <div class="col-12">
                            <table class="table table-striped align-middle">
                                <thead>
                                    <tr class="table-primary">
                                        <th>First Name</th>
                                        <th>Last Name</th>
                                        <th>TUPV ID</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = $student_res->fetch_assoc()) : ?>
                                        <tr>
                                            <td><?php echo $row['first_name']; ?></td>
                                            <td><?php echo $row['last_name']; ?></td>
                                            <td><?php echo $row['username']; ?></td>
                                            <td class="d-flex justify-content-evenly">
                                                <a href="<?= $rootURL ?>/admin/student.php?id=<?php echo $row['id']; ?>" class="btn btn-primary">View Details</a>
                                                <form action="<?= $rootURL; ?>/admin/student.php?" method="POST">
                                                    <input type="hidden" name="id" id="id" value="<?php echo $row['id']; ?>" />
                                                    <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this user?')">Delete User</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>