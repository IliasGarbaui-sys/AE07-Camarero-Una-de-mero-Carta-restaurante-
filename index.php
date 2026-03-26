<?php
/*
    Carta digital - Ándele Mexperience
*/

// aqui miramos si existe el xml, si no existe pues nada, error y fuera
if (file_exists('./datos/carta.xml')) {
        $menu = simplexml_load_file('./datos/carta.xml');
} else {
   exit('<p style="color:red; padding:2rem;">No se encuentra el archivo carta.xml en la carpeta datos/</p>');
}

// datos basicos del restaurante sacados del xml (nombre ciudad horario)
$restaurante = (string)$menu['restaurante'];
   $ciudad      = (string)$menu['ciudad'];
$horario     = (string)$menu['horario'];


// array asociativo de tipos (id => label + icono)
// esto luego sirve para los filtros de arriba
$tipos = [];
 foreach ($menu->tipos->tipo as $tipo) {
    $tipos[(string)$tipo['id']] = [
     'label' => (string)$tipo['label'],
        'icono' => (string)$tipo['icono'],
    ];
 }


// otro array pero de categorias (vegano, picante etc)
// tambien guardamos color para pintar cosas
$categorias = [];
foreach ($menu->categorias->categoria as $cat) {
        $categorias[(string)$cat['id']] = [
      'label' => (string)$cat['label'],
   'icono' => (string)$cat['icono'],
        'color' => (string)$cat['color'],
    ];
}

// filtro por url (?tipo=algo) si no hay nada pues todos
$filtroActivo = isset($_GET['tipo']) ? $_GET['tipo'] : 'todos';
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
    <!-- para moviles y que no se rompa todo -->
   <meta name="viewport" content="width=device-width, initial-scale=1.0">

 <!-- titulo dinamico con el nombre -->
<title><?= htmlspecialchars($restaurante) ?> — Carta</title>

   <!-- bootstrap para estilos rapidos sin complicarse mucho -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
     rel="stylesheet"   crossorigin="anonymous">


        <!-- libreria de iconos, esto lo uso pa los iconitos -->
   <link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
        crossorigin="anonymous">


  <!-- fuentes externas, un poco a ojo la verdad -->
        <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;1,400&family=Crimson+Pro:wght@300;400;600&family=Space+Mono:wght@400;700&display=swap"
   rel="stylesheet">


    <!-- css propio (esto ya es nuestro) -->
<link rel="stylesheet" href="./css/estilos.css">
</head>
<body>

<!-- cabecera con logo y nombre del restaurante -->
<header class="site-header">
    <div class="header-content">
<div class="logo-container">
            <div class="logo-emblem">7A</div>
        <div class="logo-text">
                <!-- aqui separa el nombre en palabras -->
<h1 class="restaurant-name"><?= htmlspecialchars(explode(' ', $restaurante)[0]) ?></h1>
   <!-- resto del nombre en mayusculas -->
                <p class="restaurant-sub"><?= htmlspecialchars(strtoupper(implode(' ', array_slice(explode(' ', $restaurante), 1)))) ?></p>
            </div>
        </div>
   <!-- texto secundario con ciudad -->
        <p class="header-tagline">Cocina Mexicana Auténtica · <?= htmlspecialchars($ciudad) ?></p>
    </div>
</header>

<!-- barra de filtros -->
<nav class="filter-nav">
    <div class="filter-nav-inner">

        <!-- boton para ver todo -->
<a href="?tipo=todos" class="filter-btn <?= ($filtroActivo === 'todos') ? 'active' : '' ?>">
            <i class="fa-solid fa-border-all"></i>
     <span>Todo</span>
        </a>

        <!-- botones generados desde el array tipos -->
<?php foreach ($tipos as $id => $tipo): ?>
<a href="?tipo=<?= htmlspecialchars($id) ?>"
           class="filter-btn <?= ($filtroActivo === $id) ? 'active' : '' ?>">
<i class="fa-solid <?= htmlspecialchars($tipo['icono']) ?>"></i>
            <span><?= htmlspecialchars($tipo['label']) ?></span>
        </a>
<?php endforeach; ?>

    </div>
</nav>

<!-- contenido principal -->
<main class="menu-main">
    <div class="container">

        <!-- titulo segun filtro -->
<div class="section-header">
            <h2 class="section-title">
<?= htmlspecialchars($filtroActivo === 'todos' ? 'Todo el Menú' : ($tipos[$filtroActivo]['label'] ?? ucfirst($filtroActivo))) ?>
            </h2>
        </div>

        <!-- grid de platos -->
        <div class="row g-4 platos-grid">
        <?php
