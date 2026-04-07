<?php
require_once 'models/animals_model.php';

function listAnimals()
{
    $search = $_GET['search'] ?? '';
    $order = $_GET['order'] ?? '';
    $species_id = $_GET['species_id'] ?? '';
    $gender = $_GET['gender'] ?? '';
    $status = $_GET['status'] ?? '';
    $active = $_GET['active'] ?? '';
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $perPage = 8;

    $totalAnimals = countAnimals($search, $species_id, $gender, $status, $active);
    $totalPages = $totalAnimals > 0 ? ceil($totalAnimals / $perPage) : 1;

    $offset = ($page - 1) * $perPage;
    $animals = getAnimals($search, $order, $species_id, $gender, $status, $active, $perPage, $offset);

    if (isset($_GET['ajax'])) {
        require 'views/lists/animals_list.php';
        exit;
    }
    require 'views/animals.php';
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