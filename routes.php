<?php
$routes = [
    'inicio' => ['views/home.php', null],
    'iniciar_sesion' => ['controllers/users_controller.php', 'logIn'],
    'cerrar_sesion' => ['controllers/users_controller.php', 'logOut'],
    'animales' => ['controllers/animals_controller.php', 'listAnimals'],
    'animal' => ['controllers/animals_controller.php', 'viewAnimalDetails'],
    'salas' => ['controllers/rooms_controller.php', 'listRooms'],
    'sala' => ['controllers/rooms_controller.php', 'viewRoomDetails'],
    'usuarios' => ['controllers/users_controller.php', 'listUsers'],
    'usuario' => ['controllers/users_controller.php', 'viewUserDetails'],
    'reservas' => ['controllers/reservations_controller.php', 'listReservations'],
    'reserva' => ['controllers/reservations_controller.php', 'viewReservationDetails'],
    'especies' => ['controllers/species_controller.php', 'listSpecies'],
    'especie' => ['controllers/species_controller.php', 'viewSpeciesDetails'],
    'mis_animales' => ['controllers/animals_controller.php', 'listMyAnimals'],
    'mis_reservas' => ['controllers/reservations_controller.php', 'listMyReservations'],
    'perfil' => ['controllers/users_controller.php', 'viewProfileDetails'],
    'restablecer_contraseña' => ['controllers/users_controller.php', 'resetPassword'],
    'cambiar_estado_usuario' => ['controllers/users_controller.php', 'changeUserActiveStatus'],
    'modificar_usuario' => ['controllers/users_controller.php', 'editUser'],
    'crear_usuario' => ['controllers/users_controller.php', 'createUser'],
    'modificar_especie' => ['controllers/species_controller.php', 'editSpecies'],
    'crear_especie' => ['controllers/species_controller.php', 'createSpecies'],
    'eliminar_especie' => ['controllers/species_controller.php', 'removeSpecies'],
];


$publicViews = ['inicio', 'iniciar_sesion', 'restablecer_contraseña'];
$privateViews = [
    'animales',
    'animal',
    'salas',
    'sala',
    'usuarios',
    'usuario',
    'reservas',
    'reserva',
    'especies',
    'especie',
    'mis_animales',
    'mis_reservas',
    'perfil',
    'cambiar_estado_usuario',
    'crear_usuario',
    'modificar_usuario',
    'crear_especie',
    'modificar_especie',
    'eliminar_especie'
];

if (!isset($routes[$view]) && !isset($_SESSION['user'])) {
    header("Location: " . BASE_URL . "inicio");
    exit;
}

if (!isset($routes[$view]) && isset($_SESSION['user'])) {
    header("Location: " . BASE_URL . "animales");
    exit;
}

if (in_array($view, $privateViews) && !isset($_SESSION['user'])) {
    header("Location: " . BASE_URL . "inicio");
    exit;
}

if (in_array($view, $publicViews) && isset($_SESSION['user'])) {
    header("Location: " . BASE_URL . "animales");
    exit;
}


list($controllerFile, $function) = $routes[$view];
if ($function) {
    require_once $controllerFile;
    $function();
} else {
    require_once $controllerFile;
}