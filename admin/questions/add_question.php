<?php
session_start();

if (isset($_GET['id'])) {
    $conn = mysqli_connect('localhost', 'root', '', 'student_profiling');
    $questionnaire_id = $_GET['id'];

    if ($conn) {
        // Get Questionnaire Info
        $sql = "SELECT * FROM questionnaire WHERE id = '$questionnaire_id'";
        $questionnaire_res = mysqli_query($conn, $sql);
        $questionnaire = $questionnaire_res->fetch_assoc();

        $sql = "SELECT * FROM question_group WHERE questionnaire_id = '$questionnaire_id'";
        $question_group_res = mysqli_query($conn, $sql);
        if ($question_group_res) {
            $question_group = $question_group_res->fetch_assoc();
            $question_group_id = $question_group['id'];

            if (isset($_POST['submit'])) {
                $question_text = $_POST['question_text'];

                // If there is image
                if (!empty($_FILES["question_file"]["name"])) {
                    $fileName = basename($_FILES["question_file"]["name"]);
                    $fileType = pathinfo($fileName, PATHINFO_EXTENSION);

                    // Allow only certain file formats
                    $allowTypes = array('jpg', 'png', 'jpeg', 'gif');
                    if (in_array($fileType, $allowTypes)) {
                        $image = $_FILES["question_file"]["tmp_name"];
                        $imgContent = addslashes(file_get_contents($image));

                        $insert = "INSERT into questions (question_group_id, question_text, question_image) VALUES ('$question_group_id', '$question_text', '$imgContent')";

                        if (mysqli_query($conn, $insert)) {
                            echo "File Uploaded";
                        } else {
                            echo $conn->error;
                        }
                    }
                }
                // $sql = "INSERT into questions (questionnaire_id, question_text) VALUES ('$questionnaire_id', '$question_text')";
                // $res = mysqli_query($conn, $sql);
                // if ($res) {
                //     header('location: /student_profiling/admin/questionnaire.php?id=' . $questionnaire_id);
                // }
            }
        }
    } else {
        echo "Couldn't connect to database.";
    }
} else {
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
                            <div class="display-6">Add a new Question</div>
                            <div class="row mt-4">
                                <div class="col-12">
                                    <form method="POST" enctype="multipart/form-data">
                                        <div class="mb-3">
                                            <label for="question_text" class="form-label">Question</label>
                                            <input type="text" name="question_text" id="question_text" class="form-control required">
                                        </div>
                                        <?php switch ($questionnaire['question_type']) {
                                            case "range": ?>
                                                <!-- <div class="mb-3">
                                                    <label for="question_text" class="form-label">Answer Range</label>
                                                    <input type="text" name="question_text" id="question_text" class="form-control required">
                                                </div> -->
                                            <?php break;
                                            case "rank": ?>
                                                <div class="mb-3">
                                                    <label for="question_text" class="form-label">Question</label>
                                                    <input type="text" name="question_text" id="question_text" class="form-control" required>
                                                </div>
                                            <?php break;
                                            case "choices": ?>
                                                <div class="mb-3">
                                                    <label for="question_file" class="form-label">Image (optional)</label>
                                                    <input type="file" name="question_file" id="question_file" class="form-control" required>
                                                </div>
                                        <?php break;
                                        }
                                        ?>
                                        <input type="submit" name="submit" value="Add Question" class="btn btn-success">
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