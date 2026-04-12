<?php
require_once 'models/animals_model.php';

function listAnimals()
{
    $search = $_GET['search'] ?? '';
    $order = $_GET['order'] ?? '';
    $species_id = $_GET['species_id'] ?? '';
    $gender = $_GET['gender'] ?? '';
    $status = $_GET['status'] ?? '';
    if ($_SESSION['user']['role'] == "administrador") {
        $active = $_GET['active'] ?? '';
    } else {
        $active = 1;
    }
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $per_page = 8;

    $total_animals = countAnimals($search, $species_id, $gender, $status, $active);
    $total_pages = $total_animals > 0 ? ceil($total_animals / $per_page) : 1;

    $offset = ($page - 1) * $per_page;
    $animals = getAnimals($search, $order, $species_id, $gender, $status, $active, $per_page, $offset);

    if (isset($_GET['ajax'])) {
        require 'views/lists/animals_list.php';
        exit;
    }
    if ($_SESSION['user']['role'] == "administrador") {
        require 'views/animals.php';
    } else {
        require 'views/public_animals.php';
    }
}

function viewAnimalDetails()
{
    require 'views/animal.php';
}

function listMyAnimals()
{
    require 'views/my_animals.php';
}
?>