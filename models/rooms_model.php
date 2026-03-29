<?php
require_once 'config/connect_db.php';

function getRooms($search, $order, $active, $limit = null, $offset = 0)
{
    $con = get_conexion();
    $sql = "SELECT * FROM rooms WHERE (code LIKE :search OR name LIKE :search)";

    $params = [
        ':search' => "%$search%"
    ];

    if ($active != '' && $active != null) {
        $sql .= " AND active = :active";
        $params[':active'] = $active;
    }
    switch ($order) {
        case 'code_asc':
            $sql .= " ORDER BY code ASC";
            break;
        case 'code_desc':
            $sql .= " ORDER BY code DESC";
            break;
        case 'name_asc':
            $sql .= " ORDER BY name ASC";
            break;
        case 'name_desc':
            $sql .= " ORDER BY name DESC";
            break;
        case 'capacity_asc':
            $sql .= " ORDER BY capacity ASC";
            break;
        case 'capacity_desc':
            $sql .= " ORDER BY capacity DESC";
            break;
        default:
            $sql .= " ORDER BY code ASC";
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
?>