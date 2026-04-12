<?php
require_once 'models/species_model.php';

function listSpecies()
{
    $search = $_GET['search'] ?? '';
    $order = $_GET['order'] ?? '';
    $role = $_GET['role'] ?? '';
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $per_page = 8;

    $total_species = countSpecies($search);
    $total_pages = $total_species > 0 ? ceil($total_species / $per_page) : 1;

    $offset = ($page - 1) * $per_page;
    $species = getSpecies($search, $order, $per_page, $offset);

    if (isset($_GET['ajax'])) {
        require 'views/lists/species_list.php';
        exit;
    }

    require 'views/species.php';
}

function viewSpeciesDetails()
{
    $id = $_GET['id'] ?? null;
    if (!$id) {
        header("Location: " . BASE_URL . "especies");
        exit();
    }
    $species = getSpeciesById($id);
    if (!$species) {
        header("Location: " . BASE_URL . "especies");
        exit();
    }
    require 'views/individual_species.php';
}

function createSpecies()
{
    $errors = [];
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $name = trim($_POST['name'] ?? '');

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
        exit();

    } else {
        $species = [
            'name' => ''
        ];
        require 'views/create_species.php';
    }
}

function editSpecies()
{
    $errors = [];
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $id = trim($_POST['id'] ?? '');
        $data = [
            'name' => trim($_POST['name'] ?? '')
        ];

        if (empty($data['name'])) {
            $errors[] = "El nombre es obligatorio.";
        }

        $existing_name = getSpeciesByName($data['name']);
        if ($existing_name && $existing_name['id'] != $id) {
            $errors[] = "El nombre ya existe";
        }

        if (!empty($errors)) {
            $species = array_merge(getSpeciesById($id), $data);

            require 'views/edit_species.php';
            return;
        }

        updateSpecies($id, $data);

        header("Location: " . BASE_URL . "especie/$id");
        exit();

    } else {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header("Location: " . BASE_URL . "especies");
            exit();
        }
        $species = getSpeciesById($id);
        if (!$species) {
            header("Location: " . BASE_URL . "especies");
            exit();
        }
        require 'views/edit_species.php';
    }
}

function removeSpecies()
{
    $isAjax = isset($_GET['ajax']);
    if (empty($_SESSION['user']) || $_SESSION['user']['role'] != 'administrador') {
        if ($isAjax) {
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

        $result = deleteSpecies($id);

        if ($result) {
            echo json_encode([
                'success' => true,
                'message' => 'Eliminado correctamente'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'No puedes eliminar esta especie porque tiene animales asociados'
            ]);
        }
        exit();
    }
}
?>