<?php
session_start();
include '../../../config.php';
if (isset($_POST['submit'])) {
    if (isset($_POST['username']) && isset($_POST['name']) && isset($_POST['password-confirm']) && isset($_POST['password'])) {
        $conn = mysqli_connect($host, $username, $password, $database);

        if ($_POST['password-confirm'] == $_POST['password']) {
            if ($conn) {
                $name = $_POST['name'];
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
    <?php include_once "../../components/header.php"; ?>
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
                    <?php include_once "../components/panel.php" ?>
                </div>
                <div class="col-8">
                    <?php if (isset($_SESSION['msg_type']) && isset($_SESSION['flash_message'])) : ?>
                        <div class="alert alert-<?php echo $_SESSION["msg_type"]; ?> alert-dismissible fade show" role="alert">
                            <?php echo $_SESSION["flash_message"]; ?>
                        </div>
                    <?php endif; ?>
                    <?php
                    unset($_SESSION['msg_type']);
                    unset($_SESSION['flash_message']);
                    ?>
                    <div class="card shadow">
                        <div class="card-body">
                            <div class="display-6">Add Faculty</div>
                            <div class="row">
                                <div class="col-12 mt-4">
                                    <form method="POST">
                                        <div class="form-floating mb-3">
                                            <input type="text" name="name" class="form-control" placeholder="Juan Dela Cruz" value="<?= isset($_POST['name']) ? $_POST['name'] : ""; ?>" required>
                                            <label for="floatingInput">Full Name</label>
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
        </div>
    </div>
</body>

</html>