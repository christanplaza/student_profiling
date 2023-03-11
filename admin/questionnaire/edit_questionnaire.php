<?php
session_start();
include '../../../config.php';

$conn = mysqli_connect($host, $username, $password, $database);

if ($conn) {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $sql = "SELECT * FROM questionnaire WHERE id = '$id'";

        $questionnaire_res = mysqli_query($conn, $sql);
        $questionnaire = $questionnaire_res->fetch_assoc();

        if (isset($_POST['questionnaire_type']) && isset($_POST['questionnaire_name']) && isset($_POST['questionnaire_desc']) && isset($_POST['questionnaire_instruction'])) {
            $questionnaire_type = $_POST['questionnaire_type'];
            $questionnaire_name = $_POST['questionnaire_name'];
            $questionnaire_desc = str_replace("'", "''", $_POST['questionnaire_desc']);
            $questionnaire_instruction = str_replace("'", "''", $_POST['questionnaire_instruction']);
            $questionnaire_range = $_POST['questionnaire_range'];

            if ($questionnaire_type == "range" && isset($questionnaire_range) && strlen($questionnaire_range) > 0) {
                $sql = "UPDATE questionnaire SET question_type = '$questionnaire_type', name = '$questionnaire_name', description = '$questionnaire_desc', instruction = '$questionnaire_instruction', selection_range = '$questionnaire_range' WHERE id = '$id'";
            } else if ($questionnaire_type == "range" && isset($questionnaire_range) && strlen($questionnaire_range) == 0) {
                $sql = "UPDATE questionnaire SET question_type = '$questionnaire_type', name = '$questionnaire_name', description = '$questionnaire_desc', instruction = '$questionnaire_instruction', selection_range = '5' WHERE id = '$id'";
            } else {
                $sql = "UPDATE questionnaire SET question_type = '$questionnaire_type', name = '$questionnaire_name', description = '$questionnaire_desc', instruction = '$questionnaire_instruction' WHERE id = '$id'";
            }

            $res = mysqli_query($conn, $sql);

            if ($res) {
                header("location: $rootURL/admin/questionnaire_management.php");
            } else {
                echo $conn->error;
            }
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
                            <div class="display-6">Edit Questionnaire</div>
                            <div class="row mt-4">
                                <div class="col-12">
                                    <form method="POST">
                                        <div class="mb-3">
                                            <label for="questionnaire_name" class="form-label">Name</label>
                                            <input type="text" name="questionnaire_name" id="questionnaire_name" class="form-control" value="<?= $questionnaire['name']; ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="questionnaire_desc" class="form-label">Description</label>
                                            <textarea class="form-control" name="questionnaire_desc" id="questionnaire_desc" rows="3" style="resize:none;" required><?= $questionnaire['description']; ?></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label for="questionnaire_instruction" class="form-label">Instruction</label>
                                            <textarea class="form-control" name="questionnaire_instruction" id="questionnaire_instruction" rows="3" style="resize:none;" required><?= $questionnaire['instruction']; ?></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label for="questionnaire_type" class="form-label">Question Type</label>
                                            <select name="questionnaire_type" id="questionnaire_type" class="form-select" required>
                                                <option value="range" <?php echo $questionnaire['question_type'] == "range" ? "selected" : "" ?>>Range</option>
                                                <option value="rank" <?php echo $questionnaire['question_type'] == "rank" ? "selected" : "" ?>>Ranking</option>
                                                <option value="choices" <?php echo $questionnaire['question_type'] == "choices" ? "selected" : "" ?>>Choices</option>
                                            </select>
                                        </div>
                                        <?php if ($questionnaire['question_type'] == "range") : ?>
                                            <div class="mb-3">
                                                <label for="questionnaire_range" class="form-label">Range (Number of choices, e.g for range 1-5, put 5. Default is 5)</label>
                                                <input type="number" min="1" name="questionnaire_range" id="questionnaire_range" value="<?= $questionnaire['selection_range']; ?>" class="form-control">
                                            </div>
                                        <?php endif; ?>
                                        <input type="submit" name="submit" value="Update Questionnaire" class="btn btn-success">
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