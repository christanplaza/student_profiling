<?php
session_start();
include '../../config.php';
$letters = ["A", "B", "C", "D", "E", "F", "G"];

$conn = mysqli_connect($host, $username, $password, $database);
if (isset($_COOKIE['id'])) {
    if ($conn) {
        $id = $_COOKIE['id'];
        $sql = "SELECT * FROM users WHERE id = '$id' AND role = 'faculty'";

        $user_res = mysqli_query($conn, $sql);
        $user = mysqli_fetch_assoc($user_res);


        if (isset($_POST['submit'])) {
            $first_name = $_POST['first_name'];
            $last_name = $_POST['last_name'];
            $username = $_POST['username'];

            if (isset($_POST['password']) && isset($_POST['confirm_password']) && strlen($_POST['password']) > 0 && strlen($_POST['confirm_password']) > 0) {
                $password = $_POST['password'];
                $confirm_password = $_POST['confirm_password'];

                if ($password == $confirm_password) {
                    $hashed = md5($password);
                    $sql = "UPDATE users SET first_name = '$first_name', last_name = '$last_name', username = '$username', password = '$hashed'  WHERE id = '$id'";
                    if (mysqli_query($conn, $sql)) {
                        $_SESSION['msg_type'] = 'success';
                        $_SESSION['flash_message'] = 'Account Updated';
                        header("location: $rootURL/faculty/account_information.php");
                        exit();
                    } else {
                        header("location: $rootURL/faculty/account_information.php");
                    }
                } else {
                    $_SESSION['msg_type'] = 'danger';
                    $_SESSION['flash_message'] = 'Password and Confirm Password does not match';
                }
            } else {
                $sql = "UPDATE users SET first_name = '$first_name', last_name = '$last_name', username = '$username' WHERE id = '$id'";
                if (mysqli_query($conn, $sql)) {
                    $_SESSION['msg_type'] = 'success';
                    $_SESSION['flash_message'] = 'Account Updated';
                    header("location: $rootURL/faculty/account_information.php");
                    exit();
                } else {
                    header("location: $rootURL/faculty/account_information.php");
                }
            }
        }
    } else {
        echo "Couldn't connect to database.";
    }
} else {
    header("location: $rootURL/student/");
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
                    <div class="display-6 mb-4">Account Information</div>
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
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
                                    <form method="POST">
                                        <div class="card-header bg-secondary-subtle">
                                            <div class="row">
                                                <div class="col-12">
                                                    Faculty Profile
                                                    <button type="submit" class="btn btn-success btn-sm float-end" name="submit">Save Changes</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-12">
                                                    <table class="table">
                                                        <tr>
                                                            <td>First Name</td>
                                                            <td>
                                                                <input type="text" name="first_name" value="<?= $user['first_name']; ?>" class="form-control" required>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>Last Name</td>
                                                            <td>
                                                                <input type="text" name="last_name" value="<?= $user['last_name']; ?>" class="form-control" required>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>TUPV ID</td>
                                                            <td>
                                                                <input type="text" name="username" value="<?= $user['username']; ?>" class="form-control" required>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="2"><b>You may leave password blank if you don't want to change password.</b></td>
                                                        </tr>
                                                        <tr>
                                                            <td>Password</td>
                                                            <td>
                                                                <input type="password" name="password" class="form-control">
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>Confirm Password <br>(required if you input new password)</td>
                                                            <td>
                                                                <input type="password" name="confirm_password" class="form-control">
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
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