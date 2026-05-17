<?php
require_once 'models/reservations_model.php';
require_once 'models/users_model.php';
require_once 'models/animals_model.php';
require_once 'models/species_model.php';
require_once 'models/rooms_model.php';
require_once 'models/room_schedules_model.php';

function listReservations()
{
    $search = $_GET['search'] ?? '';
    $order = $_GET['order'] ?? '';
    $status = $_GET['status'] ?? '';
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $per_page = 8;

    $total_reservations = countReservations($search, $status);

    $total_pages = $total_reservations > 0 ? ceil($total_reservations / $per_page) : 1;

    $offset = ($page - 1) * $per_page;
    $reservations = getReservations($search, $order, $status, $per_page, $offset);

    if (isset($_GET['ajax'])) {
        require 'views/lists/reservations_list.php';
        exit;
    }
    require 'views/reservations.php';
}

function viewReservationDetails()
{
    require 'views/reservation.php';
}

function listMyReservations()
{
    $search = $_GET['search'] ?? '';
    $order = $_GET['order'] ?? '';
    $status = $_GET['status'] ?? '';
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $per_page = 8;

    if ($_SESSION['user']['role'] == "usuario") {
        $total_reservations = countReservationsByUserId($search, $status, $_SESSION['user']['id']);
    } else {
        $total_reservations = countReservationsByMonitorId($search, $status, $_SESSION['user']['id']);
    }

    $total_pages = $total_reservations > 0 ? ceil($total_reservations / $per_page) : 1;

    $offset = ($page - 1) * $per_page;

    if ($_SESSION['user']['role'] == "usuario") {
        $reservations = getReservationsByUserId($search, $order, $status, $_SESSION['user']['id'], $per_page, $offset);
    } else {
        $reservations = getReservationsByMonitorId($search, $order, $status, $_SESSION['user']['id'], $per_page, $offset);
    }

    if (isset($_GET['ajax'])) {
        require 'views/lists/reservations_list.php';
        exit;
    }
    require 'views/my_reservations.php';
}

function createReservation()
{
    $errors = [];

    $species = getSpecies("", "", 1000, 0);

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $reason = trim($_POST['reason'] ?? '');
        $companions = trim($_POST['companions'] ?? '');
        $status = trim($_POST['status'] ?? '');
        $user_id = trim($_POST['user-id'] ?? '');
        $animal_id = trim($_POST['animal-id'] ?? '');
        $room_id = trim($_POST['room-id'] ?? '');
        $monitor_id = trim($_POST['monitor-id'] ?? '');

        $monitor_id = $monitor_id !== '' ? $monitor_id : null;

        if (empty($reason)) {
            $errors[] = "El motivo es obligatorio.";
        }

        if (!is_numeric($companions) || (int) $companions < 0) {
            $errors[] = "Los acompañantes deben ser 0 o más.";
        }

        if ($status != "pendiente" && $status != "aceptada") {
            $errors[] = "Estado incorrecto.";
        }

        if (empty($user_id)) {
            $errors[] = "Debes seleccionar un usuario.";
        } else if (!getUserById($user_id)) {
            $errors[] = "El usuario no existe.";
        }

        if (empty($animal_id)) {
            $errors[] = "Debes seleccionar un animal.";
        } else if (!getAnimalById($animal_id)) {
            $errors[] = "El animal no existe.";
        }

        $room = null;

        if (empty($room_id)) {
            $errors[] = "Debes seleccionar una sala.";
        } else {
            $room = getRoomById($room_id);
            if (!$room) {
                $errors[] = "La sala no existe.";
            }
        }

        if ($room && is_numeric($companions) && $room['capacity'] <= $companions) {
            $errors[] = "La capacidad de la sala no permite tantos acompañantes.";
        }

        if ($status == "aceptada" && empty($monitor_id)) {
            $errors[] = "Debes seleccionar un monitor.";
        } else if ($status == "aceptada" && !getUserById($monitor_id)) {
            $errors[] = "El monitor no existe.";
        }

        if (!empty($errors)) {
            $reservation = [
                'reason' => $reason,
                'companions' => $companions,
                'status' => $status,
                'user_id' => $user_id,
                'animal_id' => $animal_id,
                'room_id' => $room_id,
                'monitor_id' => $monitor_id,
            ];
            $users = $users ?? [];
            $animals = $animals ?? [];
            $rooms = $rooms ?? [];
            $page = $page ?? 1;
            $total_pages = $total_pages ?? 1;

            require 'views/create_reservation.php';
            return;
        }

        $_SESSION['reservation'] = [
            'reason' => $reason,
            'companions' => $companions,
            'status' => $status,
            'user_id' => $user_id,
            'animal_id' => $animal_id,
            'room_id' => $room_id,
            'monitor_id' => $monitor_id,
        ];

        header("Location: " . BASE_URL . "seleccionar_fecha_reserva");
        exit;

    } else {
        $reservation = [
            'reason' => '',
            'companions' => '',
            'status' => 'pendiente',
            'user_id' => '',
            'animal_id' => '',
            'room_id' => '',
            'monitor_id' => '',
        ];
        $users = $users ?? [];
        $animals = $animals ?? [];
        $rooms = $rooms ?? [];
        $page = $page ?? 1;
        $total_pages = $total_pages ?? 1;
        require 'views/create_reservation.php';
    }
}

