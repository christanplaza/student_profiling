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
            $first_name = $_POST['first_name'];
            $last_name = $_POST['last_name'];
            $username = $_POST['username'];
            $age = $_POST['age'];
            $birthdate = $_POST['birthdate'];
            $gender = $_POST['gender'];
            $course = $_POST['course'];
            $year = $_POST['year'];
            $section = $_POST['section'];
            $address = $_POST['address'];
            $educ_elementary = $_POST['educ_elementary'];
            $educ_secondary = $_POST['educ_secondary'];
            $educ_highschool = $_POST['educ_highschool'];

            if (isset($_POST['password']) && isset($_POST['confirm_password'])) {
                $password = $_POST['password'];
                $confirm_password = $_POST['confirm_password'];

                if ($password == $confirm_password) {
                    $hashed = md5($password);
                    $sql = "UPDATE users SET first_name = '$first_name', last_name = '$last_name', username = '$username', password = '$hashed', age = '$age', birthdate = '$birthdate', gender = '$gender', course = '$course', year = '$year', section = '$section', address = '$address', educ_elementary = '$educ_elementary', educ_secondary = '$educ_secondary', educ_highschool = '$educ_highschool' WHERE id = '$id'";
                    if (mysqli_query($conn, $sql)) {
                        $_SESSION['msg_type'] = 'success';
                        $_SESSION['flash_message'] = 'Account Updated';
                        header("location: $rootURL/student/account_information.php");
                        exit();
                    } else {
                        header("location: $rootURL/student/account_information.php");
                    }
                } else {
                    $_SESSION['msg_type'] = 'danger';
                    $_SESSION['flash_message'] = 'Password and Confirm Password does not match';
                }
            } else {
                $sql = "UPDATE users SET first_name = '$first_name', last_name = '$last_name', username = '$username', age = '$age', birthdate = '$birthdate', gender = '$gender', course = '$course', year = '$year', section = '$section', address = '$address', educ_elementary = '$educ_elementary', educ_secondary = '$educ_secondary', educ_highschool = '$educ_highschool' WHERE id = '$id'";
                if (mysqli_query($conn, $sql)) {
                    $_SESSION['msg_type'] = 'success';
                    $_SESSION['flash_message'] = 'Account Updated';
                    header("location: $rootURL/student/account_information.php");
                    exit();
                } else {
                    header("location: $rootURL/student/account_information.php");
                }
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
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Intelli.fied | Student</title>
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
                    <div class="display-6 mb-4">Account Information</div>
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
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
                                                <div class="col-6">
                                                    <table class="table">
                                                        <tr>
                                                            <td>First Name</td>
                                                            <td>
                                                                <input type="text" name="first_name" value="<?= $user['first_name']; ?>" class="form-control" required>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>Last Name</td>
                                                            <td>
                                                                <input type="text" name="last_name" value="<?= $user['last_name']; ?>" class="form-control" required>
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
                                                            <td>Birthdate</td>
                                                            <td>
                                                                <input type="text" name="birthdate" value="<?= $user['birthdate']; ?>" class="form-control" required>
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
                                                    </table>
                                                </div>
                                                <div class="col-6">
                                                    <table class="table">
                                                        <tr>
                                                            <td>Elementary Education</td>
                                                            <td>
                                                                <input type="text" name="educ_elementary" value="<?= $user['educ_elementary']; ?>" class="form-control" required>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>Senior Highschool Education</td>
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
                                                        <tr>
                                                            <td colspan="2"><b>You may leave password blank if you don't want to change password.</b></td>
                                                        </tr>
                                                        <tr>
                                                            <td>Password</td>
                                                            <td>
                                                                <input type="password" name="password" class="form-control">
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>Confirm Password <br>(required if you input new password)</td>
                                                            <td>
                                                                <input type="password" name="confirm_password" class="form-control">
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
            </div>
        </div>
    </div>
</body>

</html>