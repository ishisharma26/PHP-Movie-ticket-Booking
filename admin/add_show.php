<?php
// Include database connection
require_once 'Database.php';

// Define variables to store form data and error messages
$show_time = $theater_name = $show_time_err = $theater_name_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate show time
    if (empty(trim($_POST["show_time"]))) {
        $show_time_err = "Please enter show time.";
    } else {
        $show_time = trim($_POST["show_time"]);
    }

    // Validate theater name
    if (empty(trim($_POST["theater_name"]))) {
        $theater_name_err = "Please enter theater name.";
    } else {
        $theater_name = trim($_POST["theater_name"]);
    }

    // Check input errors before inserting into database
    if (empty($show_time_err) && empty($theater_name_err)) {
        // Check if theater already exists
        $sql_check = "SELECT id FROM theaters WHERE name = ?";
        if ($stmt_check = $conn->prepare($sql_check)) {
            $stmt_check->bind_param("s", $param_theater_name);
            $param_theater_name = $theater_name;
            if ($stmt_check->execute()) {
                $stmt_check->store_result();
                if ($stmt_check->num_rows > 0) {
                    $stmt_check->bind_result($theater_id);
                    $stmt_check->fetch();
                } else {
                    // Insert new theater
                    $sql_insert_theater = "INSERT INTO theaters (name) VALUES (?)";
                    if ($stmt_insert_theater = $conn->prepare($sql_insert_theater)) {
                        $stmt_insert_theater->bind_param("s", $param_theater_name);
                        $param_theater_name = $theater_name;
                        if ($stmt_insert_theater->execute()) {
                            $theater_id = $stmt_insert_theater->insert_id;
                        } else {
                            echo "Something went wrong. Please try again later.";
                        }
                        $stmt_insert_theater->close();
                    }
                }
            } else {
                echo "Something went wrong. Please try again later.";
            }
            $stmt_check->close();
        }

        // Prepare an INSERT statement for the show
        $sql_insert_show = "INSERT INTO shows (show_time, theater_id) VALUES (?, ?)";
        if ($stmt_insert_show = $conn->prepare($sql_insert_show)) {
            $stmt_insert_show->bind_param("si", $param_show_time, $param_theater_id);
            $param_show_time = $show_time;
            $param_theater_id = $theater_id;
            if ($stmt_insert_show->execute()) {
                // Show added successfully, redirect to show list
                header("location: show_list.php");
                exit();
            } else {
                echo "Something went wrong. Please try again later.";
            }
            $stmt_insert_show->close();
        }
    }

    // Close connection
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Show</title>
    <style>
        .error { color: red; }
    </style>
</head>
<body>
    <h2>Add Show</h2>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <div>
            <label>Show Time:</label>
            <input type="text" name="show_time" value="<?php echo $show_time; ?>">
            <span class="error"><?php echo $show_time_err; ?></span>
        </div>
        <div>
            <label>Theater Name:</label>
            <input type="text" name="theater_name" value="<?php echo $theater_name; ?>">
            <span class="error"><?php echo $theater_name_err; ?></span>
        </div>
        <div>
            <input type="submit" value="Add Show">
            <input type="reset" value="Reset">
        </div>
    </form>
</body>
</html>
    