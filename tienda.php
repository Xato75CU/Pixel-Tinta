<?php
// Betty's Scent - La experiencia olfativa definitiva
session_start();

// Configuración
$whatsapp_number = '+5358363310';
$business_name = "Betty's Scent";
$business_location = "Santa Clara, Villa Clara, Cuba";

// Productos disponibles
$products = [
    1 => [
        'id' => 1,
        'name' => 'El Impacto',
        'price' => 2500,
        'description' => 'Una fragancia intensa y duradera que deja una impresión imborrable. Notas de bergamota, pimienta rosa y ámbar que evocan poder y elegancia.',
        'icon' => '✨',
        'category' => 'Best Sellers'
    ],
    2 => [
        'id' => 2,
        'name' => 'Mariposa',
        'price' => 2200,
        'description' => 'Frescura floral con notas de jazmín, peonía y musk blanco. Perfecto para el día a día, evoca la ligereza y belleza de una mariposa en primavera.',
        'icon' => '🦋',
        'category' => 'Mujer'
    ],
    3 => [
        'id' => 3,
        'name' => 'El Camerata',
        'price' => 2800,
        'description' => 'Elegancia clásica con notas de vainilla, almizcle y maderas preciosas. Una fragancia orquestada para ocasiones especiales que requieren sofisticación.',
        'icon' => '🎻',
        'category' => 'Hombre'
    ],
    4 => [
        'id' => 4,
        'name' => 'Voltaje',
        'price' => 2400,
        'description' => 'Energía y vitalidad con notas cítricas, jengibre y maderas cálidas. Para quienes marcan la diferencia y viven con intensidad.',
        'icon' => '⚡',
        'category' => 'Novedades'
    ]
];

// Inicializar carrito si no existe
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Función para añadir al carrito
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $product_id = (int)$_POST['product_id'];
    if (isset($products[$product_id])) {
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id]['quantity']++;
        } else {
            $_SESSION['cart'][$product_id] = [
                'id' => $product_id,
                'name' => $products[$product_id]['name'],
                'price' => $products[$product_id]['price'],
                'quantity' => 1
            ];
        }
        $message = "¡{$products[$product_id]['name']} añadido al carrito!";
        $message_type = "success";
    }
}

// Función para actualizar cantidad
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_quantity'])) {
    $product_id = (int)$_POST['product_id'];
    $quantity = (int)$_POST['quantity'];
    if (isset($_SESSION['cart'][$product_id]) && $quantity > 0) {
        $_SESSION['cart'][$product_id]['quantity'] = $quantity;
    } elseif (isset($_SESSION['cart'][$product_id]) && $quantity <= 0) {
        unset($_SESSION['cart'][$product_id]);
    }
}

// Función para eliminar del carrito
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_from_cart'])) {
    $product_id = (int)$_POST['product_id'];
    if (isset($_SESSION['cart'][$product_id])) {
        unset($_SESSION['cart'][$product_id]);
        $message = "Producto eliminado del carrito";
        $message_type = "info";
    }
}

// Calcular total del carrito
$cart_total = 0;
$cart_count = 0;
foreach ($_SESSION['cart'] as $item) {
    $cart_total += $item['price'] * $item['quantity'];
    $cart_count += $item['quantity'];
}

