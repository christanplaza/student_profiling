<?php
session_start();
include '../../config.php';
$conn = mysqli_connect($host, $username, $password, $database);

if ($conn) {
    $sql = "SELECT * FROM questionnaire";

    $questionnaire_res = mysqli_query($conn, $sql);

    if (isset($_POST['delete']) && isset($_POST['id'])) {
        $id = $_POST['id'];
        $sql = "DELETE FROM questionnaire WHERE id = '$id'";

        if (mysqli_query($conn, $sql)) {
            header("location: $rootURL/admin/questionnaire_management.php");
        }
    }
} else {
    echo "Couldn't connect to database.";
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


            <div class="col">
                <div class="mt-4">
                    <h1>Questionnaire Management</h1>
                    <div class="row mt-4">
                        <div class="col-12 mb-4">
                            <a href="<?= $rootURL ?>/admin/questionnaire/add_questionnaire.php" class="float-end btn btn-success">Add Questionnaire</a>
                        </div>
                        <div class="col-12">
                            <table class="table table-striped align-middle">
                                <thead>
                                    <tr class="table-primary">
                                        <th>Assessment</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = $questionnaire_res->fetch_assoc()) : ?>
                                        <tr>
                                            <td><?php echo $row['name']; ?></td>
                                            <td class="d-flex justify-content-evenly">
                                                <a href="<?= $rootURL ?>/admin/questionnaire.php?id=<?php echo $row['id']; ?>" class="btn btn-primary">View Details</a>
                                                <a href="<?= $rootURL ?>/admin/questionnaire/edit_questionnaire.php?id=<?php echo $row['id']; ?>" class="btn btn-warning">Edit</a>
                                                <form method="POST">
                                                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>" />
                                                    <button type="submit" name="delete" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this questionnaire?')">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
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