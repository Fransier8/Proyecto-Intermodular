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

        if (strtotime($start_time) >= strtotime($end_time)) {
            $errors[] = "La hora de inicio debe ser anterior a la de fin.";
        }

        if (strtotime($date) <= strtotime(date('Y-m-d'))) {
            $errors[] = "No puedes seleccionar fechas de hoy o anteriores.";
        }

        $reservations = getReservationsByUserIdOrAnimalIdOrRoomIdOrMonitorId($reservation['user_id'], $reservation['animal_id'], $reservation['room_id'], $reservation['monitor_id']);

        $new_start = strtotime($date . " " . $start_time);
        $new_end = strtotime($date . " " . $end_time);

        $buffer = 5 * 60;

        foreach ($reservations as $r) {

            if (
                $r['status'] === "cancelada" ||
                $r['status'] === "denegada"
            ) {
                continue;
            }

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

        $user_test = null;

        if (empty($reservation['user_id'])) {
            $errors[] = "Debes seleccionar un usuario.";
        } else {
            $user_test = getUserById($reservation['user_id']);
            if (!$user_test || !$user_test['active']) {
                $errors[] = "El usuario no existe.";
            }
        }

        $animal_test = null;

        if (empty($reservation['animal_id'])) {
            $errors[] = "Debes seleccionar un animal.";
        } else {
            $animal_test = getAnimalById($reservation['animal_id']);
            if (!$animal_test || !$animal_test['active']) {
                $errors[] = "El animal no existe.";
            }
        }

        $room_test = null;

        if (empty($reservation['room_id'])) {
            $errors[] = "Debes seleccionar una sala.";
        } else {
            $room_test = getRoomById($reservation['room_id']);
            if (!$room_test || !$room_test['active']) {
                $errors[] = "La sala no existe.";
            }
        }

        if ($room_test && is_numeric($reservation['companions']) && $room_test['capacity'] <= $reservation['companions']) {
            $errors[] = "La capacidad de la sala no permite tantos acompañantes.";
        }

        $monitor_test = null;

        if ($reservation['status'] == "aceptada" && empty($reservation['monitor_id'])) {
            $errors[] = "Debes seleccionar un monitor.";
        } else if ($reservation['status'] == "aceptada") {
            $monitor_test = getUserById($reservation['monitor_id']);
            if (!$monitor_test || !$monitor_test['active']) {
                $errors[] = "El monitor no existe.";
            }
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
    $monitor_id = $_GET['monitor_id'] ?? null;
    $exclude_id = $_GET['reservation_id'] ?? null;

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

                    if ($exclude_id && $r['id'] == $exclude_id) {
                        continue;
                    }

                    if (
                        $r['status'] === "cancelada" ||
                        $r['status'] === "denegada"
                    ) {
                        continue;
                    }

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

        if (!$reservation || ($_SESSION['user']['role'] == 'administrador' && !$reservation['monitor_id']) || ($_SESSION['user']['role'] == 'monitor' && $reservation['monitor_id'] && $reservation['monitor_id'] != $_SESSION['user']['id'])) {
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

        header("Location: " . BASE_URL . "seleccionar_fecha_reserva_solicitada");
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

function selectReservationDateUser()
{
    $errors = [];

    $reservation = $_SESSION['reservation'] ?? null;

    if (!$reservation) {
        header("Location: " . BASE_URL . "solicitar_reserva");
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

        if (strtotime($start_time) >= strtotime($end_time)) {
            $errors[] = "La hora de inicio debe ser anterior a la de fin.";
        }

        if (strtotime($date) <= strtotime(date('Y-m-d'))) {
            $errors[] = "No puedes seleccionar fechas de hoy o anteriores.";
        }

        $reservations = getReservationsByUserIdOrAnimalIdOrRoomIdOrMonitorId($reservation['user_id'], $reservation['animal_id'], $reservation['room_id'], null);

        $same_day_reservations = array_filter(
            $reservations,
            function ($r) use ($date) {

                return (
                    isset($r['date']) &&
                    $r['date'] === $date &&
                    $r['status'] !== "cancelada" &&
                    $r['status'] !== "denegada"
                );
            }
        );

        if (count($same_day_reservations) >= 1) {
            $errors[] =
                "No puedes realizar más de una reserva el mismo día.";
        }

        $selected_week = getWeekNumber($date);

        $selected_year = date('Y', strtotime($date));

        $animal_reservations_week = array_filter(
            $reservations,
            function ($r) use ($reservation, $selected_week, $selected_year) {

                return (
                    $r['status'] !== "cancelada" &&
                    $r['status'] !== "denegada" &&
                    (int) $r['animal_id'] === (int) $reservation['animal_id'] &&
                    getWeekNumber($r['date']) === $selected_week &&
                    date('Y', strtotime($r['date'])) == $selected_year
                );
            }
        );

        if (count($animal_reservations_week) >= 2) {
            $errors[] =
                "No puedes reservar el mismo animal más de 2 veces por semana.";
        }

        $room_reservations_week = array_filter(
            $reservations,
            function ($r) use ($reservation, $selected_week, $selected_year) {

                return (
                    $r['status'] !== "cancelada" &&
                    $r['status'] !== "denegada" &&
                    (int) $r['room_id'] === (int) $reservation['room_id'] &&
                    getWeekNumber($r['date']) === $selected_week &&
                    date('Y', strtotime($r['date'])) == $selected_year
                );
            }
        );

        if (count($room_reservations_week) >= 2) {
            $errors[] =
                "No puedes reservar la misma sala más de 2 veces por semana.";
        }

        $new_start = strtotime($date . " " . $start_time);
        $new_end = strtotime($date . " " . $end_time);

        $buffer = 5 * 60;

        foreach ($reservations as $r) {

            if (
                $r['status'] === "cancelada" ||
                $r['status'] === "denegada"
            ) {
                continue;
            }

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

        $user_test = null;

        if (empty($_SESSION['user']['id'])) {
            $errors[] = "Debes seleccionar un usuario.";
        } else {
            $user_test = getUserById($_SESSION['user']['id']);
            if (!$user_test || !$user_test['active']) {
                $errors[] = "El usuario no existe.";
            }
        }

        $animal_test = null;

        if (empty($reservation['animal_id'])) {
            $errors[] = "Debes seleccionar un animal.";
        } else {
            $animal_test = getAnimalById($reservation['animal_id']);
            if (!$animal_test || !$animal_test['active']) {
                $errors[] = "El animal no existe.";
            }
        }

        $room_test = null;

        if (empty($reservation['room_id'])) {
            $errors[] = "Debes seleccionar una sala.";
        } else {
            $room_test = getRoomById($reservation['room_id']);
            if (!$room_test || !$room_test['active']) {
                $errors[] = "La sala no existe.";
            }
        }

        if ($room_test && is_numeric($reservation['companions']) && $room_test['capacity'] <= $reservation['companions']) {
            $errors[] = "La capacidad de la sala no permite tantos acompañantes.";
        }


        if (!empty($errors)) {

            $user = getUserById($_SESSION['user']['id']);
            $animal = getAnimalById($reservation['animal_id']);
            $room = getRoomById($reservation['room_id']);
            $schedules = getRoomSchedulesByRoomId($reservation['room_id']);

            require 'views/public_select_reservation_date.php';
            return;
        }

        insertReservation(
            $_SESSION['user']['id'],
            $reservation['animal_id'],
            $reservation['room_id'],
            null,
            $date,
            $start_time,
            $end_time,
            $reservation['companions'],
            $reservation['reason'],
            "pendiente"
        );

        unset($_SESSION['reservation']);

        header("Location: " . BASE_URL . "mis_reservas");
        exit;

    } else {

        $animal = getAnimalById($reservation['animal_id']);
        $room = getRoomById($reservation['room_id']);
        $schedules = getRoomSchedulesByRoomId($reservation['room_id']);
        $reservations = getReservationsByUserIdOrAnimalIdOrRoomIdOrMonitorId($_SESSION['user']['id'], $reservation['animal_id'], $reservation['room_id'], null);

        require 'views/public_select_reservation_date.php';
    }
}

function getWeekNumber($date)
{
    return (int) date('W', strtotime($date));
}

function editReservation()
{
    $errors = [];

    $species = getSpecies("", "", 1000, 0);

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $id = $_POST['reservation-id'] ?? null;
        $reason = trim($_POST['reason'] ?? '');
        $companions = trim($_POST['companions'] ?? '');
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

        if (!empty($monitor_id) && !getUserById($monitor_id)) {
            $errors[] = "El monitor no existe.";
        }

        $current_reservation = getReservationById($id);

        if (!$current_reservation) {
            $errors[] = "Error de id.";
        }

        if (!empty($errors)) {
            $reservation = array_merge(
                getReservationById($id),
                [
                    'reason' => $reason,
                    'companions' => $companions,
                    'user_id' => $user_id,
                    'animal_id' => $animal_id,
                    'room_id' => $room_id,
                    'monitor_id' => $monitor_id,
                ]
            );

            $users = $users ?? [];
            $animals = $animals ?? [];
            $rooms = $rooms ?? [];
            $page = $page ?? 1;
            $total_pages = $total_pages ?? 1;

            require 'views/edit_reservation.php';
            return;
        }

        $start = strtotime($current_reservation['start_time']);
        $end = strtotime($current_reservation['end_time']);

        $interval = ($end - $start) / 60;

        $_SESSION['reservation'] = [
            'id' => $id,
            'reason' => $reason,
            'companions' => $companions,
            'user_id' => $user_id,
            'animal_id' => $animal_id,
            'room_id' => $room_id,
            'monitor_id' => $monitor_id,
            'date' => $current_reservation['date'],
            'start_time' => $current_reservation['start_time'],
            'end_time' => $current_reservation['end_time'],
            'interval' => $interval
        ];

        header("Location: " . BASE_URL . "modificar_fecha_reserva");
        exit;

    } else {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header("Location: " . BASE_URL . "reservas");
            exit();
        }
        $reservation = getReservationById($id);
        if (!$reservation) {
            header("Location: " . BASE_URL . "reservas");
            exit();
        }

        $users = $users ?? [];
        $animals = $animals ?? [];
        $rooms = $rooms ?? [];
        $page = $page ?? 1;
        $total_pages = $total_pages ?? 1;
        require 'views/edit_reservation.php';
    }
}

function editReservationDate()
{
    $errors = [];

    $reservation = $_SESSION['reservation'] ?? null;

    if (!$reservation) {
        header("Location: " . BASE_URL . "reservas");
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

        if (strtotime($start_time) >= strtotime($end_time)) {
            $errors[] = "La hora de inicio debe ser anterior a la de fin.";
        }

        if (strtotime($date) <= strtotime(date('Y-m-d'))) {
            $errors[] = "No puedes seleccionar fechas de hoy o anteriores.";
        }

        $reservations = getReservationsByUserIdOrAnimalIdOrRoomIdOrMonitorId($reservation['user_id'], $reservation['animal_id'], $reservation['room_id'], $reservation['monitor_id']);

        $new_start = strtotime($date . " " . $start_time);
        $new_end = strtotime($date . " " . $end_time);

        $buffer = 5 * 60;

        foreach ($reservations as $r) {

            if ($r['id'] == $reservation['id']) {
                continue;
            }

            if (
                $r['status'] === "cancelada" ||
                $r['status'] === "denegada"
            ) {
                continue;
            }

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

        $current_reservation = getReservationById($reservation['id']);

        if (!$current_reservation) {
            $errors[] = "Error de id.";
        } else if ($current_reservation['status'] != "pendiente") {
            $errors[] = "Estado incorrecto.";
        }

        $user_test = null;

        if (empty($reservation['user_id'])) {
            $errors[] = "Debes seleccionar un usuario.";
        } else {
            $user_test = getUserById($reservation['user_id']);
            if (!$user_test || !$user_test['active']) {
                $errors[] = "El usuario no existe.";
            }
        }

        $animal_test = null;

        if (empty($reservation['animal_id'])) {
            $errors[] = "Debes seleccionar un animal.";
        } else {
            $animal_test = getAnimalById($reservation['animal_id']);
            if (!$animal_test || !$animal_test['active']) {
                $errors[] = "El animal no existe.";
            }
        }

        $room_test = null;

        if (empty($reservation['room_id'])) {
            $errors[] = "Debes seleccionar una sala.";
        } else {
            $room_test = getRoomById($reservation['room_id']);
            if (!$room_test || !$room_test['active']) {
                $errors[] = "La sala no existe.";
            }
        }

        if ($room_test && is_numeric($reservation['companions']) && $room_test['capacity'] <= $reservation['companions']) {
            $errors[] = "La capacidad de la sala no permite tantos acompañantes.";
        }

        $monitor_test = null;


        if (!empty($reservation['monitor_id'])) {
            $monitor_test = getUserById($reservation['monitor_id']);
            if (!$monitor_test || !$monitor_test['active']) {
                $errors[] = "El monitor no existe.";
            }
        }


        if (!empty($errors)) {

            $user = getUserById($reservation['user_id']);
            $animal = getAnimalById($reservation['animal_id']);
            $room = getRoomById($reservation['room_id']);
            $monitor = $reservation['monitor_id'] ? getUserById($reservation['monitor_id']) : '';
            $schedules = getRoomSchedulesByRoomId($reservation['room_id']);

            require 'views/edit_reservation_date.php';
            return;
        }

        $data = [
            'user_id' => $reservation['user_id'],
            'animal_id' => $reservation['animal_id'],
            'room_id' => $reservation['room_id'],
            'monitor_id' => $reservation['monitor_id'],
            'date' => $date,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'companions' => $reservation['companions'],
            'reason' => $reservation['reason'],
            'status' => "pendiente"
        ];

        updateReservation($reservation['id'], $data);

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

        require 'views/edit_reservation_date.php';
    }
}


function denyReservation()
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

        $reservation = getReservationById($id);

        if (!$reservation || !$reservation['monitor_id']) {
            echo json_encode([
                'success' => false,
                'message' => 'Reserva inválida'
            ]);
            exit();
        }

        $result = changeReservationStatus($id, "denegada");

        if ($result) {
            echo json_encode([
                'success' => true,
                'message' => 'Denegada correctamente'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'No puedes denegar esta reserva'
            ]);
        }
        exit();
    }
}

function editReservationRequest()
{
    $errors = [];

    $species = getSpecies("", "", 1000, 0);

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $id = $_POST['reservation-id'] ?? null;
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

        $current_reservation = getReservationById($id);

        if (!$current_reservation) {
            $errors[] = "Error de id.";
        }

        if (!empty($errors)) {
            $reservation = array_merge(
                getReservationById($id),
                [
                    'reason' => $reason,
                    'companions' => $companions,
                    'user_id' => $_SESSION['user']['id'],
                    'animal_id' => $animal_id,
                    'room_id' => $room_id,
                    'monitor_id' => $current_reservation['monitor_id'],
                ]
            );

            $users = $users ?? [];
            $animals = $animals ?? [];
            $rooms = $rooms ?? [];
            $page = $page ?? 1;
            $total_pages = $total_pages ?? 1;

            require 'views/public_edit_reservation.php';
            return;
        }

        $start = strtotime($current_reservation['start_time']);
        $end = strtotime($current_reservation['end_time']);

        $interval = ($end - $start) / 60;

        $_SESSION['reservation'] = [
            'id' => $id,
            'reason' => $reason,
            'companions' => $companions,
            'user_id' => $_SESSION['user']['id'],
            'animal_id' => $animal_id,
            'room_id' => $room_id,
            'monitor_id' => $current_reservation['monitor_id'],
            'date' => $current_reservation['date'],
            'start_time' => $current_reservation['start_time'],
            'end_time' => $current_reservation['end_time'],
            'interval' => $interval
        ];

        header("Location: " . BASE_URL . "modificar_fecha_solicitud_reserva");
        exit;

    } else {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header("Location: " . BASE_URL . "mis_reservas");
            exit();
        }
        $reservation = getReservationById($id);
        if (!$reservation) {
            header("Location: " . BASE_URL . "mis_reservas");
            exit();
        }

        $users = $users ?? [];
        $animals = $animals ?? [];
        $rooms = $rooms ?? [];
        $page = $page ?? 1;
        $total_pages = $total_pages ?? 1;
        require 'views/public_edit_reservation.php';
    }
}

