<?php
require_once 'config/connect_db.php';

function getAdoptionApplications($search, $order, $status, $limit = null, $offset = 0)
{
    $con = get_conexion();
    $sql = "SELECT aa.*, u.identification AS user_identification, u.name AS user_name, u.user_name AS user_user_name, a.name AS animal_name, a.breed AS animal_breed
    FROM adoption_applications aa JOIN users u ON aa.user_id = u.id JOIN animals a ON a.id = aa.animal_id WHERE 
    (a.name LIKE :search OR a.breed LIKE :search OR u.identification LIKE :search OR u.user_name LIKE :search OR u.name LIKE :search)";

    $params = [
        ':search' => "%$search%"
    ];

    if (!empty($status)) {
        $sql .= " AND aa.status = :status";
        $params[':status'] = $status;
    }

    switch ($order) {
        case 'application_date_asc':
            $sql .= " ORDER BY application_date ASC";
            break;
        case 'application_date_desc':
            $sql .= " ORDER BY application_date DESC";
            break;
        case 'modification_date_asc':
            $sql .= " ORDER BY modification_date ASC";
            break;
        case 'modification_date_desc':
            $sql .= " ORDER BY modification_date DESC";
            break;
        default:
            $sql .= " ORDER BY application_date DESC";
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

function getAdoptionApplicationsByUserId($search, $order, $status, $user_id, $limit = null, $offset = 0)
{
    $con = get_conexion();
    $sql = "SELECT aa.*, u.identification AS user_identification, u.name AS user_name, u.user_name AS user_user_name, a.name AS animal_name, a.breed AS animal_breed
    FROM adoption_applications aa JOIN users u ON aa.user_id = u.id JOIN animals a ON a.id = aa.animal_id WHERE aa.user_id = :user_id AND
    (a.name LIKE :search OR a.breed LIKE :search)";

    $params = [
        ':search' => "%$search%",
        ':user_id' => $user_id
    ];

    if (!empty($status)) {
        $sql .= " AND aa.status = :status";
        $params[':status'] = $status;
    }

    switch ($order) {
        case 'application_date_asc':
            $sql .= " ORDER BY application_date ASC";
            break;
        case 'application_date_desc':
            $sql .= " ORDER BY application_date DESC";
            break;
        case 'modification_date_asc':
            $sql .= " ORDER BY modification_date ASC";
            break;
        case 'modification_date_desc':
            $sql .= " ORDER BY modification_date DESC";
            break;
        default:
            $sql .= " ORDER BY application_date DESC";
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

function getAdoptionApplicationById($id)
{
    $con = get_conexion();
    $stmt = $con->prepare("SELECT * FROM adoption_applications WHERE id = :id");
    $stmt->execute([':id' => $id]);
    return $stmt->fetch();
}

function deleteAdoptionApplication($id)
{
    $con = get_conexion();
    $stmt = $con->prepare("DELETE FROM adoption_applications WHERE id = :id");
    $stmt->execute([
        ':id' => "$id"
    ]);

    return true;
}

function insertAdoptionApplication($user_id, $animal_id, $message)
{
    $con = get_conexion();
    $stmt = $con->prepare(
        "INSERT INTO adoption_applications (user_id, animal_id, message) 
         VALUES (:user_id, :animal_id, :message)"
    );
    $stmt->execute([
        ':user_id' => $user_id,
        ':animal_id' => $animal_id,
        ':message' => $message
    ]);
    return $con->lastInsertId();
}

function updateAdoptionApplication($id, $data)
{
    $con = get_conexion();
    $stmt = $con->prepare(
        "UPDATE adoption_applications
             SET status = :status,
             modification_date = NOW()
             WHERE id = :id"
    );
    $stmt->execute([
        ':id' => $id,
        ':status' => $data['status']
    ]);
}

function countAdoptionApplications($search, $status)
{
    $con = get_conexion();

    $sql = "SELECT COUNT(*) FROM adoption_applications aa JOIN users u ON aa.user_id = u.id JOIN animals a ON a.id = aa.animal_id WHERE 
    (a.name LIKE :search OR a.breed LIKE :search OR u.identification LIKE :search OR u.user_name LIKE :search OR u.name LIKE :search)";

    $params = [':search' => "%$search%"];

    if (!empty($status)) {
        $sql .= " AND aa.status = :status";
        $params[':status'] = $status;
    }

    $stmt = $con->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchColumn();
}

function countAdoptionApplicationsByUserId($search, $status, $user_id)
{
    $con = get_conexion();

    $sql = "SELECT COUNT(*) FROM adoption_applications aa JOIN users u ON aa.user_id = u.id JOIN animals a ON a.id = aa.animal_id WHERE aa.user_id = :user_id AND
    (a.name LIKE :search OR a.breed LIKE :search)";

    $params = [
        ':search' => "%$search%",
        ':user_id' => $user_id
    ];

    if (!empty($status)) {
        $sql .= " AND aa.status = :status";
        $params[':status'] = $status;
    }

    $stmt = $con->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchColumn();
}

function updatePendingAdoptionApplications($data)
{
    $con = get_conexion();
    $stmt = $con->prepare(
        "UPDATE adoption_applications
             SET status = :status,
             modification_date = NOW()
             WHERE status = 'pendiente' AND animal_id = :animal_id"
    );
    $stmt->execute([
        ':status' => $data['status'],
        ':animal_id' => $data['animal_id']
    ]);
}
?>