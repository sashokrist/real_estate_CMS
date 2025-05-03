<?php
require_once 'db_connection.php';

// Fetch news
$stmt = $pdo->query("SELECT * FROM news ORDER BY created_at DESC LIMIT 5");
$news = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch properties
$stmt = $pdo->query("SELECT * FROM properties ORDER BY created_at DESC LIMIT 6");
$properties = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch renovating services
$stmt = $pdo->query("SELECT * FROM renovating_services ORDER BY created_at DESC");
$services = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Real Estate & Renovation</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .section-padding {
      padding: 60px 0;
    }
    .hero {
      background: url('https://images.unsplash.com/photo-1568605117036-5fe5e7bab0b7') no-repeat center center;
      height: 100vh;
      color: white;
      display: flex;
      align-items: center;
      justify-content: center;
      text-align: center;
    }
    .hero h1 {
      font-size: 3rem;
      font-weight: bold;
      text-shadow: 2px 2px 8px rgba(0,0,0,0.7);
    }
    .admin-link {
      color: #fff;
      text-decoration: none;
      margin-left: 15px;
    }
    .language-selector {
      margin-left: 15px;
    }
    .language-selector select {
      background-color: transparent;
      color: white;
      border: 1px solid white;
      padding: 5px 10px;
      border-radius: 4px;
      cursor: pointer;
    }
    .language-selector select option {
      background-color: #343a40;
      color: white;
    }
    #google_translate_element {
      display: none;
    }
    .goog-te-banner-frame {
      display: none !important;
    }
    .goog-te-menu-value:hover {
      text-decoration: none !important;
    }
    .goog-te-gadget {
      color: transparent !important;
    }
  </style>
  <!-- Add Google Translate Script -->
  <script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
</head>
<body>

<!-- Google Translate Element -->
<div id="google_translate_element"></div>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top" data-translate="false">
  <div class="container">
    <a class="navbar-brand" href="#" data-translate="false">DreamSpace Realty</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu" data-translate="false">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navMenu" data-translate="false">
      <ul class="navbar-nav ms-auto" data-translate="false">
        <li class="nav-item" data-translate="false"><a class="nav-link" href="#home">Home</a></li>
        <li class="nav-item" data-translate="false"><a class="nav-link" href="#about">About Us</a></li>
        <li class="nav-item" data-translate="false"><a class="nav-link" href="#properties">Properties</a></li>
        <li class="nav-item" data-translate="false"><a class="nav-link" href="#renovating">Renovating</a></li>
        <li class="nav-item" data-translate="false"><a class="nav-link" href="#contact">Contact</a></li>
        <li class="nav-item" data-translate="false"><a class="nav-link admin-link" href="login.php">Admin</a></li>
        <li class="nav-item language-selector" data-translate="false">
          <select id="languageSelect" onchange="changeLanguage(this.value)" data-translate="false">
            <option value="en" data-translate="false">English</option>
            <option value="bg" data-translate="false">Български</option>
          </select>
        </li>
      </ul>
    </div>
  </div>
</nav>

<!-- Hero Section -->
<header id="home" class="hero">
  <div class="container">
    <h1>Find Your Dream Home & Renovate It Right</h1>
  </div>
</header>

<!-- News Section -->
<section id="news" class="section-padding bg-light">
  <div class="container">
    <h2 class="text-center mb-5">Latest News</h2>
    <div class="row g-4">
      <?php foreach ($news as $item): ?>
      <div class="col-md-4">
        <div class="card h-100">
          <div class="row g-0">
            <div class="col-4">
              <img src="<?php echo htmlspecialchars($item['image_url']); ?>" class="img-fluid rounded-start" style="width: 100px; height: 100px; object-fit: cover;" alt="News">
            </div>
            <div class="col-8">
              <div class="card-body">
                <h5 class="card-title"><?php echo htmlspecialchars($item['title']); ?></h5>
                <p class="card-text"><?php echo htmlspecialchars($item['content']); ?></p>
              </div>
            </div>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- About Us -->
