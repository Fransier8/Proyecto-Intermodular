<?php
require_once 'models/reservations_model.php';
require_once 'models/animals_model.php';
require_once 'models/species_model.php';

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
        /*$name = trim($_POST['name'] ?? '');

        if (empty($name)) {
            $errors[] = "El nombre es obligatorio.";
        }

        $existing_name = getSpeciesByName($name);
        if ($existing_name) {
            $errors[] = "El nombre ya existe";
        }

        if (!empty($errors)) {
            $species = [
                'name' => $name
            ];

            require 'views/create_species.php';
            return;
        }

        $id = insertSpecies($name);

        header("Location: " . BASE_URL . "especie/$id");
        exit();*/

    } else {
        $reservation = [
            'reason' => ''
        ];
        require 'views/create_reservation.php';
    }
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
?>