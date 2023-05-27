<?php
session_start();
include '../../config.php';
$conn = mysqli_connect($host, $username, $password, $database);

if ($conn) {
    if (isset($_GET['sort_by']) && isset($_GET['order'])) {
        $sort_by = $_GET['sort_by'];
        $order = $_GET['order'];
        $sql = "SELECT * FROM users WHERE role = 'student' and reset_password = TRUE ORDER BY $sort_by $order";

        $students_res = mysqli_query($conn, $sql);
    } else {
        $sql = "SELECT * FROM users WHERE role = 'student' and reset_password = TRUE ORDER BY last_name";

        $students_res = mysqli_query($conn, $sql);
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
    <title>Intelli.fied | Faculty</title>
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
                    <h1>List of Requests</h1>
                    <div class="row mt-5">
                        <div class="col-12 mb-2">
                            <a href="<?= $rootURL ?>/faculty/student_management.php?sort_by=first_name&order=ASC" class="btn btn-sm btn-primary">Sort By First Name (ASC)</a>
                            <a href="<?= $rootURL ?>/faculty/student_management.php?sort_by=first_name&order=DESC" class="btn btn-sm btn-primary">Sort By First Name (DESC)</a>
                            <a href="<?= $rootURL ?>/faculty/student_management.php?sort_by=last_name&order=ASC" class="btn btn-sm btn-primary">Sort By Last Name (ASC)</a>
                            <a href="<?= $rootURL ?>/faculty/student_management.php?sort_by=last_name&order=DESC" class="btn btn-sm btn-primary">Sort By Last Name (DESC)</a>
                        </div>
                        <div class="col-12">
                            <table class="table table-striped align-middle">
                                <thead>
                                    <tr class="table-primary">
                                        <th>First Name</th>
                                        <th>Last Name</th>
                                        <th>TUPV ID</th>
                                        <th>Reset Key</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = $students_res->fetch_assoc()) : ?>
                                        <tr>
                                            <td>
                                                <?php echo $row['first_name']; ?>
                                            </td>
                                            <td>
                                                <?php echo $row['last_name']; ?>
                                            </td>
                                            <td>
                                                <?php echo $row['username']; ?>
                                            </td>
                                            <td>
                                                <div class="input-group">
                                                    <input type="password" name="password" class="form-control" id="password<?= $row['id'] ?>" value="<?= $row['reset_key']; ?>" readonly>
                                                    <span class="input-group-text" onclick="togglePasswordVisibility(<?= $row['id'] ?>)">
                                                        <i class="bi bi-eye-slash" id="icon"></i>
                                                    </span>
                                                </div>
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
<script>
    function togglePasswordVisibility(id) {
        const passwordInput = document.getElementById("password" + id);
        const icon = document.getElementById("icon");

        if (passwordInput.type == "password") {
            passwordInput.type = "text";
            icon.classList.remove("bi-eye-slash");
            icon.classList.add("bi-eye-fill");
        } else {
            passwordInput.type = "password";
            icon.classList.remove("bi-eye-fill");
            icon.classList.add("bi-eye-slash");
        }
    }

    function copyToClipboard() {
        // Select the password input field
        var passwordInput = document.getElementById("password");

        // Select its text
        passwordInput.select();

        // Copy the selected text to the clipboard
        document.execCommand("copy");

        // Deselect the text
        window.getSelection().removeAllRanges();
        console.log("hit");
    }
</script>

</html>