<?php
session_start();
include '../../config.php';
$letters = ["A", "B", "C", "D", "E", "F", "G"];

$conn = mysqli_connect($host, $username, $password, $database);
if (isset($_COOKIE['id'])) {
    if ($conn) {
        $id = $_COOKIE['id'];
        $sql = "SELECT * FROM users WHERE id = '$id' AND role = 'student'";

        $user_res = mysqli_query($conn, $sql);
        $user = mysqli_fetch_assoc($user_res);


        if (isset($_POST['submit'])) {
            $name = $_POST['name'];
            $username = $_POST['username'];
            $age = $_POST['age'];
            $gender = $_POST['gender'];
            $course = $_POST['course'];
            $year = $_POST['year'];
            $section = $_POST['section'];
            $address = $_POST['address'];
            $educ_elementary = $_POST['educ_elementary'];
            $educ_secondary = $_POST['educ_secondary'];
            $educ_highschool = $_POST['educ_highschool'];

            $sql = "UPDATE users SET name = '$name', username = '$username', age = '$age', gender = '$gender', course = '$course', year = '$year', section = '$section', address = '$address', educ_elementary = '$educ_elementary', educ_secondary = '$educ_secondary', educ_highschool = '$educ_highschool' WHERE id = '$id'";
            if (mysqli_query($conn, $sql)) {
                header("location: $rootURL/student/account_information.php");
            } else {
                header("location: $rootURL/student/account_information.php");
            }
        }

        $sql = "SELECT eval.*, q.name FROM evaluation as eval INNER JOIN questionnaire as q ON eval.questionnaire_id = q.id WHERE student_id = '$id' ORDER BY datetime_taken DESC";
        $evaluations_res = mysqli_query($conn, $sql);
    } else {
        echo "Couldn't connect to database.";
    }
} else {
    header("location: $rootURL/student/");
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
                    <div class="card shadow">
                        <form method="POST">
                            <div class="card-header bg-secondary-subtle">
                                <div class="row">
                                    <div class="col-12">
                                        Student Profile
                                        <button type="submit" class="btn btn-success btn-sm float-end" name="submit">Save Changes</button>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12">
                                        <table class="table">
                                            <tr>
                                                <td>Name</td>
                                                <td>
                                                    <input type="text" name="name" value="<?= $user['name']; ?>" class="form-control" required>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Username</td>
                                                <td>
                                                    <input type="text" name="username" value="<?= $user['username']; ?>" class="form-control" required>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Age</td>
                                                <td>
                                                    <input type="text" name="age" value="<?= $user['age']; ?>" class="form-control" required>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Gender</td>
                                                <td>
                                                    <input type="text" name="gender" value="<?= $user['gender']; ?>" class="form-control" required>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Course</td>
                                                <td>
                                                    <select name="course" class="form-select" required>
                                                        <option label="Select your Course"></option>
                                                        <option value="electronics" <?= $user['course'] == "electronics" ? "selected" : ""; ?>>Electronics Engineering</option>
                                                        <option value="mechanical" <?= $user['course'] == "mechanical" ? "selected" : ""; ?>>Mechanical Engineering</option>
                                                        <option value="computer" <?= $user['course'] == "computer" ? "selected" : ""; ?>>Computer Engineering</option>
                                                        <option value="electrical" <?= $user['course'] == "electrical" ? "selected" : ""; ?>>Electrical Engineering</option>
                                                        <option value="mechatronics" <?= $user['course'] == "mechatronics" ? "selected" : ""; ?>>Mechatronics Engineering</option>
                                                        <option value="instrumentation_control" <?= $user['course'] == "instrumentation_control" ? "selected" : ""; ?>>Instrumentation and Control Engineering</option>
                                                    </select>
                                                    <!-- <input type="text" name="gender" value="<?= $user['gender']; ?>" class="form-control" required> -->
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Year Level</td>
                                                <td>
                                                    <select name="year" class="form-select" required>
                                                        <option label="Select your Year Level"></option>
                                                        <option value="1" <?= $user['year'] == "1" ? "selected" : ""; ?>>1st Year</option>
                                                        <option value="2" <?= $user['year'] == "2" ? "selected" : ""; ?>>2nd Year</option>
                                                        <option value="3" <?= $user['year'] == "3" ? "selected" : ""; ?>>3rd Year</option>
                                                        <option value="4" <?= $user['year'] == "4" ? "selected" : ""; ?>>4th Year</option>
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Section</td>
                                                <td>
                                                    <select class="form-control" name="section" required>
                                                        <?php for ($i = 0; $i < 7; $i++) : ?>
                                                            <option value="<?php echo $letters[$i]; ?>" <?= $user['section'] == $letters[$i] ? "selected" : ""; ?>><?php echo $letters[$i]; ?></option>
                                                        <?php endfor; ?>
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Address</td>
                                                <td>
                                                    <input type="text" name="address" value="<?= $user['address']; ?>" class="form-control" required>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Elementary Education</td>
                                                <td>
                                                    <input type="text" name="educ_elementary" value="<?= $user['educ_elementary']; ?>" class="form-control" required>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Secondary Education</td>
                                                <td>
                                                    <input type="text" name="educ_secondary" value="<?= $user['educ_secondary']; ?>" class="form-control" required>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Highschool Education</td>
                                                <td>
                                                    <input type="text" name="educ_highschool" value="<?= $user['educ_highschool']; ?>" class="form-control" required>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>