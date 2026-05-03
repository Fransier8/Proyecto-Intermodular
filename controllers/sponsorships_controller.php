<?php
require_once 'models/sponsorships_model.php';
require_once 'models/animals_model.php';
require_once 'models/users_model.php';

function listSponsorships()
{
    $search = $_GET['search'] ?? '';
    $order = $_GET['order'] ?? '';
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $per_page = 8;

    $total_sponsorships = countSponsorships($search);
    $total_pages = $total_sponsorships > 0 ? ceil($total_sponsorships / $per_page) : 1;

    $offset = ($page - 1) * $per_page;
    $sponsorships = getSponsorships($search, $order, $per_page, $offset);

    if (isset($_GET['ajax'])) {
        require 'views/lists/sponsorships_list.php';
        exit;
    }

    require 'views/sponsorships.php';
}

function createSponsorship()
{
    $errors = [];
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        
    } else {
        $id = $_GET['id'] ?? null;
        $user_id = $_SESSION['user']['id'];
        if (!$id || !$user_id) {
            header("Location: " . BASE_URL . "animales");
            exit();
        }
        $animal = getAnimalById($id);
        $user = getUserById($user_id);
        if (!$animal || !$user || $animal['status'] != "sin adoptar") {
            header("Location: " . BASE_URL . "animales");
            exit();
        }
        $sponsorship = [
            'amount' => '',
            'message' => ''
        ];
        require 'views/sponsor_animal.php';
    }
}

?>