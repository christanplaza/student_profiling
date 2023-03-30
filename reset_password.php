<?php
include '../config.php';
session_start();

if (isset($_POST['submit'])) {
    if (isset($_POST['reset_key'])) {
        $conn = mysqli_connect($host, $username, $password, $database);

        if ($conn) {
            $reset_key = $_POST['reset_key'];

            $sql = "SELECT * FROM users WHERE reset_key = '$reset_key'";

            $res = mysqli_query($conn, $sql);

            if (mysqli_num_rows($res) != 0) {
                $user = mysqli_fetch_assoc($res);
                $user_id = $user['id'];

                // Next, prepare and execute the SQL update statement
                $query = "UPDATE users SET password = NULL WHERE id = '" . $user_id . "'";
                $result = mysqli_query($conn, $query);

                if (!$result) {
                    $_SESSION['msg_type'] = 'danger';
                    $_SESSION['flash_message'] = 'Something went wrong';
                } else {
                    $_SESSION['user_id'] = $user_id;
                    $_SESSION['reset_key'] = $reset_key;
                    header("location: password_input.php");
                    session_write_close();
                }
            } else {
                $_SESSION['msg_type'] = 'danger';
                $_SESSION['flash_message'] = 'Invalid Reset Key';
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
                        <h1 class="h3 mb-3 fw-normal">Enter your password reset key</h1>
                        <form method="POST">
                            <div class="form-floating mb-3">
                                <input type="text" name="reset_key" class="form-control" required>
                                <label for="floatingInput">Reset Key</label>
                            </div>
                            <button class="w-100 btn btn-lg btn-primary-brand btn-dark" type="submit" name="submit">Reset Password</button>
                            <div class="mt-3">
                                Remember your password? <a href="index.php">Back to Login</a>
                            </div>
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
    </script>
</body>

</html>