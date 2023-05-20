<?php
session_start();
include '../../../config.php';
$conn = mysqli_connect($host, $username, $password, $database);
$letters = ["A", "B", "C", "D", "E", "F", "G", "H"];

if ($conn) {
    $questionnaire_id = $_GET['questionnaire'];
    $evaluation_id = $_GET['eval'];

    $sql = "SELECT * FROM questionnaire WHERE id = '$questionnaire_id'";
    $questionnaire_res = mysqli_query($conn, $sql);

    if ($questionnaire_res) {
        $questionnaire = $questionnaire_res->fetch_assoc();

        $sql = "SELECT * FROM question_group WHERE questionnaire_id = '$questionnaire_id'";
        $question_group_res = mysqli_query($conn, $sql);

        if ($questionnaire['question_type'] == "range" && $question_group_res) {
            $question_group = $question_group_res->fetch_assoc();
            $question_group_id = $question_group['id'];

            $sql = "SELECT COUNT(*) as total from questions WHERE question_group_id = '$question_group_id'";
            $result = mysqli_query($conn, $sql);
            $row = mysqli_fetch_assoc($result);
            $total = $row['total'];

            // Define number of questions per page
            $per_page = 5;

            $total_pages = ceil($total / $per_page);

            if (isset($_GET['page'])) {
                $current_page = $_GET['page'];
                if (isset($_SESSION['current_page'])) {
                    if ($current_page < $_SESSION['current_page']) {
                        $prev = $_SESSION['current_page'];
                        $_SESSION['msg_type'] = 'danger';
                        $_SESSION['flash_message'] = 'Going back to previous page is not allowed.';
                        header("Location: $rootURL/student/examination/index.php?questionnaire=$questionnaire_id&eval=$evaluation_id&page=$prev");
                        session_write_close();
                    }
                }
            } else {
                $current_page = 1;
            }

            $_SESSION['current_page'] = $current_page;

            // Calculate offset
            $offset = ($current_page - 1) * $per_page;

            // Get questions for current page
            $sql = "SELECT * FROM questions WHERE question_group_id = '$question_group_id' LIMIT $offset, $per_page";
            $questions_res = mysqli_query($conn, $sql);
        }

        if ($questionnaire['question_type'] == "choices" && $question_group_res) {
            $question_group = $question_group_res->fetch_assoc();
            $question_group_id = $question_group['id'];

            $sql = "SELECT * FROM questions WHERE question_group_id = '$question_group_id'";
            $questions_res = mysqli_query($conn, $sql);
        }

        if ($questionnaire['question_type'] == "rank" && $question_group_res) {
            $allQuestions = array();

            if (isset($_GET['page'])) {
                $current_page = $_GET['page'];
                if (isset($_SESSION['current_page'])) {
                    if ($current_page < $_SESSION['current_page']) {
                        $prev = $_SESSION['current_page'];
                        $_SESSION['msg_type'] = 'danger';
                        $_SESSION['flash_message'] = 'Going back to previous page is not allowed.';
                        header("Location: $rootURL/student/examination/index.php?questionnaire=$questionnaire_id&eval=$evaluation_id&page=$prev");
                        session_write_close();
                    }
                }
            } else {
                $current_page = 1;
            }

            $_SESSION['current_page'] = $current_page;

            while ($question_group = $question_group_res->fetch_assoc()) {
                $question_group_id = $question_group['id'];

                if ($question_group['count'] == $current_page) {
                    $sql = "SELECT * FROM questions WHERE question_group_id = '$question_group_id'";
                    $questions_res = mysqli_query($conn, $sql);
                    while ($question = $questions_res->fetch_assoc()) {
                        $question['group'] = $question_group['count'];
                        array_push($allQuestions, $question);
                    }
                }
            }
        }
    }

    if (isset($_POST['submit'])) {
        if ($_POST['type'] == "choices") {
            // Compile all answers
            $questionCount = mysqli_num_rows($questions_res);

            $totalScore = 0;
            $user_answers = array();

            for ($i = 1; $i <= $questionCount; $i++) {
                array_push($user_answers, $_POST["question" . $i . "_answer"]);
            }

            $index = 0;
            while ($question = $questions_res->fetch_assoc()) {
                if ($question['correct_answer'] == $user_answers[$index]) {
                    $totalScore++;
                }

                $index++;
            }

            $category_result = "";

            // Define the score ranges and raw score ranges as arrays
            $scoreRanges = array(
                "130 – above",
                "120 – 129",
                "110 – 119",
                "90 – 109",
                "80 – 89",
                "70 – 79",
                "69 – below"
            );

            $rawScoreRanges = array(
                array(21, 25),
                array(19, 20),
                array(16, 18),
                array(10, 15),
                array(8, 9),
                array(5, 7),
                array(4, -1)
            );

            $category = array(
                "Very Superior",
                "Superior",
                "High Average",
                "Average",
                "Low Average",
                "Borderline",
                "Extremely Low"
            );

            // Loop through the raw score ranges and check if the total score falls within any of them
            for ($i = 0; $i < count($rawScoreRanges); $i++) {
                $rawScoreRange = $rawScoreRanges[$i];
                if ($totalScore >= $rawScoreRange[0] && ($totalScore <= $rawScoreRange[1] || $rawScoreRange[1] == -1)) {
                    // If the total score falls within the raw score range, assign the corresponding category and score range to variables
                    $category_result = $category[$i];
                    $iq = $scoreRanges[$i];
                    break;
                }
            }

            // Output the result and IQ variables
            echo "Result: " . $category_result . "<br>";
            echo "IQ: " . $iq;

            $iqCategories = require 'iq_categories.php';

            $advantages = $iqCategories[$category_result]["Advantages"];
            $disadvantages = $iqCategories[$category_result]["Disadvantages"];

            $result = "The student''s Intelligence Quotient score is $iq which corresponds to (a/an) $category_result IQ \n\n";
            $result .= "The advantages and disadvantages of having this IQ score are as follows: \n\n";
            $result .= "Advantages of having (a/an) $category_result IQ may include: \n";
            $result .= $advantages . " \n\n";
            $result .= "Disadvantages of having (a/an) $category_result IQ may include: \n";
            $result .= $disadvantages . " \n\n";

            $data = array(
                'type' => 'iq',
                'value' => array(
                    'category' => $category_result,
                    'iq' => $iq
                )
            );

            $evaluation_json = json_encode($data);

            $sql = "UPDATE evaluation SET is_complete = '1', validity = '1', evaluation_result = '$result', evaluation_json = '$evaluation_json' WHERE id = '$evaluation_id'";
            if (mysqli_query($conn, $sql)) {
                header("location: $rootURL/student/result.php?id=$evaluation_id");
            } else {
                echo $conn->error;
            }
        } else if ($_POST['type'] == "rank") {
            if ($_POST['progress'] == "append") {
                if (isset($_SESSION['responses'])) {
                    $responses = $_SESSION['responses'];
                } else {
                    $responses = array();
                }
                $answers = array();

                $count_start = $_POST['count_start'];
                $count_end = $_POST['count_end'];

                for ($i = $count_start; $i <= $count_end; $i++) {
                    $answers["question$i"] = $_POST["question$i"];
                }

                $hasError = false;
                $errorMsg = "";

                // check if the array contains exactly 9 elements
                if (count($answers) !== 9) {
                    $errorMsg =  "The array does not contain exactly 9 elements.";
                    $hasError = true;
                }

                $duplicates = array();
                foreach ($answers as $key => $value) {
                    if (isset($duplicates[$value])) {
                        $duplicates[$value][] = $key;
                    } else {
                        $duplicates[$value] = array($key);
                    }
                }
                if (count(array_unique($answers)) !== 9) {
                    $hasError = true;
                    $duplicateKeys = array();
                    foreach ($duplicates as $value => $keys) {
                        if (count($keys) > 1) {
                            $duplicateKeys[] = implode(", ", $keys);
                        }
                    }
                    if (count($duplicateKeys) > 0) {
                        $errorMsg = "The following question(s) have duplicates: " . implode("; ", $duplicateKeys) . ".";
                    } else {
                        $errorMsg = "There should be no duplicates.";
                    }
                }

                // check if all the elements in the array are between 1 and 9
                foreach ($answers as $question => $answer) {
                    $questionNumber = (int) filter_var($answer, FILTER_SANITIZE_NUMBER_INT);
                    if ($answer < 1 || $answer > 9) {
                        $errorMsg =  "The answer for question $questionNumber is not between 1 and 9.";
                        $hasError = true;
                    }
                    if (!in_array($questionNumber, range(1, 9))) {
                        var_dump($questionNumber);
                        $errorMsg =  "The question key $question is not in the correct format.";
                        $hasError = true;
                    }
                }

                if ($hasError) {
                    $_SESSION['msg_type'] = 'danger';
                    $_SESSION['flash_message'] = $errorMsg;
                } else {
                    for ($i = $count_start; $i <= $count_end; $i++) {
                        $responses += array("question" . $i => $_POST["question" . $i]);
                    }
                    $_SESSION['responses'] = $responses;
                    $next_page = ++$current_page;

                    header("location: $rootURL/student/examination/index.php?questionnaire=$questionnaire_id&eval=$evaluation_id&page=$next_page");
                }
            } else if ($_POST['progress'] == "final") {
                $responses = $_SESSION['responses'];
                $answers = array();

                $count_start = $_POST['count_start'];
                $count_end = $_POST['count_end'];

                for ($i = $count_start; $i <= $count_end; $i++) {
                    $answers["question$i"] = $_POST["question$i"];
                }

                $hasError = false;
                $errorMsg = "";

                // check if the array contains exactly 9 elements
                if (count($answers) !== 9) {
                    $errorMsg =  "The array does not contain exactly 9 elements.";
                    $hasError = true;
                }

                $duplicates = array();
                foreach ($answers as $key => $value) {
                    if (isset($duplicates[$value])) {
                        $duplicates[$value][] = $key;
                    } else {
                        $duplicates[$value] = array($key);
                    }
                }
                if (count(array_unique($answers)) !== 9) {
                    $hasError = true;
                    $duplicateKeys = array();
                    foreach ($duplicates as $value => $keys) {
                        if (count($keys) > 1) {
                            $duplicateKeys[] = implode(", ", $keys);
                        }
                    }
                    if (count($duplicateKeys) > 0) {
                        $errorMsg = "The following question(s) have duplicates: " . implode("; ", $duplicateKeys) . ".";
                    } else {
                        $errorMsg = "There should be no duplicates.";
                    }
                }

                // check if all the elements in the array are between 1 and 9
                foreach ($answers as $question => $answer) {
                    $questionNumber = (int) filter_var($answer, FILTER_SANITIZE_NUMBER_INT);
                    if ($answer < 1 || $answer > 9) {
                        $errorMsg =  "The answer for question $questionNumber is not between 1 and 9.";
                        $hasError = true;
                    }
                    if (!in_array($questionNumber, range(1, 9))) {
                        $errorMsg =  "The question key $question is not in the correct format.";
                        $hasError = true;
                    }
                }

                if ($hasError) {
                    $_SESSION['msg_type'] = 'danger';
                    $_SESSION['flash_message'] = $errorMsg;
                } else {
                    for ($i = $count_start; $i <= $count_end; $i++) {
                        $responses += array("question" . $i => $_POST["question" . $i]);
                    }

                    $kinesthetic = $responses['question1'] + $responses['question10'] + $responses['question19'];
                    $existential = $responses['question2'] + $responses['question11'] + $responses['question20'];
                    $intrapersonal = $responses['question4'] + $responses['question13'] + $responses['question12'];
                    $interpersonal = $responses['question3'] +  $responses['question21'] + $responses['question22'];
                    $logic = $responses['question5'] + $responses['question14'] + $responses['question23'];
                    $musical = $responses['question6'] + $responses['question15'] + $responses['question24'];
                    $naturalistic = $responses['question7'] + $responses['question16'] + $responses['question25'];
                    $verbal = $responses['question8'] + $responses['question17'] + $responses['question26'];
                    $visual = $responses['question9'] + $responses['question18'] + $responses['question27'];
                    $results = array("Bodily / Kinesthetic" => $kinesthetic, "Existential" => $existential, "Interpersonal" => $interpersonal, "Intrapersonal" => $intrapersonal, "Logic" => $logic, "Musical" => $musical, "Naturalistic" => $naturalistic, "Verbal" => $verbal, "Visual" => $visual);
                    asort($results);

                    $descriptions = array(
                        "Bodily / Kinesthetic" => "People high in bodily–kinaesthetic intelligence have an excellent sense of timing and a great mind-body coordination as well as fine and gross motor skills. They are able to use their bodies to convey feelings and ideas and, as a result, they often take up roles in dance, sports or medicine. They use their bodies to solve problems and create something meaningful.",
                        "Existential" => "People with high levels of existential intelligence often think more deeply about daily occurrences. They ask questions similar to why are we here? And, what is the point of all this? They are often deeply philosophical thinkers and they have the capacity to look for answers to questions bigger than themselves. Existential intelligence is often called spiritual or moral intelligence.",
                        "Interpersonal" => "People with this type of intelligence are often good at reading verbal and non-verbal cues as well as determining temperament and mood. They feel empathy easily. Often this type of intelligence can be found in leaders, politicians, social workers, life coaches and psychologists.",
                        "Intrapersonal" => " People with high Intrapersonal intelligence understands oneself and the human condition as a whole. They are known as ''self-smart'' people and, despite having a deeper understanding of their own emotions, they are often quite shy. Philosophers, spiritual leaders, psychologist and writers usually have high intra-personal intelligence.",
                        "Logic" => "People with this type of intelligence are excellent at maths and working with numbers. They can recognise patterns easily and work out processes in a logical manner. They have excellent reasoning skills and can often talk themselves out of trouble. People with high logical–mathematical intelligence are often drawn to games involving strategy and the solving of puzzles.",
                        "Musical" => "People with musical intelligence are generally more sensitive to sound and often pick up on noises that others would not normally be aware of. They have an excellent sense of rhythm and the ability to recognise tone and pitch. More often than not they play an instrument or are involved in music as a profession.",
                        "Naturalistic" => "People with high naturalistic intelligence are sensitive to subtle changes in nature and the environment around them. Others connect with animals easily and some are completely at home in nature. Naturalistic intelligence describes people who are sensitive to the natural world. They enjoy being outside, nurturing and exploring the environment.",
                        "Verbal" => "People with high linguistic intelligence are very good at putting their feelings and thoughts into words in order to make others understand them. They are drawn to activities such as reading, writing and public speaking.",
                        "Visual" => "People with high spatial intelligence are generally very creative and usually have a vivid imagination, high artistic ability and excellent spatial reasoning. These people are often referred to as ''picture smart'' and can be found in professions such as architecture, design and map reading.",
                    );


                    $data = array(
                        'type' => 'rank',
                        'value' => $results
                    );

                    $evaluation_json = json_encode($data);

                    $keys = array_keys($results);

                    $eval = "Your Multiple Intelligence is ranked as follows, from top to bottom. \n\n";
                    $i = 1;
                    foreach ($results as $key => $result) {
                        $eval .= $i . ". " . $key . "\n";
                        $eval .= $descriptions[$key] . "\n\n";
                        $i++;
                    }

                    $sql = "UPDATE evaluation SET is_complete = '1', validity = '1', evaluation_result = '$eval', evaluation_json = '$evaluation_json' WHERE id = '$evaluation_id'";
                    if (mysqli_query($conn, $sql)) {
                        if (isset($_SESSION['responses'])) {
                            unset($_SESSION['responses']);
                        }
                        header("location: $rootURL/student/result.php?id=$evaluation_id");
                    } else {
                        echo $conn->error;
                    }
                }
            }
        } else if ($_POST['type'] == "range") {
            if ($questionnaire['range_type'] == "eq") {
                if ($_POST['progress'] == "append") {
                    if (isset($_SESSION['responses'])) {
                        $responses = $_SESSION['responses'];
                    } else {
                        $responses = array();
                    }

                    $count_start = $_POST['count_start'];
                    $count_end = $_POST['count_end'];

                    for ($i = $count_start; $i <= $count_end; $i++) {
                        $responses += array("question" . $i . "_id" => $_POST["question" . $i . "_id"]);
                        $responses += array("question" . $i . "_answer" => $_POST["question" . $i . "_answer"]);
                    }

                    $_SESSION['responses'] = $responses;
                    $next_page = ++$current_page;

                    header("location: $rootURL/student/examination/index.php?questionnaire=$questionnaire_id&eval=$evaluation_id&page=$next_page");
                } else if ($_POST['progress'] == "final") {
                    $responses = $_SESSION['responses'];

                    $count_start = $_POST['count_start'];
                    $count_end = $_POST['count_end'];

                    for ($i = $count_start; $i <= $count_end; $i++) {
                        $responses += array("question" . $i . "_id" => $_POST["question" . $i . "_id"]);
                        $responses += array("question" . $i . "_answer" => $_POST["question" . $i . "_answer"]);
                    }

                    $self_awareness =       ["1", "6", "11", "16", "21", "26", "31", "36", "41", "46"];
                    $managing_emotions =    ["2", "7", "12", "17", "22", "27", "32", "37", "42", "47"];
                    $motivating_oneself =   ["3", "8", "13", "18", "23", "28", "33", "38", "43", "48"];
                    $empathy =              ["4", "9", "14", "19", "24", "29", "34", "39", "44", "49"];
                    $social_skill =         ["5", "10", "15", "20", "25", "30", "35", "40", "45", "50"];

                    $self_awareness_total = 0;
                    $managing_emotions_total = 0;
                    $motivating_oneself_total = 0;
                    $empathy_total = 0;
                    $social_skill_total = 0;

                    for ($i = 1; $i <= $total; $i++) {
                        if (in_array($i, $self_awareness)) {
                            $self_awareness_total += (int)$responses["question" . $i . "_answer"];
                        } else if (in_array($i, $managing_emotions)) {
                            $managing_emotions_total += (int)$responses["question" . $i . "_answer"];
                        } else if (in_array($i, $motivating_oneself)) {
                            $motivating_oneself_total += (int)$responses["question" . $i . "_answer"];
                        } else if (in_array($i, $empathy)) {
                            $empathy_total += (int)$responses["question" . $i . "_answer"];
                        } else if (in_array($i, $social_skill)) {
                            $social_skill_total += (int)$responses["question" . $i . "_answer"];
                        }
                    }

                    $complete_array = array("Self Awareness" => $self_awareness_total, "Managing Emotions" => $managing_emotions_total, "Motivating Oneself" => $motivating_oneself_total, "Empathy" => $empathy_total, "Social Skills" => $social_skill_total);
                    $results_array = array(
                        "Self-awareness is the ability for an individual to understand their own emotions and how they impact their performance. If this is a person''s strength, it indicates that they are able to recognize their own emotions, the reasons behind those emotions, and how they can positively or negatively affect their actions. They also have the ability to perceive how others perceive them and can adjust their self-image to align with the perceptions of others.",
                        "The ability to effectively manage emotions is a critical aspect of emotional intelligence. If this is someone''s strength, it implies that they possess the capacity to regulate their emotions, which helps them to make better decisions regardless of the situation''s magnitude. Such individuals are aware of emotional triggers and can gain insights on how to respond in a constructive and positive manner.",
                        "Self-motivation is the personal drive to improve and achieve, a commitment to goals, initiative, readiness to act on opportunities, and optimism and resilience. If this is someone''s strength, it indicates that they possess the ability to take initiative and action in pursuit of their objectives and completion of tasks. They have an inherent drive to create, achieve and take action, especially when they are pursuing something out of their own will rather than being directed by someone else. This internal drive is what keeps them going on tasks, even in challenging circumstances.",
                        "Empathy is a crucial component of Emotional Quotient (EQ) that involves the ability to place oneself in another person''s shoes to understand their perspective. By doing so, individuals become more conscious of other people''s emotions and feelings. If this is someone''s strength, it indicates that they possess a great understanding of others, as they have the ability to view the world from different people''s viewpoints and empathize with their situation.",
                        "People with high social skills are proficient in communication, empathy, and conflict resolution. They are able to express their thoughts and feelings clearly, listen actively to others, and work collaboratively to achieve common goals. They are also able to recognize and respond appropriately to the emotions of others, which allows them to build rapport and establish trust."
                    );

                    $eval = "Your Emotional Quotient results are as follows: \n";
                    $count = 0;
                    foreach ($complete_array as $key => $item) {
                        $text = "";
                        if ($item >= 35 && $item <= 50) {
                            $text .= "Strength";
                        } else if ($item >= 18 && $item <= 34) {
                            $text .= "Needs Attention";
                        } else if ($item >= 10 && $item <= 17) {
                            $text .= "Development Priority";
                        }
                        $eval .= $key . ": " . $text . " (Score = $item / 50)\n";
                        $count++;
                    }

                    $eval .= "\n";
                    $eval .= "Implications of the five EQ competencies: \n";
                    $eval .= "Self-Awareness: \n";
                    $eval .= "$results_array[0]\n\n";
                    $eval .= "Managing Emotions: \n";
                    $eval .= "$results_array[1]\n\n";
                    $eval .= "Motivating Oneself: \n";
                    $eval .= "$results_array[2]\n\n";
                    $eval .= "Empathy: \n";
                    $eval .= "$results_array[3]\n\n";
                    $eval .= "Social Skills: \n";
                    $eval .= "$results_array[4]\n\n";


                    $data = array(
                        'type' => 'eq',
                        'value' => $complete_array
                    );

                    $evaluation_json = json_encode($data);

                    $sql = "UPDATE evaluation SET is_complete = '1', validity = '1', evaluation_result = '$eval', evaluation_json = '$evaluation_json' WHERE id = '$evaluation_id'";
                    if (mysqli_query($conn, $sql)) {
                        if (isset($_SESSION['responses'])) {
                            unset($_SESSION['responses']);
                        }
                        header("location: $rootURL/student/result.php?id=$evaluation_id");
                    } else {
                        echo $conn->error;
                    }
                }
            } else {
                if ($_POST['progress'] == "append") {
                    if (isset($_SESSION['responses'])) {
                        $responses = $_SESSION['responses'];
                    } else {
                        $responses = array();
                    }

                    $count_start = $_POST['count_start'];
                    $count_end = $_POST['count_end'];

                    for ($i = $count_start; $i <= $count_end; $i++) {
                        $responses += array("question" . $i . "_id" => $_POST["question" . $i . "_id"]);
                        $responses += array("question" . $i . "_answer" => $_POST["question" . $i . "_answer"]);
                    }

                    $_SESSION['responses'] = $responses;
                    $next_page = ++$current_page;
                    header("location: $rootURL/student/examination/index.php?questionnaire=$questionnaire_id&eval=$evaluation_id&page=$next_page");
                } else if ($_POST['progress'] == "final") {
                    $responses = $_SESSION['responses'];

                    $count_start = $_POST['count_start'];
                    $count_end = $_POST['count_end'];

                    for ($i = $count_start; $i <= $count_end; $i++) {
                        $responses += array("question" . $i . "_id" => $_POST["question" . $i . "_id"]);
                        $responses += array("question" . $i . "_answer" => $_POST["question" . $i . "_answer"]);
                    }

                    $c = ["1", "7", "13", "15", "17"];
                    $o = ["2", "6", "9", "11", "12", "16", "18", "20"];
                    $r = ["3", "5", "10", "14", "19"];
                    $e = ["4", "8"];

                    $questionCount = count($responses) / 2;

                    $c_total = 0;
                    $o_total = 0;
                    $r_total = 0;
                    $e_total = 0;

                    $adversities = [
                        "control" => [],
                        "ownership" => [],
                        "reach" => [],
                        "endurance" => []
                    ];

                    for ($i = 1; $i <= $questionCount; $i++) {
                        if (in_array($i, $c)) {
                            $c_total += (int)$responses["question" . $i . "_answer"];
                            $adversities["control"][] = (int)$responses["question" . $i . "_answer"];
                        } else if (in_array($i, $o)) {
                            $o_total += (int)$responses["question" . $i . "_answer"];
                            $adversities["ownership"][] = (int)$responses["question" . $i . "_answer"];
                        } else if (in_array($i, $r)) {
                            $r_total += (int)$responses["question" . $i . "_answer"];
                            $adversities["reach"][] = (int)$responses["question" . $i . "_answer"];
                        } else if (in_array($i, $e)) {
                            $e_total += (int)$responses["question" . $i . "_answer"];
                            $adversities["endurance"][] = (int)$responses["question" . $i . "_answer"];
                        }
                    }

                    $final_arp_score = ($c_total + $o_total + $r_total + $e_total) * 2;

                    $score = "";

                    if ($final_arp_score <= 59) {
                        $score = "Very Low";
                    } else if ($final_arp_score >= 60 && $final_arp_score <= 94) {
                        $score = "Low";
                    } else if ($final_arp_score >= 95 && $final_arp_score <= 134) {
                        $score = "Medium";
                    } else if ($final_arp_score >= 135 && $final_arp_score <= 165) {
                        $score = "High";
                    } else if ($final_arp_score >= 166 && $final_arp_score <= 200) {
                        $score = "Very High";
                    }

                    $eval = "The student''s Adversity Quotient Score is $final_arp_score, this means that the student has a $score Adversity Quotient \n\n";

                    $aqCategories = require 'aq_categories.php';

                    $eval .= $aqCategories[$score]["description"] . "\n\n";

                    $eval .= "The student''s scores (over 100) in the four dimensions of Adversity Quotient are as follows: \n\n";

                    // Compute for Control percentage
                    $c_percentage = $c_total / 25 * 100;
                    $eval .= "Control = $c_percentage% \n";

                    // Compute for Ownership percentage
                    $o_percentage = $o_total / 40 * 100;
                    $eval .= "Ownership =  $o_percentage% \n";

                    // Compute for Reach percentage
                    $r_percentage = $r_total / 25 * 100;
                    $eval .= "Reach = $r_percentage% \n";

                    // Compute for Endurance percentage
                    $e_percentage = $e_total / 10 * 100;
                    $eval .= "Endurance = $e_percentage% \n\n";

                    $eval .= "Implications of the four dimensions of Adversity Quotient:\n\n";
                    $eval .= "Control - It is the extent to which individuals are able to manage their life and control the negative consequences before getting worse.\n\n";
                    $eval .= "Ownership - It is the extent to which an individual is accountable for their actions and is willing to take responsibilities to improve the situation, regardless of their formal responsibilities.\n\n";
                    $eval .= "Reach - It is the extent to which the challenges an individual face extrapolate to other aspects of their life. It measures how much capable an individual is able to resist such challenges from affecting other necessities such as their studies or family life.\n\n";
                    $eval .= "Endurance - It is the extent to which an individual is capable of tolerating pain and yet be optimistic of future and believing that something positive waits for them in the opposite side of all adversities.\n\n";

                    $results = array(
                        "Control" => $c_percentage,
                        "Ownership" => $o_percentage,
                        "Reach" => $r_percentage,
                        "Endurance" => $e_percentage
                    );

                    $summary = [
                        "adversities" => $adversities,
                        "raw_core" => array(
                            "control" => $c_total,
                            "ownership" => $o_total,
                            "reach" => $r_total,
                            "endurance" => $e_total
                        ),
                        "final_arp_score" => $final_arp_score,
                        "arp_result" => $score,
                        "percentage_core" => $results
                    ];

                    $data = array(
                        'type' => 'aq',
                        'value' => $results,
                        'summary' => $summary
                    );

                    $evaluation_json = json_encode($data);

                    $sql = "UPDATE evaluation SET is_complete = '1', validity = '1', evaluation_result = '$eval', evaluation_json = '$evaluation_json' WHERE id = '$evaluation_id'";
                    if (mysqli_query($conn, $sql)) {
                        if (isset($_SESSION['responses'])) {
                            unset($_SESSION['responses']);
                        }
                        header("location: $rootURL/student/result.php?id=$evaluation_id");
                    } else {
                        echo $conn->error;
                    }
                }
            }
        }
        if (isset($_SESSION['current_page'])) {
            unset($_SESSION['current_page']);
        }
    }
}

