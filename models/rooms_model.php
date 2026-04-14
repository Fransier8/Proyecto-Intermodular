<?php
require_once 'config/connect_db.php';

function getRooms($search, $order, $active, $limit = null, $offset = 0)
{
    $con = get_conexion();
    $sql = "SELECT r.*,
               (SELECT photo 
                FROM room_photos rp 
                WHERE rp.room_id = r.id 
                ORDER BY rp.id ASC 
                LIMIT 1) AS photo FROM rooms r WHERE (r.code LIKE :search OR r.name LIKE :search)";

    $params = [
        ':search' => "%$search%"
    ];

    if ($active != '' && $active != null) {
        $sql .= " AND r.active = :active";
        $params[':active'] = $active;
    }
    switch ($order) {
        case 'code_asc':
            $sql .= " ORDER BY r.code ASC";
            break;
        case 'code_desc':
            $sql .= " ORDER BY r.code DESC";
            break;
        case 'name_asc':
            $sql .= " ORDER BY r.name ASC";
            break;
        case 'name_desc':
            $sql .= " ORDER BY r.name DESC";
            break;
        case 'capacity_asc':
            $sql .= " ORDER BY r.capacity ASC";
            break;
        case 'capacity_desc':
            $sql .= " ORDER BY r.capacity DESC";
            break;
        default:
            $sql .= " ORDER BY r.code ASC";
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

function getRoomById($id)
{
    $con = get_conexion();
    $stmt = $con->prepare("SELECT * FROM rooms WHERE id = :id");
    $stmt->execute([':id' => $id]);
    return $stmt->fetch();
}

function getRoomByCode($code)
{
    $con = get_conexion();
    $stmt = $con->prepare("SELECT * FROM rooms WHERE code = :code");
    $stmt->execute([':code' => $code]);
    return $stmt->fetch();
}

function countRooms($search, $active)
{
    $con = get_conexion();

    $sql = "SELECT COUNT(*) FROM rooms 
            WHERE (code LIKE :search OR name LIKE :search)";

    $params = [':search' => "%$search%"];
    
    if ($active != '' && $active != null) {
        $sql .= " AND active = :active";
        $params[':active'] = $active;
    }

    $stmt = $con->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchColumn();
}

function deleteRoom($id)
{
    $con = get_conexion();
    $stmt = $con->prepare("DELETE FROM rooms WHERE id = :id");
    $stmt->execute([
        ':id' => "$id"
    ]);
}

function changeRoomStatus($id, $active)
{
    $con = get_conexion();
    $stmt = $con->prepare("UPDATE rooms SET active = :active WHERE id = :id");
    $stmt->execute([
        ':id' => $id,
        ':active' => $active
    ]);
}

function insertRoom($code, $name, $description, $location, $capacity, $active)
{
    $con = get_conexion();
    $stmt = $con->prepare(
        "INSERT INTO rooms (code, name, description, location, capacity, active) 
         VALUES (:code, :name, :description, :location, :capacity, :active)"
    );
    $stmt->execute([
        ':code' => $code,
        ':name' => $name,
        ':description' => $description,
        ':location' => $location,
        ':capacity' => $capacity,
        ':active' => $active
    ]);
    return $con->lastInsertId();
}

function updateRoom($id, $data)
{
    $con = get_conexion();
    $stmt = $con->prepare(
        "UPDATE rooms
             SET code = :code, 
                 name = :name,
                 description = :description,
                 location = :location,
                 capacity = :capacity,
                 active = :active
             WHERE id = :id"
    );
    $stmt->execute([
        ':id' => $id,
        ':code' => $data['code'],
        ':name' => $data['name'],
        ':description' => $data['description'],
        ':location' => $data['location'],
        ':capacity' => $data['capacity'],
        ':active' => $data['active']
    ]);
}

?>