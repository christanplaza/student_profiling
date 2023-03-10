<?php
session_start();
include '../../../config.php';
$conn = mysqli_connect($host, $username, $password, $database);
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
                header("location: $rootURL/student/result.php?id=$evaluation_id");
            } else {
                echo $conn->error;
            }
        } else if ($_POST['type'] == "rank") {
            $answers = array();
            $hasError = false;
            $error = "";

            // Verify answers
            for ($i = 1; $i <= 27; $i++) {
                array_push($answers, $_POST["question$i"]);
            }

            // 1-9 only.
            if (max($answers) > 9 || min($answers) < 1) {
                $hasError = true;
                $error = "Rank each group from 1-9 only.";
            }

            // Groups
            for ($i = 0; $i < 3; $i++) {
                // Questions
                $num = $i * 9;
                $group = array();
                for ($j = 1; $j <= 9; $j++) {
                    $idx = $num + $j;
                    array_push($group, $_POST["question$idx"]);
                }

                $filtered = array_unique($group, SORT_NUMERIC);

                if (count($group) != count($filtered)) {
                    $hasError = true;
                    $error = "Rank each group from 1-9 only. There should be no duplicates";
                }
            }

            // Get all answers
            if (!$hasError) {
                $kinesthetic = $_POST['question1'] + $_POST['question10'] + $_POST['question19'];
                $existential = $_POST['question2'] + $_POST['question11'] + $_POST['question20'];
                $intrapersonal = $_POST['question4'] + $_POST['question13'] + $_POST['question12'];
                $interpersonal = $_POST['question3'] +  $_POST['question21'] + $_POST['question22'];
                $logic = $_POST['question5'] + $_POST['question14'] + $_POST['question23'];
                $musical = $_POST['question6'] + $_POST['question15'] + $_POST['question24'];
                $naturalistic = $_POST['question7'] + $_POST['question16'] + $_POST['question25'];
                $verbal = $_POST['question8'] + $_POST['question17'] + $_POST['question26'];
                $visual = $_POST['question9'] + $_POST['question18'] + $_POST['question27'];
                $results = array("Bodily / Kinesthetic" => $kinesthetic, "Existential" => $existential, "Interpersonal" => $interpersonal, "Intrapersonal" => $intrapersonal, "Logic" => $logic, "Musical" => $musical, "Naturalistic" => $naturalistic, "Verbal" => $verbal, "Visual" => $visual);
                asort($results);

                $eval = "Your Multiple Intelligence is ranked as follows, from top to bottom. \n";
                foreach ($results as $key => $result) {
                    $eval .= $key . ": " . $result . "\n";
                }

                $sql = "UPDATE evaluation SET is_complete = '1', validity = '1', evaluation_result = '$eval' WHERE id = '$evaluation_id'";
                if (mysqli_query($conn, $sql)) {
                    header("location: $rootURL/student/result.php?id=$evaluation_id");
                } else {
                    echo $conn->error;
                }
            } else {
                $_SESSION['error'] = $error;
            }
        } else if ($_POST['type'] == "range") {

            if ($questionnaire['range_type'] == "eq") {
                $self_awareness =       ["1", "6", "11", "16", "21", "26", "31", "36", "41", "46"];
                $managing_emotions =    ["2", "7", "12", "17", "22", "27", "32", "37", "42", "47"];
                $motivating_oneself =   ["3", "8", "13", "18", "23", "28", "33", "38", "43", "48"];
                $empathy =              ["4", "9", "14", "19", "24", "29", "34", "39", "44", "49"];
                $social_skill =         ["5", "10", "15", "20", "25", "30", "35", "40", "45", "50"];

                $questionCount = mysqli_num_rows($questions_res);

                $self_awareness_total = 0;
                $managing_emotions_total = 0;
                $motivating_oneself_total = 0;
                $empathy_total = 0;
                $social_skill_total = 0;

                for ($i = 1; $i <= $questionCount; $i++) {
                    if (in_array($i, $self_awareness)) {
                        $self_awareness_total += (int)$_POST["question" . $i . "_answer"];
                    } else if (in_array($i, $managing_emotions)) {
                        $managing_emotions_total += (int)$_POST["question" . $i . "_answer"];
                    } else if (in_array($i, $motivating_oneself)) {
                        $motivating_oneself_total += (int)$_POST["question" . $i . "_answer"];
                    } else if (in_array($i, $empathy)) {
                        $empathy_total += (int)$_POST["question" . $i . "_answer"];
                    } else if (in_array($i, $social_skill)) {
                        $social_skill_total += (int)$_POST["question" . $i . "_answer"];
                    }
                }

                $complete_array = array("Self Awareness" => $self_awareness_total, "Managing Emotions" => $managing_emotions_total, "Motivating Oneself" => $motivating_oneself_total, "Empathy" => $empathy_total, "Social Skills" => $social_skill_total);

                $eval = "Your Emotional Quotient results are as follows: \n";
                foreach ($complete_array as $key => $item) {
                    $text = "";
                    if ($item >= 35 && $item <= 50) {
                        $text .= "Strength";
                    } else if ($item >= 18 && $item <= 34) {
                        $text .= "Needs Attention";
                    } else if ($item >= 10 && $item <= 17) {
                        $text .= "Development Priority";
                    }
                    $eval .= $key . ": " . $item . " ($text)\n";
                }

                $sql = "UPDATE evaluation SET is_complete = '1', validity = '1', evaluation_result = '$eval' WHERE id = '$evaluation_id'";
                if (mysqli_query($conn, $sql)) {
                    header("location: $rootURL/student/result.php?id=$evaluation_id");
                } else {
                    echo $conn->error;
                }
            } else {
                $c = ["1", "7", "13", "15", "17"];
                $o = ["2", "6", "9", "11", "12", "16", "18", "20"];
                $r = ["3", "5", "10", "14", "19"];
                $e = ["4", "8"];

                $questionCount = mysqli_num_rows($questions_res);

                $c_total = 0;
                $o_total = 0;
                $r_total = 0;
                $e_total = 0;

                for ($i = 1; $i <= $questionCount; $i++) {
                    if (in_array($i, $c)) {
                        $c_total += (int)$_POST["question" . $i . "_answer"];
                    } else if (in_array($i, $o)) {
                        $o_total += (int)$_POST["question" . $i . "_answer"];
                    } else if (in_array($i, $r)) {
                        $r_total += (int)$_POST["question" . $i . "_answer"];
                    } else if (in_array($i, $e)) {
                        $e_total += (int)$_POST["question" . $i . "_answer"];
                    }
                }

                $final_arp_score = ($c_total + $o_total + $r_total + $e_total) * 2;

                $score = "";

                if ($final_arp_score <= 59) {
                    $score = "Very Low";
                } else if ($final_arp_score >= 60 && $final_arp_score <= 94) {
                    $score = "Low";
                } else if ($final_arp_score >= 95 && $final_arp_score <= 134) {
                    $score = "Medium";
                } else if ($final_arp_score >= 135 && $final_arp_score <= 165) {
                    $score = "High";
                } else if ($final_arp_score >= 166 && $final_arp_score <= 200) {
                    $score = "Very High";
                }

                $eval = "Your Adversity Response Profile is: " . $score . " \n";
                // Compute for Control percentage
                $c_percentage = $c_total / 25 * 100;
                $eval .= "Control: $c_percentage% \n";

                // Compute for Ownership percentage
                $o_percentage = $o_total / 40 * 100;
                $eval .= "Ownership: $o_percentage% \n";

                // Compute for Reach percentage
                $r_percentage = $r_total / 25 * 100;
                $eval .= "Reach: $r_percentage% \n";

                // Compute for Endurance percentage
                $e_percentage = $e_total / 10 * 100;
                $eval .= "Endurance: $e_percentage% \n";

                $sql = "UPDATE evaluation SET is_complete = '1', validity = '1', evaluation_result = '$eval' WHERE id = '$evaluation_id'";
                if (mysqli_query($conn, $sql)) {
                    header("location: $rootURL/student/result.php?id=$evaluation_id");
                } else {
                    echo $conn->error;
                }
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
                <div class="col-12">
                    <?php if (isset($_SESSION['error'])) : ?>
                        <div class="alert alert-danger" role="alert">
                            <?= $_SESSION['error']; ?>
                        </div>
                        <?php unset($_SESSION['error']); ?>
                    <?php endif; ?>
                </div>
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