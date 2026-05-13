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

    $total_reservations = countReservationsByUserId($search, $status, $_SESSION['user']['id']);

    $total_pages = $total_reservations > 0 ? ceil($total_reservations / $per_page) : 1;

    $offset = ($page - 1) * $per_page;
    $reservations = getReservationsByUserId($search, $order, $status, $_SESSION['user']['id'], $per_page, $offset);

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

        if (empty($reason)) {
            $errors[] = "El motivo es obligatorio.";
        }

        if (!is_numeric($companions) || (int) $companions <= 0) {
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

    $species = getSpecies("", "", 1000, 0);

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $reason = trim($_POST['reason'] ?? '');
        $companions = trim($_POST['companions'] ?? '');
        $status = trim($_POST['status'] ?? '');
        $user_id = trim($_POST['user-id'] ?? '');
        $animal_id = trim($_POST['animal-id'] ?? '');
        $room_id = trim($_POST['room-id'] ?? '');
        $monitor_id = trim($_POST['monitor-id'] ?? '');

        if (empty($reason)) {
            $errors[] = "El motivo es obligatorio.";
        }

        if (!is_numeric($companions) || (int) $companions <= 0) {
            $errors[] = "Los acompañantes deben ser 0 o más.";
        }

        if ($status != "pendiente" && $status != "aceptada") {
            $errors[] = "Estado incorrecto.";
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

function generateSlots($start, $end, $interval = 60)
{
    $slots = [];

    $current = strtotime($start);
    $end_time = strtotime($end);

    while ($current < $end_time) {

        $next = $current + ($interval * 60);

        $slots[] = [
            "start" => date("H:i", $current),
            "end" => date("H:i", $next)
        ];

        $current = $next;
    }

    return $slots;
}

function calendarAvailability()
{
    $room_id = $_GET['room_id'];

    $schedules = getRoomSchedulesByRoomId($room_id);
    //$reservations = getReservationsByRoomId($room_id);
    $reservations = [];


    $result = [];

    $today = new DateTime();

    $year = $_GET['year'] ?? date('Y');
    $month = $_GET['month'] ?? date('m');
    $days = cal_days_in_month(CAL_GREGORIAN, $month, $year);

    for ($i = 0; $i < $days; $i++) {

        $date = new DateTime("$year-$month-01");
        $date->modify("+$i day");

        $day_name = strtolower($date->format("l"));

        $day_schedules = array_filter($schedules, fn($s) => $s['day_of_week'] == $day_name);

        $slots = [];

        foreach ($day_schedules as $schedule) {

            $generated = generateSlots($schedule['start_time'], $schedule['end_time'], 60);

            foreach ($generated as $slot) {

                $start_date_time = $date->format("Y-m-d") . " " . $slot['start'];
                $end_date_time = $date->format("Y-m-d") . " " . $slot['end'];

                $busy = false;

                foreach ($reservations as $r) {
                    if (
                        $start_date_time < $r['end_datetime'] &&
                        $end_date_time > $r['start_datetime']
                    ) {
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
?>