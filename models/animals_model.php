<?php
require_once 'config/connect_db.php';

function getAnimals($search, $order, $species_id, $gender, $status, $active, $limit = null, $offset = 0, $is_admin)
{
    $con = get_conexion();
    $sql = "SELECT a.*, s.name AS species, u.user_name AS user FROM animals a JOIN species s ON a.species_id = s.id LEFT JOIN users u ON u.id = a.user_id WHERE (a.name LIKE :search OR a.breed LIKE :search)";

    $params = [
        ':search' => "%$search%"
    ];

    if (!empty($species_id)) {
        $sql .= " AND a.species_id = :species_id";
        $params[':species_id'] = $species_id;
    }

    if (!empty($gender)) {
        $sql .= " AND a.gender = :gender";
        $params[':gender'] = $gender;
    }

    if (!empty($status)) {
        $sql .= " AND a.status = :status";
        $params[':status'] = $status;
    }

    if (!$is_admin) {
        $sql .= " AND a.status != 'adoptado'";
    }

    if ($active != '' && $active != null) {
        $sql .= " AND a.active = :active";
        $params[':active'] = $active;
    }

    switch ($order) {
        case 'name_asc':
            $sql .= " ORDER BY a.name ASC";
            break;
        case 'name_desc':
            $sql .= " ORDER BY a.name DESC";
            break;
        case 'breed_asc':
            $sql .= " ORDER BY breed ASC";
            break;
        case 'breed_desc':
            $sql .= " ORDER BY breed DESC";
            break;
        case 'birth_day_asc':
            $sql .= " ORDER BY a.birth_day ASC";
            break;
        case 'birth_day_desc':
            $sql .= " ORDER BY a.birth_day DESC";
            break;
        default:
            $sql .= " ORDER BY a.name ASC";
            break;
    }
    if ($limit != null) {
        $sql .= " LIMIT :offset, :limit";
        $params[':offset'] = (int) $offset;
        $params[':limit'] = (int) $limit;
    }
    $stmt = $con->prepare($sql);
    foreach ($params as $key => $value) {
        if ($key === ':offset' || $key === ':limit') {
            $stmt->bindValue($key, $value, PDO::PARAM_INT);
        } else {
            $stmt->bindValue($key, $value);
        }
    }
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getAnimalById($id)
{
    $con = get_conexion();
    $stmt = $con->prepare("SELECT a.*, s.name AS species, u.user_name AS user FROM animals a JOIN species s ON a.species_id = s.id LEFT JOIN users u ON u.id = a.user_id WHERE a.id = :id");
    $stmt->execute([':id' => $id]);
    return $stmt->fetch();
}

function getAnimalByName($name)
{
    $con = get_conexion();
    $stmt = $con->prepare("SELECT a.*, s.name AS species, u.user_name AS user FROM animals a JOIN species s ON a.species_id = s.id LEFT JOIN users u ON u.id = a.user_id WHERE a.name = :name");
    $stmt->execute([':name' => $name]);
    return $stmt->fetch();
}

function deleteAnimal($id)
{
    $con = get_conexion();
    $stmt = $con->prepare("DELETE FROM animals WHERE id = :id");
    $stmt->execute([
        ':id' => "$id"
    ]);
}

function changeAnimalActiveStatus($id, $active)
{
    $con = get_conexion();
    $stmt = $con->prepare("UPDATE animals SET active = :active WHERE id = :id");
    $stmt->execute([
        ':id' => $id,
        ':active' => $active
    ]);
}

function changeAnimalStatus($id, $status)
{
    $con = get_conexion();
    $stmt = $con->prepare("UPDATE animals SET status = :status WHERE id = :id");
    $stmt->execute([
        ':id' => $id,
        ':status' => $status
    ]);
}

function insertAnimal($name, $description, $species_id, $breed, $status, $gender, $birth_day, $photo, $user_id, $active)
{
    $con = get_conexion();
    $stmt = $con->prepare(
        "INSERT INTO animals (name, description, species_id, breed, status, gender, birth_day, photo, user_id, active) 
         VALUES (:name, :description, :species_id, :breed, :status, :gender, :birth_day, :photo, :user_id, :active)"
    );
    $stmt->execute([
        ':name' => $name,
        ':description' => $description,
        ':species_id' => $species_id,
        ':breed' => $breed,
        ':status' => $status,
        ':gender' => $gender,
        ':birth_day' => $birth_day,
        ':photo' => $photo,
        ':user_id' => $user_id,
        ':active' => $active
    ]);
    return $con->lastInsertId();
}

function updateAnimal($id, $data)
{
    $con = get_conexion();
    $stmt = $con->prepare(
        "UPDATE animals
             SET name = :name,
                 description = :description,
                 species_id = :species_id,
                 breed = :breed,
                 status = :status,
                 gender = :gender,
                 birth_day = :birth_day,
                 photo = :photo,
                 user_id = :user_id,
                 active = :active
             WHERE id = :id"
    );
    $stmt->execute([
        ':id' => $id,
        ':name' => $data['name'],
        ':description' => $data['description'],
        ':species_id' => $data['species_id'],
        ':breed' => $data['breed'],
        ':status' => $data['status'],
        ':gender' => $data['gender'],
        ':birth_day' => $data['birth_day'],
        ':photo' => $data['photo'],
        ':user_id' => $data['user_id'],
        ':active' => $data['active']
    ]);
}

function countAnimals($search, $species_id, $gender, $status, $active, $is_admin)
{
    $con = get_conexion();

    $sql = "SELECT COUNT(*) FROM animals
            WHERE (name LIKE :search OR breed LIKE :search)";

    $params = [':search' => "%$search%"];

    if (!empty($species_id)) {
        $sql .= " AND species_id = :species_id";
        $params[':species_id'] = $species_id;
    }

    if (!empty($gender)) {
        $sql .= " AND gender = :gender";
        $params[':gender'] = $gender;
    }

    if (!empty($status)) {
        $sql .= " AND status = :status";
        $params[':status'] = $status;
    }

    if (!$is_admin) {
        $sql .= " AND status != 'adoptado'";
    }

    if ($active != '' && $active != null) {
        $sql .= " AND active = :active";
        $params[':active'] = $active;
    }

    $stmt = $con->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchColumn();
}
?>