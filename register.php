<?php
include '../config.php';
session_start();
$letters = ["A", "B", "C", "D", "E", "F", "G"];

if (isset($_POST['submit'])) {
    if (isset($_POST['username']) && isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['password-confirm']) && isset($_POST['password'])) {
        $conn = mysqli_connect($host, $username, $password, $database);

        if ($_POST['password-confirm'] == $_POST['password']) {
            if ($conn) {
                $first_name = $_POST['first_name'];
                $last_name = $_POST['last_name'];
                $username = $_POST['username'];
                $course = $_POST['course'];
                $year = $_POST['year'];
                $section = $_POST['section'];
                $password = md5($_POST['password']);

                $sql = "SELECT * FROM users WHERE username = '$username'";
                $username_res = mysqli_query($conn, $sql);

                if (mysqli_num_rows($username_res) > 0) {
                    $_SESSION['msg_type'] = 'danger';
                    $_SESSION['flash_message'] = 'Username already exists';
                } else {
                    $sql = "INSERT INTO users (first_name, last_name, username, password, role, course, year, section) VALUES ('$first_name', '$last_name', '$username', '$password', 'student', '$course', '$year', '$section');";

                    if (mysqli_query($conn, $sql)) {
                        $_SESSION['msg_type'] = 'success';
                        $_SESSION['flash_message'] = 'Account Created';

                        header("location: $rootURL/");
                        exit();
                    } else {
                        echo "Registration Failed.";
                    }
                }
            } else {
                echo "Database connection failed.";
            }
        } else {
            $_SESSION['msg_type'] = 'danger';
            $_SESSION['flash_message'] = 'Password and Confirm Password do not match.';
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
    <title>Student Profiling | Register</title>
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
                <div class="card border-0">
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
                                <input type="text" name="first_name" class="form-control" placeholder="Juan" required>
                                <label for="floatingInput">First Name</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="text" name="last_name" class="form-control" placeholder="Dela Cruz" required>
                                <label for="floatingInput">Last Name</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="text" name="username" class="form-control" placeholder="JohnDoe27" required>
                                <label for="floatingInput">TUPV ID (e.g., TUPV-XX-XXXX)</label>
                            </div>
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
                            <div class="form-floating mb-3">
                                <select name="course" class="form-select" required>
                                    <option label="Select your Course"></option>
                                    <option value="electronics">Electronics Engineering</option>
                                    <option value="mechanical">Mechanical Engineering</option>
                                    <option value="computer">Computer Engineering</option>
                                    <option value="electrical">Electrical Engineering</option>
                                    <option value="mechatronics">Mechatronics Engineering</option>
                                    <option value="instrumentation_control">Instrumentation and Control Engineering</option>
                                </select>
                                <label for="floatingInput">Course</label>
                            </div>
                            <div class="form-floating mb-3">
                                <select name="year" class="form-select" required>
                                    <option label="Select your Year Level"></option>
                                    <option value="1">1st Year</option>
                                    <option value="2">2nd Year</option>
                                    <option value="3">3rd Year</option>
                                    <option value="4">4th Year</option>
                                </select>
                                <label for="floatingInput">Year Level</label>
                            </div>
                            <div class="form-floating mb-3">
                                <select name="section" class="form-select" required>
                                    <option label="Select your Section"></option>
                                    <?php for ($i = 0; $i < 7; $i++) : ?>
                                        <option value="<?= $letters[$i]; ?>"><?= $letters[$i]; ?></option>
                                    <?php endfor; ?>
                                </select>
                                <label for="floatingInput">Section</label>
                            </div>
                            <button class="w-100 btn btn-lg btn-primary-brand btn-dark" type="submit" name="submit">Create Account</button>
                            <div class="mt-3">
                                Already have an account? <a href="index.php">Login Here.</a>
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