<?php
include '../model/db.php';

if (!isset($_GET['user_id'])) {
  echo "User not found.";
  exit;
}

$user_id = $_GET['user_id'];

$userQuery = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($userQuery);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$userResult = $stmt->get_result();
$user = $userResult->fetch_assoc();

if (!$user) {
  echo "User not found.";
  exit;
}

$postsQuery = "SELECT * FROM posts WHERE user_id = ? ORDER BY created_at DESC";
$stmt2 = $conn->prepare($postsQuery);
$stmt2->bind_param("i", $user_id);
$stmt2->execute();
$postsResult = $stmt2->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?php echo htmlspecialchars($user['name']); ?>'s Profile</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

  <style>

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
      font-family: 'Poppins', sans-serif;
    }

    body {
      background: linear-gradient(135deg, #dbeafe, #f0f4ff);
      padding: 40px 20px;
      display: flex;
      flex-direction: column;
      align-items: center;
      color: #1e293b;
    }

    a {
      text-decoration: none;
    }

    .back-button {
      margin-bottom: 30px;
      background: #2563eb;
      color: white;
      padding: 10px 22px;
      border-radius: 10px;
      font-weight: 500;
      font-size: 15px;
      transition: background 0.3s ease, transform 0.2s ease;
    }

    .back-button:hover {
      background: #1d4ed8;
      transform: translateY(-2px);
    }

    .profile-card {
      background-color: white;
      padding: 30px 20px;
      border-radius: 18px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
      text-align: center;
      max-width: 460px;
      width: 100%;
      margin-bottom: 35px;
      position: relative;
    }

    .profile-card img {
      width: 120px;
      height: 120px;
      border-radius: 50%;
      object-fit: cover;
      border: 4px solid #60a5fa;
      margin-bottom: 18px;
    }

    .profile-card h2 {
      font-size: 26px;
      margin-bottom: 6px;
      color: #0f172a;
    }

    .profile-card p {
      font-size: 15px;
      color: #475569;
    }

    .message-button {
      margin-top: 18px;
      background-color: #22c55e;
      color: white;
      padding: 10px 24px;
      border-radius: 10px;
      font-size: 15px;
      transition: background 0.3s ease, transform 0.2s ease;
    }

    .message-button:hover {
      background-color: #16a34a;
      transform: scale(1.03);
    }

    .posts-container {
      max-width: 1000px;
      width: 100%;
      display: grid;
      gap: 28px;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    }

    .post-card {
      background-color: #ffffff;
      border-radius: 16px;
      overflow: hidden;
      box-shadow: 0 10px 18px rgba(0, 0, 0, 0.07);
      transition: all 0.25s ease;
      display: flex;
      flex-direction: column;
    }

    .post-card:hover {
      transform: translateY(-6px);
      box-shadow: 0 16px 30px rgba(0, 0, 0, 0.1);
    }

    .post-card img {
      width: 100%;
      height: 220px;
      object-fit: cover;
    }

    .post-details {
      padding: 18px 20px;
      flex-grow: 1;
    }

    .post-details h4 {
      font-size: 20px;
      margin-bottom: 10px;
      color: #1e293b;
    }

    .post-details p {
      font-size: 14px;
      color: #475569;
      margin-bottom: 5px;
      line-height: 1.4;
    }

    @media (max-width: 600px) {
      .profile-card, .post-card {
        border-radius: 14px;
      }

      .back-button, .message-button {
        font-size: 14px;
        padding: 8px 18px;
      }
    }
  </style>

</head>
<body>

  <a href="home.php" class="back-button">← Back to Home</a>
  <div class="profile-card">
  <img src="../controller/uploads/<?php echo $user['profile_photo']; ?>" alt="Profile Picture">
  <h2><?php echo htmlspecialchars($user['name']); ?></h2>
  <p>📞 <?php echo htmlspecialchars($user['phone']); ?></p>
  <br>
 <a href="https://wa.me/91<?php echo $user['phone']; ?>?text=Hi%20<?php echo urlencode($user['name']); ?>,%20I%20found%20your%20lost%20item." target="_blank" class="message-button">💬 Message</a>


</div>

  <div class="posts-container">
    <?php while($post = $postsResult->fetch_assoc()): ?>
      <div class="post-card">
        <img src="../controller/uploads/<?php echo $post['image']; ?>" alt="Post Image">
        <div class="post-details">
          <h4><?php echo htmlspecialchars($post['title']); ?></h4>
          <p><strong>Type:</strong> <?php echo ucfirst($post['type']); ?></p>
          <p><strong>Category:</strong> <?php echo $post['category']; ?></p>
          <p><strong>Location:</strong> <?php echo $post['location']; ?></p>
          <p><strong>Date:</strong> <?php echo date('d M Y, h:i A', strtotime($post['datetime'])); ?></p>
          <p><strong>Status:</strong> <?php echo ucfirst($post['status']); ?></p>
        </div>
      </div>
    <?php endwhile; ?>
  </div>

</body>
</html>
