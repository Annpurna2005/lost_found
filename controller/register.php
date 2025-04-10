<?php
include '../model/db.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Sanitize user inputs
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    $phone = trim($_POST["phone"]);

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Handle profile photo
    $targetDir = __DIR__ . '/../uploads/'; // Absolute path
    $profilePhotoName = "";

    // Make sure uploads folder exists
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0755, true);
    }

    if (!empty($_FILES["profile_photo"]["name"])) {
        $profilePhotoName = basename($_FILES["profile_photo"]["name"]);
        $targetFilePath = $targetDir . $profilePhotoName;
        $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($fileType, $allowedTypes)) {
            if (!move_uploaded_file($_FILES["profile_photo"]["tmp_name"], $targetFilePath)) {
                die("Error uploading profile photo.");
            }
        } else {
            die("Only JPG, JPEG, PNG, and GIF files are allowed.");
        }
    }

    // Prepare SQL insert
    $sql = "INSERT INTO users (name, email, password, phone, profile_photo) 
            VALUES (?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $name, $email, $hashedPassword, $phone, $profilePhotoName);

    if ($stmt->execute()) {
        echo "<script>alert('Registration successful!'); window.location.href = '../views/login.html';</script>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}



?>
