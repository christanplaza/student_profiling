<?php
include '../config.php';
session_start();

if (isset($_POST['submit'])) {
    if (isset($_POST['username'])) {
        $conn = mysqli_connect($host, $username, $password, $database);

        if ($conn) {
            $username = $_POST['username'];

            $sql = "SELECT * FROM users WHERE username = '$username'";

            $res = mysqli_query($conn, $sql);

            if (mysqli_num_rows($res) != 0) {
                $user = mysqli_fetch_assoc($res);
                $user_id = $user['id'];
                $reset_key = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 6);

                // Next, prepare and execute the SQL update statement
                $query = "UPDATE users SET reset_password = true, reset_key = '" . $reset_key . "' WHERE id = '" . $user_id . "'";
                $result = mysqli_query($conn, $query);

                if (!$result) {
                    $_SESSION['msg_type'] = 'danger';
                    $_SESSION['flash_message'] = 'Something went wrong';
                } else {
                    $_SESSION['msg_type'] = 'success';
                    $_SESSION['flash_message'] = 'Password Reset Request has been sent. Contact a faculty member to get your reset key';
                }
            } else {
                $_SESSION['msg_type'] = 'danger';
                $_SESSION['flash_message'] = 'Incorrect Username';
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
                        <h1 class="h3 mb-3 fw-normal">Request for Password Reset</h1>
                        <form method="POST">
                            <div class="form-floating mb-3">
                                <input type="text" name="username" class="form-control" required>
                                <label for="floatingInput">Username / TUPV ID</label>
                            </div>
                            <button class="w-100 btn btn-lg btn-primary-brand btn-dark" type="submit" name="submit">Submit Request</button>
                            <a href="reset_password.php" class="w-100 btn btn-md btn-success btn-outline mt-2" type="submit" name="submit">I already have my Reset Key</a>
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