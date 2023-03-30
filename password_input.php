<?php
include '../config.php';
session_start();

if (isset($_POST['submit'])) {
    if (isset($_POST['password']) && isset($_POST['password-confirm'])) {
        $conn = mysqli_connect($host, $username, $password, $database);

        if ($conn) {
            if ($_POST['password-confirm'] == $_POST['password']) {
                $password = md5($_POST['password']);
                $reset_key = $_SESSION['reset_key'];
                $user_id = $_SESSION['user_id'];

                $sql = "SELECT * FROM users WHERE id = '$user_id' AND reset_key = '$reset_key'";

                $res = mysqli_query($conn, $sql);

                if (mysqli_num_rows($res) != 0) {
                    $user = mysqli_fetch_assoc($res);
                    $user_id = $user['id'];

                    // Next, prepare and execute the SQL update statement
                    $query = "UPDATE users SET password = '$password', reset_password = FALSE, reset_key = NULL WHERE id = '$user_id'";
                    $result = mysqli_query($conn, $query);

                    if (!$result) {
                        $_SESSION['msg_type'] = 'danger';
                        $_SESSION['flash_message'] = 'Something went wrong';
                    } else {
                        $_SESSION['msg_type'] = 'success';
                        $_SESSION['flash_message'] = 'Password has been reset';
                        header("location: index.php");
                        session_write_close();
                    }
                } else {
                    $_SESSION['msg_type'] = 'danger';
                    $_SESSION['flash_message'] = 'Invalid Reset Key';
                }
            } else {
                $_SESSION['msg_type'] = 'danger';
                $_SESSION['flash_message'] = 'Password and Confirm Password do not match';
            }
        } else {
            $_SESSION['msg_type'] = 'danger';
            $_SESSION['flash_message'] = 'Database Connection Failed';
        }
    } else {
        $_SESSION['msg_type'] = 'danger';
        $_SESSION['flash_message'] = 'All Fields are required';
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Profiling | Login</title>
    <!-- CSS only -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <style>
        body {
            background-size: cover;
            background-repeat: no-repeat;
            background-image: url("<?= $rootURL ?>/assets/bg.jpg");
        }

        .btn-primary-brand {
            background-color: #282f39;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row justify-content-end align-items-center" style="height: 95vh">
            <div class="col-4">
                <?php if (isset($_SESSION['msg_type']) && isset($_SESSION['flash_message'])) : ?>
                    <div class="alert alert-<?php echo $_SESSION["msg_type"]; ?> alert-dismissible fade show" role="alert">
                        <?php echo $_SESSION["flash_message"]; ?>
                    </div>
                <?php endif; ?>
                <?php
                unset($_SESSION['msg_type']);
                unset($_SESSION['flash_message']);
                ?>
                <div class="card border-0">
                    <div class="card-body text-center form-signin">
                        <h1 class="h3 mb-3 fw-normal">Enter your new Password</h1>
                        <form method="POST">
                            <div class="form-floating mb-3 input-group">
                                <input type="password" name="password" class="form-control" id="password" required>
                                <span class="input-group-text" onclick="togglePasswordVisibility()">
                                    <i class="bi bi-eye-slash" id="icon"></i>
                                </span>
                                <label for="password">Password</label>
                            </div>

                            <div class="form-floating mb-3 input-group">
                                <input type="password" name="password-confirm" class="form-control" id="password-confirm" required>
                                <span class="input-group-text" onclick="toggleConfirmPasswordVisibility()">
                                    <i class="bi bi-eye-slash" id="icon2"></i>
                                </span>
                                <label for="password-confirm">Confirm Password</label>
                            </div>
                            <button class="w-100 btn btn-lg btn-primary-brand btn-dark" type="submit" name="submit">Reset Password</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById("password");
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

        function toggleConfirmPasswordVisibility() {
            const passwordInput = document.getElementById("password-confirm");
            const icon = document.getElementById("icon2");

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
    </script>
</body>

</html>