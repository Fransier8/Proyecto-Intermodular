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

function getUserById($id)
{
    $con = get_conexion();
    $stmt = $con->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->execute([':id' => $id]);
    return $stmt->fetch();
}

function getUserByEmail($email)
{
    $con = get_conexion();
    $stmt = $con->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute([':email' => $email]);
    return $stmt->fetch();
}

function getUserByUserName($user_name)
{
    $con = get_conexion();
    $stmt = $con->prepare("SELECT * FROM users WHERE user_name = :user_name");
    $stmt->execute([':user_name' => $user_name]);
    return $stmt->fetch();
}

function getUserByIdentification($identification)
{
    $con = get_conexion();
    $stmt = $con->prepare("SELECT * FROM users WHERE identification = :identification");
    $stmt->execute([':identification' => $identification]);
    return $stmt->fetch();
}

function getUserByEmailAndIdentification($email, $identification)
{
    $con = get_conexion();
    $stmt = $con->prepare("SELECT * FROM users WHERE email = :email AND identification = :identification");
    $stmt->execute([':email' => $email, ':identification' => $identification]);
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

function updatePassword($id, $password)
{
    $con = get_conexion();
    $stmt = $con->prepare(
        "UPDATE users 
             SET password = :password
             WHERE id = :id"
    );
    $stmt->execute([
        ':id' => $id,
        ':password' => $password
    ]);
}

function deleteUser($id)
{
    $con = get_conexion();
    $stmt = $con->prepare("DELETE FROM users WHERE id = :id");
    $stmt->execute([
        ':id' => "$id"
    ]);
}

function changeUserStatus($id, $active)
{
    $con = get_conexion();
    $stmt = $con->prepare("UPDATE users SET active = :active WHERE id = :id");
    $stmt->execute([
        ':id' => $id,
        ':active' => $active
    ]);
}

function insertUser($user_name, $name, $email, $password, $role, $phone, $identification, $address, $active)
{
    $con = get_conexion();
    $stmt = $con->prepare(
        "INSERT INTO users (user_name, name, email, password, role, phone, identification, address, active) 
         VALUES (:user_name, :name, :email, :password, :role, :phone, :identification, :address, :active)"
    );
    $stmt->execute([
        ':user_name' => $user_name,
        ':name' => $name,
        ':email' => $email,
        ':password' => $password,
        ':role' => $role,
        ':phone' => $phone,
        ':identification' => $identification,
        ':address' => $address,
        ':active' => $active
    ]);
    return $con->lastInsertId();
}

function updateUser($id, $data)
{
    $con = get_conexion();
    $stmt = $con->prepare(
        "UPDATE users
             SET user_name = :user_name, 
                 name = :name,
                 email = :email,
                 role = :role,
                 phone = :phone,
                 identification = :identification,
                 address = :address,
                 active = :active
             WHERE id = :id"
    );
    $stmt->execute([
        ':id' => $id,
        ':user_name' => $data['user_name'],
        ':name' => $data['name'],
        ':email' => $data['email'],
        ':role' => $data['role'],
        ':phone' => $data['phone'],
        ':identification' => $data['identification'],
        ':address' => $data['address'],
        ':active' => $data['active']
    ]);
}
?>