// Procesar pedido y redirigir a WhatsApp
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['process_order'])) {
    $name = trim($_POST['name'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    
    if (empty($name) || empty($address)) {
        $error = "Por favor, completa todos los campos obligatorios.";
    } elseif (empty($_SESSION['cart'])) {
        $error = "Tu carrito está vacío. Añade productos antes de enviar el pedido.";
    } else {
        // Construir mensaje para WhatsApp
        $message = "¡Hola! Quisiera hacer un pedido en {$business_name}:\n\n";
        
        foreach ($_SESSION['cart'] as $item) {
            $message .= "• {$item['name']} x{$item['quantity']} - " . number_format($item['price'], 0, ',', '.') . " CUP c/u\n";
        }
        
        $message .= "\nTotal: " . number_format($cart_total, 0, ',', '.') . " CUP\n\n";
        $message .= "Cliente: {$name}\n";
        $message .= "Dirección: {$address}\n";
        
        if (!empty($phone)) {
            $message .= "Teléfono: {$phone}\n";
        }
        
        $message .= "\nPor favor, confirma mi pedido y los detalles de entrega. ¡Gracias!";
        
        // Redirigir a WhatsApp
        $whatsapp_url = "https://wa.me/{$whatsapp_number}?text=" . urlencode($message);
        header("Location: {$whatsapp_url}");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Betty's Scent | Perfumes que Cuentan Historias</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600;700&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">
  <style>
    /* Reset y configuración base */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    :root {
      --primary: #1a140f;
      --secondary: #c9a981;
      --gold: #d4af37;
      --light: #f9f6f2;
      --dark: #0a0806;
      --gray: #6b7280;
      --transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
      --shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.2);
    }

    body {
      font-family: 'Montserrat', sans-serif;
      background: var(--light);
      color: var(--primary);
      line-height: 1.7;
      overflow-x: hidden;
    }

    h1, h2, h3, h4, h5, h6 {
      font-family: 'Cormorant Garamond', serif;
      font-weight: 600;
    }

    .container {
      max-width: 1400px;
      margin: 0 auto;
      padding: 0 2rem;
    }

    /* Header */
    header {
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(10px);
      position: sticky;
      top: 0;
      z-index: 1000;
      box-shadow: 0 2px 20px rgba(0, 0, 0, 0.08);
    }

    .header-content {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 1.2rem 0;
    }

    .logo {
      font-size: 2.2rem;
      font-weight: 700;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .logo-icon {
      color: var(--gold);
    }

    .cart-icon {
      position: relative;
      font-size: 1.8rem;
      color: var(--primary);
      cursor: pointer;
    }

    .cart-count {
      position: absolute;
      top: -8px;
      right: -8px;
      background: var(--gold);
      color: var(--dark);
      font-size: 0.75rem;
      width: 22px;
      height: 22px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 700;
    }

    /* Hero */
    .hero {
      height: 90vh;
      display: flex;
      align-items: center;
      background: linear-gradient(rgba(26, 20, 15, 0.85), rgba(26, 20, 15, 0.9)), url('https://images.unsplash.com/photo-1523050854058-8df90110c9f1?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80');
      background-size: cover;
      background-position: center;
      color: white;
      position: relative;
      overflow: hidden;
    }

    .hero::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: radial-gradient(circle at 10% 20%, rgba(212, 175, 55, 0.15), transparent 40%);
    }

    .hero-content {
      max-width: 700px;
      z-index: 2;
    }

    .hero h1 {
      font-size: 4.8rem;
      line-height: 1.1;
      margin-bottom: 1.5rem;
      letter-spacing: -1px;
      text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
    }

    .hero p {
      font-size: 1.4rem;
      opacity: 0.95;
      margin-bottom: 2.5rem;
      max-width: 600px;
    }

    .btn {
      display: inline-block;
      padding: 1rem 2.8rem;
      background: var(--gold);
      color: var(--dark);
      font-weight: 700;
      border-radius: 50px;
      text-transform: uppercase;
      letter-spacing: 1.5px;
      font-size: 0.95rem;
      transition: var(--transition);
      border: none;
      cursor: pointer;
      box-shadow: 0 8px 25px rgba(212, 175, 55, 0.4);
    }

    .btn:hover {
      transform: translateY(-5px);
      box-shadow: 0 12px 35px rgba(212, 175, 55, 0.6);
    }

    /* Secciones */
    .section {
      padding: 6rem 0;
    }

    .section-title {
      text-align: center;
      font-size: 2.8rem;
      margin-bottom: 1.5rem;
      color: var(--primary);
      position: relative;
    }

    .section-title::after {
      content: '';
      position: absolute;
      bottom: -15px;
      left: 50%;
      transform: translateX(-50%);
      width: 80px;
      height: 3px;
      background: var(--gold);
    }

    .section-subtitle {
      text-align: center;
      max-width: 700px;
      margin: 0 auto 4rem;
      color: var(--gray);
      font-size: 1.2rem;
    }

    /* Colección */
    .collection-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 2.5rem;
    }

    .product-card {
      background: white;
      border-radius: 20px;
      overflow: hidden;
      box-shadow: var(--shadow);
      transition: var(--transition);
      position: relative;
    }

    .product-card:hover {
      transform: translateY(-12px);
      box-shadow: 0 20px 50px -15px rgba(0, 0, 0, 0.25);
    }

    .product-badge {
      position: absolute;
      top: 15px;
      left: 15px;
      background: var(--gold);
      color: var(--dark);
      padding: 0.3rem 0.8rem;
      border-radius: 20px;
      font-size: 0.85rem;
      font-weight: 700;
      z-index: 2;
    }

    .product-img {
      height: 300px;
      display: flex;
      align-items: center;
      justify-content: center;
      background: linear-gradient(135deg, #f0e6e1 0%, #e8d9d0 100%);
      font-size: 5rem;
      color: rgba(212, 175, 55, 0.7);
    }

    .product-info {
      padding: 2rem;
    }

    .product-name {
      font-size: 1.6rem;
      margin-bottom: 0.8rem;
    }

    .product-desc {
      color: var(--gray);
      margin-bottom: 1.5rem;
      min-height: 90px;
    }

    .product-price {
      font-size: 1.8rem;
      font-weight: 700;
      color: var(--primary);
      margin-bottom: 1.5rem;
    }

    .add-to-cart-form {
      display: flex;
      gap: 1rem;
    }

    .add-to-cart-btn {
      flex: 1;
      padding: 0.9rem;
      background: var(--primary);
      color: white;
      border: none;
      border-radius: 10px;
      font-weight: 600;
      cursor: pointer;
      transition: var(--transition);
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.5rem;
    }

    .add-to-cart-btn:hover {
      background: var(--gold);
      color: var(--dark);
    }

    /* Testimonios */
    .testimonials {
      background: var(--primary);
      color: white;
    }

    .testimonial-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 2.5rem;
    }

    .testimonial-card {
      background: rgba(255, 255, 255, 0.08);
      padding: 2.5rem;
      border-radius: 20px;
      backdrop-filter: blur(10px);
      border: 1px solid rgba(212, 175, 55, 0.2);
    }

    .testimonial-text {
      font-style: italic;
      margin-bottom: 1.5rem;
      line-height: 1.8;
      position: relative;
    }

    .testimonial-text::before,
    .testimonial-text::after {
      content: '"';
      font-size: 3rem;
      opacity: 0.2;
      position: absolute;
      color: var(--gold);
    }

    .testimonial-text::before {
      top: -20px;
      left: -15px;
    }

    .testimonial-text::after {
      bottom: -40px;
      right: -15px;
    }

    .testimonial-author {
      font-weight: 700;
      color: var(--gold);
      font-size: 1.1rem;
    }

    /* Carrito lateral */
    .cart-overlay {
      position: fixed;
      top: 0;
      right: -500px;
      width: 500px;
      height: 100vh;
      background: white;
      box-shadow: -5px 0 30px rgba(0, 0, 0, 0.15);
      z-index: 2000;
      transition: var(--transition);
      padding: 2.5rem;
      display: flex;
      flex-direction: column;
    }

    .cart-overlay.active {
      right: 0;
    }

    .cart-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 2.5rem;
      padding-bottom: 1.5rem;
      border-bottom: 2px solid #eee;
    }

    .cart-title {
      font-size: 2rem;
    }

    .close-cart {
      background: none;
      border: none;
      font-size: 1.8rem;
      cursor: pointer;
      color: var(--gray);
    }

    .cart-items {
      flex: 1;
      overflow-y: auto;
      padding-right: 1rem;
    }

    .cart-item {
      display: flex;
      justify-content: space-between;
      padding: 1.5rem 0;
      border-bottom: 1px solid #eee;
    }

    .cart-item-details {
      flex: 1;
    }

    .cart-item-name {
      font-weight: 600;
      margin-bottom: 0.5rem;
      font-size: 1.1rem;
    }

    .cart-item-price {
      font-weight: 700;
      color: var(--primary);
    }

    .cart-item-controls {
      display: flex;
      align-items: center;
      gap: 15px;
    }

    .quantity-btn {
      width: 32px;
      height: 32px;
      border-radius: 8px;
      background: #f0f0f0;
      border: 1px solid #ddd;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 600;
      cursor: pointer;
      transition: var(--transition);
    }

    .quantity-btn:hover {
      background: #e0e0e0;
    }

    .cart-item-quantity {
      min-width: 32px;
      text-align: center;
      font-weight: 600;
    }

    .remove-item {
      color: #ef4444;
      background: none;
      border: none;
      cursor: pointer;
      font-size: 1.2rem;
    }

    .cart-footer {
      padding-top: 2rem;
      border-top: 2px solid #eee;
    }

    .cart-total {
      display: flex;
      justify-content: space-between;
      font-size: 1.5rem;
      font-weight: 700;
      margin-bottom: 2rem;
    }

    .checkout-btn {
      width: 100%;
      padding: 1.2rem;
      background: #25D366;
      color: white;
      border: none;
      border-radius: 12px;
      font-weight: 700;
      font-size: 1.2rem;
      cursor: pointer;
      transition: var(--transition);
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 1rem;
    }

    .checkout-btn:hover {
      background: #128C7E;
      transform: translateY(-3px);
    }

    /* Formulario de pedido */
    .order-form {
      background: white;
      padding: 3rem;
      border-radius: 20px;
      box-shadow: var(--shadow);
    }

    .form-title {
      text-align: center;
      font-size: 2rem;
      margin-bottom: 2rem;
      color: var(--primary);
    }

    .form-grid {
      display: grid;
      gap: 1.5rem;
    }

    .form-group {
      margin-bottom: 1rem;
    }

    .form-group label {
      display: block;
      margin-bottom: 0.5rem;
      font-weight: 600;
      color: var(--primary);
    }

    .form-input {
      width: 100%;
      padding: 1rem 1.2rem;
      border: 1px solid #ddd;
      border-radius: 10px;
      font-family: 'Montserrat', sans-serif;
      font-size: 1rem;
    }

    .form-input:focus {
      outline: none;
      border-color: var(--gold);
      box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.2);
    }

    .submit-order-btn {
      width: 100%;
      padding: 1.2rem;
      background: var(--gold);
      color: var(--dark);
      border: none;
      border-radius: 12px;
      font-weight: 700;
      font-size: 1.2rem;
      cursor: pointer;
      transition: var(--transition);
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 1rem;
    }

    .submit-order-btn:hover {
      background: #b8996c;
      transform: translateY(-3px);
    }

    /* Footer */
    footer {
      background: var(--dark);
      color: rgba(255, 255, 255, 0.8);
      padding: 4rem 0 2rem;
    }

    .footer-content {
      display: flex;
      justify-content: center;
      gap: 4rem;
      margin-bottom: 2.5rem;
    }

    .footer-col h3 {
      color: white;
      margin-bottom: 1.5rem;
      font-size: 1.4rem;
    }

    .footer-links {
      list-style: none;
    }

    .footer-links li {
      margin-bottom: 0.8rem;
    }

    .footer-links a {
      color: rgba(255, 255, 255, 0.7);
      transition: var(--transition);
    }

    .footer-links a:hover {
      color: var(--gold);
    }

    .copyright {
      text-align: center;
      padding-top: 1.5rem;
      border-top: 1px solid rgba(255, 255, 255, 0.1);
      font-size: 0.95rem;
      color: rgba(255, 255, 255, 0.6);
    }

    /* Notificaciones */
    .notification {
      position: fixed;
      top: 20px;
      right: 20px;
      background: white;
      color: var(--primary);
      padding: 1rem 1.5rem;
      border-radius: 12px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
      z-index: 3000;
      display: flex;
      align-items: center;
      gap: 1rem;
      transform: translateX(150%);
      transition: transform 0.4s ease;
    }

    .notification.show {
      transform: translateX(0);
    }

    .notification.success {
      border-left: 4px solid #10b981;
    }

    .notification.error {
      border-left: 4px solid #ef4444;
    }

    .notification.info {
      border-left: 4px solid var(--gold);
    }

    /* WhatsApp flotante */
    .whatsapp-float {
      position: fixed;
      bottom: 25px;
      right: 25px;
      width: 65px;
      height: 65px;
      background: #25D366;
      color: white;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.8rem;
      box-shadow: 0 8px 25px rgba(37, 211, 102, 0.5);
      z-index: 99;
      transition: var(--transition);
    }

    .whatsapp-float:hover {
      transform: scale(1.1) rotate(12deg);
      box-shadow: 0 12px 35px rgba(37, 211, 102, 0.7);
    }

    /* Responsive */
    @media (max-width: 992px) {
      .hero h1 {
        font-size: 3.8rem;
      }
    }

    @media (max-width: 768px) {
      .hero h1 {
        font-size: 3rem;
      }
      
      .hero p {
        font-size: 1.2rem;
      }
      
      .section-title {
        font-size: 2.2rem;
      }
      
      .collection-grid, .testimonial-grid {
        grid-template-columns: 1fr;
      }
      
      .cart-overlay {
        width: 100%;
        right: -100%;
      }
      
      .cart-overlay.active {
        right: 0;
      }
      
      .footer-content {
        flex-direction: column;
        gap: 2.5rem;
        text-align: center;
      }
      
      .btn {
        padding: 0.9rem 2rem;
        font-size: 0.9rem;
      }
    }

    @media (max-width: 480px) {
      .hero h1 {
        font-size: 2.4rem;
      }
      
      .hero {
        height: auto;
        min-height: 100vh;
        padding: 8rem 0 4rem;
      }
      
      .cart-overlay {
        padding: 1.5rem;
      }
      
      .product-img {
        height: 250px;
      }
    }
  </style>
