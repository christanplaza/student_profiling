<?php
session_start();
include '../../../config.php';
if (isset($_GET['id'])) {
    $conn = mysqli_connect($host, $username, $password, $database);
    $question_id = $_GET['id'];
    $questionnaire_type = $_GET['type'];


    if ($conn) {
        // Get Question
        $sql = "SELECT * FROM questions WHERE id = '$question_id'";
        $question_res = mysqli_query($conn, $sql);
        $question = $question_res->fetch_assoc();
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
                    <h1>Question Details</h1>
                    <div class="row mt-4 justify-content-center">
                        <div class="col-8 mb-4">
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
</body>

</html>