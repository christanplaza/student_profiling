<?php
session_start();
include '../../../config.php';
if (isset($_POST['submit'])) {
    if (isset($_POST['username']) && isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['password-confirm']) && isset($_POST['password'])) {
        $conn = mysqli_connect($host, $username, $password, $database);

        if ($_POST['password-confirm'] == $_POST['password']) {
            if ($conn) {
                $first_name = $_POST['first_name'];
                $last_name = $_POST['last_name'];
                $username = $_POST['username'];
                $password = md5($_POST['password']);

                $sql = "SELECT * FROM users WHERE username = '$username'";
                $username_res = mysqli_query($conn, $sql);

                if (mysqli_num_rows($username_res) > 0) {
                    $_SESSION['msg_type'] = 'danger';
                    $_SESSION['flash_message'] = 'Username already exists';
                } else {
                    $sql = "INSERT INTO users (name, username, password, role) VALUES ('$name', '$username', '$password', 'faculty');";

                    if (mysqli_query($conn, $sql)) {
                        $_SESSION['msg_type'] = 'success';
                        $_SESSION['flash_message'] = 'Account Created';

                        header("location: $rootURL/admin/faculty_management.php");
                        exit();
                    } else {
                        $_SESSION['msg_type'] = 'danger';
                        $_SESSION['flash_message'] = 'Registration Failed';
                    }
                }
            } else {
                $_SESSION['msg_type'] = 'danger';
                $_SESSION['flash_message'] = 'Database Connection Failed';
            }
        } else {
            $_SESSION['msg_type'] = 'danger';
            $_SESSION['flash_message'] = 'Password and Confirm Password do not match.';
        }
    } else {
        echo "All fields are required";
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
                    <h1>Add Faculty</h1>
                    <div class="row">
                        <div class="col-12 mt-4">
                            <form method="POST">
                                <div class="form-floating mb-3">
                                    <input type="text" name="first_name" class="form-control" placeholder="Juan" value="<?= isset($_POST['first_name']) ? $_POST['first_name'] : ""; ?>" required>
                                    <label for="floatingInput">First Name</label>
                                </div>
                                <div class="form-floating mb-3">
                                    <input type="text" name="last_name" class="form-control" placeholder="Dela Cruz" value="<?= isset($_POST['last_name']) ? $_POST['last_name'] : ""; ?>" required>
                                    <label for="floatingInput">Last Name</label>
                                </div>
                                <div class="form-floating mb-3">
                                    <input type="text" name="username" class="form-control" placeholder="JohnDoe27" value="<?= isset($_POST['username']) ? $_POST['username'] : ""; ?>" required>
                                    <label for="floatingInput">Username</label>
                                </div>
                                <div class="form-floating mb-3">
                                    <input type="password" name="password" class="form-control" placeholder="Password" required>
                                    <label for="password">Password</label>
                                </div>
                                <div class="form-floating mb-3">
                                    <input type="password" name="password-confirm" class="form-control" placeholder="Confirm Password" required>
                                    <label for="password-confirm">Confirm Password</label>
                                </div>
                                <button class="btn btn-success float-end" type="submit" name="submit">Create Account</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>