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

        if (isset($_POST['eval_id'])) {
            $eval_id = $_POST['eval_id'];
            $sql = "UPDATE evaluation SET validity = '0' WHERE id = '$eval_id'";

            mysqli_query($conn, $sql);
            header("location: $rootURL/faculty/student.php?id=$id");
        }
    } else {
        echo "Couldn't connect to database.";
    }
} else {
    header("location: $rootURL/");
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
                    <div class="display-6 mb-4">Results</div>
                    <div class="row">
                        <div class="col-12">
                            <div class="card overflow-y-scroll p-2" style="max-height: 85vh;">
                                <div class="card-body">
                                    <table class="table table-striped align-middle overflow-y-scroll" style="max-height: 90vh;">
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
                                                    <tr class="align-center">
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
                                                        <td class="d-flex justify-space-evenly">
                                                            <a href="<?= $rootURL ?>/student/result.php?id=<?= $eval['id'] ?>" class="btn btn-primary">View Results</a>
                                                        </td>
                                                    </tr>
                                                <?php endwhile; ?>
                                            <?php else : ?>
                                                <tr>
                                                    <td colspan="4" class="text-center">You have no Evaluations yet</td>
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
        </div>
    </div>
</body>

</html>