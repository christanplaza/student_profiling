<?php
session_start();
include '../../../config.php';

$conn = mysqli_connect($host, $username, $password, $database);

if ($conn) {
    if (isset($_POST['questionnaire_type']) && isset($_POST['questionnaire_name']) && isset($_POST['questionnaire_desc']) && isset($_POST['questionnaire_instruction'])) {
        $questionnaire_type = $_POST['questionnaire_type'];
        $questionnaire_name = $_POST['questionnaire_name'];
        $questionnaire_desc = str_replace("'", "''", $_POST['questionnaire_desc']);
        $questionnaire_instruction = str_replace("'", "''", $_POST['questionnaire_instruction']);
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
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Intelli.fied | Admin Dashboard</title>
    <?php include_once "../../components/header.php"; ?>
</head>

<body>
    <div class="container-fluid">
        <div class="row flex-nowrap">
            <div class="col-auto col-md-3 col-xl-2 px-sm-2 px-0 bg-dark shadow">
                <?php $deep = true; ?>
                <?php include_once "../components/new_panel.php" ?>
            </div>
            <div class="col">
                <div class="mt-4">
                    <h1>Add Questionnaire</h1>
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
</body>

</html>