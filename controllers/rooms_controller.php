<?php
require_once 'models/rooms_model.php';
require_once 'models/room_schedules_model.php';
require_once 'models/room_photos_model.php';

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
    $id = $_GET['id'] ?? null;
    if (!$id) {
        header("Location: " . BASE_URL . "salas");
        exit();
    }
    $room = getRoomById($id);
    if (!$room || ($_SESSION['user']['role'] != "administrador" && !$room['active'])) {
        header("Location: " . BASE_URL . "salas");
        exit();
    }
    $schedules = getRoomSchedulesByRoomId($id);
    $photos = getRoomPhotosByRoomId($id);

    require 'views/room.php';
}

function createRoom()
{
    $errors = [];
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $code = trim($_POST['code'] ?? '');
        $name = trim($_POST['name'] ?? '');
        $capacity = trim($_POST['capacity'] ?? '');
        $location = trim($_POST['location'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $schedules = $_POST['schedules'] ?? [];
        $photos = $_FILES['photos'] ?? [];
        $active = isset($_POST['active']) ? 1 : 0;

        if (empty($code)) {
            $errors[] = "El código es obligatorio.";
        }

        $existing_code = getRoomByCode($code);
        if ($existing_code) {
            $errors[] = "El código ya existe";
        }

        if (empty($name)) {
            $errors[] = "El nombre es obligatorio.";
        }

        if (!is_numeric($capacity) || (int) $capacity <= 0) {
            $errors[] = "La capacidad debe ser un número mayor que 0.";
        }

        if (empty($schedules) || !is_array($schedules)) {
            $errors[] = "Debes añadir al menos un horario.";
        } else {
            foreach ($schedules as $s) {
                if (empty($s['start_time']) || empty($s['end_time']))
                    continue;

                if (strtotime($s['start_time']) >= strtotime($s['end_time'])) {
                    $errors[] = "La hora de inicio debe ser menor que la de fin.";
                    break;
                }
            }
        }

        $used = [];

        if (!empty($schedules) && is_array($schedules)) {
            foreach ($schedules as $s) {
                $day = $s['day_of_week'] ?? null;
                if (!$day || empty($s['start_time']) || empty($s['end_time'])) {
                    continue;
                }
                $start = toMinutes($s['start_time']);
                $end = toMinutes($s['end_time']);

                foreach ($used[$day] ?? [] as $u) {
                    if ($start < $u['end'] && $u['start'] < $end) {
                        $errors[] = "Horarios solapados en $day.";
                        break 2;
                    }
                }

                $used[$day][] = [
                    'start' => $start,
                    'end' => $end
                ];
            }
        }

        if (!empty($photos['name'])) {
            $new = is_array($photos['name'])
                ? count(array_filter($photos['name'], fn($n) => !empty($n)))
                : 0;

            if ($new > 5) {
                $errors[] = "Máximo 5 imágenes en total.";
            }
        }

        $allowed = ['image/jpeg', 'image/png', 'image/webp'];


        if (!empty($photos['tmp_name']) && is_array($photos['tmp_name'])) {
            foreach ($photos['tmp_name'] as $i => $tmp) {

                if (!is_uploaded_file($tmp))
                    continue;

                if ($photos['error'][$i] != UPLOAD_ERR_OK) {
                    $errors[] = "Error subiendo imágenes.";
                    continue;
                }

                $type = mime_content_type($tmp);

                if (!in_array($type, $allowed)) {
                    $errors[] = "Formato de imagen no válido (solo JPG, PNG, WEBP).";
                }
            }
        }

        if (!empty($errors)) {
            $room = [
                'code' => $code,
                'name' => $name,
                'capacity' => $capacity,
                'location' => $location,
                'description' => $description,
                'active' => $active
            ];

            require 'views/create_room.php';
            return;
        }

        $id = insertRoom($code, $name, $description, $location, $capacity, $active);

        $existing_by_id = [];
        $sent_ids = [];

        foreach ($schedules as $s) {

            if (empty($s['day_of_week']) || empty($s['start_time']) || empty($s['end_time'])) {
                continue;
            }

            $schedule_id = $s['id'] ?? null;

            insertRoomSchedule(
                $id,
                $s['day_of_week'],
                $s['start_time'],
                $s['end_time']
            );
        }

        saveRoomImages($id, $code, $photos);

        header("Location: " . BASE_URL . "sala/$id");
        exit();

    } else {
        $room = [
            'code' => '',
            'name' => '',
            'capacity' => '',
            'location' => '',
            'description' => '',
            'active' => 1
        ];
        $schedules = [];
        $photos = [];
        require 'views/create_room.php';
    }
}

function editRoom()
{
    $errors = [];
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $id = trim($_POST['id'] ?? '');
        $data = [
            'code' => trim($_POST['code'] ?? ''),
            'name' => trim($_POST['name'] ?? ''),
            'capacity' => trim($_POST['capacity'] ?? ''),
            'location' => trim($_POST['location'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'schedules' => $_POST['schedules'] ?? [],
            'photos' => $_FILES['photos'] ?? [],
            'active' => isset($_POST['active']) ? 1 : 0
        ];

        if (empty($data['code'])) {
            $errors[] = "El código es obligatorio.";
        }

        $existing_code = getRoomByCode($data['code']);
        if ($existing_code && $existing_code['id'] != $id) {
            $errors[] = "El código ya existe";
        }

        if (empty($data['name'])) {
            $errors[] = "El nombre es obligatorio.";
        }

        if (!is_numeric($data['capacity']) || (int) $data['capacity'] <= 0) {
            $errors[] = "La capacidad debe ser un número mayor que 0.";
        }

        if (empty($_POST['schedules']) || !is_array($_POST['schedules'])) {
            $errors[] = "Debes añadir al menos un horario.";
        } else {

            foreach ($_POST['schedules'] as $s) {
                if (empty($s['start_time']) || empty($s['end_time']))
                    continue;

                if (strtotime($s['start_time']) >= strtotime($s['end_time'])) {
                    $errors[] = "La hora de inicio debe ser menor que la de fin.";
                    break;
                }
            }
        }

        $used = [];

        if (!empty($_POST['schedules']) && is_array($_POST['schedules'])) {
            foreach ($_POST['schedules'] as $s) {
                $day = $s['day_of_week'] ?? null;
                if (!$day || empty($s['start_time']) || empty($s['end_time'])) {
                    continue;
                }
                $start = toMinutes($s['start_time']);
                $end = toMinutes($s['end_time']);

                foreach ($used[$day] ?? [] as $u) {
                    if ($start < $u['end'] && $u['start'] < $end) {
                        $errors[] = "Horarios solapados en $day.";
                        break 2;
                    }
                }

                $used[$day][] = [
                    'start' => $start,
                    'end' => $end
                ];
            }
        }

        if (!empty($_FILES['photos']['name'])) {
            $current = count(getRoomPhotosByRoomId($id));

            $new = is_array($_FILES['photos']['name'])
                ? count(array_filter($_FILES['photos']['name'], fn($n) => !empty($n)))
                : 0;

            if (($current + $new) > 5) {
                $errors[] = "Máximo 5 imágenes en total.";
            }
        }

        $allowed = ['image/jpeg', 'image/png', 'image/webp'];


        if (!empty($_FILES['photos']['tmp_name']) && is_array($_FILES['photos']['tmp_name'])) {
            foreach ($_FILES['photos']['tmp_name'] as $i => $tmp) {

                if (!is_uploaded_file($tmp))
                    continue;

                if ($_FILES['photos']['error'][$i] !== UPLOAD_ERR_OK) {
                    $errors[] = "Error subiendo imágenes.";
                    continue;
                }

                $type = mime_content_type($tmp);

                if (!in_array($type, $allowed)) {
                    $errors[] = "Formato de imagen no válido (solo JPG, PNG, WEBP).";
                }
            }
        }

        if (!empty($_POST['delete_photos']) && is_array($_POST['delete_photos'])) {
            foreach ($_POST['delete_photos'] as $photo_id) {

                $photo = getRoomPhotoById($photo_id);

                if ($photo) {
                    $file = __DIR__ . '/../uploads/rooms/' . $photo['photo'];

                    if (file_exists($file)) {
                        unlink($file);
                    }

                    deleteRoomPhoto($photo_id);
                }
            }
        }

        if (!empty($errors)) {
            $room = array_merge(getRoomById($id), $data);
            $schedules = !empty($data['schedules']) ? $data['schedules'] : getRoomSchedulesByRoomId($id);
            $photos = getRoomPhotosByRoomId($id);

            require 'views/edit_room.php';
            return;
        }

        $old_room = getRoomById($id);
        $old_code = $old_room['code'];

        updateRoom($id, $data);

        $existing = getRoomSchedulesByRoomId($id);

        $existing_by_id = [];
        foreach ($existing as $e) {
            $existing_by_id[$e['id']] = $e;
        }

        $sent_ids = [];

        foreach ($_POST['schedules'] as $s) {

            if (empty($s['day_of_week']) || empty($s['start_time']) || empty($s['end_time'])) {
                continue;
            }

            $schedule_id = $s['id'] ?? null;

            if (!empty($schedule_id) && isset($existing_by_id[$schedule_id])) {

                updateRoomSchedule($schedule_id, [
                    'day_of_week' => $s['day_of_week'],
                    'start_time' => $s['start_time'],
                    'end_time' => $s['end_time']
                ]);

                $sent_ids[] = $schedule_id;

            } else {

                insertRoomSchedule(
                    $id,
                    $s['day_of_week'],
                    $s['start_time'],
                    $s['end_time']
                );
            }
        }

        foreach ($existing as $e) {
            if (!in_array($e['id'], $sent_ids)) {
                deleteRoomSchedule($e['id']);
            }
        }

        if ($old_code != $data['code']) {
            renameRoomImages($id, $old_code, $data['code']);
        }

        saveRoomImages($id, $data['code'], $_FILES['photos']);

        header("Location: " . BASE_URL . "sala/$id");
        exit();

    } else {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header("Location: " . BASE_URL . "salas");
            exit();
        }
        $room = getRoomById($id);
        if (!$room) {
            header("Location: " . BASE_URL . "salas");
            exit();
        }
        $schedules = getRoomSchedulesByRoomId($id);
        $photos = getRoomPhotosByRoomId($id);
        require 'views/edit_room.php';
    }
}

function saveRoomImages($id, $code, $files)
{
    $upload_dir = __DIR__ . '/../uploads/rooms/';

    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $photos = getRoomPhotosByRoomId($id);

    $last_index = 0;
    foreach ($photos as $p) {
        if (preg_match('/_(\d+)\.webp$/', $p['photo'], $m)) {
            $last_index = max($last_index, (int) $m[1]);
        }
    }

    $index = $last_index + 1;

    if (!empty($files['tmp_name']) && is_array($files['tmp_name'])) {
        foreach ($files['tmp_name'] as $i => $tmp) {

            if (!is_uploaded_file($tmp))
                continue;
            if ($files['error'][$i] !== UPLOAD_ERR_OK)
                continue;

            $filename = $code . '_' . $index . '.webp';
            $output_path = $upload_dir . $filename;

            $result = optimizeImage($tmp, $output_path);

            if (!$result) {
                continue;
            }

            insertRoomPhoto($id, $filename);

            $index++;
        }
    }
}

function renameRoomImages($room_id, $old_code, $new_code)
{
    $photos = getRoomPhotosByRoomId($room_id);

    $upload_dir = __DIR__ . '/../uploads/rooms/';

    $i = 1;

    foreach ($photos as $photo) {

        $old_path = $upload_dir . $photo['photo'];

        $new_name = $new_code . '_' . $i . '.webp';
        $new_path = $upload_dir . $new_name;

        if (file_exists($old_path)) {
            rename($old_path, $new_path);
        }

        updateRoomPhotoName($photo['id'], $new_name);

        $i++;
    }
}

function toMinutes($t)
{
    [$h, $m] = explode(':', $t);
    return ((int) $h * 60) + (int) $m;
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

function changeRoomActiveStatus()
{
    if (empty($_SESSION['user']) || $_SESSION['user']['role'] != 'administrador') {
        header("Location: " . BASE_URL . "inicio");
        exit();
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = $_POST['id'] ?? null;
        $active = isset($_POST['active']) ? intval($_POST['active']) : 0;
        if ($id) {
            changeRoomStatus($id, $active);
        }
        echo "ok";
        exit();
    }
}
?>