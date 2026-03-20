<?php
require_once 'config/connect_db.php';

function getUsers($search, $order, $role, $active, $limit = null, $offset = 0)
{
    $con = get_conexion();
    $sql = "SELECT * FROM users WHERE (user_name LIKE :search OR name LIKE :search OR email LIKE :search)";

    $params = [
        ':search' => "%$search%"
    ];

    if (!empty($role)) {
        $sql .= " AND role = :role";
        $params[':role'] = $role;
    }
    if ($active != '' && $active != null) {
        $sql .= " AND active = :active";
        $params[':active'] = $active;
    }
    switch ($order) {
        case 'user_name_asc':
            $sql .= " ORDER BY user_name ASC";
            break;
        case 'user_name_desc':
            $sql .= " ORDER BY user_name DESC";
            break;
        case 'name_asc':
            $sql .= " ORDER BY name ASC";
            break;
        case 'name_desc':
            $sql .= " ORDER BY name DESC";
            break;
        case 'email_asc':
            $sql .= " ORDER BY email ASC";
            break;
        case 'email_desc':
            $sql .= " ORDER BY email DESC";
            break;
        case 'identification_asc':
            $sql .= " ORDER BY identification ASC";
            break;
        case 'identification_desc':
            $sql .= " ORDER BY identification DESC";
            break;
        default:
            $sql .= " ORDER BY user_name ASC";
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

function getUserByEmail($email)
{
    $con = get_conexion();
    $stmt = $con->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute([':email' => $email]);
    return $stmt->fetch();
}

function countUsers($search, $role, $active)
{
    $con = get_conexion();

    $sql = "SELECT COUNT(*) FROM users 
            WHERE (user_name LIKE :search OR name LIKE :search OR email LIKE :search)";

    $params = [':search' => "%$search%"];

    if (!empty($role)) {
        $sql .= " AND role = :role";
        $params[':role'] = $role;
    }
    
    if ($active != '' && $active != null) {
        $sql .= " AND active = :active";
        $params[':active'] = $active;
    }

    $stmt = $con->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchColumn();
}
?>