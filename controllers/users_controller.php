<?php
require_once 'models/users_model.php';

function logIn()
{
    unset($_SESSION['inactive_user']);
    $errors = [];
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');

        $user = getUserByEmail($email);

        if (empty($email)) {
            $errors[] = "El email es obligatorio.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "El email no es válido.";
        } elseif (empty($password)) {
            $errors[] = "La contraseña es obligatoria.";
        } elseif (!$user || !password_verify($password, $user['password'])) {
            $errors[] = "Email o contraseña incorrectos";
        } elseif (!$user['active']) {

            $_SESSION['inactive_user'] = $user['id'];

            $errors[] = "Tu cuenta está desactivada.";
        }

        if (!empty($errors)) {
            $user = [
                'email' => $email,
            ];

            require 'views/log_in.php';
            return;
        }

        session_regenerate_id(true);
        $_SESSION['user'] = [
            'id' => $user['id'],
            'email' => $user['email'],
            'role' => $user['role'],
            'user_name' => $user['user_name']
        ];

        header("Location: " . BASE_URL . "animales");
        exit;

    } else {
        $user = [
            'email' => '',
        ];
        require 'views/log_in.php';
    }
}

function signUp()
{
    $errors = [];
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $user_name = trim($_POST['user_name'] ?? '');
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $identification = strtoupper(trim($_POST['identification'] ?? ''));
        $phone = trim($_POST['phone'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $verify_password = trim($_POST['verify_password'] ?? '');
        $role = 'usuario';
        $active = 1;

        if (empty($user_name)) {
            $errors[] = "El nombre de usuario es obligatorio.";
        }

        $existing_user_name = getUserByUserName($user_name);
        if ($existing_user_name) {
            $errors[] = "El nombre de usuario ya existe";
        }

        if (empty($name)) {
            $errors[] = "El nombre es obligatorio.";
        }

        if (empty($email)) {
            $errors[] = "El email es obligatorio.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "El email no es válido.";
        }

        $existing_email = getUserByEmail($email);
        if ($existing_email) {
            $errors[] = "El email ya existe";
        }

        if (empty($identification)) {
            $errors[] = "La identificación es obligatoria.";
        } else {
            $dni_pattern = '/^\d{8}[A-Z]$/';
            $nie_pattern = '/^[XYZ]\d{7}[A-Z]$/';

            if (
                !preg_match($dni_pattern, $identification) &&
                !preg_match($nie_pattern, $identification)
            ) {
                $errors[] = "DNI o NIE inválido.";
            } else {
                if (preg_match($dni_pattern, $identification)) {
                    $letras = "TRWAGMYFPDXBNJZSQVHLCKE";
                    $numero = substr($identification, 0, 8);
                    $letra = substr($identification, 8, 1);

                    if ($letras[$numero % 23] !== $letra) {
                        $errors[] = "La letra del DNI no es correcta.";
                    }
                }
            }
        }

        $existing_identification = getUserByIdentification($identification);
        if ($existing_identification) {
            $errors[] = "La identificación ya existe";
        }

        if (!empty($phone) && !preg_match('/^[0-9]{9}$/', $phone)) {
            $errors[] = "El teléfono debe tener 9 dígitos.";
        }

        if (strlen($password) < 4) {
            $errors[] = "La contraseña debe tener al menos 4 caracteres.";
        }

        if ($password != $verify_password) {
            $errors[] = "Las contraseñas no coinciden.";
        }

        if (!empty($errors)) {
            $user = [
                'user_name' => $user_name,
                'name' => $name,
                'email' => $email,
                'identification' => $identification,
                'phone' => $phone,
                'address' => $address,
                'role' => $role,
                'active' => $active
            ];

            require 'views/sign_up.php';
            return;
        }

        $password = password_hash($password, PASSWORD_BCRYPT);

        $id = insertUser($user_name, $name, $email, $password, $role, $phone, $identification, $address, $active);

        $new_user = getUserById($id);

        if ($new_user) {
            $_SESSION['user'] = [
                'id' => $new_user['id'],
                'email' => $new_user['email'],
                'role' => $new_user['role'],
                'user_name' => $new_user['user_name']
            ];
        }

        header("Location: " . BASE_URL . "animales");
        exit();

    } else {
        $user = [
            'user_name' => '',
            'name' => '',
            'email' => '',
            'identification' => '',
            'phone' => '',
            'address' => '',
            'role' => 'usuario',
            'active' => 1
        ];
        require 'views/sign_up.php';
    }
}

function logOut()
{
    session_unset();
    session_destroy();
    header("Location: " . BASE_URL . "inicio");
    exit();
}

function listUsers()
{
    $search = $_GET['search'] ?? '';
    $order = $_GET['order'] ?? '';
    $role = $_GET['role'] ?? '';
    $active = $_GET['active'] ?? '';
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $per_page = 8;

    $total_users = countUsers($search, $role, $active);
    $total_pages = $total_users > 0 ? ceil($total_users / $per_page) : 1;

    $offset = ($page - 1) * $per_page;
    $users = getUsers($search, $order, $role, $active, $per_page, $offset);

    if (isset($_GET['ajax'])) {
        require 'views/lists/users_list.php';
        exit;
    }

    require 'views/users.php';
}

function viewUserDetails()
{
    $id = $_GET['id'] ?? null;
    if (!$id) {
        header("Location: " . BASE_URL . "usuarios");
        exit();
    }
    $user = getUserById($id);
    if (!$user) {
        header("Location: " . BASE_URL . "usuarios");
        exit();
    }
    require 'views/user.php';
}

function createUser()
{
    $errors = [];
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $user_name = trim($_POST['user_name'] ?? '');
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $identification = strtoupper(trim($_POST['identification'] ?? ''));
        $phone = trim($_POST['phone'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $verify_password = trim($_POST['verify_password'] ?? '');
        $role = $_POST['role'] ?? '';
        $active = isset($_POST['active']) ? 1 : 0;

        if (empty($user_name)) {
            $errors[] = "El nombre de usuario es obligatorio.";
        }

        $existing_user_name = getUserByUserName($user_name);
        if ($existing_user_name) {
            $errors[] = "El nombre de usuario ya existe";
        }

        if (empty($name)) {
            $errors[] = "El nombre es obligatorio.";
        }

        if (empty($email)) {
            $errors[] = "El email es obligatorio.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "El email no es válido.";
        }

        $existing_email = getUserByEmail($email);
        if ($existing_email) {
            $errors[] = "El email ya existe";
        }

        if (empty($identification)) {
            $errors[] = "La identificación es obligatoria.";
        } else {
            $dni_pattern = '/^\d{8}[A-Z]$/';
            $nie_pattern = '/^[XYZ]\d{7}[A-Z]$/';

            if (
                !preg_match($dni_pattern, $identification) &&
                !preg_match($nie_pattern, $identification)
            ) {
                $errors[] = "DNI o NIE inválido.";
            } else {
                if (preg_match($dni_pattern, $identification)) {
                    $letras = "TRWAGMYFPDXBNJZSQVHLCKE";
                    $numero = substr($identification, 0, 8);
                    $letra = substr($identification, 8, 1);

                    if ($letras[$numero % 23] !== $letra) {
                        $errors[] = "La letra del DNI no es correcta.";
                    }
                }
            }
        }

        $existing_identification = getUserByIdentification($identification);
        if ($existing_identification) {
            $errors[] = "La identificación ya existe";
        }

        if (!empty($phone) && !preg_match('/^[0-9]{9}$/', $phone)) {
            $errors[] = "El teléfono debe tener 9 dígitos.";
        }

        if ($role != "administrador" && $role != "monitor" && $role != "usuario") {
            $errors[] = "Selecciona un rol.";
        }

        if (strlen($password) < 4) {
            $errors[] = "La contraseña debe tener al menos 4 caracteres.";
        }

        if ($password != $verify_password) {
            $errors[] = "Las contraseñas no coinciden.";
        }

        if (!empty($errors)) {
            $user = [
                'user_name' => $user_name,
                'name' => $name,
                'email' => $email,
                'identification' => $identification,
                'phone' => $phone,
                'address' => $address,
                'role' => $role,
                'active' => $active
            ];

            require 'views/create_user.php';
            return;
        }

        $password = password_hash($password, PASSWORD_BCRYPT);

        $id = insertUser($user_name, $name, $email, $password, $role, $phone, $identification, $address, $active);

        header("Location: " . BASE_URL . "usuario/$id");
        exit();

    } else {
        $user = [
            'user_name' => '',
            'name' => '',
            'email' => '',
            'identification' => '',
            'phone' => '',
            'address' => '',
            'role' => 'usuario',
            'active' => 1
        ];
        require 'views/create_user.php';
    }
}

function editUser()
{
    $errors = [];
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $id = trim($_POST['id'] ?? '');
        $data = [
            'user_name' => trim($_POST['user_name'] ?? ''),
            'name' => trim($_POST['name'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'identification' => strtoupper(trim($_POST['identification'] ?? '')),
            'phone' => trim($_POST['phone'] ?? ''),
            'address' => trim($_POST['address'] ?? ''),
            'password' => trim($_POST['password'] ?? ''),
            'verify_password' => trim($_POST['verify_password'] ?? ''),
            'role' => $_POST['role'] ?? '',
            'active' => isset($_POST['active']) ? 1 : 0
        ];

        if (empty($data['user_name'])) {
            $errors[] = "El nombre de usuario es obligatorio.";
        }

        $existing_user_name = getUserByUserName($data['user_name']);
        if ($existing_user_name && $existing_user_name['id'] != $id) {
            $errors[] = "El nombre de usuario ya existe";
        }

        if (empty($data['name'])) {
            $errors[] = "El nombre es obligatorio.";
        }

        if (empty($data['email'])) {
            $errors[] = "El email es obligatorio.";
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "El email no es válido.";
        }

        $existing_email = getUserByEmail($data['email']);
        if ($existing_email && $existing_email['id'] != $id) {
            $errors[] = "El email ya existe";
        }

        if (empty($data['identification'])) {
            $errors[] = "La identificación es obligatoria.";
        } else {
            $dni_pattern = '/^\d{8}[A-Z]$/';
            $nie_pattern = '/^[XYZ]\d{7}[A-Z]$/';

            if (
                !preg_match($dni_pattern, $data['identification']) &&
                !preg_match($nie_pattern, $data['identification'])
            ) {
                $errors[] = "DNI o NIE inválido.";
            } else {
                if (preg_match($dni_pattern, $data['identification'])) {
                    $letras = "TRWAGMYFPDXBNJZSQVHLCKE";
                    $numero = substr($data['identification'], 0, 8);
                    $letra = substr($data['identification'], 8, 1);

                    if ($letras[$numero % 23] !== $letra) {
                        $errors[] = "La letra del DNI no es correcta.";
                    }
                }
            }
        }

        $existing_identification = getUserByIdentification($data['identification']);
        if ($existing_identification && $existing_identification['id'] != $id) {
            $errors[] = "La identificación ya existe";
        }

        if (!empty($data['phone']) && !preg_match('/^[0-9]{9}$/', $data['phone'])) {
            $errors[] = "El teléfono debe tener 9 dígitos.";
        }

        if ($data['role'] != "administrador" && $data['role'] != "monitor" && $data['role'] != "usuario") {
            $errors[] = "Selecciona un rol.";
        }

        if (!empty($data['password'])) {
            if (strlen($data['password']) < 4) {
                $errors[] = "La contraseña debe tener al menos 4 caracteres.";
            }

            if ($data['password'] != $data['verify_password']) {
                $errors[] = "Las contraseñas no coinciden.";
            }
        }

        if (!empty($errors)) {
            $user = array_merge(getUserById($id), $data);

            unset($user['password']);
            unset($user['verify_password']);

            require 'views/edit_user.php';
            return;
        }

        updateUser($id, $data);
        if (!empty($data['password'])) {
            updatePassword($id, password_hash($data['password'], PASSWORD_BCRYPT));
        }

        header("Location: " . BASE_URL . "usuario/$id");
        exit();

    } else {
        $id = $_GET['id'] ?? null;
        if (!$id || $id == $_SESSION['user']['id']) {
            header("Location: " . BASE_URL . "usuarios");
            exit();
        }
        $user = getUserById($id);
        if (!$user) {
            header("Location: " . BASE_URL . "usuarios");
            exit();
        }
        require 'views/edit_user.php';
    }
}

function viewProfileDetails()
{
    $id = $_SESSION['user']['id'];
    $user = getUserById($id);
    if (!$user) {
        header("Location: " . BASE_URL . "animales");
        exit();
    }
    require 'views/profile.php';
}

function editProfile()
{
    $errors = [];
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $id = $_SESSION['user']['id'];
        $data = [
            'user_name' => trim($_POST['user_name'] ?? ''),
            'name' => trim($_POST['name'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'identification' => strtoupper(trim($_POST['identification'] ?? '')),
            'phone' => trim($_POST['phone'] ?? ''),
            'address' => trim($_POST['address'] ?? ''),
            'password' => trim($_POST['password'] ?? ''),
            'verify_password' => trim($_POST['verify_password'] ?? ''),
        ];

        if (empty($data['user_name'])) {
            $errors[] = "El nombre de usuario es obligatorio.";
        }

        $existing_user_name = getUserByUserName($data['user_name']);
        if ($existing_user_name && $existing_user_name['id'] != $id) {
            $errors[] = "El nombre de usuario ya existe";
        }

        if (empty($data['name'])) {
            $errors[] = "El nombre es obligatorio.";
        }

        if (empty($data['email'])) {
            $errors[] = "El email es obligatorio.";
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "El email no es válido.";
        }

        $existing_email = getUserByEmail($data['email']);
        if ($existing_email && $existing_email['id'] != $id) {
            $errors[] = "El email ya existe";
        }

        if (empty($data['identification'])) {
            $errors[] = "La identificación es obligatoria.";
        } else {
            $dni_pattern = '/^\d{8}[A-Z]$/';
            $nie_pattern = '/^[XYZ]\d{7}[A-Z]$/';

            if (
                !preg_match($dni_pattern, $data['identification']) &&
                !preg_match($nie_pattern, $data['identification'])
            ) {
                $errors[] = "DNI o NIE inválido.";
            } else {
                if (preg_match($dni_pattern, $data['identification'])) {
                    $letras = "TRWAGMYFPDXBNJZSQVHLCKE";
                    $numero = substr($data['identification'], 0, 8);
                    $letra = substr($data['identification'], 8, 1);

                    if ($letras[$numero % 23] !== $letra) {
                        $errors[] = "La letra del DNI no es correcta.";
                    }
                }
            }
        }

        $existing_identification = getUserByIdentification($data['identification']);
        if ($existing_identification && $existing_identification['id'] != $id) {
            $errors[] = "La identificación ya existe";
        }

        if (!empty($data['phone']) && !preg_match('/^[0-9]{9}$/', $data['phone'])) {
            $errors[] = "El teléfono debe tener 9 dígitos.";
        }

        if (!empty($data['password'])) {
            if (strlen($data['password']) < 4) {
                $errors[] = "La contraseña debe tener al menos 4 caracteres.";
            }

            if ($data['password'] != $data['verify_password']) {
                $errors[] = "Las contraseñas no coinciden.";
            }
        }

        if (!empty($errors)) {
            $user = array_merge(getUserById($id), $data);

            unset($user['password']);
            unset($user['verify_password']);

            require 'views/edit_profile.php';
            return;
        }

        $current_user = getUserById($id);

        $data['role'] = $current_user['role'];
        $data['active'] = $current_user['active'];

        updateUser($id, $data);
        if (!empty($data['password'])) {
            updatePassword($id, password_hash($data['password'], PASSWORD_BCRYPT));
        }

        header("Location: " . BASE_URL . "perfil");
        exit();

    } else {
        $id = $_SESSION['user']['id'];
        $user = getUserById($id);
        if (!$user) {
            header("Location: " . BASE_URL . "animales");
            exit();
        }
        require 'views/edit_profile.php';
    }
}

function resetPassword()
{
    $errors = [];
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $user_name = trim($_POST['user_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $identification = trim($_POST['identification'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $verify_password = trim($_POST['verify_password'] ?? '');

        if (empty($user_name)) {
            $errors[] = "El nombre de usuario es obligatorio.";
        }

        if (empty($email)) {
            $errors[] = "El email es obligatorio.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "El email no es válido.";
        }

        if (empty($identification)) {
            $errors[] = "La identificación es obligatoria.";
        } else {
            $dni_pattern = '/^\d{8}[A-Z]$/';
            $nie_pattern = '/^[XYZ]\d{7}[A-Z]$/';

            if (
                !preg_match($dni_pattern, $identification) &&
                !preg_match($nie_pattern, $identification)
            ) {
                $errors[] = "DNI o NIE inválido.";
            } else {
                if (preg_match($dni_pattern, $identification)) {
                    $letras = "TRWAGMYFPDXBNJZSQVHLCKE";
                    $numero = substr($identification, 0, 8);
                    $letra = substr($identification, 8, 1);

                    if ($letras[$numero % 23] !== $letra) {
                        $errors[] = "La letra del DNI no es correcta.";
                    }
                }
            }
        }

        if (strlen($password) < 4) {
            $errors[] = "La contraseña debe tener al menos 4 caracteres.";
        }

        if ($password != $verify_password) {
            $errors[] = "Las contraseñas no coinciden.";
        }

        $user = getUserByUserNameAndEmailAndIdentification($user_name, $email, $identification);

        if (!$user) {
            $errors[] = "Datos incorrectos.";
        }

        if (!empty($errors)) {
            $user = [
                'user_name' => $user_name,
                'email' => $email,
                'identification' => $identification
            ];

            require 'views/reset_password.php';
            return;
        }

        $password = password_hash($password, PASSWORD_BCRYPT);
        updatePassword($user['id'], $password);
        $_SESSION['password_changed'] = "Contraseña cambiada correctamente";
        header("Location: " . BASE_URL . "iniciar_sesion");
        exit;

    } else {
        $user = [
            'user_name' => '',
            'email' => '',
            'identification' => ''
        ];
        require 'views/reset_password.php';
    }
}

function changeUserActiveStatus()
{
    if (empty($_SESSION['user']) || $_SESSION['user']['role'] != 'administrador') {
        header("Location: " . BASE_URL . "inicio");
        exit();
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = $_POST['id'] ?? null;
        $active = isset($_POST['active']) ? intval($_POST['active']) : 0;
        if ($id == $_SESSION['user']['id']) {
            echo "error";
            exit();
        }
        if ($id) {
            changeUserStatus($id, $active);
        }
        echo "ok";
        exit();
    }
}

function deactivateAccount()
{
    if (empty($_SESSION['user']) || $_SESSION['user']['role'] == 'administrador') {
        header("Location: " . BASE_URL . "perfil");
        exit();
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        changeUserStatus($_SESSION['user']['id'], 0);
        echo "ok";
        session_unset();
        session_destroy();
        header("Location: " . BASE_URL . "inicio");
        exit();
    }
}

function reactivateAccount()
{
    if (!$_SESSION['inactive_user']) {
        header("Location: " . BASE_URL . "inicio");
        exit();
    }

    $user = getUserById($_SESSION['inactive_user']);
    if ($user) {
        changeUserStatus($user['id'], 1);
        unset($_SESSION['inactive_user']);
        session_regenerate_id(true);
        $_SESSION['user'] = [
            'id' => $user['id'],
            'email' => $user['email'],
            'role' => $user['role'],
            'user_name' => $user['user_name']
        ];

        header("Location: " . BASE_URL . "animales");
        exit;

    } else {
        header("Location: " . BASE_URL . "inicio");
        exit();
    }
}
?>