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
                        <div class="card-body">
                            <div class="display-6 mb-5">Assessments</div>
                            <div class="row">
                                <?php foreach ($questionnaires as $questionnaire) : ?>
                                    <div class="col-6">
                                        <div class="card mb-4">
                                            <div class="card-header">
                                                <?php echo $questionnaire['name']; ?>
                                            </div>
                                            <div class="card-body">
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
        </div>
    </div>
</body>

</html>