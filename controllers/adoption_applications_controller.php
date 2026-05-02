<?php
require_once 'models/adoption_applications_model.php';

function listAdoptionApplications()
{
    $search = $_GET['search'] ?? '';
    $order = $_GET['order'] ?? '';
    $status = $_GET['status'] ?? '';
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $per_page = 8;

    $total_adoption_applications = countAdoptionApplications($search, $status);
    $total_pages = $total_adoption_applications > 0 ? ceil($total_adoption_applications / $per_page) : 1;

    $offset = ($page - 1) * $per_page;
    $adoption_applications = getAdoptionApplications($search, $order, $status, $per_page, $offset);

    if (isset($_GET['ajax'])) {
        require 'views/lists/adoption_applications_list.php';
        exit;
    }

    require 'views/adoption_applications.php';
}

?>