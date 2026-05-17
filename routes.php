<?php
$routes = [
    'inicio' => ['views/home.php', null],
    'iniciar_sesion' => ['controllers/users_controller.php', 'logIn'],
    'registrarse' => ['controllers/users_controller.php', 'signUp'],
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
    'crear_animal' => ['controllers/animals_controller.php', 'createAnimal'],
    'modificar_animal' => ['controllers/animals_controller.php', 'editAnimal'],
    'cambiar_estado_animal' => ['controllers/animals_controller.php', 'changeTheAnimalActiveStatus'],
    'cambiar_estado_usuario' => ['controllers/users_controller.php', 'changeUserActiveStatus'],
    'crear_sala' => ['controllers/rooms_controller.php', 'createRoom'],
    'modificar_sala' => ['controllers/rooms_controller.php', 'editRoom'],
    'cambiar_estado_sala' => ['controllers/rooms_controller.php', 'changeRoomActiveStatus'],
    'modificar_usuario' => ['controllers/users_controller.php', 'editUser'],
    'crear_usuario' => ['controllers/users_controller.php', 'createUser'],
    'modificar_especie' => ['controllers/species_controller.php', 'editSpecies'],
    'crear_especie' => ['controllers/species_controller.php', 'createSpecies'],
    'eliminar_especie' => ['controllers/species_controller.php', 'removeSpecies'],
    'crear_reserva' => ['controllers/reservations_controller.php', 'createReservation'],
    'usuarios_reserva' => ['controllers/reservations_controller.php', 'listReservationUsers'],
    'animales_reserva' => ['controllers/reservations_controller.php', 'listReservationAnimals'],
    'salas_reserva' => ['controllers/reservations_controller.php', 'listReservationRooms'],
    'monitores_reserva' => ['controllers/reservations_controller.php', 'listReservationMonitors'],
    'seleccionar_fecha_reserva' => ['controllers/reservations_controller.php', 'selectReservationDate'],
    'disponibilidad_calendario' => ['controllers/reservations_controller.php', 'calendarAvailability'],
    'eliminar_reserva' => ['controllers/reservations_controller.php', 'removeReservation'],
    'cancelar_reserva' => ['controllers/reservations_controller.php', 'cancelReservation'],
    'asignar_monitor_reserva' => ['controllers/reservations_controller.php', 'assignMonitor'],
    'aceptar_reserva' => ['controllers/reservations_controller.php', 'acceptReservation'],
    'solicitar_reserva' => ['controllers/reservations_controller.php', 'requestReservation'],
    'solicitudes_de_adopcion' => ['controllers/adoption_applications_controller.php', 'listAdoptionApplications'],
    'adoptar_animal' => ['controllers/adoption_applications_controller.php', 'createAdoptionApplication'],
    'modificar_solicitud_de_adopcion' => ['controllers/adoption_applications_controller.php', 'editAdoptionApplication'],
    'eliminar_solicitud_de_adopcion' => ['controllers/adoption_applications_controller.php', 'removeAdoptionApplication'],
    'quitar_usuario_animal' => ['controllers/animals_controller.php', 'removeAnimalUser'],
    'apadrinamientos' => ['controllers/sponsorships_controller.php', 'listSponsorships'],
    'apadrinar_animal' => ['controllers/sponsorships_controller.php', 'createSponsorship'],
    'pago_exitoso' => ['controllers/sponsorships_controller.php', 'paymentSuccess'],
    'pago_cancelado' => ['controllers/sponsorships_controller.php', 'paymentCancel'],
    'informes' => ['controllers/reports_controller.php', 'viewReports'],
    'descargar_animales' => ['controllers/reports_controller.php', 'downloadAnimalsPdf'],
    'descargar_usuarios' => ['controllers/reports_controller.php', 'downloadUsersPdf'],
    'descargar_salas' => ['controllers/reports_controller.php', 'downloadRoomsPdf'],
    'descargar_especies' => ['controllers/reports_controller.php', 'downloadSpeciesPdf'],
    'descargar_reservas' => ['controllers/reports_controller.php', 'downloadReservationsPdf'],
    'desactivar_cuenta' => ['controllers/users_controller.php', 'deactivateAccount'],
    'reactivar_cuenta' => ['controllers/users_controller.php', 'reactivateAccount']
];


$publicViews = ['inicio', 'iniciar_sesion', 'registrarse', 'restablecer_contraseña', 'reactivar_cuenta'];
$userViews = [
    'animales',
    'animal',
    'salas',
    'sala',
    'reserva',
    'mis_animales',
    'mis_reservas',
    'perfil',
    'modificar_perfil',
    'cerrar_sesion',
    'adoptar_animal',
    'solicitudes_de_adopcion',
    'apadrinar_animal',
    'apadrinamientos',
    'pago_exitoso',
    'pago_cancelado',
    'desactivar_cuenta',
    'cancelar_reserva',
    'solicitar_reserva',
    'animales_reserva',
    'salas_reserva'
];
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
    'cerrar_sesion',
    'desactivar_cuenta',
    'asignar_monitor_reserva',
    'aceptar_reserva'
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
    'crear_animal',
    'modificar_animal',
    'cambiar_estado_animal',
    'crear_sala',
    'modificar_sala',
    'cambiar_estado_sala',
    'crear_usuario',
    'modificar_usuario',
    'cambiar_estado_usuario',
    'crear_especie',
    'modificar_especie',
    'eliminar_especie',
    'crear_reserva',
    'usuarios_reserva',
    'animales_reserva',
    'salas_reserva',
    'monitores_reserva',
    'seleccionar_fecha_reserva',
    'disponibilidad_calendario',
    'eliminar_reserva',
    'perfil',
    'modificar_perfil',
    'cerrar_sesion',
    'solicitudes_de_adopcion',
    'modificar_solicitud_de_adopcion',
    'eliminar_solicitud_de_adopcion',
    'quitar_usuario_animal',
    'apadrinamientos',
    'informes',
    'descargar_animales',
    'descargar_usuarios',
    'descargar_salas',
    'descargar_especies',
    'descargar_reservas',
    'aceptar_reserva'
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