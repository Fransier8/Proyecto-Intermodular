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
    'modificar_perfil' => ['controllers/users_controller.php', 'editProfile'],
    'restablecer_contraseña' => ['controllers/users_controller.php', 'resetPassword'],
    'cambiar_estado_usuario' => ['controllers/users_controller.php', 'changeUserActiveStatus'],
    'modificar_sala' => ['controllers/rooms_controller.php', 'editRoom'],
    'modificar_usuario' => ['controllers/users_controller.php', 'editUser'],
    'crear_usuario' => ['controllers/users_controller.php', 'createUser'],
    'modificar_especie' => ['controllers/species_controller.php', 'editSpecies'],
    'crear_especie' => ['controllers/species_controller.php', 'createSpecies'],
    'eliminar_especie' => ['controllers/species_controller.php', 'removeSpecies'],
];


$publicViews = ['inicio', 'iniciar_sesion', 'restablecer_contraseña'];
$userViews = ['animales', 'animal', 'salas', 'sala', 'reserva', 'mis_animales', 'mis_reservas', 'perfil', 'modificar_perfil', 'cerrar_sesion'];
$monitorViews = [
    'animales',
    'animal',
    'salas',
    'sala',
    'reservas',
    'reserva',
    'mis_reservas',
    'perfil',
    'modificar_perfil',
    'cerrar_sesion'
];
$administratorViews = [
    'animales',
    'animal',
    'salas',
    'sala',
    'usuarios',
    'usuario',
    'especies',
    'especie',
    'reservas',
    'reserva',
    'modificar_sala',
    'crear_usuario',
    'modificar_usuario',
    'cambiar_estado_usuario',
    'crear_especie',
    'modificar_especie',
    'eliminar_especie',
    'perfil',
    'modificar_perfil',
    'cerrar_sesion'
];

if (!in_array($view, $publicViews) && !isset($_SESSION['user'])) {
    header("Location: " . BASE_URL . "inicio");
    exit;
}

if (
    isset($_SESSION['user']) && (!isset($routes[$view]) || in_array($view, $publicViews)
        || ($_SESSION['user']['role'] == "usuario" && !in_array($view, $userViews))
        || ($_SESSION['user']['role'] == "monitor" && !in_array($view, $monitorViews))
        || ($_SESSION['user']['role'] == "administrador" && !in_array($view, $administratorViews)))
) {
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