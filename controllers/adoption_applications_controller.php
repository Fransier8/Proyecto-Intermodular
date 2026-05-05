<?php
require_once 'models/adoption_applications_model.php';
require_once 'models/animals_model.php';
require_once 'models/users_model.php';

function listAdoptionApplications()
{
    $search = $_GET['search'] ?? '';
    $order = $_GET['order'] ?? '';
    $status = $_GET['status'] ?? '';
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $per_page = 8;

    if ($_SESSION['user']['role'] == 'administrador') {
        $total_adoption_applications = countAdoptionApplications($search, $status);
    } else {
        $total_adoption_applications = countAdoptionApplicationsByUserId($search, $status, $_SESSION['user']['id']);
    }

    $total_pages = $total_adoption_applications > 0 ? ceil($total_adoption_applications / $per_page) : 1;

    $offset = ($page - 1) * $per_page;
    if ($_SESSION['user']['role'] == 'administrador') {
        $adoption_applications = getAdoptionApplications($search, $order, $status, $per_page, $offset);
    } else {
        $adoption_applications = getAdoptionApplicationsByUserId($search, $order, $status, $_SESSION['user']['id'], $per_page, $offset);
    }

    if (isset($_GET['ajax'])) {
        require 'views/lists/adoption_applications_list.php';
        exit;
    }

    require 'views/adoption_applications.php';
}

function createAdoptionApplication()
{
    $errors = [];
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $animal_id = trim($_POST['id'] ?? '');
        $message = trim($_POST['message'] ?? '');
        $user_id = $_SESSION['user']['id'];

        if (empty($animal_id) || empty($user_id)) {
            $errors[] = "Error con los ids.";
        }

        $animal = getAnimalById($animal_id);

        if (!$animal || !$animal['active'] || $animal['status'] != "sin adoptar") {
            $errors[] = "No puedes adoptar este animal.";
        }

        if (empty($message)) {
            $errors[] = "El mensaje es obligatorio.";
        }

        if (!empty($errors)) {
            $adoption_application = [
                'message' => $message
            ];
            require 'views/adopt_animal.php';
            return;
        }

        insertAdoptionApplication($user_id, $animal_id, $message);

        header("Location: " . BASE_URL . "solicitudes_de_adopcion");
        exit();
    } else {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header("Location: " . BASE_URL . "animales");
            exit();
        }
        $animal = getAnimalById($id);
        if (!$animal || !$animal['active'] || $animal['status'] != "sin adoptar") {
            header("Location: " . BASE_URL . "animales");
            exit();
        }
        $adoption_application = [
            'message' => ''
        ];
        require 'views/adopt_animal.php';
    }
}

function editAdoptionApplication()
{
    $errors = [];
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $id = trim($_POST['id'] ?? '');

        $adoption_application = getAdoptionApplicationById($id);
        $animal = getAnimalById($adoption_application['animal_id']);
        $user = getUserById($adoption_application['user_id']);

        $action = $_POST['action'] ?? null;

        $data = null;

        switch ($action) {
            case 'aceptar':
                $data = [
                    'status' => "aceptada"
                ];

                if ($animal['status'] == "adoptado") {
                    $errors[] = "El animal ya está adoptado.";
                }

                if ($animal['status'] == "reservado" && $animal['user_id'] != $adoption_application['user_id']) {
                    $errors[] = "El animal está reservado por otro usuario.";
                }

                break;

            case 'reservar':
                $data = [
                    'status' => "reservado"
                ];

                if ($animal['status'] == "adoptado") {
                    $errors[] = "El animal está adoptado.";
                }

                if ($animal['status'] == "reservado") {
                    $errors[] = "El animal ya está reservado.";
                }

                break;

            case 'denegar':
                $data = [
                    'status' => "denegada"
                ];
                break;
        }

        if (!in_array($action, ['aceptar', 'reservar', 'denegar'])) {
            $errors[] = "Acción no válida.";
        }

        if ($adoption_application['status'] != "pendiente" && $adoption_application['status'] != "reservado") {
            $errors[] = "Error con la solicitud de adopción o su estado.";
        }

        if (!empty($errors)) {
            require 'views/edit_adoption_application.php';
            return;
        }

        updateAdoptionApplication($id, $data);

        if ($data['status'] == "aceptada") {
            $animal_data = [
                'status' => "adoptado",
                'user_id' => $adoption_application['user_id']
            ];
            updateAnimalAdoptionStatus($adoption_application['animal_id'], $animal_data);
            $pending_data = [
                'status' => "denegada",
                'animal_id' => $adoption_application['animal_id']
            ];
            updatePendingAdoptionApplications($pending_data);
        } else if ($data['status'] == "reservado") {
            $animal_data = [
                'status' => "reservado",
                'user_id' => $adoption_application['user_id']
            ];
            updateAnimalAdoptionStatus($adoption_application['animal_id'], $animal_data);
        }

        header("Location: " . BASE_URL . "solicitudes_de_adopcion");
        exit();

    } else {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header("Location: " . BASE_URL . "solicitudes_de_adopcion");
            exit();
        }
        $adoption_application = getAdoptionApplicationById($id);
        if (!$adoption_application || ($adoption_application['status'] != "pendiente" && $adoption_application['status'] != "reservado")) {
            header("Location: " . BASE_URL . "solicitudes_de_adopcion");
            exit();
        }
        $animal = getAnimalById($adoption_application['animal_id']);
        $user = getUserById($adoption_application['user_id']);

        require 'views/edit_adoption_application.php';
    }
}

function removeAdoptionApplication()
{
    $is_ajax = isset($_GET['ajax']);
    if (empty($_SESSION['user']) || $_SESSION['user']['role'] != 'administrador') {
        if ($is_ajax) {
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

        $result = deleteAdoptionApplication($id);

        if ($result) {
            echo json_encode([
                'success' => true,
                'message' => 'Eliminada correctamente'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'No puedes eliminar esta solicitud de adopción'
            ]);
        }
        exit();
    }
}
?>