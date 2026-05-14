<?php
require_once 'config/connect_db.php';

function getReservations($search, $order, $status, $limit = null, $offset = 0)
{
    $con = get_conexion();
    $sql = "SELECT r.*, a.name AS animal_name, u.user_name AS user_user_name, m.user_name AS monitor_user_name, ro.code AS room_code FROM reservations r JOIN animals a ON r.animal_id = a.id JOIN users u ON r.user_id = u.id LEFT JOIN users m ON r.monitor_id = m.id
    JOIN rooms ro ON r.room_id = ro.id WHERE (a.name LIKE :search OR a.breed LIKE :search OR u.identification LIKE :search OR u.user_name LIKE :search OR u.name LIKE :search
     OR ro.code LIKE :search OR m.identification LIKE :search OR m.user_name LIKE :search OR m.name LIKE :search)";

    $params = [
        ':search' => "%$search%"
    ];

    if (!empty($status)) {
        $sql .= " AND r.status = :status";
        $params[':status'] = $status;
    }

    switch ($order) {
        case 'date_asc':
            $sql .= " ORDER BY r.date ASC, r.start_time ASC, r.end_time ASC";
            break;
        case 'date_desc':
            $sql .= " ORDER BY r.date DESC, r.end_time DESC, r.start_time DESC";
            break;
        case 'companions_asc':
            $sql .= " ORDER BY r.companions ASC";
            break;
        case 'companions_desc':
            $sql .= " ORDER BY r.companions DESC";
            break;
        default:
            $sql .= " ORDER BY r.date DESC, r.end_time DESC, r.start_time DESC";
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

function getReservationsByUserId($search, $order, $status, $user_id, $limit = null, $offset = 0)
{
    $con = get_conexion();
    $sql = "SELECT r.*, a.name AS animal_name, u.user_name AS user_user_name, m.user_name AS monitor_user_name, ro.code AS room_code FROM reservations r JOIN animals a ON r.animal_id = a.id JOIN users u ON r.user_id = u.id LEFT JOIN users m ON r.monitor_id = m.id
    JOIN rooms ro ON r.room_id = ro.id WHERE r.user_id = :user_id AND (a.name LIKE :search OR a.breed LIKE :search
     OR ro.code LIKE :search OR m.user_name LIKE :search)";

    $params = [
        ':search' => "%$search%",
        ':user_id' => $user_id
    ];

    if (!empty($status)) {
        $sql .= " AND r.status = :status";
        $params[':status'] = $status;
    }

    switch ($order) {
        case 'date_asc':
            $sql .= " ORDER BY r.date ASC, r.start_time ASC, r.end_time ASC";
            break;
        case 'date_desc':
            $sql .= " ORDER BY r.date DESC, r.end_time DESC, r.start_time DESC";
            break;
        case 'companions_asc':
            $sql .= " ORDER BY r.companions ASC";
            break;
        case 'companions_desc':
            $sql .= " ORDER BY r.companions DESC";
            break;
        default:
            $sql .= " ORDER BY r.date DESC, r.end_time DESC, r.start_time DESC";
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

function deleteReservation($id)
{
    $con = get_conexion();
    $stmt = $con->prepare("DELETE FROM reservations WHERE id = :id");
    $stmt->execute([
        ':id' => "$id"
    ]);

    return true;
}

function changeReservationStatus($id, $status)
{
    $con = get_conexion();
    $stmt = $con->prepare("UPDATE reservations SET status = :status WHERE id = :id");
    $stmt->execute([
        ':id' => $id,
        ':status' => $status
    ]);
}

function insertReservation($user_id, $animal_id, $room_id, $monitor_id, $date, $start_time, $end_time, $companions, $reason, $status)
{
    $con = get_conexion();
    $stmt = $con->prepare(
        "INSERT INTO reservations (user_id, animal_id, room_id, monitor_id, date, start_time, end_time, companions, reason, status) 
         VALUES (:user_id, :animal_id, :room_id, :monitor_id, :date, :start_time, :end_time, :companions, :reason, :status)"
    );
    $stmt->execute([
        ':user_id' => $user_id,
        ':animal_id' => $animal_id,
        ':room_id' => $room_id,
        ':monitor_id' => $monitor_id,
        ':date' => $date,
        ':start_time' => $start_time,
        ':end_time' => $end_time,
        ':companions' => $companions,
        ':reason' => $reason,
        ':status' => $status
    ]);
}

function updateReservation($id, $data)
{
    $con = get_conexion();
    $stmt = $con->prepare(
        "UPDATE reservations
             SET user_id = :user_id, 
                 animal_id = :animal_id,
                 room_id = :room_id,
                 monitor_id = :monitor_id,
                 date = :date,
                 start_time = :start_time,
                 end_time = :end_time,
                 companions = :companions,
                 reason = :reason,
                 status = :status
             WHERE id = :id"
    );
    $stmt->execute([
        ':id' => $id,
        ':user_id' => $data['user_id'],
        ':animal_id' => $data['animal_id'],
        ':room_id' => $data['room_id'],
        ':monitor_id' => $data['monitor_id'],
        ':date' => $data['date'],
        ':start_time' => $data['start_time'],
        ':end_time' => $data['end_time'],
        ':companions' => $data['companions'],
        ':reason' => $data['reason'],
        ':status' => $data['status']
    ]);
}

function countReservations($search, $status)
{
    $con = get_conexion();

    $sql = "SELECT COUNT(*) FROM reservations r JOIN animals a ON r.animal_id = a.id JOIN users u ON r.user_id = u.id LEFT JOIN users m ON r.monitor_id = m.id
    JOIN rooms ro ON r.room_id = ro.id WHERE (a.name LIKE :search OR a.breed LIKE :search OR u.identification LIKE :search OR u.user_name LIKE :search OR u.name LIKE :search
     OR ro.code LIKE :search OR m.identification LIKE :search OR m.user_name LIKE :search OR m.name LIKE :search)";

    $params = [':search' => "%$search%"];

    if (!empty($status)) {
        $sql .= " AND r.status = :status";
        $params[':status'] = $status;
    }

    $stmt = $con->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchColumn();
}

function countReservationsByUserId($search, $status, $user_id)
{
    $con = get_conexion();

    $sql = "SELECT COUNT(*) FROM reservations r JOIN animals a ON r.animal_id = a.id JOIN users u ON r.user_id = u.id LEFT JOIN users m ON r.monitor_id = m.id
    JOIN rooms ro ON r.room_id = ro.id WHERE r.user_id = :user_id AND (a.name LIKE :search OR a.breed LIKE :search
     OR ro.code LIKE :search OR m.user_name LIKE :search)";

    $params = [
        ':search' => "%$search%",
        ':user_id' => $user_id
    ];

    if (!empty($status)) {
        $sql .= " AND r.status = :status";
        $params[':status'] = $status;
    }

    $stmt = $con->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchColumn();
}

function getReservationsByUserIdOrAnimalIdOrRoomIdOrMonitorId($user_id, $animal_id, $room_id, $monitor_id)
{
    $con = get_conexion();
    $sql = "SELECT * FROM reservations WHERE user_id = :user_id OR animal_id = :animal_id OR room_id = :room_id";

    $params = [
        ':user_id' => $user_id,
        ':animal_id' => $animal_id,
        ':room_id' => $room_id,
    ];

    if (!empty($monitor_id)) {
        $sql .= " OR monitor_id = :monitor_id";
        $params[':monitor_id'] = $monitor_id;
    }


    $sql .= " ORDER BY date ASC, start_time ASC, end_time ASC";

    $stmt = $con->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}
?>