<?php
/*
    Carta digital - Ándele Mexperience
*/

// Intentamos cargar el XML. 
if (file_exists('./datos/carta.xml')) {
    $menu = simplexml_load_file('./datos/carta.xml');
} else {
    exit('<p style="color:red; padding:2rem;">No se encuentra el archivo carta.xml en la carpeta datos/</p>');
}

// Recorremos todos los platos para sacar los tipos únicos que existen.
$tipos = [];
foreach ($menu->plato as $plato) {
    $tipo = (string)$plato['tipo'];
    if (!in_array($tipo, $tipos)) {
        $tipos[] = $tipo;
    }
}

$filtroActivo = isset($_GET['tipo']) ? $_GET['tipo'] : 'todos';

$tipoLabels = [
    'todos'     => 'Todo el Menú',
    'botana'    => 'Botanas',
    'entrante'  => 'Antojitos',
    'principal' => 'Platillos',
    'taco'      => 'Puro Taco',
    'postre'    => 'Postres',
];


$iconosCategoria = [
    'vegano'       => ['icon' => 'fa-leaf',                          'label' => 'Vegano',          'color' => '#27a91e'],
    'vegetariano'  => ['icon' => 'fa-seedling',                      'label' => 'Vegetariano',     'color' => '#8bc34a'],
    'sin-gluten'   => ['icon' => 'fa-wheat-awn-circle-exclamation',  'label' => 'Sin Gluten',      'color' => '#ff9800'],
    'picante'      => ['icon' => 'fa-fire',                          'label' => 'Picante',         'color' => '#f44336'],
    'carne'        => ['icon' => 'fa-drumstick-bite',                'label' => 'Carne',           'color' => '#795548'],
    'pescado'      => ['icon' => 'fa-fish',                          'label' => 'Pescado/Mariscos','color' => '#2196f3'],
    'lacteo'       => ['icon' => 'fa-cow',                           'label' => 'Lácteo',          'color' => '#90a4ae'],
    'destacado'    => ['icon' => 'fa-star',                          'label' => 'Destacado',       'color' => '#ffc107'],
];

