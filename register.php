<?php
include '../config.php';
session_start();

if (isset($_POST['submit'])) {
    if (isset($_POST['username']) && isset($_POST['name']) && isset($_POST['password-confirm']) && isset($_POST['password'])) {
        $conn = mysqli_connect($host, $username, $password, $database);

        if ($_POST['password-confirm'] == $_POST['password']) {
            if ($conn) {
                $name = $_POST['name'];
                $username = $_POST['username'];
                $password = md5($_POST['password']);

                $sql = "INSERT INTO users (name, username, password, role) VALUES ('$name', '$username', '$password', 'student');";

                if (mysqli_query($conn, $sql)) {
                    $_SESSION['msg_type'] = 'success';
                    $_SESSION['flash_message'] = 'Account Created';

                    header("location: $rootURL/");
                } else {
                    echo "Registration Failed.";
                }
            } else {
                echo "Database connection failed.";
            }
        } else {
            echo "Password and Password Confirm must be the same.";
        }
    } else {
        echo "All fields are required";
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
</head>

<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-4">
                <div class="card">
                    <div class="card-body text-center form-signin">
                        <h1 class="h3 mb-3 fw-normal">Register User</h1>

                        <?php if (isset($_SESSION['msg_type']) && isset($_SESSION['flash_message'])) : ?>
                            <div class="alert alert-<?php echo $_SESSION["msg_type"]; ?> alert-dismissible fade show" role="alert">
                                <?php echo $_SESSION["flash_message"]; ?>
                            </div>
                        <?php endif; ?>
                        <?php
                        unset($_SESSION['msg_type']);
                        unset($_SESSION['flash_message']);
                        ?>
                        <form method="POST">
                            <div class="form-floating mb-3">
                                <input type="text" name="name" class="form-control" placeholder="Juan Dela Cruz" required>
                                <label for="floatingInput">Full Name</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="text" name="username" class="form-control" placeholder="JohnDoe27" required>
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
                            <button class="w-100 btn btn-lg btn-primary" type="submit" name="submit">Create Account</button>
                            <div class="mt-3">
                                Already have an account? <a href="index.php">Login Here.</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>