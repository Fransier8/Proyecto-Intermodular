<?php
require_once 'config/connect_db.php';

function getRoomPhotos()
{
    $con = get_conexion();

    $stmt = $con->prepare(
        "SELECT * FROM room_photos"
    );

    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getRoomPhotosByRoomId($room_id)
{
    $con = get_conexion();

    $stmt = $con->prepare(
        "SELECT * FROM room_photos WHERE room_id = :room_id"
    );

    $stmt->execute([':room_id' => $room_id]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getRoomPhotoById($id)
{
    $con = get_conexion();

    $stmt = $con->prepare(
        "SELECT * FROM room_photos WHERE id = :id"
    );

    $stmt->execute([':id' => $id]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function deleteRoomPhoto($id)
{
    $con = get_conexion();
    $stmt = $con->prepare("DELETE FROM room_photos WHERE id = :id");
    $stmt->execute([
        ':id' => "$id"
    ]);
}

function insertRoomPhoto($room_id, $photo)
{
    $con = get_conexion();

    $stmt = $con->prepare(
        "INSERT INTO room_photos (room_id, photo)
         VALUES (:room_id, :photo)"
    );

    $stmt->execute([
        ':room_id' => $room_id,
        ':photo' => $photo
    ]);
}

function updateRoomPhoto($id, $data)
{
    $con = get_conexion();
    $stmt = $con->prepare(
        "UPDATE room_photos
             SET photo = :photo
             WHERE id = :id"
    );
    $stmt->execute([
        ':id' => $id,
        ':photo' => $data['photo'],
    ]);
}

function updateRoomPhotoName($id, $photo)
{
    $con = get_conexion();
    $stmt = $con->prepare("
        UPDATE room_photos SET photo = :photo WHERE id = :id
    ");
    $stmt->execute([
        ':id' => $id,
        ':photo' => $photo
    ]);
}
?>