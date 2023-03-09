<?php
session_start();
include '../../../config.php';

if (isset($_GET['id'])) {
    $conn = mysqli_connect($host, $username, $password, $database);
    $question_id = $_GET['id'];
    $questionnaire_type = $_GET['type'];


    if ($conn) {
        // $sql = "SELECT * FROM questionnaire WHERE id = '$questionnaire_id'";
        // $questionnaire_res = mysqli_query($conn, $sql);
        // $questionnaire = $questionnaire_res->fetch_assoc();

        // $sql = "SELECT * FROM question_group WHERE questionnaire_id = '$questionnaire_id'";
        // $question_group_res = mysqli_query($conn, $sql);
        // if ($question_group_res) {
        //     $question_group = $question_group_res->fetch_assoc();
        //     $question_group_id = $question_group['id'];
        // }

        // Get Question
        $sql = "SELECT * FROM questions WHERE id = '$question_id'";
        $question_res = mysqli_query($conn, $sql);
        $question = $question_res->fetch_assoc();
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
                            <div class="display-6">Question Details</div>
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="question_text" class="form-label">Question</label>
                                        <input type="text" name="question_text" id="question_text" class="form-control" value="<?php echo $question['question_text']; ?>" readonly>
                                    </div>
                                    <?php if (isset($question['question_image'])) : ?>
                                        <img src="data:image/jpg;charset=utf8;base64,<?php echo base64_encode($question['question_image']); ?>" class="w-100" />
                                    <?php endif; ?>
                                    <?php if (isset($question['disagree_text'])) : ?>
                                        <div class="mb-3">
                                            <label for="disagree_text" class="form-label">Disagree Label</label>
                                            <input type="text" name="disagree_text" id="disagree_text" class="form-control" value="<?php echo $question['disagree_text']; ?>" readonly>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (isset($question['agree_text'])) : ?>
                                        <div class=" mb-3">
                                            <label for="agree_text" class="form-label">Agree Label</label>
                                            <input type="text" name="agree_text" id="agree_text" class="form-control" value="<?php echo $question['agree_text']; ?>" readonly>
                                        </div>
                                    <?php endif; ?>
                                    <a href=" <?php echo $_SERVER['HTTP_REFERER']; ?>" class="btn btn-secondary">Go back</a>
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