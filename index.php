<?php
require_once 'db_connection.php';

// Fetch news
$stmt = $pdo->query("SELECT * FROM news ORDER BY created_at DESC LIMIT 6");
$news = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch properties
$stmt = $pdo->query("SELECT * FROM properties ORDER BY created_at DESC LIMIT 6");
$properties = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch renovating services
$stmt = $pdo->query("SELECT * FROM renovating_services ORDER BY created_at DESC");
$services = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="bg">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Недвижими имоти и реновации</title>
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
  </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
  <div class="container">
    <a class="navbar-brand" href="#">DreamSpace Realty</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navMenu">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="#home">Начало</a></li>
        <li class="nav-item"><a class="nav-link" href="#about">За нас</a></li>
        <li class="nav-item"><a class="nav-link" href="#properties">Имоти</a></li>
        <li class="nav-item"><a class="nav-link" href="#renovating">Реновации</a></li>
        <li class="nav-item"><a class="nav-link" href="#contact">Контакт</a></li>
        <li class="nav-item"><a class="nav-link admin-link" href="login.php">Админ</a></li>
      </ul>
    </div>
  </div>
</nav>

<!-- Hero Section -->
<header id="home" class="hero">
  <div class="container">
    <h1>Намерете своя мечтан дом и го реновирайте правилно</h1>
  </div>
</header>

<!-- News Section -->
<section id="news" class="section-padding bg-light">
  <div class="container">
    <h2 class="text-center mb-5">Последни новини</h2>
    <div class="row g-4">
      <?php foreach ($news as $item): ?>
      <div class="col-md-4">
        <div class="card h-100">
          <div class="row g-0">
            <div class="col-4">
              <?php
                $imagePath = 'images/' . htmlspecialchars($item['image_url']);
                if (!empty($item['image_url']) && file_exists($imagePath)):
              ?>
                <img src="<?php echo $imagePath; ?>" class="img-fluid rounded-start" style="width: 100px; height: 100px; object-fit: cover;" alt="<?php echo htmlspecialchars($item['title']); ?>">
              <?php endif; ?>
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
    <h2 class="text-center mb-5">За нас</h2>
    <div class="row mb-5">
      <div class="col-md-12">
        <h4>Какво правим</h4>
        <p>DreamSpace Realty е компания за недвижими имоти и реновации, специализирана в покупка, продажба и трансформация на домове. Ние помагаме на клиентите да намерят идеалния си имот и да го превърнат в мечтаното пространство с персонализирани решения за реновация.</p>
      </div>
    </div>
    <div class="row align-items-center">
      <div class="col-md-4">
        <img src="https://images.unsplash.com/photo-1599423300746-b62533397364" class="img-fluid rounded-circle shadow" alt="Снимка на собственика">
      </div>
      <div class="col-md-8">
        <h4>Запознайте се с Джон Смит</h4>
        <p>С над 15 години опит в недвижимите имоти и строителството, Джон основа DreamSpace Realty с мисията да опрости притежаването на дом и реновациите. Неговата страст към дизайна и обслужването на клиенти направи компанията доверено име в региона.</p>
      </div>
    </div>
  </div>
</section>

<!-- Properties -->
<section id="properties" class="section-padding bg-light">
  <div class="container">
    <h2 class="text-center mb-5">Недвижими имоти</h2>
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
                <?php echo htmlspecialchars($property['bedrooms']); ?> спални · <?php echo htmlspecialchars($property['bathrooms']); ?> бани · 
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
    <h2 class="mb-5">Услуги за реновация</h2>
    <?php foreach ($services as $service): ?>
      <div class="row my-5 align-items-center <?php echo $service['service_type'] === 'bathroom' ? 'flex-md-row-reverse' : ''; ?>">
        <div class="col-md-6">
          <?php
            $imagePath = 'images/' . htmlspecialchars($service['image_name']);
          ?>
        <img src="<?php echo $imagePath; ?>" width="250" height="250" class="img-fluid rounded shadow" alt="<?php echo htmlspecialchars($service['title']); ?>">
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
    <h2 class="text-center mb-5">Свържете се с нас</h2>
    <div class="row justify-content-center">
      <div class="col-md-8">
        <form>
          <div class="mb-3">
            <label for="name" class="form-label">Вашето име</label>
            <input type="text" class="form-control" id="name" placeholder="Иван Иванов">
          </div>
          <div class="mb-3">
            <label for="email" class="form-label">Вашият имейл</label>
            <input type="email" class="form-control" id="email" placeholder="email@example.com">
          </div>
          <div class="mb-3">
            <label for="message" class="form-label">Вашето съобщение</label>
            <textarea class="form-control" id="message" rows="5" placeholder="Как можем да ви помогнем?"></textarea>
          </div>
          <button type="submit" class="btn btn-primary">Изпратете съобщение</button>
        </form>
      </div>
    </div>
  </div>
</section>

<!-- Footer -->
<footer class="bg-dark text-white text-center py-3">
  &copy; <?php echo date('Y'); ?> DreamSpace Realty. Всички права запазени.
</footer>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>