<section id="about" class="section-padding">
  <div class="container">
    <h2 class="text-center mb-5">About Us</h2>
    <div class="row mb-5">
      <div class="col-md-12">
        <h4>What We Do</h4>
        <p>DreamSpace Realty is a full-service real estate and renovation company specializing in buying, selling, and transforming homes. We help clients find their ideal property and turn it into their dream space with tailored renovation solutions.</p>
      </div>
    </div>
    <div class="row align-items-center">
      <div class="col-md-4">
        <img src="https://images.unsplash.com/photo-1599423300746-b62533397364" class="img-fluid rounded-circle shadow" alt="Owner Photo">
      </div>
      <div class="col-md-8">
        <h4>Meet John Smith</h4>
        <p>With over 15 years in real estate and construction, John founded DreamSpace Realty with the mission to simplify homeownership and renovation. His passion for design and customer service has made the company a trusted name in the region.</p>
      </div>
    </div>
  </div>
</section>

<!-- Properties -->
<section id="properties" class="section-padding bg-light">
  <div class="container">
    <h2 class="text-center mb-5">Real Estate Properties</h2>
    <div class="row g-4">
      <?php foreach ($properties as $property): ?>
      <div class="col-md-4">
        <div class="card h-100 property-card" style="cursor: pointer;" onclick="window.location.href='property-detail.php?id=<?php echo htmlspecialchars($property['id']); ?>'">
          <img src="<?php echo htmlspecialchars($property['image_url']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($property['title']); ?>">
          <div class="card-body">
            <h5 class="card-title"><?php echo htmlspecialchars($property['title']); ?></h5>
            <p class="card-text"><?php echo htmlspecialchars($property['description']); ?></p>
            <p class="card-text">
              <small class="text-muted">
                <?php echo htmlspecialchars($property['bedrooms']); ?> bed · <?php echo htmlspecialchars($property['bathrooms']); ?> bath · 
                $<?php echo number_format($property['price'], 2); ?>
              </small>
            </p>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Renovating -->
<section id="renovating" class="section-padding">
  <div class="container text-center">
    <h2 class="mb-5">Renovating Services</h2>
    <?php foreach ($services as $service): ?>
    <div class="row my-5 align-items-center <?php echo $service['service_type'] === 'bathroom' ? 'flex-md-row-reverse' : ''; ?>">
      <div class="col-md-6">
        <img src="<?php echo htmlspecialchars($service['image_url']); ?>" class="img-fluid rounded shadow" alt="<?php echo htmlspecialchars($service['title']); ?>">
      </div>
      <div class="col-md-6 text-start">
        <h4 class="mb-3"><?php echo htmlspecialchars($service['title']); ?></h4>
        <p><?php echo htmlspecialchars($service['description']); ?></p>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
</section>

<!-- Contact -->
<section id="contact" class="section-padding bg-light">
  <div class="container">
    <h2 class="text-center mb-5">Contact Us</h2>
    <div class="row justify-content-center">
      <div class="col-md-8">
        <form>
          <div class="mb-3">
            <label for="name" class="form-label">Your Name</label>
            <input type="text" class="form-control" id="name" placeholder="John Doe">
          </div>
          <div class="mb-3">
            <label for="email" class="form-label">Your Email</label>
            <input type="email" class="form-control" id="email" placeholder="email@example.com">
          </div>
          <div class="mb-3">
            <label for="message" class="form-label">Your Message</label>
            <textarea class="form-control" id="message" rows="5" placeholder="How can we help you?"></textarea>
          </div>
          <button type="submit" class="btn btn-primary">Send Message</button>
        </form>
      </div>
    </div>
  </div>
</section>

<!-- Admin Section -->
<section id="admin" class="section-padding admin-section" style="display: none;">
  <div class="container">
    <h2 class="text-center mb-5">Admin Panel</h2>
    <div class="row justify-content-center">
      <div class="col-md-8">
        <form id="newsForm" class="bg-light p-4 rounded">
          <div class="mb-3">
            <label for="newsTitle" class="form-label">News Title</label>
            <input type="text" class="form-control" id="newsTitle" required>
          </div>
          <div class="mb-3">
            <label for="newsImage" class="form-label">Image URL</label>
            <input type="url" class="form-control" id="newsImage" required>
          </div>
          <div class="mb-3">
            <label for="newsContent" class="form-label">News Content (max 50 words)</label>
            <textarea class="form-control" id="newsContent" rows="3" maxlength="250" required></textarea>
            <small class="text-muted">Maximum 50 words</small>
          </div>
          <button type="submit" class="btn btn-primary">Add News</button>
        </form>
      </div>
    </div>
  </div>
