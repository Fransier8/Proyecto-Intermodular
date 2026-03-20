<?php
$routes = [
    'home' => ['views/home.php', null],
    'login' => ['controllers/users_controller.php', 'logIn'],
    'logout' => ['controllers/users_controller.php', 'logOut'],
    'animals' => ['controllers/animals_controller.php', 'listAnimals'],
    'animal' => ['controllers/animals_controller.php', 'viewAnimalDetails'],
    'rooms' => ['controllers/rooms_controller.php', 'listRooms'],
    'room' => ['controllers/rooms_controller.php', 'viewRoomDetails'],
    'users' => ['controllers/users_controller.php', 'listUsers'],
    'user' => ['controllers/users_controller.php', 'viewUserDetails'],
    'reservations' => ['controllers/reservations_controller.php', 'listReservations'],
    'reservation' => ['controllers/reservations_controller.php', 'viewReservationDetails'],
    'my_animals' => ['controllers/animals_controller.php', 'listMyAnimals'],
    'my_reservations' => ['controllers/reservations_controller.php', 'listMyReservations'],
    'profile' => ['controllers/users_controller.php', 'viewProfileDetails'],
];


$publicViews = ['home', 'login'];
$privateViews = ['animals', 'animal', 'rooms', 'room', 'users', 'user', 'reservations', 'reservation', 'my_animals', 'my_reservations', 'profile'];

if (!isset($routes[$view]) && !isset($_SESSION['user'])) {
    header("Location: " . BASE_URL . "home");
    exit;
}

if (!isset($routes[$view]) && isset($_SESSION['user'])) {
    header("Location: " . BASE_URL . "animals");
    exit;
}

if (in_array($view, $privateViews) && !isset($_SESSION['user'])) {
    header("Location: " . BASE_URL . "home");
    exit;
}

if (in_array($view, $publicViews) && isset($_SESSION['user'])) {
    header("Location: " . BASE_URL . "animals");
    exit;
}


list($controllerFile, $function) = $routes[$view];
if ($function) {
    require_once $controllerFile;
    $function();
} else {
    require_once $controllerFile;
}