function editReservationRequestDate()
{
    $errors = [];

    $reservation = $_SESSION['reservation'] ?? null;

    if (!$reservation) {
        header("Location: " . BASE_URL . "mis_reservas");
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

        if (strtotime($start_time) >= strtotime($end_time)) {
            $errors[] = "La hora de inicio debe ser anterior a la de fin.";
        }

        if (strtotime($date) <= strtotime(date('Y-m-d'))) {
            $errors[] = "No puedes seleccionar fechas de hoy o anteriores.";
        }

        $reservations = getReservationsByUserIdOrAnimalIdOrRoomIdOrMonitorId($_SESSION['user']['id'], $reservation['animal_id'], $reservation['room_id'], $reservation['monitor_id']);

        $new_start = strtotime($date . " " . $start_time);
        $new_end = strtotime($date . " " . $end_time);

        $buffer = 5 * 60;

        foreach ($reservations as $r) {

            if ($r['id'] == $reservation['id']) {
                continue;
            }

            if (
                $r['status'] === "cancelada" ||
                $r['status'] === "denegada"
            ) {
                continue;
            }

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

        $current_reservation = getReservationById($reservation['id']);

        if (!$current_reservation) {
            $errors[] = "Error de id.";
        } else if ($current_reservation['status'] != "pendiente") {
            $errors[] = "Estado incorrecto.";
        }

        $user_test = null;

        if (empty($_SESSION['user']['id'])) {
            $errors[] = "Debes seleccionar un usuario.";
        } else {
            $user_test = getUserById($_SESSION['user']['id']);
            if (!$user_test || !$user_test['active']) {
                $errors[] = "El usuario no existe.";
            }
        }

        $animal_test = null;

        if (empty($reservation['animal_id'])) {
            $errors[] = "Debes seleccionar un animal.";
        } else {
            $animal_test = getAnimalById($reservation['animal_id']);
            if (!$animal_test || !$animal_test['active']) {
                $errors[] = "El animal no existe.";
            }
        }

        $room_test = null;

        if (empty($reservation['room_id'])) {
            $errors[] = "Debes seleccionar una sala.";
        } else {
            $room_test = getRoomById($reservation['room_id']);
            if (!$room_test || !$room_test['active']) {
                $errors[] = "La sala no existe.";
            }
        }

        if ($room_test && is_numeric($reservation['companions']) && $room_test['capacity'] <= $reservation['companions']) {
            $errors[] = "La capacidad de la sala no permite tantos acompañantes.";
        }

        $monitor_test = null;

        if (!empty($errors)) {

            $user = getUserById($_SESSION['user']['id']);
            $animal = getAnimalById($reservation['animal_id']);
            $room = getRoomById($reservation['room_id']);
            $monitor = $reservation['monitor_id'] ? getUserById($reservation['monitor_id']) : '';
            $schedules = getRoomSchedulesByRoomId($reservation['room_id']);

            require 'views/public_edit_reservation_date.php';
            return;
        }

        $data = [
            'user_id' => $_SESSION['user']['id'],
            'animal_id' => $reservation['animal_id'],
            'room_id' => $reservation['room_id'],
            'monitor_id' => $current_reservation['monitor_id'],
            'date' => $date,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'companions' => $reservation['companions'],
            'reason' => $reservation['reason'],
            'status' => $current_reservation['status']
        ];

        updateReservation($reservation['id'], $data);

        unset($_SESSION['reservation']);

        header("Location: " . BASE_URL . "mis_reservas");
        exit;

    } else {

        $user = getUserById($_SESSION['user']['id']);
        $animal = getAnimalById($reservation['animal_id']);
        $room = getRoomById($reservation['room_id']);
        $monitor = $reservation['monitor_id'] ? getUserById($reservation['monitor_id']) : '';
        $schedules = getRoomSchedulesByRoomId($reservation['room_id']);
        $reservations = getReservationsByUserIdOrAnimalIdOrRoomIdOrMonitorId($_SESSION['user']['id'], $reservation['animal_id'], $reservation['room_id'], $reservation['monitor_id']);

        require 'views/public_edit_reservation_date.php';
    }
}
?>