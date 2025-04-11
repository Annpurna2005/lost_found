<?php
session_start();
include '../model/db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    // Check if email and password are not empty
    if (!empty($email) && !empty($password)) {
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        // Check if user exists
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            // Verify the password
            if (password_verify($password, $user['password'])) {
                // Store data in session
                $_SESSION["user_id"] = $user["id"];
                $_SESSION["name"] = $user["name"];
                $_SESSION["email"] = $user["email"];
                $_SESSION["profile_photo"] = $user["profile_photo"];

                $_SESSION["phone"] = $user["phone"];

                // Redirect to dashboard or home
           // Redirect to dashboard or home
echo "<script>alert('Login successful!'); window.location.href = '../views/dashboard.php';</script>";
exit;

                exit;
            } else {
                echo "<script>alert('Invalid password!'); window.history.back();</script>";
            }
        } else {
            echo "<script>alert('User not found!'); window.history.back();</script>";
        }

        $stmt->close();
    } else {
        echo "<script>alert('Please fill all fields!'); window.history.back();</script>";
    }
} else {
    echo "Invalid Request Method!";
}
?>
