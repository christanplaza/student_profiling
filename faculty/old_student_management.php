<?php
session_start();
include '../../config.php';
$conn = mysqli_connect($host, $username, $password, $database);

if ($conn) {
    if (isset($_GET['sort_by']) && isset($_GET['order'])) {
        $sort_by = $_GET['sort_by'];
        $order = $_GET['order'];
        $sql = "SELECT * FROM users WHERE role = 'student' ORDER BY $sort_by $order";

        $students_res = mysqli_query($conn, $sql);
    } else {
        $sql = "SELECT * FROM users WHERE role = 'student' ORDER BY last_name";

        $students_res = mysqli_query($conn, $sql);
    }
} else {
    echo "Couldn't connect to database.";
}

include('../logout.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once "../components/header.php"; ?>
    <title>Student Profiling | Faculty</title>
</head>

<body>
    <nav class="navbar navbar-expand-lg bg-body-tertiary shadow-lg">
        <div class="container">
            <a class="navbar-brand" href="#">Technological University Of The Philippines Visayas</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                    </li>
                </ul>
                <div class="dropdown">
                    <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Menu
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#">About</a></li>
                        <li><a class="dropdown-item" href="#">Help</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <form method="POST"><button type="submit" name="logout" class="dropdown-item" href="#">Logout</button></form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="mt-5">
            <div class="row">
                <div class="col-4">
                    <?php include_once "components/panel.php" ?>
                </div>
                <div class="col-8">
                    <div class="card shadow">
                        <div class="card-body">
                            <div class="display-6">List of Students</div>
                            <div class="row mt-5">
                                <div class="col-12 mb-2">
                                    <a href="<?= $rootURL ?>/faculty/student_management.php?sort_by=first_name&order=ASC" class="btn btn-sm btn-primary">Sort By First Name (ASC)</a>
                                    <a href="<?= $rootURL ?>/faculty/student_management.php?sort_by=first_name&order=DESC" class="btn btn-sm btn-primary">Sort By First Name (DESC)</a>
                                    <a href="<?= $rootURL ?>/faculty/student_management.php?sort_by=last_name&order=ASC" class="btn btn-sm btn-primary">Sort By Last Name (ASC)</a>
                                    <a href="<?= $rootURL ?>/faculty/student_management.php?sort_by=last_name&order=DESC" class="btn btn-sm btn-primary">Sort By Last Name (DESC)</a>
                                </div>
                                <div class="col-12">
                                    <table class="table table-striped align-middle">
                                        <thead>
                                            <tr class="table-primary">
                                                <th>First Name</th>
                                                <th>Last Name</th>
                                                <th>Username</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while ($row = $students_res->fetch_assoc()) : ?>
                                                <tr>
                                                    <td>
                                                        <?php echo $row['first_name']; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $row['last_name']; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $row['username']; ?>
                                                    </td>
                                                    <td class="d-flex justify-content-evenly">
                                                        <a href="<?= $rootURL ?>/faculty/student.php?id=<?php echo $row['id']; ?>" class="btn btn-primary">View Details</a>
                                                        <form action="<?= $rootURL; ?>/faculty/student.php?" method="POST">
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
        </div>
    </div>
</body>

</html>