</head>
<body>
  <!-- Header -->
  <header>
    <div class="container">
      <div class="header-content">
        <div class="logo">
          <span class="logo-icon">🌸</span>
          Betty's Scent
        </div>
        <div class="cart-icon" id="cartToggle">
          <i class="fas fa-shopping-bag"></i>
          <span class="cart-count" id="cartCount"><?= $cart_count ?></span>
        </div>
      </div>
    </div>
  </header>

  <!-- Hero -->
  <section class="hero">
    <div class="container">
      <div class="hero-content">
        <h1>Perfumes que<br>cuentan tu historia</h1>
        <p>Fragancias exclusivas cuidadosamente seleccionadas para mujeres y hombres que buscan dejar una huella imborrable en el mundo.</p>
        <a href="#colección" class="btn">Descubrir Colección</a>
      </div>
    </div>
  </section>

  <!-- Colección -->
  <section class="section" id="colección">
    <div class="container">
      <h2 class="section-title">Nuestra Colección</h2>
      <p class="section-subtitle">Cada fragancia es una obra maestra creada para realzar tu personalidad única y evocar emociones inolvidables.</p>
      
      <div class="collection-grid">
        <?php foreach ($products as $product): ?>
        <div class="product-card">
          <div class="product-badge"><?= htmlspecialchars($product['category']) ?></div>
          <div class="product-img"><?= $product['icon'] ?></div>
          <div class="product-info">
            <h3 class="product-name"><?= htmlspecialchars($product['name']) ?></h3>
            <p class="product-desc"><?= htmlspecialchars($product['description']) ?></p>
            <div class="product-price"><?= number_format($product['price'], 0, ',', '.') ?> CUP</div>
            <form method="POST" class="add-to-cart-form">
              <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
              <button type="submit" name="add_to_cart" class="add-to-cart-btn">
                <i class="fas fa-shopping-bag"></i> Añadir al carrito
              </button>
            </form>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- Testimonios -->
  <section class="section testimonials">
    <div class="container">
      <h2 class="section-title">Lo que dicen nuestros clientes</h2>
      <p class="section-subtitle">Historias reales de personas que han encontrado su fragancia perfecta con nosotros.</p>
      
      <div class="testimonial-grid">
        <div class="testimonial-card">
          <p class="testimonial-text">"El Impacto es exactamente como el original. ¡Me dura todo el día y recibo cumplidos donde quiera que voy!"</p>
          <div class="testimonial-author">— Ana L., Santa Clara</div>
        </div>
        <div class="testimonial-card">
          <p class="testimonial-text">"Mariposa es suave, fresco y perfecto para el verano. ¡Ya voy por mi segundo frasco y lo recomiendo a todas mis amigas!"</p>
          <div class="testimonial-author">— Yariel G., Camajuaní</div>
        </div>
        <div class="testimonial-card">
          <p class="testimonial-text">"Entrega rápida, perfume sellado y original. La atención personalizada de Betty hizo que mi experiencia fuera excepcional."</p>
          <div class="testimonial-author">— Raúl M., Remedios</div>
        </div>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer>
    <div class="container">
      <div class="footer-content">
        <div class="footer-col">
          <h3><?= $business_name ?></h3>
          <p>Tu destino para fragancias de lujo en Cuba. Calidad, elegancia y servicio excepcional en cada interacción.</p>
        </div>
        <div class="footer-col">
          <h3>Perfumes</h3>
          <ul class="footer-links">
            <?php foreach ($products as $product): ?>
            <li><a href="#"><?= htmlspecialchars($product['name']) ?></a></li>
            <?php endforeach; ?>
          </ul>
        </div>
        <div class="footer-col">
          <h3>Contacto</h3>
          <ul class="footer-links">
            <li>📱 <?= $whatsapp_number ?></li>
            <li>📍 <?= $business_location ?></li>
            <li>✉️ info@bettyscent.cu</li>
          </ul>
        </div>
      </div>
      <div class="copyright">
        &copy; 2025 <?= $business_name ?>. Todos los derechos reservados.
      </div>
    </div>
  </footer>

  <!-- Carrito lateral -->
  <div class="cart-overlay" id="cartOverlay">
    <div class="cart-header">
      <h3 class="cart-title">Tu carrito</h3>
      <button class="close-cart" id="closeCart">
        <i class="fas fa-times"></i>
      </button>
    </div>
    <div class="cart-items" id="cartItems">
      <?php if (empty($_SESSION['cart'])): ?>
        <p style="text-align: center; padding: 2rem; color: var(--gray);">Tu carrito está vacío</p>
      <?php else: ?>
        <?php foreach ($_SESSION['cart'] as $item): ?>
        <div class="cart-item">
          <div class="cart-item-details">
            <div class="cart-item-name"><?= htmlspecialchars($item['name']) ?></div>
            <div class="cart-item-price"><?= number_format($item['price'], 0, ',', '.') ?> CUP c/u</div>
          </div>
          <div class="cart-item-controls">
            <form method="POST" style="display: flex; align-items: center; gap: 15px;">
              <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
              <button type="submit" name="update_quantity" value="decrease" class="quantity-btn">-</button>
              <input type="hidden" name="quantity" value="<?= $item['quantity'] - 1 ?>">
              <div class="cart-item-quantity"><?= $item['quantity'] ?></div>
              <input type="hidden" name="quantity" value="<?= $item['quantity'] + 1 ?>">
              <button type="submit" name="update_quantity" value="increase" class="quantity-btn">+</button>
            </form>
            <form method="POST">
              <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
              <button type="submit" name="remove_from_cart" class="remove-item">
                <i class="fas fa-trash"></i>
              </button>
            </form>
          </div>
        </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
    <div class="cart-footer">
      <div class="cart-total">
        <span>Total:</span>
        <span><?= number_format($cart_total, 0, ',', '.') ?> CUP</span>
      </div>
      <?php if (!empty($_SESSION['cart'])): ?>
      <button class="checkout-btn" id="checkoutBtn">
        <i class="fab fa-whatsapp"></i> Enviar pedido por WhatsApp
      </button>
      <?php endif; ?>
    </div>
  </div>

  <!-- Formulario de pedido modal -->
  <div class="modal-overlay" id="orderModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); z-index: 2500; justify-content: center; align-items: center;">
    <div class="order-form">
      <h3 class="form-title">Completa tu pedido</h3>
      <?php if (isset($error)): ?>
        <div class="notification error show" style="position: relative; margin-bottom: 1.5rem; transform: none;">
          <i class="fas fa-exclamation-circle"></i>
          <span><?= htmlspecialchars($error) ?></span>
        </div>
      <?php endif; ?>
      <form method="POST" class="form-grid">
        <div class="form-group">
          <label for="name">Nombre completo *</label>
          <input type="text" id="name" name="name" required placeholder="Tu nombre">
        </div>
        <div class="form-group">
          <label for="address">Dirección de entrega *</label>
          <input type="text" id="address" name="address" required placeholder="Municipio, reparto, calle y número">
        </div>
        <div class="form-group">
          <label for="phone">Teléfono (opcional)</label>
          <input type="text" id="phone" name="phone" placeholder="Para confirmar tu pedido">
        </div>
        <button type="submit" name="process_order" class="submit-order-btn">
          <i class="fab fa-whatsapp"></i> Enviar pedido por WhatsApp
        </button>
      </form>
    </div>
  </div>

  <!-- Botón flotante de WhatsApp -->
  <a href="https://wa.me/<?= $whatsapp_number ?>" class="whatsapp-float" target="_blank">
    <i class="fab fa-whatsapp"></i>
  </a>

  <!-- Notificación -->
  <div class="notification" id="notification">
    <i class="fas fa-check-circle"></i>
    <span id="notificationText">Producto añadido al carrito</span>
  </div>

  <script>
    // Mostrar notificación si hay mensaje
    <?php if (isset($message)): ?>
    document.addEventListener('DOMContentLoaded', function() {
      showNotification('<?= addslashes($message) ?>', '<?= $message_type ?>');
    });
    <?php endif; ?>

    // Función para mostrar notificaciones
    function showNotification(message, type = 'success') {
      const notification = document.getElementById('notification');
      const notificationText = document.getElementById('notificationText');
      const icon = notification.querySelector('i');
      
      notificationText.textContent = message;
      notification.className = `notification ${type} show`;
      
      if (type === 'success') {
        icon.className = 'fas fa-check-circle';
      } else if (type === 'error') {
        icon.className = 'fas fa-exclamation-circle';
      } else {
        icon.className = 'fas fa-info-circle';
      }
      
      setTimeout(() => {
        notification.classList.remove('show');
      }, 3500);
    }

    // Carrito funcionalidad
    const cartToggle = document.getElementById('cartToggle');
    const closeCart = document.getElementById('closeCart');
    const cartOverlay = document.getElementById('cartOverlay');
    const checkoutBtn = document.getElementById('checkoutBtn');
    const orderModal = document.getElementById('orderModal');

    cartToggle.addEventListener('click', () => {
      cartOverlay.classList.add('active');
    });

    closeCart.addEventListener('click', () => {
      cartOverlay.classList.remove('active');
    });

    cartOverlay.addEventListener('click', (e) => {
      if (e.target === cartOverlay) {
        cartOverlay.classList.remove('active');
      }
    });

    if (checkoutBtn) {
      checkoutBtn.addEventListener('click', () => {
        cartOverlay.classList.remove('active');
        orderModal.style.display = 'flex';
      });
    }

    // Cerrar modal de pedido
    orderModal.addEventListener('click', (e) => {
      if (e.target === orderModal) {
        orderModal.style.display = 'none';
      }
    });

    // Actualizar contador de carrito
    document.addEventListener('DOMContentLoaded', function() {
      const cartCount = document.getElementById('cartCount');
      const cartItems = <?= $cart_count ?>;
      cartCount.textContent = cartItems;
    });
  </script>
</body>
</html>