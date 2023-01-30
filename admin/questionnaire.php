<?php
session_start();
if (isset($_GET['id'])) {
    $conn = mysqli_connect('localhost', 'root', '', 'student_profiling');
    $id = $_GET['id'];
    if ($conn) {
        $sql = "SELECT * FROM questionnaire WHERE id = '$id'";

        $questionnaire_res = mysqli_query($conn, $sql);
        $questionnaire = $questionnaire_res->fetch_assoc();
    } else {
        echo "Couldn't connect to database.";
    }
} else {
    header("location: /student_profiling/admin/");
}
include('../logout.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once "../components/header.php"; ?>
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
                    <?php include_once "components/panel.php" ?>
                </div>
                <div class="col-8">
                    <div class="card shadow">
                        <div class="card-body">
                            <div class="display-6 mb-2"><?php echo $questionnaire['name']; ?></div>
                            <p><?php echo $questionnaire['description']; ?></p>
                            <div class="row mt-4">
                                <div class="col-12 mb-4">
                                    <a href="/student_profiling/admin/questions/add_question.php?id=<?php echo $id; ?>" class="float-end btn btn-success">Add Question</a>
                                </div>
                                <div class="col-12">
                                    <table class="table table-striped align-middle">
                                        <thead>
                                            <tr class="table-primary">
                                                <th>Question #</th>
                                                <th>Question</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while ($row = $questionnaire_res->fetch_assoc()) : ?>
                                                <tr>
                                                    <td><?php echo $row['name']; ?></td>
                                                    <td>0</td>
                                                    <td>
                                                        <a href="/student_profiling/admin/questionnaire.php?id=<?php echo $row['id']; ?>" class="btn btn-primary">View Details</a>
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
        </div>
    </div>
</body>

</html>