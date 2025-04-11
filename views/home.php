<?php
include '../model/db.php';

$query = "SELECT posts.*, users.name, users.phone, users.profile_photo 
          FROM posts 
          JOIN users ON posts.user_id = users.id 
          ORDER BY posts.created_at DESC";

$result = $conn->query($query);

$unread_count = 0; // Default value

if (isset($_SESSION['user_id'])) {
    $current_user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("SELECT COUNT(*) AS unread_count FROM messages WHERE receiver_id = ? AND is_read = 0");
    $stmt->bind_param("i", $current_user_id);
    $stmt->execute();
    $result_unread = $stmt->get_result();

    if ($result_unread && $row = $result_unread->fetch_assoc()) {
        $unread_count = $row['unread_count'];
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Lost & Found - Home</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <style>
  :root {
    --primary: #4361ee;
    --secondary: #3f37c9;
    --danger: #f72585;
    --success: #4cc9f0;
    --warning: #f8961e;
    --dark: #212529;
    --light: #f8f9fa;
    --gray: #6c757d;
    --white: #ffffff;
  }
  
  * {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
    font-family: 'Poppins', sans-serif;
  }

  body {
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    min-height: 100vh;
    padding: 0;
    margin: 0;
    animation: gradientBG 15s ease infinite;
    background-size: 400% 400%;
  }

  @keyframes gradientBG {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
  }

  .container {
    max-width: 800px;
    width: 100%;
    margin: 0 auto;
    padding: 80px 15px 70px;
  }

  /* Post Card Styles */
  .post {
    background-color: var(--white);
    border-radius: 16px;
    margin-bottom: 30px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
    transform: translateY(0);
    opacity: 1;
    position: relative;
    border: 1px solid rgba(0, 0, 0, 0.05);
  }

  .post:hover {
    transform: translateY(-8px);
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.12);
  }

  .user-info {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 16px 20px;
    background-color: var(--white);
    position: relative;
    z-index: 2;
  }

  .user-info::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 20px;
    right: 20px;
    height: 1px;
    background: linear-gradient(90deg, transparent, rgba(0,0,0,0.1), transparent);
  }

  .user-info img {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid rgba(255, 255, 255, 0.3);
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
  }

  .user-info:hover img {
    transform: scale(1.05);
    border-color: var(--primary);
  }

  .user-details {
    display: flex;
    flex-direction: column;
    justify-content: center;
    line-height: 1.3;
    flex-grow: 1;
  }

  .user-details .name {
    font-weight: 600;
    font-size: 15px;
    color: var(--dark);
    transition: color 0.3s ease;
  }

  .user-details .phone {
    font-size: 13px;
    color: var(--gray);
    display: flex;
    align-items: center;
    gap: 5px;
  }

  .post-image {
    position: relative;
    overflow: hidden;
    height: 380px;
  }

  .post-image::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(to bottom, rgba(0,0,0,0) 60%, rgba(0,0,0,0.3) 100%);
    z-index: 1;
  }

  .post-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
  }

  .post:hover .post-image img {
    transform: scale(1.03);
  }

  .post-details {
    padding: 20px;
    background-color: var(--white);
  }

  .post-details h3 {
    font-size: 20px;
    margin-bottom: 12px;
    color: var(--dark);
    font-weight: 600;
  }

  .post-details p {
    font-size: 14px;
    margin-bottom: 10px;
    color: var(--gray);
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .post-details p i {
    width: 20px;
    color: var(--primary);
    font-size: 14px;
  }

  .post-details p strong {
    color: var(--dark);
    font-weight: 500;
    min-width: 80px;
    display: inline-block;
  }

  .status {
    font-weight: 600;
    font-size: 13px;
    padding: 4px 10px;
    border-radius: 20px;
    display: inline-flex;
    align-items: center;
    gap: 5px;
  }

  .status.pending {
    background-color: rgba(248, 150, 30, 0.1);
    color: var(--warning);
  }

  .status.approved {
    background-color: rgba(76, 201, 240, 0.1);
    color: var(--success);
  }

  .status.rejected {
    background-color: rgba(247, 37, 133, 0.1);
    color: var(--danger);
  }

  .no-posts {
    text-align: center;
    padding: 50px 20px;
    background-color: var(--white);
    border-radius: 16px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
  }

  .no-posts i {
    font-size: 60px;
    color: #ddd;
    margin-bottom: 20px;
  }

  .no-posts h3 {
    font-size: 22px;
    color: var(--gray);
    margin-bottom: 10px;
  }

  .no-posts p {
    font-size: 16px;
    color: var(--gray);
    margin-bottom: 20px;
  }

  /* Navigation Styles */
  .navbar {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    background-color: var(--white);
    box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
    z-index: 1000;
    padding: 15px 0;
    display: flex;
    justify-content: center;
  }

  .navbar ul {
    display: flex;
    list-style: none;
    gap: 30px;
    margin: 0;
    padding: 0;
  }

  .navbar li a {
    text-decoration: none;
    color: var(--dark);
    font-weight: 500;
    font-size: 16px;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
    padding: 5px 10px;
    border-radius: 8px;
  }

  .navbar li a:hover {
    color: var(--primary);
    background-color: rgba(67, 97, 238, 0.1);
  }

  .navbar li a i {
    font-size: 18px;
  }

  .mobile-navbar {
    display: none;
    position: fixed;
    bottom: 0;
    left: 0;
    width: 100%;
    background-color: var(--white);
    box-shadow: 0 -2px 15px rgba(0, 0, 0, 0.1);
    z-index: 1000;
    padding: 12px 0;
  }

  .mobile-navbar ul {
    display: flex;
    justify-content: space-around;
    list-style: none;
    margin: 0;
    padding: 0;
  }

  .mobile-navbar li a {
    color: var(--gray);
    font-size: 22px;
    display: flex;
    flex-direction: column;
    align-items: center;
    text-decoration: none;
    transition: all 0.3s ease;
    padding: 5px 15px;
    border-radius: 8px;
  }

  .mobile-navbar li a span {
    font-size: 12px;
    margin-top: 3px;
    opacity: 0;
    transition: opacity 0.3s ease;
  }

  .mobile-navbar li a:hover {
    color: var(--primary);
  }

  .mobile-navbar li a:hover span {
    opacity: 1;
  }

  .message-badge {
    position: absolute;
    top: -5px;
    right: -5px;
    background-color: var(--danger);
    color: white;
    border-radius: 50%;
    width: 18px;
    height: 18px;
    font-size: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    animation: pulse 1.5s infinite;
  }

  @keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); }
  }

  /* Floating animation */
  @keyframes float {
    0% { transform: translateY(0px); }
    50% { transform: translateY(-10px); }
    100% { transform: translateY(0px); }
  }

  /* Responsive adjustments */
  @media (max-width: 768px) {
    .navbar {
      display: none;
    }
    
    .mobile-navbar {
      display: block;
    }

    .container {
      padding: 20px 15px 80px;
    }

    .post-image {
      height: 300px;
    }

    .post-details {
      padding: 16px;
    }

    .post-details h3 {
      font-size: 18px;
    }

    .post-details p {
      font-size: 13px;
    }
  }

  @media (max-width: 480px) {
    .post-image {
      height: 250px;
    }

    .mobile-navbar li a {
      font-size: 20px;
      padding: 5px 10px;
    }
  }

  /* Animation for posts loading */
  @keyframes fadeInUp {
    from {
      opacity: 0;
      transform: translateY(20px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  .post {
    animation: fadeInUp 0.6s ease forwards;
    opacity: 0;
  }

  /* Delay animations for each post */
  .post:nth-child(1) { animation-delay: 0.1s; }
  .post:nth-child(2) { animation-delay: 0.2s; }
  .post:nth-child(3) { animation-delay: 0.3s; }
  .post:nth-child(4) { animation-delay: 0.4s; }
  .post:nth-child(5) { animation-delay: 0.5s; }
  .post:nth-child(6) { animation-delay: 0.6s; }
  </style>
</head>
<body>
  <!-- Desktop Navigation -->
  <nav class="navbar">
    <ul>
      <li><a href="home.php"><i class="fas fa-home"></i> Home</a></li>
      <li><a href="search.php"><i class="fas fa-search"></i> Search</a></li>
      <li>
        <a href="#" style="position: relative;">
          <i class="fas fa-comment-dots"></i> Messages
          <?php if ($unread_count > 0): ?>
            <span class="message-badge"><?php echo $unread_count; ?></span>
          <?php endif; ?>
        </a>
      </li>
      <li><a href="dashboard.php"><i class="fas fa-user"></i> Profile</a></li>
    </ul>
  </nav>

  <div class="container">
    <?php if ($result && $result->num_rows > 0): ?>
      <?php while($post = $result->fetch_assoc()): ?>
        <div class="post">
          <div class="user-info">
            <a href="profile.php?user_id=<?php echo $post['user_id']; ?>" style="display: flex; align-items: center; gap: 12px; text-decoration: none; color: inherit;">
              <img src="<?php echo '../uploads/' . $post['profile_photo']; ?>" alt="User">
              <div class="user-details">
                <span class="name"><?php echo htmlspecialchars($post['name']); ?></span>
                <span class="phone"><i class="fas fa-phone"></i> <?php echo htmlspecialchars($post['phone']); ?></span>
              </div>
            </a>
          </div>

          <div class="post-image">
            <img src="<?php echo '../uploads/' . $post['image']; ?>" alt="Post">
          </div>

          <div class="post-details">
            <h3><?php echo htmlspecialchars($post['title']); ?></h3>
            <p><i class="fas fa-tag"></i><strong>Type:</strong> <?php echo ucfirst($post['type']); ?></p>
            <p><i class="fas fa-list"></i><strong>Category:</strong> <?php echo $post['category']; ?></p>
            <p><i class="fas fa-map-marker-alt"></i><strong>Location:</strong> <?php echo $post['location']; ?></p>
            <p><i class="fas fa-info-circle"></i><strong>Status:</strong> <span class="status <?php echo $post['status']; ?>">
              <i class="<?php 
                echo $post['status'] == 'approved' ? 'fas fa-check-circle' : 
                     ($post['status'] == 'rejected' ? 'fas fa-times-circle' : 'fas fa-clock'); 
              ?>"></i>
              <?php echo ucfirst($post['status']); ?>
            </span></p>
            <p><i class="far fa-clock"></i><strong>Date:</strong> <?php echo date('d M Y, h:i A', strtotime($post['datetime'])); ?></p>
          </div>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <div class="no-posts">
        <i class="far fa-folder-open"></i>
        <h3>No Posts Found</h3>
        <p>There are currently no lost or found items posted.</p>
        <a href="create_post.php" style="display: inline-block; background: var(--primary); color: white; padding: 10px 20px; border-radius: 50px; text-decoration: none; font-weight: 500; margin-top: 10px;">
          <i class="fas fa-plus"></i> Create First Post
        </a>
      </div>
    <?php endif; ?>
  </div>

  <!-- Mobile Navigation -->
  <nav class="mobile-navbar">
    <ul>
      <li><a href="home.php"><i class="fas fa-home"></i><span>Home</span></a></li>
      <li><a href="search.php"><i class="fas fa-search"></i><span>Search</span></a></li>
      <li>
        <a href="#" style="position: relative;">
          <i class="fas fa-comment-dots"></i>
          <?php if ($unread_count > 0): ?>
            <span class="message-badge" style="top: 0; right: 15px;"><?php echo $unread_count; ?></span>
          <?php endif; ?>
          <span>Messages</span>
        </a>
      </li>
      <li><a href="dashboard.php"><i class="fas fa-user"></i><span>Profile</span></a></li>
    </ul>
  </nav>

  <script>
    // Add hover effect to post cards on mobile
    document.addEventListener('touchstart', function() {}, true);
    
    // Animation for when elements come into view
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.style.opacity = "1";
          entry.target.style.transform = "translateY(0)";
        }
      });
    }, { threshold: 0.1 });

    document.querySelectorAll('.post').forEach((post, index) => {
      post.style.opacity = "0";
      post.style.transform = "translateY(20px)";
      post.style.transition = all 0.6s ease ${index * 0.1}s;
      observer.observe(post);
    });

    // Add animation to no posts section if visible
    const noPosts = document.querySelector('.no-posts');
    if (noPosts) {
      noPosts.style.opacity = "0";
      noPosts.style.transform = "translateY(20px)";
      noPosts.style.transition = "all 0.6s ease";
      
      const noPostsObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            entry.target.style.opacity = "1";
            entry.target.style.transform = "translateY(0)";
          }
        });
      });
      
      noPostsObserver.observe(noPosts);
    }
  </script>
</body>
</html>