<?php
session_start();
include '../../config.php';

$courses = array("Electrical Engineering", "Electronics Engineering", "Mechanical Engineering", "Computer Engineering", "Mechatronics Engineering", "Instrumentation and Control Engineering");
$yearlevel = array(1 => "1st Year", 2 => "2nd Year", 3 => "3rd Year", 4 => "4th Year");
$sections = array("A", "B", "C", "D", "E", "F", "G");

include('../logout.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once "../components/header.php"; ?>
    <title>Student Profiling | Admin</title>
    <style>
        .nested-accordion .accordion-button {
            cursor: pointer;
        }
    </style>
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
                    <?php include_once "components/panel.php" ?>
                </div>
                <div class="col-8">
                    <div class="card shadow">
                        <div class="card-body">
                            <div class="display-6">Section Management</div>
                            <div class="accordion mt-5" id="primaryAccordion">
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="primaryHeading">
                                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#nestedAccordion">
                                            Courses
                                        </button>
                                    </h2>
                                    <div id="nestedAccordion" class="accordion-collapse collapse show" data-bs-parent="#primaryAccordion">
                                        <div class="accordion-body">
                                            <div class="accordion nested-accordion" id="nestedAccordionItems">
                                                <?php
                                                foreach ($courses as $i => $course) :
                                                ?>
                                                    <div class="accordion-item">
                                                        <h2 class="accordion-header" id="item1Heading">
                                                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#item<?= $i ?>Content">
                                                                <?= $course ?>
                                                            </button>
                                                        </h2>
                                                        <div id="item<?= $i ?>Content" class="accordion-collapse collapse" data-bs-parent="#nestedAccordionItems">
                                                            <div class="accordion-body">
                                                                <ul>
                                                                    <?php foreach ($yearlevel as $j => $level) : ?>
                                                                        <div class="accordion-item">
                                                                            <h2 class="accordion-header" id="item1Heading">
                                                                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#item<?= $j ?>Content">
                                                                                    <?= $course ?>
                                                                                </button>
                                                                            </h2>
                                                                            <div id="item<?= $j ?>Content" class="accordion-collapse collapse" data-bs-parent="#nestedAccordionItems">
                                                                                <div class="accordion-body">
                                                                                    <ul>
                                                                                        <?php foreach ($sections as $k => $section) : ?>
                                                                                            <li><?= $level ?> - <?= $section ?></li>
                                                                                        <?php endforeach ?>
                                                                                    </ul>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    <?php endforeach; ?>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </div>
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