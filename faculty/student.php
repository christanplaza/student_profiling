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
    header("location: $rootURL/faculty/student_management.php");
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
                        <div class="card-header bg-secondary-subtle">Student Profile</div>
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
                                    <table class="table table-striped align-middle">
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
                                                                    <span class="badge bg-success">Valid</span>
                                                                </h6>
                                                            <?php else : ?>
                                                                <h6 class="m-0">
                                                                    <span class="badge bg-secondary">Invalid</span>
                                                                </h6>
                                                            <?php endif ?>
                                                        </td>
                                                        <td class="d-flex justify-space-evenly">
                                                            <a href="/student_profiling/faculty/results.php?id=<?= $eval['id'] ?>" class="btn btn-primary">View Results</a>
                                                            <?php if ($eval['validity'] == 1) : ?>
                                                                <form action="/student_profiling/faculty/student.php?id=<?= $id ?>" method="post">
                                                                    <input type="hidden" name="eval_id" value="<?= $eval['id']; ?>">
                                                                    <button type="submit" class="btn btn-warning">Invalidate</button>
                                                                </form>
                                                            <?php endif; ?>
                                                        </td>
                                                    </tr>
                                                <?php endwhile; ?>
                                            <?php else : ?>
                                                <tr>
                                                    <td colspan="3" class="text-center">Student has no Evaluations yet</td>
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