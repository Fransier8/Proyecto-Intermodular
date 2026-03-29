<?php
require_once 'config/connect_db.php';

function deleteAnimal($id)
{
    $con = get_conexion();
    $stmt = $con->prepare("DELETE FROM animals WHERE id = :id");
    $stmt->execute([
        ':id' => "$id"
    ]);
}
?>