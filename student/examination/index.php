<?php
session_start();
$conn = mysqli_connect('localhost', 'root', '', 'student_profiling');
$letters = ["A", "B", "C", "D", "E", "F", "G", "H"];

if ($conn) {
    $questionnaire_id = $_GET['questionnaire'];
    $evaluation_id = $_GET['eval'];

    $sql = "SELECT * FROM questionnaire WHERE id = '$questionnaire_id'";
    $questionnaire_res = mysqli_query($conn, $sql);

    if ($questionnaire_res) {
        $questionnaire = $questionnaire_res->fetch_assoc();

        $sql = "SELECT * FROM question_group WHERE questionnaire_id = '$questionnaire_id'";
        $question_group_res = mysqli_query($conn, $sql);

        if ($questionnaire['question_type'] == "choices" || $questionnaire['question_type'] == "range" && $question_group_res) {
            $question_group = $question_group_res->fetch_assoc();
            $question_group_id = $question_group['id'];

            $sql = "SELECT * FROM questions WHERE question_group_id = '$question_group_id'";
            $questions_res = mysqli_query($conn, $sql);
        }

        if ($questionnaire['question_type'] == "rank" && $question_group_res) {
            $allQuestions = array();
            while ($question_group = $question_group_res->fetch_assoc()) {
                $question_group_id = $question_group['id'];

                $sql = "SELECT * FROM questions WHERE question_group_id = '$question_group_id'";
                $questions_res = mysqli_query($conn, $sql);
                while ($question = $questions_res->fetch_assoc()) {
                    $question['group'] = $question_group['count'];
                    array_push($allQuestions, $question);
                }
            }
        }
    }

    if (isset($_POST['submit'])) {
        if ($_POST['type'] == "choices") {
            // Compile all answers
            $questionCount = mysqli_num_rows($questions_res);

            $totalScore = 0;
            $user_answers = array();

            for ($i = 1; $i <= $questionCount; $i++) {
                array_push($user_answers, $_POST["question" . $i . "_answer"]);
            }

            $index = 0;
            while ($question = $questions_res->fetch_assoc()) {
                if ($question['correct_answer'] == $user_answers[$index]) {
                    var_dump($question['correct_answer'], $user_answers[$index]);
                    $totalScore++;
                }

                $index++;
            }

            echo $totalScore;

            $iq = "";

            // Evaluation of Score

            switch ($totalScore) {
                case 6:
                    $iq = "77";
                    break;
                case 7:
                    $iq = "79";
                    break;
                case 8:
                    $iq = "84";
                    break;
                case 9:
                    $iq = "88";
                    break;
                case 10:
                    $iq = "92";
                    break;
                case 11:
                    $iq = "94";
                    break;
                case 12:
                    $iq = "98";
                    break;
                case 13:
                    $iq = "101";
                    break;
                case 14:
                    $iq = "104";
                    break;
                case 15:
                    $iq = "108";
                    break;
                case 16:
                    $iq = "111";
                    break;
                case 17:
                    $iq = "114";
                    break;
                case 18:
                    $iq = "119";
                    break;
                case 19:
                    $iq = "123";
                    break;
                case 20:
                    $iq = "125";
                    break;
                case 21:
                    $iq = "132";
                    break;
                default:
                    if ($totalScore <= 5) {
                        $iq = "<= 73";
                    } else if ($totalScore >= 22) {
                        $iq = ">= 139";
                    }
                    break;
            }

            $result = "Your IQ According to this assessment is: " . $iq;

            $sql = "UPDATE evaluation SET is_complete = '1', validity = '1', evaluation_result = '$result' WHERE id = '$evaluation_id'";
            if (mysqli_query($conn, $sql)) {
                header("location: /student_profiling/student/assessments.php");
            } else {
                echo $conn->error;
            }
        } else if ($_POST['type'] == "rank") {
            // Get all answers

            $kinesthetic = $_POST['question1'] + $_POST['question10'] + $_POST['question19'];
            $existential = $_POST['question2'] + $_POST['question11'] + $_POST['question20'];
            $interpersonal = $_POST['question3'] + $_POST['question4'] + $_POST['question12'] + $_POST['question13'] + $_POST['question21'] + $_POST['question22'];
            $logic = $_POST['question5'] + $_POST['question14'] + $_POST['question23'];
            $musical = $_POST['question6'] + $_POST['question15'] + $_POST['question24'];
            $naturalistic = $_POST['question7'] + $_POST['question16'] + $_POST['question25'];
            $verbal = $_POST['question8'] + $_POST['question17'] + $_POST['question26'];
            $visual = $_POST['question9'] + $_POST['question18'] + $_POST['question27'];
            $results = array("Bodily / Kinesthetic" => $kinesthetic, "Existential" => $existential, "Interpersonal" => $interpersonal, "Logic" => $logic, "Musical" => $musical, "Naturalistic" => $naturalistic, "Verbal" => $verbal, "Visual" => $visual);
            asort($results);

            $eval = "Your Multiple Intelligence is ranked as follows, from top to bottom. \n";
            foreach ($results as $key => $result) {
                $eval .= $key . ": " . $result . "\n";
            }

            $sql = "UPDATE evaluation SET is_complete = '1', validity = '1', evaluation_result = '$eval' WHERE id = '$evaluation_id'";
            if (mysqli_query($conn, $sql)) {
                header("location: /student_profiling/student/assessments.php");
            } else {
                echo $conn->error;
            }
        }
    }
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
                <div class="col-12">
                    <div class="card shadow mb-5">
                        <div class="card-body">
                            <div class="display-6 mb-5"><?php echo $questionnaire['name']; ?></div>
                            <div class="row">
                                <div class="col-12">
                                    <?php if ($questionnaire['question_type'] == "choices" && $questions_res) : ?>
                                        <?php include_once("choices.php"); ?>
                                    <?php elseif ($questionnaire['question_type'] == "rank" && $questions_res) : ?>
                                        <?php include_once("rank.php"); ?>
                                    <?php elseif ($questionnaire['question_type'] == "range" && $questions_res) : ?>
                                        <?php $range = $questionnaire['selection_range']; ?>
                                        <?php include_once("range.php"); ?>
                                    <?php endif ?>
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