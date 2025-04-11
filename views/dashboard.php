<?php
session_start();
include '../model/db.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];
$name = $_SESSION["name"];
$email = $_SESSION["email"];
$phone = $_SESSION["phone"];
$profile_photo = $_SESSION["profile_photo"];

$postsQuery = $conn->query("SELECT * FROM posts WHERE user_id = $user_id");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?php echo $name; ?>'s Profile</title>
  
  <style>* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: 'Arial', sans-serif;
}

body {
  background-color: #f2f2f2;
  padding: 40px 10px;
}

.profile-container {
  max-width: 960px;
  margin: auto;
  background-color: #fff;
  padding: 30px;
  border-radius: 10px;
  box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
}

.profile-header {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  justify-content: space-between;
  gap: 20px;
  margin-bottom: 30px;
}

.profile-left {
  display: flex;
  align-items: center;
  gap: 20px;
  flex: 1;
  min-width: 250px;
}

.profile-image img {
  width: 120px;
  height: 120px;
  border-radius: 50%;
  object-fit: cover;
  border: 2px solid #ddd;
}

.profile-info h2 {
  font-size: 24px;
  margin-bottom: 6px;
}

.profile-info .bio {
  font-size: 14px;
  color: #555;
}

.stats {
  margin-top: 10px;
  font-size: 14px;
  color: #333;
}

.stats strong {
  color: #000;
}

.profile-actions {
  display: flex;
  gap: 10px;
  flex-wrap: wrap;
}

button,
.home-btn {
  padding: 10px 16px;
  font-size: 14px;
  font-weight: bold;
  border-radius: 6px;
  cursor: pointer;
  transition: background 0.3s;
  border: none;
  text-decoration: none;
}

.add-post-btn {
  background-color: #0066ff;
  color: #fff;
}

.add-post-btn:hover {
  background-color: #004ec2;
}

.logout-btn {
  background-color: #dc3545;
  color: #fff;
}

.logout-btn:hover {
  background-color: #b52a37;
}

.home-btn {
  background-color: #17a2b8;
  color: white;
}

.home-btn:hover {
  background-color: #117a8b;
}

.gallery {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 20px;
  margin-top: 30px;
}

.insta-post {
  background-color: #fff;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  transition: 0.3s;
  display: flex;
  flex-direction: column;
}

.insta-post:hover {
  transform: translateY(-5px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.post-image img {
  width: 100%;
  height: 220px;
  object-fit: cover;
}

.post-details {
  padding: 15px;
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.post-details h3 {
  font-size: 18px;
  color: #222;
}

.post-details p {
  font-size: 14px;
  color: #555;
}

.status {
  font-weight: bold;
}

.status.approved { color: green; }
.status.pending { color: orange; }
.status.rejected { color: red; }

.no-posts {
  text-align: center;
  font-size: 16px;
  margin-top: 20px;
  color: #777;
}

.modal {
  display: none;
  position: fixed;
  z-index: 999;
  left: 0;
  top: 0;
  width: 100vw;
  height: 100vh;
  background-color: rgba(0, 0, 0, 0.6);
  justify-content: center;
  align-items: center;
}

.modal-content {
  background-color: #fff;
  padding: 25px;
  border-radius: 10px;
  width: 90%;
  max-width: 400px;
}

.modal-content input,
.modal-content textarea,
.modal-content select {
  width: 100%;
  margin-bottom: 15px;
  padding: 10px;
  border: 1px solid #ddd;
  border-radius: 6px;
}

.modal-content button {
  background-color: #28a745;
  color: white;
  padding: 10px 14px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
}

.close-btn {
  background-color: #dc3545;
  margin-left: 10px;
}

  </style>
</head>
<body>
  <div class="profile-container">
    <div class="profile-header">
      <div class="profile-left">
      
        <div class="profile-image">
        <img src="<?php echo '../uploads/' . $profile_photo; ?>" alt="Profile Picture">

        </div>
        <div class="profile-info">
          <h2><?php echo $name; ?></h2>
          <p class="bio">Welcome, <?php echo $name; ?> 👋</p>
          <p class="bio">Phone No. <?php echo $phone; ?> 📞</p>
          <div class="stats">
            <span><strong><?php echo $postsQuery->num_rows; ?></strong> posts</span>
          </div>
        </div>
      </div>
      <div class="profile-actions">
  <a href="home.php" class="home-btn">🏠 Home</a>
  <button class="add-post-btn" onclick="openModal()">+ Add Post</button>
  <form action="../controller/logout.php" method="POST" style="display:inline;">
    <button type="submit" class="logout-btn">Logout</button>
  </form>
</div>

    </div>

    <?php if ($postsQuery->num_rows > 0): ?>
      <div class="gallery">
        <?php while($post = $postsQuery->fetch_assoc()): ?>
          <div class="insta-post">
            <div class="post-image">
            <img src="<?php echo '../uploads/' . $post['image']; ?>" alt="Post Image">

            </div>
            <div class="post-details">
              <h3><?php echo htmlspecialchars($post['title']); ?></h3>
              <p><strong>Type:</strong> <?php echo ucfirst($post['type']); ?></p>
              <p><strong>Category:</strong> <?php echo $post['category']; ?></p>
              <p><strong>Location:</strong> <?php echo $post['location']; ?></p>
              <form action="../controller/update_status.php" method="POST">
  <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
  <label for="status">Status:</label>
  <select name="status" onchange="this.form.submit()">
    <option value="pending" <?php if ($post['status'] == 'pending') echo 'selected'; ?>>Pending</option>
    <option value="approved" <?php if ($post['status'] == 'approved') echo 'selected'; ?>>Approved</option>
    <option value="rejected" <?php if ($post['status'] == 'rejected') echo 'selected'; ?>>Rejected</option>
  </select>
</form>

              <p><strong>Date:</strong> <?php echo date('d M Y, h:i A', strtotime($post['datetime'])); ?></p>
            </div>
          </div>
          
        <?php endwhile; ?>
      </div>

    <?php else: ?>
      <p class="no-posts">No posts yet. Add your first lost/found post!</p>
    <?php endif; ?>
  </div>

  <!-- Modal -->
  <div class="modal" id="postModal">
    <div class="modal-content">
      <h3>Add Lost/Found Item</h3>
      <form action="../controller/add_post.php" method="POST" enctype="multipart/form-data">
        <label for="type">Type:</label>
        <select name="type" required>
          <option value="">Select Type</option>
          <option value="lost">Lost</option>
          <option value="found">Found</option>
        </select>

        <input type="text" name="title" placeholder="Item Title" required>
        <textarea name="description" placeholder="Description..." required></textarea>
        <input type="text" name="category" placeholder="Category (e.g. Wallet, Phone)" required>
        <input type="text" name="location" placeholder="Location where item was lost/found" required>
        <input type="file" name="image" accept="image/*" required>
        <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
        <button type="submit">Submit</button>
        <button type="button" class="close-btn" onclick="closeModal()">Cancel</button>
      </form>
    </div>
  </div>

  <script>
    function openModal() {
      document.getElementById("postModal").style.display = "flex";
    }
    function closeModal() {
      document.getElementById("postModal").style.display = "none";
    }
  </script>
</body>
</html>
