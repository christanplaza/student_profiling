<?php
session_start();
include '../../config.php';
$conn = mysqli_connect($host, $username, $password, $database);

$courses = array(
    "electronics" => "Electronics Engineering",
    "mechanical" => "Mechanical Engineering",
    "computer" => "Computer Engineering",
    "electrical" => "Electrical Engineering",
    "mechatronics" => "Mechatronics Engineering",
    "instrumentation_control" => "Instrumentation and Control Engineering"
);
$yearlevel = array(1 => "1st Year", 2 => "2nd Year", 3 => "3rd Year", 4 => "4th Year");
$sections = array("A", "B", "C", "D", "E", "F", "G");

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    // Retrieve the selected values from the form
    $selectedCourse = $_GET["course"] ?? null;
    $selectedYearLevel = $_GET["yearlevel"] ?? null;
    $selectedSection = $_GET["section"] ?? null;

    if (isset($selectedCourse) && isset($selectedYearLevel) && isset($selectedSection)) {
        $sql = "SELECT evaluation.*
        FROM evaluation
        JOIN users ON evaluation.student_id = users.id
        WHERE users.course = '$selectedCourse'
          AND users.year = '$selectedYearLevel'
          AND users.section = '$selectedSection'
          AND evaluation.validity = 1;";

        $eval_results = mysqli_query($conn, $sql);

        echo ("Evaluations gathered: " . mysqli_num_rows($eval_results) . "\n");

        $evaluations = [];
        while ($row = $eval_results->fetch_assoc()) {
            $evaluations[] = $row;
        }

        $aq_values = [];
        $rank_values = [];
        $eq_values = [];
        $iq_values = [];

        foreach ($evaluations as $evaluation) {
            $evaluation_json = $evaluation['evaluation_json'];
            $json_data = json_decode($evaluation_json, true);
            $json_type = $json_data['type'];
            $json_value = $json_data['value'];

            if ($json_type === 'aq') {
                $aq_values[] = $json_value;
            } elseif ($json_type === 'rank') {
                $rank_values[] = $json_value;
            } elseif ($json_type === 'eq') {
                $eq_values[] = $json_value;
            } elseif ($json_type === 'iq') {
                $iq_values[] = $json_value;
            }
        }

        $aq_updated_values = [];

        foreach ($aq_values as $aq) {
            $aq["c"] = ($aq["Control"] / 100) * 25;
            $aq["o"] = ($aq["Ownership"] / 100) * 40;
            $aq["r"] = ($aq["Reach"] / 100) * 25;
            $aq["e"] = ($aq["Endurance"] / 100) * 10;
            $aq["ARP"] = ($aq["c"] + $aq["o"] + $aq["r"] + $aq["e"]) * 2;
            $score = "";
            if ($aq["ARP"] <= 59) {
                $score = "Very Low";
            } else if ($aq["ARP"] >= 60 && $aq["ARP"] <= 94) {
                $score = "Low";
            } else if ($aq["ARP"] >= 95 && $aq["ARP"] <= 134) {
                $score = "Medium";
            } else if ($aq["ARP"] >= 135 && $aq["ARP"] <= 165) {
                $score = "High";
            } else if ($aq["ARP"] >= 166 && $aq["ARP"] <= 200) {
                $score = "Very High";
            }
            $aq["score"] = $score;
            $aq_updated_values[] = $aq;
        }

        // Print the grouped values for testing
        echo "AQ values: ";
        print_r($aq_updated_values);
        // echo "Rank values: ";
        // print_r($rank_values);
        // echo "EQ values: ";
        // print_r($eq_values);
        // echo "IQ values: ";
        // print_r($iq_values);

        // Assuming you have the $aq_updated_values array

        // Define the labels for the horizontal axis
        $labels = ['Very Low', 'Low', 'Medium', 'High', 'Very High'];

        // Initialize an array to store the count of records for each label
        $data = [0, 0, 0, 0, 0];

        // Iterate through the $aq_updated_values array and count the records for each label
        foreach ($aq_updated_values as $aq_value) {
            $score = $aq_value['score'];
            $index = array_search($score, $labels);
            if ($index !== false) {
                $data[$index]++;
            }
        }

        $sumControl = 0;
        $sumOwnership = 0;
        $sumReach = 0;
        $sumEndurance = 0;
        $count = count($aq_updated_values);

        // Iterate through the $aq_updated_values array and sum up the AQ dimension values
        foreach ($aq_updated_values as $aq_value) {
            $sumControl += $aq_value['Control'];
            $sumOwnership += $aq_value['Ownership'];
            $sumReach += $aq_value['Reach'];
            $sumEndurance += $aq_value['Endurance'];
        }

        // Calculate the average of each AQ dimension
        $avgControl = $sumControl / $count;
        $avgOwnership = $sumOwnership / $count;
        $avgReach = $sumReach / $count;
        $avgEndurance = $sumEndurance / $count;


        // Create an array of AQ dimensions
        $dimensions = ['Control', 'Ownership', 'Reach', 'Endurance'];

        // Create an array of average scores
        $averages = [$avgControl, $avgOwnership, $avgReach, $avgEndurance];
    }
}

