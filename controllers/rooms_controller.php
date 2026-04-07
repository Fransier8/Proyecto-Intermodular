<?php
require_once 'models/rooms_model.php';

function listRooms()
{
    $search = $_GET['search'] ?? '';
    $order = $_GET['order'] ?? '';
    $active = $_GET['active'] ?? '';
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $perPage = 8;

    $totalRooms = countRooms($search, $active);
    $totalPages = $totalRooms > 0 ? ceil($totalRooms / $perPage) : 1;

    $offset = ($page - 1) * $perPage;
    $rooms = getRooms($search, $order, $active, $perPage, $offset);

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