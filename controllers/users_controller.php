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
?>