<?php
require_once 'models/animals_model.php';
require_once 'models/species_model.php';

function listAnimals()
{
    $search = $_GET['search'] ?? '';
    $order = $_GET['order'] ?? '';
    $species_id = $_GET['species_id'] ?? '';
    $gender = $_GET['gender'] ?? '';
    $status = $_GET['status'] ?? '';
    $is_admin = $_SESSION['user']['role'] == "administrador";
    if ($is_admin) {
        $active = $_GET['active'] ?? '';
    } else {
        $active = 1;
    }
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $per_page = 8;

    $total_animals = countAnimals($search, $species_id, $gender, $status, $active, $is_admin);
    $total_pages = $total_animals > 0 ? ceil($total_animals / $per_page) : 1;

    $offset = ($page - 1) * $per_page;
    $animals = getAnimals($search, $order, $species_id, $gender, $status, $active, $per_page, $offset, $is_admin);

    $species = getSpecies("", "", 1000, 0);

    if (isset($_GET['ajax'])) {
        require 'views/lists/animals_list.php';
        exit;
    }
    $_SESSION['from_animals'] = 'animales';
    if ($_SESSION['user']['role'] == "administrador") {
        require 'views/animals.php';
    } else {
        require 'views/public_animals.php';
    }
}

function viewAnimalDetails()
{
    $id = $_GET['id'] ?? null;
    if (!$id) {
        header("Location: " . BASE_URL . "animales");
        exit();
    }
    $animal = getAnimalById($id);
    if (!$animal || ($_SESSION['user']['role'] != "administrador" && (!$animal['active'] || ($animal['status'] == "adoptado" && $animal['user_id'] != $_SESSION['user']['id'])))) {
        header("Location: " . BASE_URL . "animales");
        exit();
    }
    $from = $_SESSION['from_animals'] ?? 'animales';
    require 'views/animal.php';
}

function createAnimal()
{
    $errors = [];
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $name = trim($_POST['name'] ?? '');
        $species_id = trim($_POST['species_id'] ?? '');
        $breed = trim($_POST['breed'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $status = trim($_POST['status'] ?? '');
        $gender = trim($_POST['gender'] ?? '');
        $birth_day = !empty($_POST['birth_day']) ? $_POST['birth_day'] : null;
        $photo = $_FILES['photo'] ?? null;
        $active = isset($_POST['active']) ? 1 : 0;

        if (empty($name)) {
            $errors[] = "El nombre es obligatorio.";
        }

        $existing_name = getAnimalByName($name);
        if ($existing_name) {
            $errors[] = "El nombre ya existe";
        }

        if (empty($species_id) || !getSpeciesById($species_id)) {
            $errors[] = "La especie no existe";
        }

        if ($status != "sin adoptar" && $status != "reservado" && $status != "adoptado") {
            $errors[] = "Selecciona un estado.";
        }

        if ($gender != "macho" && $gender != "hembra") {
            $errors[] = "Selecciona un género.";
        }

        $allowed = ['image/jpeg', 'image/png', 'image/webp'];


        if (!empty($_FILES['photo']['tmp_name']) && is_uploaded_file($_FILES['photo']['tmp_name'])) {
            $type = mime_content_type($_FILES['photo']['tmp_name']);

            if (!in_array($type, $allowed)) {
                $errors[] = "Formato de imagen no válido (solo JPG, PNG, WEBP).";
            }
        }

        $user_id = null;

        if (!empty($errors)) {
            $animal = [
                'name' => $name,
                'species_id' => $species_id,
                'breed' => $breed,
                'description' => $description,
                'status' => $status,
                'gender' => $gender,
                'birth_day' => $birth_day,
                'photo' => '',
                'user_id' => $user_id,
                'active' => $active
            ];
            $species = getSpecies("", "", 1000, 0);

            require 'views/create_animal.php';
            return;
        }

        $id = insertAnimal($name, $description, $species_id, $breed, $status, $gender, $birth_day, '', $user_id, $active);


        $photo_name = "";

        if (!empty($_FILES['photo']['tmp_name']) && is_uploaded_file($_FILES['photo']['tmp_name'])) {

            if ($_FILES['photo']['error'] === UPLOAD_ERR_OK) {

                $type = mime_content_type($_FILES['photo']['tmp_name']);
                $allowed = ['image/jpeg', 'image/png', 'image/webp'];

                if (in_array($type, $allowed)) {

                    $upload_dir = __DIR__ . '/../uploads/animals/';

                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0777, true);
                    }

                    $file_name = 'animal_' . $id . '.webp';
                    $path = $upload_dir . $file_name;

                    optimizeImage($_FILES['photo']['tmp_name'], $path);

                    $photo_name = $file_name;

                    updateAnimal($id, [
                        'name' => $name,
                        'description' => $description,
                        'species_id' => $species_id,
                        'breed' => $breed,
                        'status' => $status,
                        'gender' => $gender,
                        'birth_day' => $birth_day,
                        'photo' => $photo_name,
                        'user_id' => $user_id,
                        'active' => $active
                    ]);
                }
            }
        }

        header("Location: " . BASE_URL . "animal/$id");
        exit();

    } else {
        $species = getSpecies("", "", 1000, 0);
        $animal = [
            'name' => '',
            'species_id' => '',
            'breed' => '',
            'description' => '',
            'status' => 'sin adoptar',
            'gender' => 'macho',
            'birth_day' => '',
            'photo' => null,
            'user_id' => '',
            'active' => 1
        ];
        require 'views/create_animal.php';
    }
}

