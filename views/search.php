<?php
include '../model/db.php';

$searchTerm = '';
$results = [];

if (isset($_GET['q'])) {
    $searchTerm = trim($_GET['q']);

    if (!empty($searchTerm)) {
        $stmt = $conn->prepare("SELECT posts.*, users.name, users.profile_photo, users.id AS user_id 
        FROM posts 
        JOIN users ON posts.user_id = users.id
        WHERE posts.title LIKE ? 
           OR posts.description LIKE ? 
           OR posts.location LIKE ? 
           OR users.name LIKE ?
        ORDER BY posts.datetime DESC");

        $likeTerm = "%" . $searchTerm . "%";
        $stmt->bind_param("ssss", $likeTerm, $likeTerm, $likeTerm, $likeTerm);

        $stmt->execute();
        $result = $stmt->get_result();
        $results = $result->fetch_all(MYSQLI_ASSOC);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Search Results | Lost & Found</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
    background: linear-gradient(-45deg, #ee7752, #e73c7e, #23a6d5, #23d5ab);
    background-size: 400% 400%;
    animation: gradientBG 15s ease infinite;
    min-height: 100vh;
    padding: 0;
    margin: 0;
    color: var(--white);
  }

  @keyframes gradientBG {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
  }

  .floating-particles {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    overflow: hidden;
    z-index: -1;
  }

  .particle {
    position: absolute;
    width: 10px;
    height: 10px;
    background-color: rgba(255, 255, 255, 0.5);
    border-radius: 50%;
    animation: float 15s infinite linear;
  }

  @keyframes float {
    0% {
      transform: translateY(0) translateX(0);
      opacity: 1;
    }
    100% {
      transform: translateY(-1000px) translateX(1000px);
      opacity: 0;
    }
  }

  .container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 30px 20px;
    position: relative;
    z-index: 1;
  }

  .search-header {
    text-align: center;
    margin-bottom: 40px;
    animation: fadeInDown 0.8s ease;
  }

  @keyframes fadeInDown {
    from {
      opacity: 0;
      transform: translateY(-30px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  .search-header h1 {
    font-size: 2.5rem;
    margin-bottom: 20px;
    color: var(--white);
    text-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
  }

  .search-box {
    display: flex;
    max-width: 700px;
    margin: 0 auto;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    border-radius: 50px;
    overflow: hidden;
    animation: scaleIn 0.8s ease;
  }

  @keyframes scaleIn {
    from {
      transform: scale(0.9);
      opacity: 0;
    }
    to {
      transform: scale(1);
      opacity: 1;
    }
  }

  .search-box input[type="text"] {
    flex: 1;
    padding: 18px 25px;
    border: none;
    font-size: 1rem;
    outline: none;
    background-color: rgba(255, 255, 255, 0.9);
  }

  .search-box button {
    padding: 0 30px;
    border: none;
    background-color: var(--primary);
    color: white;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .search-box button:hover {
    background-color: var(--secondary);
  }

  .search-box button i {
    font-size: 1.1rem;
  }

  .results-container {
    background-color: rgba(255, 255, 255, 0.9);
    border-radius: 20px;
    padding: 30px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
    backdrop-filter: blur(5px);
  }

  .results-count {
    font-size: 1.1rem;
    margin-bottom: 25px;
    color: var(--dark);
    font-weight: 500;
    padding-bottom: 10px;
    border-bottom: 1px solid rgba(0, 0, 0, 0.1);
  }

  .results-count strong {
    color: var(--primary);
  }

  .results-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 25px;
  }

  .post-card {
    background-color: var(--white);
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
    transform: translateY(0);
    opacity: 0;
    animation: fadeInUp 0.6s ease forwards;
  }

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

  .post-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
  }

  .post-image {
    position: relative;
    height: 220px;
    overflow: hidden;
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

  .post-card:hover .post-image img {
    transform: scale(1.05);
  }

  .post-content {
    padding: 20px;
  }

  .user-info {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 15px;
  }

  .user-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid rgba(67, 97, 238, 0.2);
    transition: all 0.3s ease;
  }

  .user-info:hover .user-avatar {
    transform: scale(1.1);
    border-color: var(--primary);
  }

  .user-name {
    font-weight: 600;
    color: var(--dark);
    text-decoration: none;
    transition: color 0.3s ease;
  }

  .user-name:hover {
    color: var(--primary);
  }

  .post-title {
    font-size: 1.3rem;
    margin-bottom: 12px;
    color: var(--dark);
    font-weight: 600;
  }

  .post-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    margin-bottom: 15px;
  }

  .meta-item {
    display: flex;
    align-items: center;
    gap: 5px;
    font-size: 0.9rem;
    color: var(--gray);
  }

  .meta-item i {
    color: var(--primary);
    font-size: 0.9rem;
  }

  .status {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
  }

  .status i {
    font-size: 0.8rem;
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

  .post-description {
    color: var(--gray);
    font-size: 0.95rem;
    line-height: 1.5;
    margin-top: 15px;
  }

  .post-date {
    display: flex;
    align-items: center;
    gap: 5px;
    color: var(--gray);
    font-size: 0.85rem;
    margin-top: 15px;
  }

  .post-date i {
    color: var(--primary);
    font-size: 0.9rem;
  }

  .no-results {
    text-align: center;
    padding: 50px 20px;
    background-color: var(--white);
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
  }

  .no-results i {
    font-size: 3rem;
    color: #ddd;
    margin-bottom: 20px;
  }

  .no-results h3 {
    font-size: 1.5rem;
    color: var(--dark);
    margin-bottom: 10px;
  }

  .no-results p {
    font-size: 1rem;
    color: var(--gray);
    margin-bottom: 20px;
  }

  .no-results p strong {
    color: var(--primary);
  }

  /* Responsive adjustments */
  @media (max-width: 768px) {
    .search-header h1 {
      font-size: 2rem;
    }

    .search-box {
      flex-direction: column;
      border-radius: 15px;
    }

    .search-box input[type="text"] {
      padding: 15px 20px;
    }

    .search-box button {
      padding: 15px;
      justify-content: center;
    }

    .results-grid {
      grid-template-columns: 1fr;
    }

    .container {
      padding: 20px 15px;
    }
  }

  /* Animation delays for posts */
  .post-card:nth-child(1) { animation-delay: 0.1s; }
  .post-card:nth-child(2) { animation-delay: 0.2s; }
  .post-card:nth-child(3) { animation-delay: 0.3s; }
  .post-card:nth-child(4) { animation-delay: 0.4s; }
  .post-card:nth-child(5) { animation-delay: 0.5s; }
  .post-card:nth-child(6) { animation-delay: 0.6s; }
  </style>
</head>
<body>
  <!-- Animated Background Particles -->
  <div class="floating-particles" id="particles"></div>

  <div class="container">
    <div class="search-header">
      <h1>Find Lost & Found Items</h1>
      <form method="GET" action="" class="search-box">
        <input type="text" name="q" placeholder="Search for items, locations, or people..." 
               value="<?php echo htmlspecialchars($searchTerm); ?>" autocomplete="off">
        <button type="submit"><i class="fas fa-search"></i> Search</button>
      </form>
    </div>

    <div class="results-container">
      <?php if (!empty($results)): ?>
        <div class="results-count">
          Found <strong><?php echo count($results); ?></strong> result<?php echo count($results) !== 1 ? 's' : ''; ?> 
          for "<strong><?php echo htmlspecialchars($searchTerm); ?></strong>"
        </div>

        <div class="results-grid">
          <?php foreach ($results as $post): ?>
            <div class="post-card">
              <div class="post-image">
                <img src="../controller/uploads/<?php echo htmlspecialchars($post['image']); ?>" alt="Post Image">
              </div>

              <div class="post-content">
                <div class="user-info">
                  <a href="profile.php?user_id=<?php echo $post['user_id']; ?>">
                    <img src="../controller/uploads/<?php echo htmlspecialchars($post['profile_photo']); ?>" 
                         alt="Profile" class="user-avatar">
                  </a>
                  <a href="profile.php?user_id=<?php echo $post['user_id']; ?>" class="user-name">
                    <?php echo htmlspecialchars($post['name']); ?>
                  </a>
                </div>

                <h3 class="post-title"><?php echo htmlspecialchars($post['title']); ?></h3>

                <div class="post-meta">
                  <div class="meta-item">
                    <i class="fas fa-tag"></i>
                    <?php echo ucfirst($post['type']); ?>
                  </div>
                  <div class="meta-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <?php echo $post['location']; ?>
                  </div>
                  <div class="meta-item status <?php echo $post['status']; ?>">
                    <i class="<?php 
                      echo $post['status'] == 'approved' ? 'fas fa-check-circle' : 
                           ($post['status'] == 'rejected' ? 'fas fa-times-circle' : 'fas fa-clock'); 
                    ?>"></i>
                    <?php echo ucfirst($post['status']); ?>
                  </div>
                </div>

                <p class="post-description"><?php echo htmlspecialchars($post['description']); ?></p>

                <div class="post-date">
                  <i class="far fa-clock"></i>
                  <?php echo date('d M Y, h:i A', strtotime($post['datetime'])); ?>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php elseif ($searchTerm): ?>
        <div class="no-results">
          <i class="far fa-folder-open"></i>
          <h3>No Results Found</h3>
          <p>We couldn't find any matches for "<strong><?php echo htmlspecialchars($searchTerm); ?></strong>"</p>
          <p>Try different keywords or check your spelling</p>
        </div>
      <?php else: ?>
        <div class="no-results">
          <i class="fas fa-search"></i>
          <h3>What are you looking for?</h3>
          <p>Search for lost or found items by title, description, location, or user</p>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <script>
    // Create floating particles for background
    function createParticles() {
      const container = document.getElementById('particles');
      const particleCount = window.innerWidth < 768 ? 20 : 40;
      
      for (let i = 0; i < particleCount; i++) {
        const particle = document.createElement('div');
        particle.classList.add('particle');
        
        // Random position
        const posX = Math.random() * 100;
        const posY = Math.random() * 100;
        
        // Random size
        const size = Math.random() * 5 + 3;
        
        // Random animation duration
        const duration = Math.random() * 20 + 10;
        
        // Random delay
        const delay = Math.random() * 5;
        
        particle.style.left = ${posX}%;
        particle.style.top = ${posY}%;
        particle.style.width = ${size}px;
        particle.style.height = ${size}px;
        particle.style.animationDuration = ${duration}s;
        particle.style.animationDelay = ${delay}s;
        particle.style.opacity = Math.random() * 0.5 + 0.1;
        
        container.appendChild(particle);
      }
    }
    
    // Initialize particles when page loads
    window.addEventListener('load', createParticles);
    
    // Recreate particles on resize
    window.addEventListener('resize', function() {
      const container = document.getElementById('particles');
      container.innerHTML = '';
      createParticles();
    });
    
    // Focus search input on page load if there's a search term
    document.addEventListener('DOMContentLoaded', function() {
      const searchInput = document.querySelector('input[name="q"]');
      if (searchInput.value) {
        searchInput.select();
      }
    });
  </script>
</body>
</html>