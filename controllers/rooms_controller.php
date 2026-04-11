<?php
require_once 'models/rooms_model.php';

function listRooms()
{
    $search = $_GET['search'] ?? '';
    $order = $_GET['order'] ?? '';
    $active = $_GET['active'] ?? '';
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
    require 'views/rooms.php';
}

function viewRoomDetails()
{
    require 'views/room.php';
}
?>