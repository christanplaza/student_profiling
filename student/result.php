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
                    <div class="mb-3">
                        <label for="name" class="form-label">Questionnaire Name</label>
                        <input class="form-control" name="name" type="text" value="<?= $eval['name']; ?>" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="result" class="form-label">Evaluation Result</label>
                        <textarea class="form-control" name="result" name="result" cols="30" rows="10" readonly style="resize: none;"><?= $eval['evaluation_result']; ?></textarea>
                    </div>
                    <a href="<?= $rootURL ?>/student/assessments.php" class="btn btn-primary">Go back to Assessments</a>
                </div>
            </div>
        </div>
    </div>
</body>

</html>