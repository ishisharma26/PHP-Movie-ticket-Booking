<?php
// Start session and check if user is logged in as admin
session_start();
if (!isset($_SESSION['admin'])) {
    // Redirect to login page if not logged in
    header("location: login.php");
    exit; // Stop further execution
}

// Check if movie ID is provided in the URL
if (isset($_GET['id']) && !empty($_GET['id'])) {
    // Include database connection
    include_once 'Database.php';

    // Get the movie ID from the URL
    $id = $_GET['id'];

    // Delete the movie from the database
    $sql = "DELETE FROM add_movie WHERE id = $id";

    if ($conn->query($sql) === TRUE) {
        echo "Record deleted successfully";
    } else {
        echo "Error deleting record: " . $conn->error;
    }

    // Redirect back to main page
    header("location: index.php");
    exit; // Stop further execution
} else {
    // If movie ID is not provided in the URL, redirect back to main page
    header("location: index.php");
    exit; // Stop further execution
}
?>