function editAnimal()
{
    $errors = [];
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $id = trim($_POST['id'] ?? '');
        $data = [
            'name' => trim($_POST['name'] ?? ''),
            'species_id' => trim($_POST['species_id'] ?? ''),
            'breed' => trim($_POST['breed'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'status' => trim($_POST['status'] ?? ''),
            'gender' => trim($_POST['gender'] ?? ''),
            'birth_day' => !empty($_POST['birth_day']) ? $_POST['birth_day'] : null,
            'user_id' => trim($_POST['user_id'] ?? ''),
            'active' => isset($_POST['active']) ? 1 : 0
        ];

        if (empty($data['name'])) {
            $errors[] = "El nombre es obligatorio.";
        }

        $existing_name = getAnimalByName($data['name']);
        if ($existing_name && $existing_name['id'] != $id) {
            $errors[] = "El nombre ya existe";
        }

        if (empty($data['species_id']) || !getSpeciesById($data['species_id'])) {
            $errors[] = "La especie no existe";
        }

        if ($data['status'] != "sin adoptar" && $data['status'] != "reservado" && $data['status'] != "adoptado") {
            $errors[] = "Selecciona un estado.";
        }

        if ($data['gender'] != "macho" && $data['gender'] != "hembra") {
            $errors[] = "Selecciona un género.";
        }

        $allowed = ['image/jpeg', 'image/png', 'image/webp'];


        if (!empty($_FILES['photo']['tmp_name']) && is_uploaded_file($_FILES['photo']['tmp_name'])) {
            $type = mime_content_type($_FILES['photo']['tmp_name']);

            if (!in_array($type, $allowed)) {
                $errors[] = "Formato de imagen no válido (solo JPG, PNG, WEBP).";
            }
        }

        $animal_original = getAnimalById($id);
        $data['user_id'] = $animal_original['user_id'];
        $photo_name = $animal_original['photo'];

        $preview = $animal_original['photo'];

        if (!empty($errors)) {
            $animal = array_merge(getAnimalById($id), $data);
            $species = getSpecies("", "", 1000, 0);

            $animal['photo'] = $animal_original['photo'];

            require 'views/edit_animal.php';
            return;
        }

        if (!empty($_FILES['photo']['tmp_name']) && is_uploaded_file($_FILES['photo']['tmp_name'])) {

            if ($_FILES['photo']['error'] === UPLOAD_ERR_OK) {

                $type = mime_content_type($_FILES['photo']['tmp_name']);
                $allowed = ['image/jpeg', 'image/png', 'image/webp'];

                if (in_array($type, $allowed)) {

                    $upload_dir = __DIR__ . '/../uploads/animals/';

                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0777, true);
                    }

                    $file_name = 'animal_' . $id . '.webp';
                    $path = $upload_dir . $file_name;

                    optimizeImage($_FILES['photo']['tmp_name'], $path);

                    $photo_name = $file_name;
                }
            }
        }

        $data['photo'] = $photo_name;

        updateAnimal($id, $data);

        header("Location: " . BASE_URL . "animal/$id");
        exit();

    } else {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header("Location: " . BASE_URL . "animales");
            exit();
        }
        $animal = getAnimalById($id);
        $species = getSpecies("", "", 1000, 0);
        if (!$animal) {
            header("Location: " . BASE_URL . "animales");
            exit();
        }
        require 'views/edit_animal.php';
    }
}

function listMyAnimals()
{
    $search = $_GET['search'] ?? '';
    $order = $_GET['order'] ?? '';
    $species_id = $_GET['species_id'] ?? '';
    $gender = $_GET['gender'] ?? '';
    $status = $_GET['status'] ?? '';
    $user_id = $_SESSION['user']['id'] ?? '';
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $per_page = 8;

    $total_animals = countMyAnimals($search, $species_id, $gender, $status, $user_id);
    $total_pages = $total_animals > 0 ? ceil($total_animals / $per_page) : 1;

    $offset = ($page - 1) * $per_page;
    $animals = getMyAnimals($search, $order, $species_id, $gender, $status, $user_id, $per_page, $offset);

    $species = getSpecies("", "", 1000, 0);

    if (isset($_GET['ajax'])) {
        require 'views/lists/animals_list.php';
        exit;
    }
    $_SESSION['from_animals'] = 'mis_animales';
    require 'views/my_animals.php';
}

function changeTheAnimalActiveStatus()
{
    if (empty($_SESSION['user']) || $_SESSION['user']['role'] != 'administrador') {
        header("Location: " . BASE_URL . "inicio");
        exit();
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = $_POST['id'] ?? null;
        $active = isset($_POST['active']) ? intval($_POST['active']) : 0;
        if ($id) {
            changeAnimalActiveStatus($id, $active);
        }
        echo "ok";
        exit();
    }
}

function optimizeImage($tmp_path, $output_path)
{
    $info = getimagesize($tmp_path);

    switch ($info['mime']) {
        case 'image/jpeg':
            $image = imagecreatefromjpeg($tmp_path);
            break;
        case 'image/png':
            $image = imagecreatefrompng($tmp_path);
            break;
        case 'image/webp':
            move_uploaded_file($tmp_path, $output_path);
            return $output_path;
        default:
            return false;
    }

    imagewebp($image, $output_path, 100);
    imagedestroy($image);

    return $output_path;
}

function removeAnimalUser()
{
    if (empty($_SESSION['user']) || $_SESSION['user']['role'] != 'administrador') {
        header("Location: " . BASE_URL . "inicio");
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $id = $_POST['id'] ?? null;

        if (!$id) {
            header("Location: " . BASE_URL . "animales");
            exit();
        }

        $animal = getAnimalById($id);

        if (!$animal) {
            header("Location: " . BASE_URL . "animales");
            exit();
        }

        $data = [
            'status' => 'sin adoptar',
            'user_id' => null
        ];

        updateAnimalAdoptionStatus($id, $data);

        header("Location: " . BASE_URL . "animal/" . $id);
        exit();
    }
}
?>