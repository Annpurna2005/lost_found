<?php
include '../model/db.php';
// Start session to get user ID (you must login user before this)
session_start();
$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    die("Please login first.");
}

// Get form data
$type = $_POST['type'];
$title = $_POST['title'];
$description = $_POST['description'];
$category = $_POST['category'];
$location = $_POST['location'];
$datetime = $_POST['datetime'];

// Handle image upload
$image = '';
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $imgName = basename($_FILES['image']['name']);
    $imgTmp  = $_FILES['image']['tmp_name'];
    $target  = 'uploads/' . time() . '_' . $imgName;

    if (move_uploaded_file($imgTmp, $target)) {
        $image = $target;
    } else {
        die("Image upload failed.");
    }
}

// Prepare and insert into DB
$stmt = $conn->prepare("INSERT INTO posts 
    (user_id, type, title, description, category, image, location, datetime) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

$stmt->bind_param("isssssss", $user_id, $type, $title, $description, $category, $image, $location, $datetime);

if ($stmt->execute()) {
    echo "<script>alert('Post created successfully!'); window.location.href='index.php';</script>";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();

?>
