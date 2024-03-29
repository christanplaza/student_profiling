<?php
session_start();
include '../../config.php';
if (isset($_GET['id'])) {
    $conn = mysqli_connect($host, $username, $password, $database);
    $id = $_GET['id'];
    if ($conn) {
        $sql = "SELECT * FROM questionnaire WHERE id = '$id'";

        $questionnaire_res = mysqli_query($conn, $sql);
        $questionnaire = $questionnaire_res->fetch_assoc();

        $sql = "SELECT * FROM question_group WHERE questionnaire_id = '$id'";
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
    } else {
        echo "Couldn't connect to database.";
    }
} else {
    header("location: $rootURL/admin/");
}
include('../logout.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Intelli.fied | Admin Dashboard</title>
    <?php include_once "../components/header.php"; ?>
</head>

<body>
    <div class="container-fluid">
        <div class="row flex-nowrap">
            <div class="col-auto col-md-3 col-xl-2 px-sm-2 px-0 bg-dark shadow">
                <?php include_once "components/new_panel.php" ?>
            </div>


            <div class="col" style="overflow-y: scroll;">
                <div class="mt-4" style="max-height: 95vh;">
                    <h1><?php echo $questionnaire['name']; ?></h1>
                    <p><?php echo $questionnaire['description']; ?></p>
                    <p>Questionnaire Type:
                        <strong>
                            <?php switch ($questionnaire['question_type']) {
                                case "range":
                                    echo "Ranged";
                                    break;
                                case "rank":
                                    echo "Ranking";
                                    break;
                                case "choices":
                                    echo "Choices";
                                    break;
                            }
                            ?>
                        </strong>
                    </p>
                    <div class="row mt-4">
                        <div class="col-12 mb-4">
                            <a href="<?= $rootURL ?>/admin/questions/add_question.php?id=<?php echo $id; ?>" class="float-end btn btn-success">Add Question</a>
                        </div>
                        <div class="col-12">
                            <table class="table table-striped align-middle">
                                <thead>
                                    <tr class="table-primary">
                                        <th>Question</th>
                                        <?php if ($questionnaire['question_type'] == "rank") : ?>
                                            <th>Group</th>
                                            <th>Intelligence Area</th>
                                        <?php endif; ?>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($questionnaire['question_type'] == "choices" && $questions_res) : ?>
                                        <?php while ($row = $questions_res->fetch_assoc()) : ?>
                                            <tr>
                                                <td>
                                                    <a href="<?= $rootURL ?>/admin/questions/question_detail.php?id=<?php echo $row['id']; ?>&type=<?php echo $questionnaire['question_type']; ?>">
                                                        <?php echo $row['question_text']; ?>
                                                    </a>
                                                </td>
                                                <?php if ($questionnaire['question_type'] == "rank") : ?>
                                                    <td> Grouping thing </td>
                                                <?php endif; ?>
                                                <td class="d-flex justify-content-evenly">
                                                    <a class="btn btn-sm btn-warning mr-4" href="<?= $rootURL; ?>/admin/questions/edit_question.php?id=<?php echo $row['id']; ?>&questionnaire=<?= $id; ?>">Edit</a>
                                                    <form action="<?= $rootURL; ?>/admin/questions/remove_question.php" method="POST">
                                                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>" />
                                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this question?')">Delete</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php elseif ($questionnaire['question_type'] == "rank" && $questions_res) : ?>
                                        <?php foreach ($allQuestions as $question) : ?>
                                            <tr>
                                                <td>
                                                    <?php echo $question['question_text']; ?>
                                                </td>
                                                <td><?php echo $question['group']; ?></td>
                                                <td><?php echo $question['intelligence_area']; ?></td>
                                                <td class="d-flex justify-content-evenly">
                                                    <a class="btn btn-sm btn-warning mr-4" href="<?= $rootURL; ?>/admin/questions/edit_question.php?id=<?php echo $question['id']; ?>&questionnaire=<?= $id; ?>">Edit</a>
                                                    <form action="<?= $rootURL; ?>/admin/questions/remove_question.php" method="POST">
                                                        <input type="hidden" name="id" value="<?php echo $question['id']; ?>" />
                                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this question?')">Delete</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php elseif ($questionnaire['question_type'] == "range" && $questions_res) : ?>
                                        <?php while ($row = $questions_res->fetch_assoc()) : ?>
                                            <tr>
                                                <td>
                                                    <a href="<?= $rootURL ?>/admin/questions/question_detail.php?id=<?php echo $row['id']; ?>&type=<?php echo $questionnaire['question_type']; ?>">
                                                        <?php echo $row['question_text']; ?>
                                                    </a>
                                                </td>
                                                <?php if ($questionnaire['question_type'] == "rank") : ?>
                                                    <td> Grouping thing </td>
                                                <?php endif; ?>
                                                <td class="d-flex justify-content-evenly">
                                                    <a class="btn btn-sm btn-warning mr-4" href="<?= $rootURL; ?>/admin/questions/edit_question.php?id=<?php echo $row['id']; ?>&questionnaire=<?= $id; ?>">Edit</a>
                                                    <form action="<?= $rootURL; ?>/admin/questions/remove_question.php" method="POST">
                                                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>" />
                                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this question?')">Delete</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else : ?>
                                        <tr>
                                            <td colspan="3" class="text-center">This Questionnaire has no Questions</td>
                                        </tr>
                                    <?php endif ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>