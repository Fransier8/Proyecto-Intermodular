<?php
$routes = [
    'home' => ['views/home.php', null],
    'login' => ['views/login.php', null],
    'pets' => ['controllers/pets_controller.php', 'listPets']
];

if (!isset($routes[$view])) {
    header("Location: home");
    exit;
}

list($controllerFile, $function) = $routes[$view];
if ($function) {
    require_once $controllerFile;
    $function();
} else {
    require_once $controllerFile;
}