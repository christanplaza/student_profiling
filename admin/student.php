<?php
session_start();
include '../../config.php';
$conn = mysqli_connect($host, $username, $password, $database);
if (isset($_GET['id'])) {
    if ($conn) {
        $id = $_GET['id'];
        $sql = "SELECT * FROM users WHERE id = '$id' AND role = 'student'";

        $user_res = mysqli_query($conn, $sql);
        $user = mysqli_fetch_assoc($user_res);

        $sql = "SELECT eval.*, q.name FROM evaluation as eval INNER JOIN questionnaire as q ON eval.questionnaire_id = q.id WHERE student_id = '$id' ORDER BY datetime_taken DESC";
        $evaluations_res = mysqli_query($conn, $sql);
    } else {
        echo "Couldn't connect to database.";
    }
} else if (isset($_POST['id'])) {
    if ($conn) {
        $id = $_POST['id'];
        $sql = "DELETE FROM users WHERE id = '$id'";

        mysqli_query($conn, $sql);
        header("location: $rootURL/admin/student_management.php");
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
                    <h1>Student Profile</h1>
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
                                <td>Gender</td>
                                <td><?php echo $user['gender']; ?></td>
                            </tr>
                            <tr>
                                <td>Address</td>
                                <td><?php echo $user['address']; ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-12">
                        <div class="mb-4">
                            <h3>Evaluation</h3>
                        </div>
                    </div>
                    <div class="col-12">
                        <table class="table table-striped">
                            <thead>
                                <tr class="table-primary">
                                    <th>Date Taken</th>
                                    <th>Questionnaire</th>
                                    <th>Validity</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (mysqli_num_rows($evaluations_res) > 0) : ?>
                                    <?php while ($eval = $evaluations_res->fetch_assoc()) : ?>
                                        <tr>
                                            <td><?php echo $eval['datetime_taken']; ?></td>
                                            <td><?php echo $eval['name']; ?></td>
                                            <td>
                                                <?php if ($eval['validity'] == 1) : ?>
                                                    <h6 class="m-0">
                                                        <span class="badge bg-success">Taken</span>
                                                    </h6>
                                                <?php else : ?>
                                                    <h6 class="m-0">
                                                        <span class="badge bg-secondary">Invalid</span>
                                                    </h6>
                                                <?php endif ?>
                                            </td>
                                            <td>
                                                <a href="#" class="btn btn-warning">Retake</a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="4" class="text-center">Student has no Evaluations yet</td>
                                    </tr>
                                <?php endif; ?>
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