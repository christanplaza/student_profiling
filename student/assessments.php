<?php
session_start();
include '../../config.php';
$conn = mysqli_connect($host, $username, $password, $database);

if ($conn) {
    $student_id = $_COOKIE['id'];
    $sql = "SELECT * FROM questionnaire";

    $questionnaires_res = mysqli_query($conn, $sql);

    $sql = "SELECT * FROM evaluation WHERE student_id = '$student_id' AND validity = '1'";
    $evaluations_res = mysqli_query($conn, $sql);

    $eval_questionnaires = array();

    while ($eval = $evaluations_res->fetch_assoc()) {
        array_push($eval_questionnaires, $eval['questionnaire_id']);
    }

    $questionnaires = array();
    while ($questionnaire = $questionnaires_res->fetch_assoc()) {
        if (!in_array($questionnaire['id'], $eval_questionnaires)) {
            array_push($questionnaires, $questionnaire);
        }
    }

    if (isset($_POST['submit']) && isset($_POST['id'])) {
        $student_id = $_COOKIE['id'];
        $questionnaire_id = $_POST['id'];
        $sql = "INSERT INTO evaluation (student_id, questionnaire_id, validity, datetime_taken) VALUES ('$student_id', '$questionnaire_id', '0', NOW())";

        if (mysqli_query($conn, $sql)) {
            $eval_id = $conn->insert_id;
            header("location: $rootURL/student/examination/index.php?questionnaire=$questionnaire_id&eval=$eval_id");
        } else {
            echo "Error: " . $conn->error;
        }
    }
} else {
    echo "Couldn't connect to database.";
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
                    <div class="display-6">Assessments</div>

                    <div class="row mt-4">
                        <?php foreach ($questionnaires as $questionnaire) : ?>
                            <div class="col-6 d-flex align-items-stretc">
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <?php echo $questionnaire['name']; ?>
                                    </div>
                                    <div class="card-body d-flex flex-column justify-content-between">
                                        <?php echo $questionnaire['description']; ?>
                                        <form method="POST">
                                            <input type="hidden" name="id" value="<?php echo $questionnaire['id']; ?>">
                                            <button type="submit" name="submit" class="btn btn-primary mt-4 float-end">
                                                Take Assessment
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>