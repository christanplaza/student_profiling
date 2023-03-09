<?php
session_start();
include '../../../config.php';

$conn = mysqli_connect($host, $username, $password, $database);

if ($conn) {
    if (isset($_POST['questionnaire_type']) && isset($_POST['questionnaire_name']) && isset($_POST['questionnaire_desc']) && isset($_POST['questionnaire_instruction'])) {
        $questionnaire_type = $_POST['questionnaire_type'];
        $questionnaire_name = $_POST['questionnaire_name'];
        $questionnaire_desc = $_POST['questionnaire_desc'];
        $questionnaire_instruction = $_POST['questionnaire_instruction'];
        $questionnaire_range = $_POST['questionnaire_range'];

        if ($questionnaire_type == "range" && isset($questionnaire_range) && strlen($questionnaire_range) > 0) {
            $sql = "INSERT into questionnaire (question_type, name, description, instruction, selection_range) VALUES ('$questionnaire_type', '$questionnaire_name', '$questionnaire_desc', '$questionnaire_instruction', '$questionnaire_range')";
        } else if ($questionnaire_type == "range" && isset($questionnaire_range) && strlen($questionnaire_range) == 0) {
            $sql = "INSERT into questionnaire (question_type, name, description, instruction, selection_range) VALUES ('$questionnaire_type', '$questionnaire_name', '$questionnaire_desc', '$questionnaire_instruction', '5')";
        } else {
            $sql = "INSERT into questionnaire (question_type, name, description, instruction) VALUES ('$questionnaire_type', '$questionnaire_name', '$questionnaire_desc', '$questionnaire_instruction')";
        }

        $res = mysqli_query($conn, $sql);
        if ($res) {

            // If choices
            if ($questionnaire_type == "choices" || $questionnaire_type == "range") {
                $questionnaire_id = $conn->insert_id;

                $sql = "INSERT INTO question_group (questionnaire_id) VALUES ('$questionnaire_id')";

                if (mysqli_query($conn, $sql)) {
                    header("location: $rootURL/admin/questionnaire_management.php");
                } else {
                    echo $conn->error;
                }
            } else if ($questionnaire_type == "rank") {
                $questionnaire_id = $conn->insert_id;

                for ($i = 0; $i < 3; $i++) {
                    $count = $i + 1;
                    $sql = "INSERT INTO question_group (questionnaire_id, count) VALUES ('$questionnaire_id', '$count')";
                    mysqli_query($conn, $sql);
                }
                header("location: $rootURL/admin/questionnaire_management.php");
            }
            header("location: $rootURL/admin/questionnaire_management.php");
        } else {
            echo $conn->error;
        }
    }
} else {
    echo "Couldn't connect to database.";
}
include('../../logout.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once "../../components/header.php"; ?>
    <title>Student Profiling | Admin</title>
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
                    <?php include_once "../components/panel.php" ?>
                </div>
                <div class="col-8">
                    <div class="card shadow">
                        <div class="card-body">
                            <div class="display-6">Create a Questionnaire</div>
                            <div class="row mt-4">
                                <div class="col-12">
                                    <form method="POST">
                                        <div class="mb-3">
                                            <label for="questionnaire_name" class="form-label">Name</label>
                                            <input type="text" name="questionnaire_name" id="questionnaire_name" class="form-control" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="questionnaire_desc" class="form-label">Description</label>
                                            <textarea class="form-control" name="questionnaire_desc" id="questionnaire_desc" rows="3" style="resize:none;" required></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label for="questionnaire_instruction" class="form-label">Instruction</label>
                                            <textarea class="form-control" name="questionnaire_instruction" id="questionnaire_instruction" rows="3" style="resize:none;" required></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label for="questionnaire_type" class="form-label">Question Type</label>
                                            <select name="questionnaire_type" id="questionnaire_type" class="form-select" required>
                                                <option selected disabled>Select a Question Type</option>
                                                <option value="range">Range</option>
                                                <option value="rank">Ranking</option>
                                                <option value="choices">Choices</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="questionnaire_range" class="form-label">Range (Number of choices, e.g for range 1-5, put 5. Default is 5)</label>
                                            <input type="number" min="1" name="questionnaire_range" id="questionnaire_range" class="form-control">
                                        </div>
                                        <input type="submit" name="submit" value="Create Questionnaire" class="btn btn-success">
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