function selectReservationDate()
{
    $errors = [];

    $reservation = $_SESSION['reservation'] ?? null;

    if (!$reservation) {
        header("Location: " . BASE_URL . "crear_reserva");
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $date = trim($_POST['reservation-date'] ?? '');
        $start_time = trim($_POST['reservation-start'] ?? '');
        $end_time = trim($_POST['reservation-end'] ?? '');

        $interval = (int) ($_POST['interval'] ?? 5);
        $allowed_intervals = [5, 10, 15, 20, 25, 30, 35, 40, 45, 50, 55, 60];

        if (!in_array($interval, $allowed_intervals)) {
            $errors[] = "Intervalo inválido.";
        }

        if (empty($date) || empty($start_time) || empty($end_time)) {
            $errors[] = "Debes seleccionar una fecha y hora.";
        }

        if ($date <= date('Y-m-d')) {
            $errors[] = "No puedes seleccionar fechas de hoy o anteriores.";
        }

        $reservations = getReservationsByUserIdOrAnimalIdOrRoomIdOrMonitorId($reservation['user_id'], $reservation['animal_id'], $reservation['room_id'], $reservation['monitor_id']);

        $new_start = strtotime($date . " " . $start_time);
        $new_end = strtotime($date . " " . $end_time);

        $buffer = 5 * 60;

        foreach ($reservations as $r) {

            if (!isset($r['date']) || $r['date'] !== $date) {
                continue;
            }

            $r_start = strtotime($r['date'] . " " . $r['start_time']) - $buffer;
            $r_end = strtotime($r['date'] . " " . $r['end_time']) + $buffer;

            if ($new_start < $r_end && $new_end > $r_start) {
                $errors[] = "Ya existe una reserva que se solapa (con margen de 5 minutos).";
                break;
            }
        }

        if (empty($reservation['reason'])) {
            $errors[] = "El motivo es obligatorio.";
        }

        if (!is_numeric($reservation['companions']) || (int) $reservation['companions'] < 0) {
            $errors[] = "Los acompañantes deben ser 0 o más.";
        }

        if ($reservation['status'] != "pendiente" && $reservation['status'] != "aceptada") {
            $errors[] = "Estado incorrecto.";
        }

        if (empty($reservation['user_id'])) {
            $errors[] = "Debes seleccionar un usuario.";
        } else if (!getUserById($reservation['user_id'])) {
            $errors[] = "El usuario no existe.";
        }

        if (empty($reservation['animal_id'])) {
            $errors[] = "Debes seleccionar un animal.";
        } else if (!getAnimalById($reservation['animal_id'])) {
            $errors[] = "El animal no existe.";
        }

        $room_test = null;

        if (empty($reservation['room_id'])) {
            $errors[] = "Debes seleccionar una sala.";
        } else {
            $room_test = getRoomById($reservation['room_id']);
            if (!$room_test) {
                $errors[] = "La sala no existe.";
            }
        }

        if ($room_test && is_numeric($reservation['companions']) && $room_test['capacity'] <= $reservation['companions']) {
            $errors[] = "La capacidad de la sala no permite tantos acompañantes.";
        }

        if ($reservation['status'] == "aceptada" && empty($reservation['monitor_id'])) {
            $errors[] = "Debes seleccionar un monitor.";
        } else if ($reservation['status'] == "aceptada" && !getUserById($reservation['monitor_id'])) {
            $errors[] = "El monitor no existe.";
        }


        if (!empty($errors)) {

            $user = getUserById($reservation['user_id']);
            $animal = getAnimalById($reservation['animal_id']);
            $room = getRoomById($reservation['room_id']);
            $monitor = $reservation['monitor_id'] ? getUserById($reservation['monitor_id']) : '';
            $schedules = getRoomSchedulesByRoomId($reservation['room_id']);

            require 'views/select_reservation_date.php';
            return;
        }

        insertReservation(
            $reservation['user_id'],
            $reservation['animal_id'],
            $reservation['room_id'],
            $reservation['monitor_id'],
            $date,
            $start_time,
            $end_time,
            $reservation['companions'],
            $reservation['reason'],
            $reservation['status']
        );

        unset($_SESSION['reservation']);

        header("Location: " . BASE_URL . "reservas");
        exit;

    } else {

        $user = getUserById($reservation['user_id']);
        $animal = getAnimalById($reservation['animal_id']);
        $room = getRoomById($reservation['room_id']);
        $monitor = $reservation['monitor_id'] ? getUserById($reservation['monitor_id']) : '';
        $schedules = getRoomSchedulesByRoomId($reservation['room_id']);
        $reservations = getReservationsByUserIdOrAnimalIdOrRoomIdOrMonitorId($reservation['user_id'], $reservation['animal_id'], $reservation['room_id'], $reservation['monitor_id']);

        require 'views/select_reservation_date.php';
    }
}

