<?php
// Start session and check if user is logged in as admin
session_start();
if (!isset($_SESSION['admin'])) {
    // Redirect to login page if not logged in
    header("location: login.php");
    exit; // Stop further execution
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Include database connection
    include_once 'Database.php';

    // Fetch data from the form
    $movieName = $_POST['movie_name'];
    $director = $_POST['director_name'];
    $category = $_POST['category'];
    $language = $_POST['language'];
    $releaseDate = $_POST['release_date'];
    $trailer = $_POST['trailer'];
    $action = $_POST['action'];
    $description = $_POST['description'];

    // File upload handling
    $targetDirectory = "image/";
    $targetFile = $targetDirectory . basename($_FILES["img"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    // Check if image file is a actual image or fake image
    if(isset($_POST["submit"])) {
        $check = getimagesize($_FILES["img"]["tmp_name"]);
        if($check !== false) {
            echo "File is an image - " . $check["mime"] . ".";
            $uploadOk = 1;
        } else {
            echo "File is not an image.";
            $uploadOk = 0;
        }
    }

    // Check if file already exists
    if (file_exists($targetFile)) {
        echo "Sorry, file already exists.";
        $uploadOk = 0;
    }

    // Check file size
    if ($_FILES["img"]["size"] > 500000) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    // Allow certain file formats
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
    && $imageFileType != "gif" ) {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
    // if everything is ok, try to upload file
    } else {
        if (move_uploaded_file($_FILES["img"]["tmp_name"], $targetFile)) {
            echo "The file ". htmlspecialchars( basename( $_FILES["img"]["name"])). " has been uploaded.";
            
            // Insert movie details into the database
            $sql = "INSERT INTO add_movie (movie_name, directer, categroy, language, release_date, you_tube_link, action, decription, image)
            VALUES ('$movieName', '$director', '$category', '$language', '$releaseDate', '$trailer', '$action', '$description', '".basename( $_FILES["img"]["name"])."')";

            if ($conn->query($sql) === TRUE) {
                echo "New record created successfully";
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
            
            // Redirect back to main page
            header("location: index.php");
            exit; // Stop further execution
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Movie</title>
</head>
<body>
    <h2>Add Movie</h2>
    <!-- Form for adding a movie -->
    <form method="post" enctype="multipart/form-data">
        <label>Movie Name:</label><br>
        <input type="text" name="movie_name"><br>
        
        <label>Director Name:</label><br>
        <input type="text" name="director_name"><br>
        
        <label>Category:</label><br>
        <input type="text" name="category"><br>
        
        <label>Language:</label><br>
        <input type="text" name="language"><br>
        
        <label>Release Date:</label><br>
        <input type="date" name="release_date"><br>
        
        <label>Trailer:</label><br>
        <input type="text" name="trailer"><br>
        
        <label>Action:</label><br>
        <select name="action">
            <option value="upcoming">Upcoming</option>
            <option value="running">Running</option>
        </select><br>
        
        <label>Description:</label><br>
        <textarea name="description"></textarea><br>
        
        <label>Upload Poster:</label><br>
        <input type="file" name="img"><br>
        
        <input type="submit" name="submit" value="Submit">
    </form>
</body>
</html>
