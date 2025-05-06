<!DOCTYPE html>
<html lang="bg">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Детайли за имота - DreamSpace Realty</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <style>
    .section-padding {
      padding: 60px 0;
    }
    .property-image {
      width: 100%;
      height: 400px;
      object-fit: cover;
    }
    .property-details {
      background-color: #f8f9fa;
      padding: 20px;
      border-radius: 10px;
    }
    .feature-list {
      list-style: none;
      padding: 0;
    }
    .feature-list li {
      margin-bottom: 10px;
    }
    .feature-list li i {
      color: #0d6efd;
      margin-right: 10px;
    }
  </style>
</head>
<body>

<?php
// Връзка с базата данни
$db = new mysqli('localhost', 'root', '', 'real_estate');

if ($db->connect_error) {
    die("Възникна грешка при свързването: " . $db->connect_error);
}

// Получаване на ID на имота от URL
$propertyId = isset($_GET['id']) ? $_GET['id'] : null;

if (!$propertyId) {
    header('Location: index.html');
    exit();
}

// Извличане на детайли за имота
$stmt = $db->prepare("SELECT * FROM properties WHERE id = ?");
$stmt->bind_param("s", $propertyId);
$stmt->execute();
$result = $stmt->get_result();
$property = $result->fetch_assoc();

if (!$property) {
    header('Location: index.html');
    exit();
}

// Извличане на характеристики на имота
$stmt = $db->prepare("SELECT feature FROM property_features WHERE property_id = ?");
$stmt->bind_param("s", $propertyId);
$stmt->execute();
$features_result = $stmt->get_result();
$features = [];
while ($row = $features_result->fetch_assoc()) {
    $features[] = $row['feature'];
}
?>

<!-- Навигация -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
  <div class="container">
    <a class="navbar-brand" href="index.php">DreamSpace Realty</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navMenu">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="index.php#home">Начало</a></li>
        <li class="nav-item"><a class="nav-link" href="index.php#about">За нас</a></li>
        <li class="nav-item"><a class="nav-link" href="index.php#properties">Имоти</a></li>
        <li class="nav-item"><a class="nav-link" href="index.php#renovating">Реновации</a></li>
        <li class="nav-item"><a class="nav-link" href="index.php#contact">Контакт</a></li>
      </ul>
    </div>
  </div>
</nav>

<!-- Секция с детайли за имота -->
<section class="section-padding" style="padding-top: 100px;">
  <div class="container">
    <div class="row">
      <div class="col-md-8">
        <img src="<?php echo htmlspecialchars($property['image_url']); ?>" class="property-image rounded mb-4" alt="<?php echo htmlspecialchars($property['title']); ?>">
        <div class="property-details">
          <h2 class="mb-4"><?php echo htmlspecialchars($property['title']); ?></h2>
          <p class="mb-4"><?php echo htmlspecialchars($property['description']); ?></p>
          <h4>Характеристики</h4>
          <ul class="feature-list">
            <?php foreach ($features as $feature): ?>
              <li><i class="fas fa-check"></i> <?php echo htmlspecialchars($feature); ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card">
          <div class="card-body">
            <h3 class="card-title">Информация за имота</h3>
            <p class="card-text"><strong>Цена:</strong> <?php echo htmlspecialchars($property['price']); ?></p>
            <p class="card-text"><strong>Локация:</strong> <?php echo htmlspecialchars($property['location']); ?></p>
            <p class="card-text"><strong>Размер:</strong> <?php echo htmlspecialchars($property['size']); ?></p>
            <p class="card-text"><strong>Година на строеж:</strong> <?php echo htmlspecialchars($property['year_built']); ?></p>
            <button class="btn btn-primary w-100 mt-3">Запазете оглед</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Footer -->
<footer class="bg-dark text-white text-center py-3">
  &copy; 2025 DreamSpace Realty. Всички права запазени.
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
        const lang = urlParams.get('lang') || localStorage.getItem('selectedLanguage') || 'en';
        
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
            if (element.dataset.translate === 'false' || element.closest('[data-translate="false"]')) {
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
    });
</script>

</body>
</html> 