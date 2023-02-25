<?php

// Make sure we have received the id parameter via POST
if (!isset($_POST['id'])) {
    die('No id provided.');
}

// Establish a connection to the database
$mysqli = new mysqli('localhost', 'root', '', 'student_profiling');

// Check for errors connecting to the database
if ($mysqli->connect_errno) {
    die('Failed to connect to MySQL: ' . $mysqli->connect_error);
}

// Prepare a DELETE statement
if ($stmt = $mysqli->prepare('DELETE FROM questions WHERE id = ?')) {

    // Bind the id parameter to the statement
    $stmt->bind_param('i', $_POST['id']);

    // Execute the statement
    $stmt->execute();

    // Check for errors executing the statement
    if ($stmt->errno) {
        echo 'Failed to delete question: ' . $stmt->error;
    } else {
        // Close the statement
        $stmt->close();
        // Close the database connection
        $mysqli->close();
        // Redirect back to the previous page
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }
}

// Close the statement
$stmt->close();

// Close the database connection
$mysqli->close();
