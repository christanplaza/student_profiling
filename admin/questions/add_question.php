<?php
session_start();
include '../../../config.php';
$letters = ["A", "B", "C", "D", "E", "F", "G", "H"];

if (isset($_GET['id'])) {
    $conn = mysqli_connect($host, $username, $password, $database);
    $questionnaire_id = $_GET['id'];

    if ($conn) {
        // Get Questionnaire Info
        $sql = "SELECT * FROM questionnaire WHERE id = '$questionnaire_id'";
        $questionnaire_res = mysqli_query($conn, $sql);
        $questionnaire = $questionnaire_res->fetch_assoc();

        $sql = "SELECT * FROM question_group WHERE questionnaire_id = '$questionnaire_id'";
        $question_group_res = mysqli_query($conn, $sql);
        if ($questionnaire['question_type'] == "choices") {
            if ($question_group_res) {
                $question_group = $question_group_res->fetch_assoc();
                $question_group_id = $question_group['id'];

                if (isset($_POST['submit'])) {
                    $question_text = str_replace("'", "''", $_POST['question_text']);
                    $correct_answer = $_POST['correct_answer'];

                    // If there is image
                    if (!empty($_FILES["question_file"]["name"])) {
                        $fileName = basename($_FILES["question_file"]["name"]);
                        $fileType = pathinfo($fileName, PATHINFO_EXTENSION);

                        // Allow only certain file formats
                        $allowTypes = array('jpg', 'png', 'jpeg', 'gif', 'JPG', 'JPEG', 'PNG', 'GIF');
                        if (in_array($fileType, $allowTypes)) {
                            $image = $_FILES["question_file"]["tmp_name"];
                            $imgContent = addslashes(file_get_contents($image));

                            $insert = "INSERT into questions (question_group_id, question_text, question_image, correct_answer) VALUES ('$question_group_id', '$question_text', '$imgContent', '$correct_answer')";

                            if (mysqli_query($conn, $insert)) {
                                header("location: $rootURL/admin/questionnaire.php?id=$questionnaire_id");
                            } else {
                                echo $conn->error;
                            }
                        }
                    }
                }
            }
        } else if ($questionnaire['question_type'] == "rank") {
            if ($question_group_res) {
                if (isset($_POST["submit"])) {
                    $question_text = str_replace("'", "''", $_POST['question_text']);
                    $intelligence_area = $_POST['intelligence_area'];

                    while ($question_group = $question_group_res->fetch_assoc()) {
                        if ($_POST['question_group'] == $question_group['count']) {
                            $question_group_id = $question_group['id'];
                            $insert = "INSERT into questions (question_group_id, question_text, intelligence_area) VALUES ('$question_group_id', '$question_text', '$intelligence_area')";

                            if (mysqli_query($conn, $insert)) {
                                header("location: $rootURL/admin/questionnaire.php?id=$questionnaire_id");
                            } else {
                                echo $conn->error;
                            }
                        }
                    }
                }
            }
        } else if ($questionnaire['question_type'] == "range") {
            if ($question_group_res) {
                $question_group = $question_group_res->fetch_assoc();
                $question_group_id = $question_group['id'];

                if (isset($_POST["submit"])) {
                    $question_text = str_replace("'", "''", $_POST['question_text']);
                    $disagree_text = $_POST['disagree_text'];
                    $agree_text = $_POST['agree_text'];

                    $insert = "INSERT into questions (question_group_id, question_text, agree_text, disagree_text) VALUES ('$question_group_id', '$question_text', '$agree_text', '$disagree_text')";

                    if (mysqli_query($conn, $insert)) {
                        header("location: $rootURL/admin/questionnaire.php?id=$questionnaire_id");
                    } else {
                        echo $conn->error;
                    }
                }
            }
        }
    } else {
        echo "Couldn't connect to database.";
    }
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
                    <h1>Add Question</h1>
                    <div class="row mt-4">
                        <div class="col-12">
                            <form method="POST" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label for="question_text" class="form-label">Question</label>
                                    <input type="text" name="question_text" id="question_text" class="form-control" required>
                                </div>
                                <?php switch ($questionnaire['question_type']):
                                    case "range": ?>
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="mb-3">
                                                    <label for="disagree_text" class="form-label">Disagree Label</label>
                                                    <input type="text" name="disagree_text" id="disagree_text" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="mb-3">
                                                    <label for="agree_text" class="form-label">Agree Label</label>
                                                    <input type="text" name="agree_text" id="agree_text" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                    <?php break;
                                    case "rank": ?>
                                        <div class="mb-3">
                                            <label for="question_group" class="form-label">Question Group</label>
                                            <select name="question_group" class="form-select" required>
                                                <option value="1">1</option>
                                                <option value="2">2</option>
                                                <option value="3">3</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="intelligence_area" class="form-label">Multiple Intelligence Area</label>
                                            <select name="intelligence_area" class="form-select" required>
                                                <option label="Choose an Intelligence Area"></option>
                                                <option value="kinesthetic">Bodily/Kinesthetic</option>
                                                <option value="existential">Existential</option>
                                                <option value="intrapersonal">Intrapersonal</option>
                                                <option value="interpersonal">Interpersonal</option>
                                                <option value="logic">Logic</option>
                                                <option value="musical">Musical</option>
                                                <option value="naturalistic">Naturalistic</option>
                                                <option value="verbal">Verbal</option>
                                                <option value="visual">Visual</option>
                                            </select>
                                        </div>
                                    <?php break;
                                    case "choices": ?>
                                        <div class="mb-3">
                                            <label for="question_file" class="form-label">Image</label>
                                            <input type="file" name="question_file" id="question_file" class="form-control" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label" for="question<?php echo $count; ?>">The Correct Answer</label>
                                            <select class="form-control" name="correct_answer" required>
                                                <option selected disabled>Choose the Correct Answer</option>
                                                <?php for ($i = 0; $i < 8; $i++) : ?>
                                                    <option value="<?php echo $letters[$i]; ?>"><?php echo $letters[$i]; ?></option>
                                                <?php endfor; ?>
                                            </select>
                                        </div>
                                <?php break;
                                endswitch;
                                ?>
                                <input type="submit" name="submit" value="Add Question" class="btn btn-success">
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>