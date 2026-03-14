<?php
define('BASE_URL', '/proyecto/Proyecto-Intermodular/');
session_start();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Enlace al archivo CSS de Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>styles/styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <title>Protectora de animales</title>
</head>

<body>
    <?php
    $view = $_GET['view'] ?? 'home';
    require 'views/header.php';
    require_once 'routes.php';
    require 'views/footer.php'
        ?>
    <!-- Enlace a los archivos JavaScript de Bootstrap 5 y Popper en un solo archivo (bootstrap.bundle.min.js) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>