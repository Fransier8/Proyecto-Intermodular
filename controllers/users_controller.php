<?php
require_once 'models/users_model.php';

function logIn()
{
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        $user = getUserByEmail($email);
        if ($user && $user['active'] && password_verify($password, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['user'] = [
                'dni' => $user['dni'],
                'email' => $user['email'],
                'role' => $user['role'],
                'user_name' => $user['user_name']
            ];
            //unset($_SESSION['errores'], $_SESSION['datos_antiguos']);
            header("Location: " . BASE_URL . "animals");
            exit;
        } else {
            $_SESSION['errors'] = ["Email o contraseña incorrectos o user inactivo"];
            //$_SESSION['datos_antiguos'] = ['email' => $email];
            header("Location: " . BASE_URL . "login");
            exit();
        }
    }
    require 'views/login.php';
}

function logOut()
{
    if (!empty($_SESSION['shopping_basket'])) {
        setcookie('shopping_basket', json_encode($_SESSION['shopping_basket']), time() + 604800, "/");
    }
    session_unset();
    session_destroy();
    header("Location: " . BASE_URL . "home");
    exit();
}

function listUsers()
{
    $search = $_GET['search'] ?? '';
    $order = $_GET['order'] ?? '';
    $role = $_GET['role'] ?? '';
    $active = $_GET['active'] ?? '';
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $perPage = 8;

    $totalUsers = countUsers($search, $role, $active);
    $totalPages = ceil($totalUsers / $perPage);

    $offset = ($page - 1) * $perPage;
    $users = getUsers($search, $order, $role, $active, $perPage, $offset);

    if (isset($_GET['ajax'])) {
        require 'views/lists/users_list.php';
        exit;
    }

    require 'views/users.php';
}

function viewUserDetails()
{
    require 'views/user.php';
}

function viewProfileDetails()
{
    require 'views/profile.php';
}

function resetPassword()
{
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $email = $_POST['email'] ?? '';
        $identification = $_POST['identification'] ?? '';
        $password = $_POST['password'] ?? '';

        $user = getUserByEmailAndIdentification($email, $identification);
        if ($user && $user['active']) {
            $password = password_hash($password, PASSWORD_BCRYPT);
            updatePassword($user['id'], $password);
            header("Location: " . BASE_URL . "login");
            exit;
        } else {
            $_SESSION['errors'] = ["Email o contraseña incorrectos o user inactivo"];
            header("Location: " . BASE_URL . "reset_password");
            exit();
        }
    }
    require 'views/reset_password.php';
}

function cambiarEstadoActivoUsuario()
{
    if (empty($_SESSION['user']) || $_SESSION['user']['role'] != 'administrador') {
        header("Location: " . BASE_URL . "home");
        exit();
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = $_POST['id'] ?? null;
        $active = isset($_POST['active']) ? intval($_POST['active']) : 0;
        if ($id) {
            cambiarEstadoUsuario($id, $active);
        }
        header("Location: " . BASE_URL . "users");
        exit();
    }
}
?>