function listReservationUsers()
{
    $search = $_GET['search'] ?? '';
    $order = $_GET['order'] ?? '';
    $role = 'usuario';
    $active = 1;
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $per_page = 8;

    $total_users = countUsers($search, $role, $active);
    $total_pages = $total_users > 0 ? ceil($total_users / $per_page) : 1;

    $offset = ($page - 1) * $per_page;
    $users = getUsers($search, $order, $role, $active, $per_page, $offset);

    require 'views/lists/users_reservation_list.php';
    exit;
}

function listReservationAnimals()
{
    $search = $_GET['search'] ?? '';
    $order = $_GET['order'] ?? '';
    $species_id = $_GET['species_id'] ?? '';
    $gender = $_GET['gender'] ?? '';
    $status = 'sin adoptar';
    $active = 1;
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $per_page = 8;

    $total_animals = countAnimals($search, $species_id, $gender, $status, $active, true);
    $total_pages = $total_animals > 0 ? ceil($total_animals / $per_page) : 1;

    $offset = ($page - 1) * $per_page;
    $animals = getAnimals($search, $order, $species_id, $gender, $status, $active, $per_page, $offset, true);

    require 'views/lists/animals_reservation_list.php';
    exit;
}

function listReservationRooms()
{
    $search = $_GET['search'] ?? '';
    $order = $_GET['order'] ?? '';
    $active = 1;
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $per_page = 8;

    $total_rooms = countRooms($search, $active);
    $total_pages = $total_rooms > 0 ? ceil($total_rooms / $per_page) : 1;

    $offset = ($page - 1) * $per_page;
    $rooms = getRooms($search, $order, $active, $per_page, $offset);

    require 'views/lists/rooms_reservation_list.php';
    exit;
}

function listReservationMonitors()
{
    $search = $_GET['search'] ?? '';
    $order = $_GET['order'] ?? '';
    $role = 'monitor';
    $active = 1;
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $per_page = 8;

    $total_users = countUsers($search, $role, $active);
    $total_pages = $total_users > 0 ? ceil($total_users / $per_page) : 1;

    $offset = ($page - 1) * $per_page;
    $users = getUsers($search, $order, $role, $active, $per_page, $offset);

    require 'views/lists/users_reservation_list.php';
    exit;
}

function generateSlots($start, $end, $interval = 60, $step = 5)
{
    $slots = [];

    $current = strtotime($start);
    $end_time = strtotime($end);

    while ($current + ($interval * 60) <= $end_time) {

        $next = $current + ($interval * 60);

        $slots[] = [
            "start" => date("H:i", $current),
            "end" => date("H:i", $next)
        ];

        $current += ($step * 60);
    }

    return $slots;
}