// Icono para cada tipo de plato — se usa en la barra de filtros
$tipoIconos = [
    'botana'    => 'fa-bowl-food',
    'entrante'  => 'fa-utensils',
    'principal' => 'fa-plate-wheat',
    'taco'      => 'fa-taco',
    'postre'    => 'fa-ice-cream',
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ándele Mexperience — Carta</title>

    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet" crossorigin="anonymous">
    
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
          crossorigin="anonymous">

    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;1,400&family=Crimson+Pro:wght@300;400;600&family=Space+Mono:wght@400;700&display=swap"
          rel="stylesheet">
    <!-- Nuestra hoja de estilos propia -->
    <link rel="stylesheet" href="./css/estilos.css">
</head>
<body>

<!-- CABECERA con el nombre del restaurante -->
<header class="site-header">
    <div class="header-content">
        <div class="logo-container">
            <div class="logo-emblem">7A</div>
            <div class="logo-text">
                <h1 class="restaurant-name">Ándele</h1>
                <p class="restaurant-sub">MEXPERIENCE</p>
            </div>
        </div>
        <p class="header-tagline">Cocina Mexicana Auténtica · Barcelona</p>
    </div>
</header>

<nav class="filter-nav">
    <div class="filter-nav-inner">

        <!-- Botón "todo el menú" siempre primero -->
        <a href="?tipo=todos"
           class="filter-btn <?= ($filtroActivo === 'todos') ? 'active' : '' ?>">
            <i class="fa-solid fa-border-all"></i>
            <span>Todo</span>
        </a>

        <?php
        // Generamos un botón por cada tipo de plato que existe en el XML.
        foreach ($tipos as $tipo):
            $icon = $tipoIconos[$tipo] ?? 'fa-circle';
        ?>
        <a href="?tipo=<?= htmlspecialchars($tipo) ?>"
           class="filter-btn <?= ($filtroActivo === $tipo) ? 'active' : '' ?>">
            <i class="fa-solid <?= $icon ?>"></i>
            <span><?= htmlspecialchars($tipoLabels[$tipo] ?? ucfirst($tipo)) ?></span>
        </a>
        <?php endforeach; ?>

    </div>
</nav>

<!-- CONTENIDO PRINCIPAL: el grid de tarjetas de platos -->
<main class="menu-main">
    <div class="container">

        <!-- Título de la sección activa en este momento -->
        <div class="section-header">
            <h2 class="section-title">
                <?= htmlspecialchars($tipoLabels[$filtroActivo] ?? ucfirst($filtroActivo)) ?>
            </h2>
        </div>

        <!-- Grid Bootstrap: 1 columna en móvil, 2 en tablet, 3 en escritorio -->
        <div class="row g-4 platos-grid">
        <?php
        $contador = 0;

        foreach ($menu->plato as $plato):
            $tipo = (string)$plato['tipo'];

            // Si hay un filtro activo y este plato no es de ese tipo, lo saltamos
            if ($filtroActivo !== 'todos' && $tipo !== $filtroActivo) continue;

            $contador++;

            // Miramos si el plato tiene la categoría "destacado"
            $esDestacado = false;
            foreach ($plato->ingredientes->categoria as $cat) {
                if ((string)$cat === 'destacado') {
                    $esDestacado = true;
                    break;
                }
            }
        ?>
        <div class="col-12 col-sm-6 col-xl-4">
            <div class="plato-card <?= $esDestacado ? 'plato-destacado' : '' ?>">

                <?php if ($esDestacado): ?>
                <div class="destacado-badge">
                    <i class="fa-solid fa-star"></i> Destacado
                </div>
                <?php endif; ?>

                <!-- Etiqueta pequeña con el tipo de plato -->
                <div class="plato-tipo-tag">
                    <i class="fa-solid <?= $tipoIconos[$tipo] ?? 'fa-circle' ?>"></i>
                    <?= htmlspecialchars($tipoLabels[$tipo] ?? ucfirst($tipo)) ?>
                </div>

                <div class="plato-body">
                    <!-- Nombre del plato — viene de $plato->nombre en el XML -->
                    <h3 class="plato-nombre"><?= htmlspecialchars((string)$plato->nombre) ?></h3>

                    <!-- Descripción del plato -->
                    <p class="plato-descripcion"><?= htmlspecialchars((string)$plato->descripcion) ?></p>

                    <div class="plato-meta">
                        <!-- Calorías aproximadas -->
                        <div class="plato-calorias">
                            <i class="fa-solid fa-fire-flame-curved"></i>
                            <span><?= htmlspecialchars((string)$plato->calorias) ?> kcal</span>
                        </div>

                        <!-- aquí accedemos a $plato->ingredientes->categoria
                             que es el subelemento anidado dentro de <ingredientes> -->
                        <div class="plato-iconos">
                            <?php foreach ($plato->ingredientes->categoria as $categoria):
                                $cat = (string)$categoria;
                                if ($cat === 'destacado') continue;
                                if (isset($iconosCategoria[$cat])):
                                    $info = $iconosCategoria[$cat];
                            ?>
                            <span class="icono-caracteristica"
                                  style="color: <?= $info['color'] ?>"
                                  title="<?= htmlspecialchars($info['label']) ?>">
                                <i class="fa-solid <?= $info['icon'] ?>"></i>
                            </span>
                            <?php endif; endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Pie de tarjeta: pills de texto + precio -->
                <div class="plato-footer">
                    <div class="caracteristicas-pills">
                        <?php foreach ($plato->ingredientes->categoria as $categoria):
                            $cat = (string)$categoria;
                            if ($cat === 'destacado') continue;
                            if (isset($iconosCategoria[$cat])):
                        ?>
                        <span class="pill"
                              style="background:<?= $iconosCategoria[$cat]['color'] ?>22;
                                     color:<?= $iconosCategoria[$cat]['color'] ?>;
                                     border-color:<?= $iconosCategoria[$cat]['color'] ?>55">
                            <?= htmlspecialchars($iconosCategoria[$cat]['label']) ?>
                        </span>
                        <?php endif; endforeach; ?>
                    </div>

                    <!-- Precio -->
                    <div class="plato-precio">
                        <?= number_format((float)$plato->precio, 2, ',', '.') ?> €
                    </div>
                </div>

            </div>
        </div>
        <?php endforeach; ?>

        <?php if ($contador === 0): ?>
        <div class="col-12 text-center py-5">
            <p class="no-results">No hay platos en esta categoría.</p>
        </div>
        <?php endif; ?>

        </div><!-- fin .row -->
    </div><!-- fin .container -->
</main>

<!-- LEYENDA de iconos al pie de página -->
<section class="leyenda-section">
    <div class="container">
        <h3 class="leyenda-titulo">Leyenda</h3>
        <div class="leyenda-grid">
            <?php foreach ($iconosCategoria as $key => $info):
                if ($key === 'destacado') continue;
            ?>
            <div class="leyenda-item">
                <i class="fa-solid <?= $info['icon'] ?>" style="color:<?= $info['color'] ?>"></i>
                <span><?= htmlspecialchars($info['label']) ?></span>
            </div>
            <?php endforeach; ?>
        </div>
        <p class="alergenos-aviso">
            <i class="fa-solid fa-triangle-exclamation"></i>
            Información disponible sobre Alergias e Intolerancias. Consulte a nuestro personal.
        </p>
    </div>
</section>

<!-- PIE DE PÁGINA -->
<footer class="site-footer">
    <div class="container text-center">
        <div class="footer-logo">Ándele Mexperience</div>
        <p class="footer-info">
            <i class="fa-solid fa-location-dot"></i> Barcelona &nbsp;|&nbsp;
            <i class="fa-solid fa-clock"></i> Mar–Dom: 13:00–16:00 / 20:00–00:00
        </p>
    </div>
</footer>

<!-- JS de Bootstrap — necesario para el navbar en móvil -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        crossorigin="anonymous"></script>
</body>
</html>
