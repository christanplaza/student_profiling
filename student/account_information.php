<?php
session_start();
include '../../config.php';

$conn = mysqli_connect($host, $username, $password, $database);
if (isset($_COOKIE['id'])) {
    if ($conn) {
        $id = $_COOKIE['id'];
        $sql = "SELECT * FROM users WHERE id = '$id' AND role = 'student'";

        $user_res = mysqli_query($conn, $sql);
        $user = mysqli_fetch_assoc($user_res);

        $sql = "SELECT eval.*, q.name FROM evaluation as eval INNER JOIN questionnaire as q ON eval.questionnaire_id = q.id WHERE student_id = '$id' ORDER BY datetime_taken DESC";
        $evaluations_res = mysqli_query($conn, $sql);
    } else {
        echo "Couldn't connect to database.";
    }
} else {
    header("location: $rootURL/admin/student_management.php");
}
include('../logout.php');

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once "../components/header.php"; ?>
    <title>Student Profiling | Student</title>
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
                    <?php if (isset($_SESSION['msg_type']) && isset($_SESSION['flash_message'])) : ?>
                        <div class="alert alert-<?php echo $_SESSION["msg_type"]; ?> alert-dismissible fade show" role="alert">
                            <?php echo $_SESSION["flash_message"]; ?>
                        </div>
                    <?php endif; ?>
                    <?php
                    unset($_SESSION['msg_type']);
                    unset($_SESSION['flash_message']);
                    ?>
                    <div class="card shadow">
                        <div class="card-header bg-secondary-subtle">
                            <div class="row">
                                <div class="col-12">
                                    Student Profile
                                    <a href="<?= $rootURL ?>/student/edit_profile.php" class="btn btn-secondary btn-sm float-end">Edit Profile</a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 mb-4">
                                    <table class="table">
                                        <tr>
                                            <td>Name</td>
                                            <td><?php echo $user['name']; ?></td>
                                        </tr>
                                        <tr>
                                            <td>Username</td>
                                            <td><?php echo $user['username']; ?></td>
                                        </tr>
                                        <tr>
                                            <td>Age</td>
                                            <td><?php echo $user['age']; ?></td>
                                        </tr>
                                        <tr>
                                            <td>Birthdate</td>
                                            <td><?php echo $user['birthdate']; ?></td>
                                        </tr>
                                        <tr>
                                            <td>Gender</td>
                                            <td><?php echo $user['gender']; ?></td>
                                        </tr>
                                        <tr>
                                            <td>Course</td>
                                            <td><?php echo $user['course']; ?></td>
                                        </tr>
                                        <tr>
                                            <td>Year Level</td>
                                            <td><?php echo $user['year']; ?></td>
                                        </tr>
                                        <tr>
                                            <td>Section</td>
                                            <td><?php echo $user['section']; ?></td>
                                        </tr>
                                        <tr>
                                            <td>Address</td>
                                            <td><?php echo $user['address']; ?></td>
                                        </tr>
                                        <tr>
                                            <td>Elementary Education</td>
                                            <td><?php echo $user['educ_elementary']; ?></td>
                                        </tr>
                                        <tr>
                                            <td>Senior Highschool Education</td>
                                            <td><?php echo $user['educ_secondary']; ?></td>
                                        </tr>
                                        <tr>
                                            <td>Highschool Education</td>
                                            <td><?php echo $user['educ_highschool']; ?></td>
                                        </tr>
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