<?php
session_start();
include '../../config.php';
$conn = mysqli_connect($host, $username, $password, $database);

if ($conn) {
    $id = $_GET['id'];

    $sql = "SELECT eval.*, q.name, q.description FROM evaluation as eval INNER JOIN questionnaire as q ON eval.questionnaire_id = q.id  WHERE eval.id = '$id'";
    $eval_res = mysqli_query($conn, $sql);
    $eval = $eval_res->fetch_assoc();
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
            <div class="row justify-content-center">
                <!-- <div class="col-4">
                    <?php include_once "components/panel.php" ?>
                </div> -->
                <div class="col-6">
                    <div class="card shadow">
                        <div class="card-body">
                            <div class="display-6 mb-4">Results</div>
                            <div class="mb-3">
                                <label for="name" class="form-label">Questionnaire Name</label>
                                <input class="form-control" name="name" type="text" value="<?= $eval['name']; ?>" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="result" class="form-label">Evaluation Result</label>
                                <textarea class="form-control" name="result" name="result" cols="30" rows="10" readonly style="resize: none;"><?= $eval['evaluation_result']; ?></textarea>
                            </div>
                            <a href="<?= $_SERVER['HTTP_REFERER'] ?>" class="btn btn-primary">Go back</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>