include('../../logout.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Intelli.fied | Student</title>
    <?php include_once "../../components/header.php"; ?>
</head>

<body>
    <div class="container-fluid">
        <div class="row flex-nowrap">
            <div class="col-auto col-md-3 col-xl-2 px-sm-2 px-0 bg-dark shadow">
                <?php $deep = true; ?>
                <?php include_once "../components/new_panel.php" ?>
            </div>
            <div class="col">
                <div class="mt-4">
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
                        </div>
                        <div class="col-12">
                            <div class="card shadow mb-5 overflow-y-scroll p-2" style="max-height: 92vh;">
                                <div class="card-body">
                                    <div class="display-4 mb-4"><?php echo $questionnaire['name']; ?></div>
                                    <div class="mb-2 display-6">Description</div>
                                    <p><?php echo $questionnaire['description']; ?></p>
                                    <hr class="mb-4" />
                                    <div class="display-6 mb-2">Instructions</div>
                                    <p><?php echo $questionnaire['instruction']; ?></p>
                                    <hr class="mb-5" />
                                    <div class="row">
                                        <div class="col-12">
                                            <?php if ($questionnaire['question_type'] == "choices" && $questions_res) : ?>
                                                <?php include_once("choices.php"); ?>
                                            <?php elseif ($questionnaire['question_type'] == "rank" && $questions_res) : ?>
                                                <?php include_once("rank.php"); ?>
                                            <?php elseif ($questionnaire['question_type'] == "range" && $questions_res) : ?>
                                                <?php $range = $questionnaire['selection_range']; ?>
                                                <?php include_once("range.php"); ?>
                                            <?php endif ?>
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