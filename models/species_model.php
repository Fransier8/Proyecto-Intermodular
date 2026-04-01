<?php
require_once 'config/connect_db.php';

function getSpecies($search, $order, $limit = null, $offset = 0)
{
    $con = get_conexion();
    $sql = "SELECT * FROM species WHERE (name LIKE :search)";

    $params = [
        ':search' => "%$search%"
    ];

    switch ($order) {
        case 'name_asc':
            $sql .= " ORDER BY name ASC";
            break;
        case 'name_desc':
            $sql .= " ORDER BY name DESC";
            break;
        default:
            $sql .= " ORDER BY name ASC";
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

function deleteSpecies($id)
{
    $con = get_conexion();
    $stmt = $con->prepare("DELETE FROM species WHERE id = :id");
    $stmt->execute([
        ':id' => "$id"
    ]);
}

function insertSpecies($name)
{
    $con = get_conexion();
    $stmt = $con->prepare(
        "INSERT INTO species (name) 
         VALUES (:name)"
    );
    $stmt->execute([
        ':name' => $name
    ]);
}

function updateSpecies($id, $data)
{
    $con = get_conexion();
    $stmt = $con->prepare(
        "UPDATE species
             SET name = :name
             WHERE id = :id"
    );
    $stmt->execute([
        ':id' => $id,
        ':name' => $data['name']
    ]);
}

function countSpecies($search)
{
    $con = get_conexion();

    $sql = "SELECT COUNT(*) FROM species 
            WHERE (name LIKE :search)";

    $params = [':search' => "%$search%"];

    $stmt = $con->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchColumn();
}
?>