$contador = 0;

// recorremos todos los platos del xml
foreach ($menu->plato as $plato):
    $tipoplato = (string)$plato['tipo'];

            // si no coincide con filtro lo saltamos
            if ($filtroActivo !== 'todos' && $tipoplato !== $filtroActivo) continue;

    $contador++;

    // comprobamos si es destacado
$esDestacado = in_array('destacado', (array)$plato->ingredientes->categoria);
?>
<div class="col-12 col-sm-6 col-xl-4">
            <div class="plato-card <?= $esDestacado ? 'plato-destacado' : '' ?>">

<?php if ($esDestacado): ?>
                <!-- etiqueta destacado -->
<div class="destacado-badge">
<i class="fa-solid fa-star"></i> Destacado
                </div>
<?php endif; ?>

                <!-- tipo del plato -->
<div class="plato-tipo-tag">
<i class="fa-solid <?= htmlspecialchars($tipos[$tipoplato]['icono'] ?? 'fa-circle') ?>"></i>
<?= htmlspecialchars($tipos[$tipoplato]['label'] ?? ucfirst($tipoplato)) ?>
                </div>

<div class="plato-body">
                    <!-- nombre -->
<h3 class="plato-nombre"><?= htmlspecialchars((string)$plato->nombre) ?></h3>

                    <!-- descripcion -->
<p class="plato-descripcion"><?= htmlspecialchars((string)$plato->descripcion) ?></p>

                    <div class="plato-meta">
                        <!-- calorias -->
<div class="plato-calorias">
<i class="fa-solid fa-fire-flame-curved"></i>
<span><?= htmlspecialchars((string)$plato->calorias) ?> kcal</span>
                        </div>

                        <!-- iconos de categorias -->
<div class="plato-iconos">
<?php foreach ($plato->ingredientes->categoria as $catId):
$cat = (string)$catId;
if ($cat === 'destacado') continue;
 if (!isset($categorias[$cat])) continue;
$info = $categorias[$cat];
?>
<span class="icono-caracteristica"
      style="color: <?= $info['color'] ?>"
title="<?= htmlspecialchars($info['label']) ?>">
<i class="fa-solid <?= htmlspecialchars($info['icono']) ?>"></i>
</span>
<?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- parte inferior -->
<div class="plato-footer">
<div class="caracteristicas-pills">
<?php foreach ($plato->ingredientes->categoria as $catId):
$cat = (string)$catId;
if ($cat === 'destacado') continue;
if (!isset($categorias[$cat])) continue;
$info = $categorias[$cat];
?>
<span class="pill"
style="background:<?= $info['color'] ?>22;
 color:<?= $info['color'] ?>;
 border-color:<?= $info['color'] ?>55">
<?= htmlspecialchars($info['label']) ?>
</span>
<?php endforeach; ?>
                    </div>

                    <!-- precio -->
<div class="plato-precio">
<?= number_format((float)$plato->precio, 2, ',', '.') ?> €
                    </div>
                </div>

            </div>
        </div>
<?php endforeach; ?>

        <!-- si no hay platos -->
<?php if ($contador === 0): ?>
<div class="col-12 text-center py-5">
<p class="no-results">No hay platos en esta categoría.</p>
</div>
<?php endif; ?>

        </div>
    </div>
</main>

<!-- leyenda -->
<section class="leyenda-section">
    <div class="container">
<h3 class="leyenda-titulo">Leyenda</h3>
        <div class="leyenda-grid">
<?php foreach ($categorias as $id => $info):
if ($id === 'destacado') continue;
?>
<div class="leyenda-item">
<i class="fa-solid <?= htmlspecialchars($info['icono']) ?>" style="color:<?= $info['color'] ?>"></i>
<span><?= htmlspecialchars($info['label']) ?></span>
</div>
<?php endforeach; ?>
        </div>

        <!-- aviso alergias -->
<p class="alergenos-aviso">
<i class="fa-solid fa-triangle-exclamation"></i>
Información disponible sobre Alergias e Intolerancias. Consulte a nuestro personal.
</p>
    </div>
</section>

<!-- footer -->
<footer class="site-footer">
<div class="container text-center">
<div class="footer-logo"><?= htmlspecialchars($restaurante) ?></div>
<p class="footer-info">
<i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($ciudad) ?> &nbsp;|&nbsp;
<i class="fa-solid fa-clock"></i> <?= htmlspecialchars($horario) ?>
</p>
    </div>
</footer>

<!-- js bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        crossorigin="anonymous"></script>
</body>
</html>