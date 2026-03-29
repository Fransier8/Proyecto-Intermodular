<?php
require_once 'config/connect_db.php';

function deleteReservation($id)
{
    $con = get_conexion();
    $stmt = $con->prepare("DELETE FROM reservations WHERE id = :id");
    $stmt->execute([
        ':id' => "$id"
    ]);
}
?>