</section>

<!-- Footer -->
<footer class="bg-dark text-white text-center py-3">
  &copy; <?php echo date('Y'); ?> DreamSpace Realty. All rights reserved.
</footer>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // Function to change language
  function changeLanguage(lang) {
    // Store the selected language in localStorage
    localStorage.setItem('selectedLanguage', lang);
    
    // Always redirect to index.php with the language parameter
    window.location.href = 'index.php?lang=' + lang;
  }

  // Function to set initial language
  function setInitialLanguage() {
    // Get language from URL parameter or localStorage
    const urlParams = new URLSearchParams(window.location.search);
    const lang = urlParams.get('lang') || localStorage.getItem('selectedLanguage') || 'bg';
    
    // Set the select value
    const languageSelect = document.getElementById('languageSelect');
    if (languageSelect) {
      languageSelect.value = lang;
    }
    
    // If language is not English, translate the page
    if (lang !== 'en') {
      translatePage(lang);
    }
  }

  // Function to translate the page
  function translatePage(targetLang) {
    const elements = document.querySelectorAll('h1, h2, h3, h4, h5, p, a, button, span, li');
    elements.forEach(element => {
      // Skip elements with data-translate="false" or their children
      if (element.dataset.translate === 'false') {
        return;
      }
      
      // Skip language selector and its children
      if (element.closest('.language-selector')) {
        return;
      }
      
      // Skip the navbar structure but allow menu items to be translated
      if (element.closest('.navbar')) {
        // Only translate the text content of menu items
        if (element.classList.contains('nav-link') && !element.closest('.language-selector')) {
          const originalText = element.getAttribute('data-original-text') || element.textContent;
          element.setAttribute('data-original-text', originalText);
          
          // Use Google Translate API
          const url = `https://translate.googleapis.com/translate_a/single?client=gtx&sl=en&tl=${targetLang}&dt=t&q=${encodeURIComponent(originalText)}`;
          
          fetch(url)
            .then(response => response.json())
            .then(data => {
              if (data && data[0] && data[0][0] && data[0][0][0]) {
                element.textContent = data[0][0][0];
              }
            })
            .catch(error => console.error('Translation error:', error));
        }
        return;
      }
      
      const originalText = element.getAttribute('data-original-text') || element.textContent;
      element.setAttribute('data-original-text', originalText);
      
      // Use Google Translate API
      const url = `https://translate.googleapis.com/translate_a/single?client=gtx&sl=en&tl=${targetLang}&dt=t&q=${encodeURIComponent(originalText)}`;
      
      fetch(url)
        .then(response => response.json())
        .then(data => {
          if (data && data[0] && data[0][0] && data[0][0][0]) {
            element.textContent = data[0][0][0];
          }
        })
        .catch(error => console.error('Translation error:', error));
    });
  }

  // Initialize language when page loads
  document.addEventListener('DOMContentLoaded', function() {
    setInitialLanguage();
    
    // Ensure navbar is visible
    const navbar = document.querySelector('.navbar');
    if (navbar) {
      navbar.style.display = 'block';
    }
    
    // Ensure language selector is visible
    const languageSelector = document.querySelector('.language-selector');
    if (languageSelector) {
      languageSelector.style.display = 'block';
    }

    // Admin section toggle
    document.getElementById('adminLink').addEventListener('click', function(e) {
      e.preventDefault();
      const adminSection = document.getElementById('admin');
      if (adminSection.style.display === 'none' || adminSection.style.display === '') {
        adminSection.style.display = 'block';
      } else {
        adminSection.style.display = 'none';
      }
    });
  });
</script>
</body>
</html> 