include('../logout.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Intelli.fied | Admin Dashboard</title>
    <?php include_once "../components/header.php"; ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <div class="container-fluid">
        <div class="row flex-nowrap">
            <div class="col-auto col-md-3 col-xl-2 px-sm-2 px-0 bg-dark shadow">
                <?php include_once "components/new_panel.php" ?>
            </div>

            <div class="col">
                <div class="mt-4">
                    <h1>Section Management</h1>
                    <div class="row">
                        <div class="col-12">
                            <form method="get" action="<?php echo $_SERVER["PHP_SELF"]; ?>" class="row g-3">
                                <div class="col-md-4">
                                    <label for="course" class="form-label">Course:</label>
                                    <select name="course" id="course" class="form-select" required>
                                        <option value="">-- Select Course --</option>
                                        <?php foreach ($courses as $key => $course) : ?>
                                            <option value="<?php echo $key; ?>" <?php echo $selectedCourse == $key ? "selected" : ""; ?>><?php echo $course; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label for="yearlevel" class="form-label">Year Level:</label>
                                    <select name="yearlevel" id="yearlevel" class="form-select" required>
                                        <option value="">-- Select Year Level --</option>
                                        <?php foreach ($yearlevel as $key => $level) : ?>
                                            <option value="<?php echo $key; ?>" <?php echo $selectedYearLevel == $key ? "selected" : ""; ?>><?php echo $level; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label for="section" class="form-label">Section:</label>
                                    <select name="section" id="section" class="form-select" required>
                                        <option value="">-- Select Section --</option>
                                        <?php foreach ($sections as $section) : ?>
                                            <option value="<?php echo $section; ?>" <?php echo $selectedSection == $section ? "selected" : ""; ?>><?php echo $section; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-primary">Search</button>
                                </div>
                            </form>
                        </div>

                        <div class="col-6">
                            <canvas id="aqChart"></canvas>
                        </div>
                        <div class="col-6">
                            <canvas id="aqChartDimensions"></canvas>
                        </div>
                        <div class="col-12">
                            <p>Test</p>
                        </div>
                    </div>
                    <script>
                        // Access the prepared data from PHP
                        var labels = <?php echo json_encode($labels); ?>;
                        var data = <?php echo json_encode($data); ?>;

                        var dimensions = <?php echo json_encode($dimensions); ?>;
                        var percentageAverages = <?php echo json_encode($averages); ?>;

                        // Create the chart using Chart.js
                        var ctx = document.getElementById('aqChart').getContext('2d');
                        new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: labels,
                                datasets: [{
                                    label: 'Number of Records',
                                    data: data,
                                    backgroundColor: 'rgba(54, 162, 235, 0.7)', // Customize the bar color
                                }]
                            },
                            options: {
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        ticks: {
                                            precision: 0
                                        }
                                    }
                                },
                                plugins: {
                                    datalabels: {
                                        anchor: 'end',
                                        align: 'end',
                                        font: {
                                            weight: 'bold'
                                        },
                                        color: '#333',
                                        formatter: function(value) {
                                            return value > 0 ? value : '';
                                        }
                                    }
                                }
                            }
                        });

                        var aqChart2ctx = document.getElementById('aqChartDimensions').getContext('2d');
                        new Chart(aqChart2ctx, {
                            type: 'bar',
                            data: {
                                labels: dimensions,
                                datasets: [{
                                    label: 'Average Score of the Section (in %)',
                                    data: percentageAverages,
                                    backgroundColor: 'rgba(54, 162, 235, 0.7)', // Customize the bar color
                                }]
                            },
                            options: {
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        ticks: {
                                            precision: 0
                                        }
                                    },
                                    x: {
                                        grid: {
                                            display: false
                                        }
                                    }
                                }
                            }
                        });
                    </script>
                </div>
            </div>
        </div>
    </div>
</body>

</html>