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
                                                        <td>Birthdate</td>
                                                        <td><?php echo $user['birthdate']; ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Gender</td>
                                                        <td>
                                                            <?php switch ($user['gender']) {
                                                                case "male":
                                                                    echo "Male";
                                                                    break;
                                                                case "female":
                                                                    echo "Female";
                                                                    break;
                                                                case "others":
                                                                    echo "Others";
                                                                    break;
                                                            } ?>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>Course</td>
                                                        <td>
                                                            <?php switch ($user['course']) {
                                                                case "electronics":
                                                                    echo "Electronics Engineering";
                                                                    break;
                                                                case "mechanical":
                                                                    echo "Mechanical Engineering";
                                                                    break;
                                                                case "computer":
                                                                    echo "Computer Engineering";
                                                                    break;
                                                                case "electrical":
                                                                    echo "Electrical Engineering";
                                                                    break;
                                                                case "mechatronics":
                                                                    echo "Mechatronics Engineering";
                                                                    break;
                                                                case "instrumentation_control":
                                                                    echo "Instrumentation and";
                                                                    break;
                                                            } ?>
                                                        </td>
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
            </div>
        </div>
    </div>
</body>

</html>