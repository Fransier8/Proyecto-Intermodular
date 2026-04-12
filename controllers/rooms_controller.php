<?php
require_once 'models/rooms_model.php';

function listRooms()
{
    $search = $_GET['search'] ?? '';
    $order = $_GET['order'] ?? '';
    if ($_SESSION['user']['role'] == "administrador") {
        $active = $_GET['active'] ?? '';
    } else {
        $active = 1;
    }
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $per_page = 8;

    $total_rooms = countRooms($search, $active);
    $total_pages = $total_rooms > 0 ? ceil($total_rooms / $per_page) : 1;

    $offset = ($page - 1) * $per_page;
    $rooms = getRooms($search, $order, $active, $per_page, $offset);

    if (isset($_GET['ajax'])) {
        require 'views/lists/rooms_list.php';
        exit;
    }
    if ($_SESSION['user']['role'] == "administrador") {
        require 'views/rooms.php';
    } else {
        require 'views/public_rooms.php';
    }
}

function viewRoomDetails()
{
    require 'views/room.php';
}
?>