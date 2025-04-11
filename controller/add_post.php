<?php
session_start();
include '../model/db.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION["user_id"];
    $type = $_POST['type'];
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    $datetime = date('Y-m-d H:i:s');
    $status = 'pending';

    // ✅ Correct path inside controller/uploads/
    $image_name = $_FILES['image']['name'];
    $image_tmp = $_FILES['image']['tmp_name'];
    $upload_path = '../uploads/' . $image_name;

    if (move_uploaded_file($image_tmp, $upload_path)) {
        // ✅ Using 'image' instead of 'image_path'
        $sql = "INSERT INTO posts (user_id, type, title, description, category, image, location, datetime, status)
                VALUES ('$user_id', '$type', '$title', '$description', '$category', '$image_name', '$location', '$datetime', '$status')";

        if ($conn->query($sql) === TRUE) {
            $_SESSION['success'] = "✅ Post added successfully!";
            header("Location: ../views/dashboard.php"); // Update path as per your folder
            exit();
        } else {
            echo "❌ Database Error: " . $conn->error;
        }
    } else {
        echo "❌ Image upload failed.";
    }
} else {
    echo "⚠️ Invalid request.";
}
?>
