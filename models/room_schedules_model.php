<?php
require_once 'config/connect_db.php';

function getRoomSchedules()
{
    $con = get_conexion();

    $stmt = $con->prepare(
        "SELECT * FROM room_schedules"
    );

    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getRoomSchedulesByRoomId($room_id)
{
    $con = get_conexion();

    $stmt = $con->prepare(
        "SELECT * FROM room_schedules WHERE room_id = :room_id ORDER BY 
            FIELD(day_of_week, 'lunes','martes','miércoles','jueves','viernes','sábado','domingo'),
            start_time ASC" 
    );

    $stmt->execute([':room_id' => $room_id]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function deleteRoomSchedule($id)
{
    $con = get_conexion();
    $stmt = $con->prepare("DELETE FROM room_schedules WHERE id = :id");
    $stmt->execute([
        ':id' => "$id"
    ]);
}

function insertRoomSchedule($room_id, $day_of_week, $start_time, $end_time)
{
    $con = get_conexion();

    $stmt = $con->prepare(
        "INSERT INTO room_schedules (room_id, day_of_week, start_time, end_time)
         VALUES (:room_id, :day_of_week, :start_time, :end_time)"
    );

    $stmt->execute([
        ':room_id' => $room_id,
        ':day_of_week' => $day_of_week,
        ':start_time' => $start_time,
        ':end_time' => $end_time
    ]);
}

function updateRoomSchedule($id, $data)
{
    $con = get_conexion();
    $stmt = $con->prepare(
        "UPDATE room_schedules
             SET day_of_week = :day_of_week, 
                 start_time = :start_time,
                 end_time = :end_time
             WHERE id = :id"
    );
    $stmt->execute([
        ':id' => $id,
        ':day_of_week' => $data['day_of_week'],
        ':start_time' => $data['start_time'],
        ':end_time' => $data['end_time'],
    ]);
}
?>