<?php
require_once 'config/connect_db.php';

function getUserByEmail($email)
{
    $con = get_conexion();
    $stmt = $con->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute([':email' => $email]);
    return $stmt->fetch();
}
?>