function calendarAvailability()
{
    $user_id = $_GET['user_id'];
    $animal_id = $_GET['animal_id'];
    $room_id = $_GET['room_id'];
    $monitor_id = $_GET['monitor_id'];

    $schedules = getRoomSchedulesByRoomId($room_id);
    $reservations = getReservationsByUserIdOrAnimalIdOrRoomIdOrMonitorId(
        $user_id,
        $animal_id,
        $room_id,
        $monitor_id
    );


    $result = [];

    $today = new DateTime();

    $year = $_GET['year'] ?? date('Y');
    $month = $_GET['month'] ?? date('m');
    $days = cal_days_in_month(CAL_GREGORIAN, $month, $year);

    for ($i = 0; $i < $days; $i++) {

        $date = new DateTime("$year-$month-01");
        $date->modify("+$i day");

        $days_map = [
            'monday' => 'lunes',
            'tuesday' => 'martes',
            'wednesday' => 'miercoles',
            'thursday' => 'jueves',
            'friday' => 'viernes',
            'saturday' => 'sabado',
            'sunday' => 'domingo'
        ];

        $english_day = strtolower($date->format("l"));

        $day_name = $days_map[$english_day];

        $day_schedules = array_filter($schedules, fn($s) => $s['day_of_week'] == $day_name);

        $slots = [];

        foreach ($day_schedules as $schedule) {

            $interval = $_GET['interval'] ?? 30;

            $step = isset($_GET['step']) ? (int) $_GET['step'] : 5;

            $generated = generateSlots(
                $schedule['start_time'],
                $schedule['end_time'],
                (int) $interval,
                $step
            );

            foreach ($generated as $slot) {

                $start_date_time = $date->format("Y-m-d") . " " . $slot['start'];
                $end_date_time = $date->format("Y-m-d") . " " . $slot['end'];

                $busy = false;

                $buffer = 5 * 60;

                foreach ($reservations as $r) {

                    if (isset($r['date']) && $r['date'] !== $date->format("Y-m-d")) {
                        continue;
                    }

                    $r_start = strtotime($date->format("Y-m-d") . " " . $r['start_time']) - $buffer;
                    $r_end = strtotime($date->format("Y-m-d") . " " . $r['end_time']) + $buffer;

                    $slot_start = strtotime($start_date_time);
                    $slot_end = strtotime($end_date_time);

                    if ($slot_start < $r_end && $slot_end > $r_start) {
                        $busy = true;
                        break;
                    }
                }

                $slots[] = [
                    "start" => $slot['start'],
                    "end" => $slot['end'],
                    "status" => $busy ? "busy" : "free"
                ];
            }
        }

        $result[] = [
            "date" => $date->format("Y-m-d"),
            "slots" => $slots
        ];
    }

    header("Content-Type: application/json");
    echo json_encode($result);
    exit;
}

function removeReservation()
{
    $is_ajax = isset($_GET['ajax']);
    if (empty($_SESSION['user']) || $_SESSION['user']['role'] != 'administrador') {
        if ($is_ajax) {
            echo json_encode([
                'success' => false,
                'message' => 'No autorizado'
            ]);
        } else {
            header("Location: " . BASE_URL . "inicio");
        }
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = $_POST['id'] ?? null;

        if (!$id) {
            echo json_encode([
                'success' => false,
                'message' => 'ID inválido'
            ]);
            exit();
        }

        $result = deleteReservation($id);

        if ($result) {
            echo json_encode([
                'success' => true,
                'message' => 'Eliminada correctamente'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'No puedes eliminar esta reserva'
            ]);
        }
        exit();
    }
}

function cancelReservation()
{
    $is_ajax = isset($_GET['ajax']);
    if (empty($_SESSION['user']) || $_SESSION['user']['role'] != 'usuario') {
        if ($is_ajax) {
            echo json_encode([
                'success' => false,
                'message' => 'No autorizado'
            ]);
        } else {
            header("Location: " . BASE_URL . "inicio");
        }
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = $_POST['id'] ?? null;

        if (!$id) {
            echo json_encode([
                'success' => false,
                'message' => 'ID inválido'
            ]);
            exit();
        }

        $result = changeReservationStatus($id, "cancelada");

        if ($result) {
            echo json_encode([
                'success' => true,
                'message' => 'Cancelada correctamente'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'No puedes cancelar esta reserva'
            ]);
        }
        exit();
    }
}

function assignMonitor()
{
    $is_ajax = isset($_GET['ajax']);
    if (empty($_SESSION['user']) || $_SESSION['user']['role'] != 'monitor') {
        if ($is_ajax) {
            echo json_encode([
                'success' => false,
                'message' => 'No autorizado'
            ]);
        } else {
            header("Location: " . BASE_URL . "inicio");
        }
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = $_POST['id'] ?? null;
        $action = $_POST['action'] ?? null;

        if (!$id) {
            echo json_encode([
                'success' => false,
                'message' => 'ID inválido'
            ]);
            exit();
        }

        $reservation = getReservationById($id);

        if (!$reservation || ($reservation['monitor_id'] && $reservation['monitor_id'] != $_SESSION['user']['id'])) {
            echo json_encode([
                'success' => false,
                'message' => 'Reserva inválida'
            ]);
            exit();
        }

        $result = null;

        if ($action == "take") {
            $result = assignReservationMonitor($id, $_SESSION['user']['id']);
        } else {
            $result = assignReservationMonitor($id, null);
        }

        if ($result && $action == "take") {
            echo json_encode([
                'success' => true,
                'message' => 'Tomada correctamente'
            ]);
        } else if ($result && $action != "take") {
            echo json_encode([
                'success' => true,
                'message' => 'Dejada correctamente'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'No puedes asignar esta reserva'
            ]);
        }
        exit();
    }
}

