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
$setValues = false;

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    // Retrieve the selected values from the form
    $selectedCourse = $_GET["course"] ?? null;
    $selectedYearLevel = $_GET["yearlevel"] ?? null;
    $selectedSection = $_GET["section"] ?? null;

    if (isset($selectedCourse) && isset($selectedYearLevel) && isset($selectedSection)) {
        $setValues = true;
        $sql = "SELECT evaluation.*
        FROM evaluation
        JOIN users ON evaluation.student_id = users.id
        WHERE users.course = '$selectedCourse'
          AND users.year = '$selectedYearLevel'
          AND users.section = '$selectedSection'
          AND evaluation.validity = 1;";

        $eval_results = mysqli_query($conn, $sql);


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

        // AQ Computation
        $aq_updated_values = [];
        $dimensionsCount = ['Very Low' => 0, 'Low' => 0, 'Medium' => 0, 'High' => 0, 'Very High' => 0];

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
            $dimensionsCount[$score] += 1;
            $aq["score"] = $score;
            $aq_updated_values[] = $aq;
        }

        // Print the grouped values for testing
        // echo "AQ values: ";
        // print_r($aq_updated_values);
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


        // EQ Computation
        // Define the categorization ranges
        $strengthRange = [35, 50];
        $needsAttentionRange = [18, 34];
        $developmentPriorityRange = [10, 17];

        // Initialize an empty array to store the transformed data
        $transformedData = [];

        // Iterate through the EQ values array
        foreach ($eq_values as $values) {
            foreach ($values as $key => $value) {
                // Determine the category based on the value range
                if ($value >= $strengthRange[0] && $value <= $strengthRange[1]) {
                    $category = "Strength";
                } elseif ($value >= $needsAttentionRange[0] && $value <= $needsAttentionRange[1]) {
                    $category = "Needs Attention";
                } elseif ($value >= $developmentPriorityRange[0] && $value <= $developmentPriorityRange[1]) {
                    $category = "Development Priority";
                }


                // Create or update the data structure for the Emotional Quotient and category
                if (!isset($transformedData[$key])) {
                    $transformedData[$key] = [
                        "Strength" => 0,
                        "Needs Attention" => 0,
                        "Development Priority" => 0
                    ];
                }
                $transformedData[$key][$category]++;
            }
        }

        // Initialize variables to store the EQ and student counts for each category
        $strengthEQ = "";
        $strengthCount = 0;
        $needsAttentionEQ = "";
        $needsAttentionCount = 0;
        $developmentPriorityEQ = "";
        $developmentPriorityCount = 0;

        // Iterate through the arrays of EQ values
        foreach ($eq_values as $values) {
            $strengthFlag = false;
            $needsAttentionFlag = false;
            $developmentPriorityFlag = false;

            // Iterate through the Emotional Quotients and their values
            foreach ($values as $eq => $value) {
                // Determine the category based on the value range
                if ($value >= $strengthRange[0] && $value <= $strengthRange[1] && !$strengthFlag) {
                    $strengthEQ = $eq;
                    $strengthCount++;
                    $strengthFlag = true;
                } elseif ($value >= $needsAttentionRange[0] && $value <= $needsAttentionRange[1] && !$needsAttentionFlag) {
                    $needsAttentionEQ = $eq;
                    $needsAttentionCount++;
                    $needsAttentionFlag = true;
                } elseif ($value >= $developmentPriorityRange[0] && $value <= $developmentPriorityRange[1] && !$developmentPriorityFlag) {
                    $developmentPriorityEQ = $eq;
                    $developmentPriorityCount++;
                    $developmentPriorityFlag = true;
                }
            }
        }


        // IQ Computation
        // Define the IQ categories
        $iqCategories = [
            "Extremely Low",
            "Borderline",
            "Low Average",
            "Average",
            "High Average",
            "Superior",
            "Very Superior"
        ];

        // Initialize an empty array to store the IQ counts
        $iqCounts = array_fill_keys($iqCategories, 0);

        // Iterate through the $iq_values array
        foreach ($iq_values as $iq) {
            $category = $iq['category'];
            // Increment the count for the respective IQ category
            if (isset($iqCounts[$category])) {
                $iqCounts[$category]++;
            }
        }

        // MI Computations

        $intelligences = [
            "Bodily / Kinesthetic",
            "Existential",
            "Interpersonal",
            "Intrapersonal",
            "Logic",
            "Musical",
            "Naturalistic",
            "Verbal",
            "Visual"
        ];

        // Initialize arrays to store the strengths and weaknesses counts for each intelligence
        $strengths = [];
        $weaknesses = [];

        // Iterate through the Multiple Intelligences
        foreach ($intelligences as $intelligence) {
            $intelligenceStrengths = 0;
            $intelligenceWeaknesses = 0;

            // Iterate through the rank values array
            foreach ($rank_values as $rank) {
                $intelligenceRank = isset($rank[$intelligence]) ? $rank[$intelligence] : 0;
                $minRank = min($rank);
                $maxRank = max($rank);

                if ($intelligenceRank === $minRank) {
                    $intelligenceStrengths++;
                }

                if ($intelligenceRank === $maxRank) {
                    $intelligenceWeaknesses++;
                }
            }

            $strengths[] = $intelligenceStrengths;
            $weaknesses[] = $intelligenceWeaknesses;
        }

        // Prepare the data for the bar graph
        $mi_data = [];

        // Generate data for strengths
        $mi_data[] = [
            "label" => "Strengths",
            "data" => $strengths,
            "backgroundColor" => "rgba(68, 114, 196, 0.7)", // Adjust the color as needed
        ];

        // Generate data for weaknesses
        $mi_data[] = [
            "label" => "Weaknesses",
            "data" => $weaknesses,
            "backgroundColor" => "rgba(192, 0, 0, 0.7)", // Adjust the color as needed
        ];
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
                        <div class="col-12 mb-5">
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

                        <?php if ($setValues) : ?>
                            <div class="col-12 mb-4">
                                <p class="display-6 fw-bold">Adversity Quotient</p>
                            </div>
                            <div class="col-6">
                                <canvas id="aqChart"></canvas>
                            </div>
                            <div class="col-6">
                                <canvas id="aqChartDimensions"></canvas>
                            </div>
                            <?php
                            //AQ Evaluation computations
                            $total_aq_eval_count = count($aq_updated_values);
                            // Create an labeled array of average scores
                            $labelled_aq_averages = ["Control" => $avgControl, "Ownership" => $avgOwnership, "Reach" => $avgReach, "Endurance" => $avgEndurance];
                            arsort($labelled_aq_averages);

                            // Get the AQ dimension with the highest average score
                            $highestKey = key($labelled_aq_averages);
                            $highestValue = number_format(current($labelled_aq_averages), 2);
                            next($labelled_aq_averages);

                            // Get the AQ dimensions and their average scores
                            $otherDimensions = [];
                            while ($key = key($labelled_aq_averages)) {
                                $otherDimensions[$key] = number_format(current($labelled_aq_averages), 2);
                                next($labelled_aq_averages);
                            }

                            // Construct the paragraph dynamically
                            $paragraph = "<p>FOR THE SECOND GRAPH: The Adversity Quotient dimension that received the highest average score from the student(s) is $highestKey with $highestValue%, followed by";
                            foreach ($otherDimensions as $key => $value) {
                                $paragraph .= " $key with $value%";
                                if (next($otherDimensions)) {
                                    $paragraph .= " and";
                                }
                            }
                            $paragraph .= ", respectively.</p>";


                            $maxAverage = max($dimensionsCount);
                            $minAverage = min($dimensionsCount);

                            $keyMaxAverage = array_keys($dimensionsCount, $maxAverage)[0];
                            $keyMinAverage = array_keys($dimensionsCount, $minAverage)[0];
                            ?>
                            <div class="col-12 mt-4">
                                <p>
                                    FOR THE FIRST GRAPH: Among the <?= $total_aq_eval_count ?> total number of assessment takers from the class of <?= $yearlevel[$selectedYearLevel] ?> BS in <?= $courses[$selectedCourse] ?> section <?= $selectedSection ?>,
                                    majority of the class (<?= $dimensionsCount[$keyMaxAverage] ?> student(s)) received a <?= $keyMaxAverage ?> Adversity Quotient grade classification. On the contrary, the least number of student(s) (<?= $dimensionsCount[$keyMinAverage] ?> in total) received a <?= $keyMinAverage ?> Adversity Quotient grade classification.
                                </p>
                                <p><?= $paragraph ?></p>
                            </div>
                            <div class="col-12 my-4">
                                <p class="display-6 fw-bold">Emotional Quotient</p>
                            </div>
                            <div class="col-12 mt-4">
                                <canvas id="eqChart"></canvas>
                            </div>
                            <div class="col-12 mt-4">
                                <?php
                                // Generate the descriptive results template with the populated data
                                $eq_focus = [
                                    "Strength" => [],
                                    "Needs Attention" => [],
                                    "Development Priority" => [],
                                ];

                                foreach ($transformedData as $key => $eq) {

                                    foreach ($eq_focus as $fKey => $focus) {
                                        $eq_focus[$fKey][] = array($key => $eq[$fKey]);
                                    }
                                }

                                // Custom comparison function to sort the values in descending order
                                function sortByValueDesc($a, $b)
                                {
                                    $valueA = reset($a);
                                    $valueB = reset($b);
                                    return $valueB - $valueA;
                                }


                                // Sort the values in each category from largest to smallest
                                foreach ($eq_focus as &$category) {
                                    usort($category, 'sortByValueDesc');
                                }

                                // Define an array to store the maximum values and remaining counts for each category
                                $results = [];

                                // Iterate over each category (Strength, Needs Attention, Development Priority)
                                foreach ($eq_focus as $categoryName => &$category) {
                                    $maxValue = null;
                                    $remainingCount = 0;

                                    // Find the maximum value in the category and count the remaining records
                                    foreach ($category as $record) {
                                        $value = reset($record);
                                        if ($maxValue === null || $value > $maxValue) {
                                            $maxValue = $value;
                                            $remainingCount = 1;
                                        } elseif ($value === $maxValue) {
                                            $remainingCount++;
                                        }
                                    }

                                    // Remove records with values less than the maximum
                                    $category = array_filter($category, function ($record) use ($maxValue) {
                                        $value = reset($record);
                                        return $value === $maxValue;
                                    });

                                    // Store the maximum value and remaining count for the category
                                    $results[$categoryName] = [
                                        'maxValue' => $maxValue,
                                        'remainingCount' => $remainingCount
                                    ];
                                }

                                // Output the updated array, maximum values, and remaining counts
                                $result = [
                                    'eq_focus' => $eq_focus,
                                    'results' => $results
                                ];

                                function concatenateKeys($category)
                                {
                                    $count = count($category);
                                    $keys = array_map(function ($record) {
                                        return key($record);
                                    }, $category);

                                    if ($count > 1) {
                                        $keys[$count - 1] = 'and ' . $keys[$count - 1];
                                    }

                                    return implode(', ', $keys);
                                }

                                $eq_description = "In the $yearlevel[$selectedYearLevel] class of BS in $courses[$selectedCourse] section $selectedSection, the competencies in which more students think it is their strength, or one that needs attention, or a development priority are as follows:<br/><br/>
                                Strength = " . ($result['results']['Strength']['maxValue'] > 0 ? concatenateKeys($result['eq_focus']['Strength']) . " with " . $strengthCount . " student(s)" : "No results") . "<br/>
                                Needs Attention = " . ($result['results']['Needs Attention']['maxValue'] > 0 ? concatenateKeys($result['eq_focus']['Needs Attention']) . " with " . $needsAttentionCount . " student(s)" : "No results") . "<br/>
                                Development Priority = " . ($result['results']['Development Priority']['maxValue'] > 0 ? concatenateKeys($result['eq_focus']['Development Priority']) . " with " . $developmentPriorityCount . " student(s)" : "No results");
                                ?>
                                <p><?= $eq_description ?></p>
                            </div>
                            <div class="col-12 my-4">
                                <p class="display-6 fw-bold">Intelligence Quotient</p>
                            </div>
                            <div class="col-12 mt-4">
                                <canvas id="iqChart"></canvas>
                            </div>
                            <div class="col-12 mt-4">
                                <?php
                                // Calculate the total number of students
                                $totalStudents = array_sum($iqCounts);
                                $sortedIqCounts = $iqCounts;
                                arsort($sortedIqCounts);

                                // Generate the evaluation text based on the IQ result counts
                                $evaluationText = "As shown in the graph, most student(s) ($totalStudents in total) from $yearlevel[$selectedYearLevel] BS $courses[$selectedCourse] section $selectedSection have a/an ";

                                // Get the IQ category with the highest count
                                $maxCount = max($sortedIqCounts);
                                $maxIndex = array_search($maxCount, $sortedIqCounts);

                                $evaluationText .= $maxIndex . " IQ score. This is followed by ";

                                // // Generate the remaining parts of the evaluation text
                                $remainingText = "";
                                foreach ($sortedIqCounts as $index => $count) {
                                    $category = $index;
                                    $remainingText .= $category . " IQ scores with $count student(s), ";
                                    if ($index < count($sortedIqCounts) - 1) {
                                        $remainingText .= " and ";
                                    }
                                }

                                $evaluationText .= $remainingText;

                                echo $evaluationText;
                                ?>
                            </div>
                            <div class="col-12 my-4">
                                <p class="display-6 fw-bold">Multiple Intelligence</p>
                            </div>
                            <div class="col-12 mt-4">
                                <canvas id="rankChart"></canvas>
                            </div>
                            <div class="col-12 mt-4">
                                <p>
                                    <?php


                                    // Initialize arrays to store the strengths and weaknesses counts for each intelligence
                                    $strengths = [];
                                    $weaknesses = [];

                                    // Iterate through the Multiple Intelligences
                                    foreach ($intelligences as $intelligence) {
                                        $intelligenceStrengths = 0;
                                        $intelligenceWeaknesses = 0;

                                        // Iterate through the rank values array
                                        foreach ($rank_values as $rank) {
                                            $intelligenceRank = isset($rank[$intelligence]) ? $rank[$intelligence] : 0;
                                            $minRank = min($rank);
                                            $maxRank = max($rank);

                                            if ($intelligenceRank === $minRank) {
                                                $intelligenceStrengths++;
                                            }

                                            if ($intelligenceRank === $maxRank) {
                                                $intelligenceWeaknesses++;
                                            }
                                        }

                                        $strengths[$intelligence] = $intelligenceStrengths;
                                        $weaknesses[$intelligence] = $intelligenceWeaknesses;
                                    }

                                    // Sort the strengths and weaknesses array in descending order
                                    arsort($strengths);
                                    arsort($weaknesses);

                                    // Get the top three strengths and weaknesses intelligences
                                    $topStrengths = array_slice(array_keys($strengths), 0, 3);
                                    $topWeaknesses = array_slice(array_keys($weaknesses), 0, 3);

                                    // Generate the text evaluation
                                    $evaluation = "The graph shows that in the $yearlevel[$selectedYearLevel] class of $courses[$selectedCourse] section $selectedSection, the top three intelligences of MI theory with the highest number of student(s) that identified them as their strengths are as follows: " . implode(", ", $topStrengths) . ".\nOn the other hand, the top three intelligences of MI theory with the highest number of student(s) that identified them as their weaknesses are as follows: " . implode(", ", $topWeaknesses) . ".";

                                    // Output the evaluation
                                    echo $evaluation;
                                    ?>
                                </p>
                            </div>
                    </div>
                <?php endif; ?>
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
                                    max: 100,
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
                    // Access the transformed data
                    var transformedData = <?php echo json_encode($transformedData); ?>;

                    // Define the colors for each category
                    var colors = {
                        "Strength": "rgba(68, 114, 196, 0.7)",
                        "Needs Attention": "rgba(237, 125, 49, 0.7)",
                        "Development Priority": "rgba(165, 165, 165, 0.7)"
                    };

                    // Extract the Emotional Quotients and categories for the chart
                    var eqLabels = Object.keys(transformedData);
                    var categories = Object.keys(transformedData[eqLabels[0]]);

                    // Create an array to store dataset objects
                    var datasets = [];

                    // Iterate through the categories to create datasets
                    categories.forEach(function(category) {
                        var data = [];

                        // Iterate through the Emotional Quotients and retrieve the count for the category
                        eqLabels.forEach(function(eqLabel) {
                            data.push(transformedData[eqLabel][category]);
                        });

                        // Create the dataset object
                        var dataset = {
                            label: category,
                            data: data,
                            backgroundColor: colors[category]
                        };

                        // Add the dataset to the array
                        datasets.push(dataset);
                    });

                    // Create the chart using Chart.js
                    var eqChartCtx = document.getElementById('eqChart').getContext('2d');
                    new Chart(eqChartCtx, {
                        type: 'bar',
                        data: {
                            labels: eqLabels,
                            datasets: datasets
                        },
                        options: {
                            scales: {
                                x: {
                                    stacked: false // Disable stacking on the x-axis
                                },
                                y: {
                                    stacked: false, // Disable stacking on the y-axis
                                    beginAtZero: true,
                                    ticks: {
                                        precision: 0
                                    }
                                }
                            }
                        }
                    });

                    var iqCounts = <?php echo json_encode($iqCounts); ?>;

                    // Extract the IQ categories and counts
                    var iqCategories = Object.keys(iqCounts);
                    var iqCountsData = Object.values(iqCounts);

                    // Create the chart using Chart.js
                    var iqCtx = document.getElementById('iqChart').getContext('2d');
                    new Chart(iqCtx, {
                        type: 'bar',
                        data: {
                            labels: iqCategories,
                            datasets: [{
                                label: 'IQ Result Counts',
                                data: iqCountsData,
                                backgroundColor: 'rgba(68, 114, 196, 0.7)' // Adjust the color as needed
                            }]
                        },
                        options: {
                            scales: {
                                x: {
                                    beginAtZero: true
                                },
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        precision: 0
                                    }
                                }
                            }
                        }
                    });

                    var intelligences = <?php echo json_encode($intelligences); ?>;
                    var data = <?php echo json_encode($mi_data); ?>;

                    var MiCTX = document.getElementById('rankChart').getContext('2d');
                    new Chart(MiCTX, {
                        type: 'bar',
                        data: {
                            labels: intelligences,
                            datasets: data
                        },
                        options: {
                            indexAxis: 'x',
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                },
                            },
                            scales: {
                                x: {
                                    beginAtZero: true,
                                },
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        precision: 0
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