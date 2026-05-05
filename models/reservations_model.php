<?php
require_once 'config/connect_db.php';

function getReservations($search, $order, /*$user_id, $animal_id, $room_id, $monitor_id,*/ $status, $limit = null, $offset = 0)
{
    $con = get_conexion();
    $sql = "SELECT r.*, a.name AS animal_name, u.user_name AS user_user_name, m.user_name AS monitor_user_name, ro.code AS room_code FROM reservations r JOIN animals a ON r.animal_id = a.id JOIN users u ON r.user_id = u.id LEFT JOIN users m ON r.monitor_id = m.id
    JOIN rooms ro ON r.room_id = ro.id WHERE (reason LIKE :search)";

    $params = [
        ':search' => "%$search%"
    ];

    /*if (!empty($user_id)) {
        $sql .= " AND user_id = :user_id";
        $params[':user_id'] = $user_id;
    }

    if (!empty($animal_id)) {
        $sql .= " AND animal_id = :animal_id";
        $params[':animal_id'] = $animal_id;
    }

    if (!empty($room_id)) {
        $sql .= " AND room_id = :room_id";
        $params[':room_id'] = $room_id;
    }

    if (!empty($monitor_id)) {
        $sql .= " AND monitor_id = :monitor_id";
        $params[':monitor_id'] = $monitor_id;
    }*/

    if (!empty($status)) {
        $sql .= " AND status = :status";
        $params[':status'] = $status;
    }

    switch ($order) {
        case 'date_asc':
            $sql .= " ORDER BY date ASC";
            break;
        case 'date_desc':
            $sql .= " ORDER BY date DESC";
            break;
        case 'companions_asc':
            $sql .= " ORDER BY companions ASC";
            break;
        case 'companions_desc':
            $sql .= " ORDER BY companions DESC";
            break;
        default:
            $sql .= " ORDER BY date ASC";
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

function countReservations($search, /*$user_id, $animal_id, $room_id, $monitor_id,*/ $status)
{
    $con = get_conexion();

    $sql = "SELECT COUNT(*) FROM reservations 
            WHERE (reason LIKE :search)";

    $params = [':search' => "%$search%"];

    /*if (!empty($user_id)) {
        $sql .= " AND user_id = :user_id";
        $params[':user_id'] = $user_id;
    }

    if (!empty($animal_id)) {
        $sql .= " AND animal_id = :animal_id";
        $params[':animal_id'] = $animal_id;
    }

    if (!empty($room_id)) {
        $sql .= " AND room_id = :room_id";
        $params[':room_id'] = $room_id;
    }

    if (!empty($monitor_id)) {
        $sql .= " AND monitor_id = :monitor_id";
        $params[':monitor_id'] = $monitor_id;
    }*/

    if (!empty($status)) {
        $sql .= " AND status = :status";
        $params[':status'] = $status;
    }

    $stmt = $con->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchColumn();
}
?>