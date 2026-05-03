<?php
require_once 'config/connect_db.php';

function getSponsorships($search, $order, $limit = null, $offset = 0)
{
    $con = get_conexion();
    $sql = "SELECT s.*, u.identification AS user_identification, u.name AS user_name, u.user_name AS user_user_name, a.name AS animal_name, a.breed AS animal_breed
    FROM sponsorships s JOIN users u ON s.user_id = u.id JOIN animals a ON a.id = s.animal_id WHERE 
    (a.name LIKE :search OR a.breed LIKE :search OR u.identification LIKE :search OR u.user_name LIKE :search OR u.name LIKE :search)";

    $params = [
        ':search' => "%$search%"
    ];

    switch ($order) {
        case 'date_asc':
            $sql .= " ORDER BY date ASC";
            break;
        case 'date_desc':
            $sql .= " ORDER BY date DESC";
            break;
        case 'amount_asc':
            $sql .= " ORDER BY amount ASC";
            break;
        case 'amount_desc':
            $sql .= " ORDER BY amount DESC";
            break;
        default:
            $sql .= " ORDER BY date DESC";
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

function getSponsorshipsByUserId($search, $order, $user_id, $limit = null, $offset = 0)
{
    $con = get_conexion();
    $sql = "SELECT s.*, u.identification AS user_identification, u.name AS user_name, u.user_name AS user_user_name, a.name AS animal_name, a.breed AS animal_breed
    FROM sponsorships s JOIN users u ON s.user_id = u.id JOIN animals a ON a.id = s.animal_id WHERE s.user_id = :user_id AND
    (a.name LIKE :search OR a.breed LIKE :search)";

    $params = [
        ':search' => "%$search%",
        ':user_id' => $user_id
    ];

    switch ($order) {
        case 'date_asc':
            $sql .= " ORDER BY date ASC";
            break;
        case 'date_desc':
            $sql .= " ORDER BY date DESC";
            break;
        case 'amount_asc':
            $sql .= " ORDER BY amount ASC";
            break;
        case 'amount_desc':
            $sql .= " ORDER BY amount DESC";
            break;
        default:
            $sql .= " ORDER BY date DESC";
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


function getSponsorshipById($id)
{
    $con = get_conexion();
    $stmt = $con->prepare("SELECT * FROM sponsorships WHERE id = :id");
    $stmt->execute([':id' => $id]);
    return $stmt->fetch();
}

function insertSponsorship($user_id, $animal_id, $message, $amount)
{
    $con = get_conexion();
    $stmt = $con->prepare(
        "INSERT INTO sponsorships (user_id, animal_id, message, amount) 
         VALUES (:user_id, :animal_id, :message, :amount)"
    );
    $stmt->execute([
        ':user_id' => $user_id,
        ':animal_id' => $animal_id,
        ':message' => $message,
        ':amount' => $amount
    ]);
    return $con->lastInsertId();
}

function countSponsorships($search)
{
    $con = get_conexion();

    $sql = "SELECT COUNT(*) FROM sponsorships s JOIN users u ON s.user_id = u.id JOIN animals a ON a.id = s.animal_id WHERE 
    (a.name LIKE :search OR a.breed LIKE :search OR u.identification LIKE :search OR u.user_name LIKE :search OR u.name LIKE :search)";

    $params = [':search' => "%$search%"];

    $stmt = $con->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchColumn();
}

?>