function acceptReservation()
{
    $is_ajax = isset($_GET['ajax']);
    if (empty($_SESSION['user']) || $_SESSION['user']['role'] == 'usuario') {
        if ($is_ajax) {
            echo json_encode([
                'success' => false,
                'message' => 'No autorizado'
            ]);
        } else {
            header("Location: " . BASE_URL . "inicio");
        }
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = $_POST['id'] ?? null;

        if (!$id) {
            echo json_encode([
                'success' => false,
                'message' => 'ID inválido'
            ]);
            exit();
        }

        $reservation = getReservationById($id);

        if (!$reservation || ($_SESSION['user']['role'] == 'monitor' && $reservation['monitor_id'] && $reservation['monitor_id'] != $_SESSION['user']['id'])) {
            echo json_encode([
                'success' => false,
                'message' => 'Reserva inválida'
            ]);
            exit();
        }

        $result = changeReservationStatus($id, "aceptada");

        if ($result) {
            echo json_encode([
                'success' => true,
                'message' => 'Aceptada correctamente'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'No puedes aceptar esta reserva'
            ]);
        }
        exit();
    }
}

function requestReservation()
{
    $errors = [];

    $prefill_animal_id = $_GET['animal_id'] ?? null;
    $prefill_room_id = $_GET['room_id'] ?? null;

    $species = getSpecies("", "", 1000, 0);

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $reason = trim($_POST['reason'] ?? '');
        $companions = trim($_POST['companions'] ?? '');
        $animal_id = trim($_POST['animal-id'] ?? '');
        $room_id = trim($_POST['room-id'] ?? '');

        if (empty($reason)) {
            $errors[] = "El motivo es obligatorio.";
        }

        if (!is_numeric($companions) || (int) $companions < 0) {
            $errors[] = "Los acompañantes deben ser 0 o más.";
        }

        if (empty($animal_id)) {
            $errors[] = "Debes seleccionar un animal.";
        } else if (!getAnimalById($animal_id)) {
            $errors[] = "El animal no existe.";
        }

        $room = null;

        if (empty($room_id)) {
            $errors[] = "Debes seleccionar una sala.";
        } else {
            $room = getRoomById($room_id);
            if (!$room) {
                $errors[] = "La sala no existe.";
            }
        }

        if ($room && is_numeric($companions) && $room['capacity'] <= $companions) {
            $errors[] = "La capacidad de la sala no permite tantos acompañantes.";
        }

        if (!empty($errors)) {
            $reservation = [
                'reason' => $reason,
                'companions' => $companions,
                'status' => "pendiente",
                'user_id' => $_SESSION['user']['id'],
                'animal_id' => $animal_id,
                'room_id' => $room_id,
                'monitor_id' => null,
            ];
            $users = $users ?? [];
            $animals = $animals ?? [];
            $rooms = $rooms ?? [];
            $page = $page ?? 1;
            $total_pages = $total_pages ?? 1;

            require 'views/request_reservation.php';
            return;
        }

        $_SESSION['reservation'] = [
            'reason' => $reason,
            'companions' => $companions,
            'status' => "pendiente",
            'user_id' => $_SESSION['user']['id'],
            'animal_id' => $animal_id,
            'room_id' => $room_id,
            'monitor_id' => null,
        ];

        header("Location: " . BASE_URL . "seleccionar_fecha_reserva");
        exit;

    } else {
        $reservation = [
            'reason' => '',
            'companions' => '',
            'status' => 'pendiente',
            'user_id' => $_SESSION['user']['id'],
            'animal_id' => $prefill_animal_id ?? '',
            'room_id' => $prefill_room_id ?? '',
            'monitor_id' => null,
        ];
        $users = $users ?? [];
        $animals = $animals ?? [];
        $rooms = $rooms ?? [];
        $page = $page ?? 1;
        $total_pages = $total_pages ?? 1;
        require 'views/request_reservation.php';
    }
}
?>