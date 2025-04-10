<?php
include '../model/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["post_id"], $_POST["status"])) {
    $post_id = intval($_POST["post_id"]);
    $status = $_POST["status"];

    $allowed_status = ['pending', 'approved', 'rejected'];
    if (in_array($status, $allowed_status)) {
        $stmt = $conn->prepare("UPDATE posts SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $post_id);
        $stmt->execute();
        $stmt->close();
    }
}

header("Location: ../views/dashboard.php"); // वापिस डैशबोर्ड पर redirect
exit();
?>