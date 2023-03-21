<?php
session_start();
include '../../config.php';
include('../logout.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Intelli.fied | Student</title>
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
                    <div class="display-6">Hi <?= $_COOKIE['first_name']; ?> <?= $_COOKIE['last_name']; ?>, welcome to intelli.fied.</div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>