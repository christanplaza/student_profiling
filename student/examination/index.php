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
            $answerCount = mysqli_num_rows($questions_res);
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
                                        <form method="POST">
                                            <input type="hidden" value="choices" name="type" />
                                            <?php $count = 1; ?>
                                            <?php while ($row = $questions_res->fetch_assoc()) : ?>
                                                <div class="mb-4">
                                                    <p><?php echo $count; ?>. <?php echo $row['question_text']; ?></p>
                                                    <?php if (isset($row['question_image'])) : ?>
                                                        <img src="data:image/jpg;charset=utf8;base64,<?php echo base64_encode($row['question_image']); ?>" class="w-100" />
                                                    <?php endif; ?>
                                                </div>
                                                <input type="hidden" value="<?php echo $row['id']; ?>" name="question<?php echo $count; ?>_id" />
                                                <div class="mb-4">
                                                    <label class="form-label" for="question<?php echo $count; ?>">Your Answer</label>
                                                    <select class="form-control" name="question<?php echo $count; ?>_answer" required>
                                                        <option selected disabled>Choose your Answer</option>
                                                        <?php for ($i = 0; $i < 8; $i++) : ?>
                                                            <option value="<?php echo $letters[$i]; ?>"><?php echo $letters[$i]; ?></option>
                                                        <?php endfor; ?>
                                                    </select>
                                                </div>
                                                <?php $count++; ?>
                                            <?php endwhile; ?>
                                            <button type="submit" class="btn btn-success float-end" name="submit">Submit</button>
                                        </form>
                                    <?php elseif ($questionnaire['question_type'] == "rank" && $questions_res) : ?>
                                        <?php foreach ($allQuestions as $question) : ?>

                                        <?php endforeach; ?>
                                    <?php elseif ($questionnaire['question_type'] == "range" && $questions_res) : ?>
                                        <?php while ($row = $questions_res->fetch_assoc()) : ?>
                                            <tr>
                                                <?php if ($questionnaire['question_type'] == "rank") : ?>
                                                <?php endif; ?>
                                            </tr>
                                        <?php endwhile; ?>
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