<?php
require_once 'config/connect_db.php';

function getUsers($search, $order, $limit = null, $offset = 0)
{
    $con = get_conexion();
    $sql = "SELECT * FROM users WHERE user_name LIKE :search OR name LIKE :search OR email LIKE :search";
        if ($limit != null) {
        $sql .= " LIMIT $offset, $limit";
    }
    $stmt = $con->prepare($sql);
    $stmt->execute([':search' => "%$search%"]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getUserByEmail($email)
{
    $con = get_conexion();
    $stmt = $con->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute([':email' => $email]);
    return $stmt->fetch();
}
?>