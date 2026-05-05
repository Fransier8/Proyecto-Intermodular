<?php
require_once 'models/sponsorships_model.php';
require_once 'models/animals_model.php';
require_once 'services/stripe_service.php';

function listSponsorships()
{
    $search = $_GET['search'] ?? '';
    $order = $_GET['order'] ?? '';
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $per_page = 8;

    if ($_SESSION['user']['role'] == 'administrador') {
        $total_sponsorships = countSponsorships($search);
    } else {
        $total_sponsorships = countSponsorshipsByUserId($search, $_SESSION['user']['id']);
    }
    
    $total_pages = $total_sponsorships > 0 ? ceil($total_sponsorships / $per_page) : 1;

    $offset = ($page - 1) * $per_page;
    if ($_SESSION['user']['role'] == 'administrador') {
        $sponsorships = getSponsorships($search, $order, $per_page, $offset);
    } else {
        $sponsorships = getSponsorshipsByUserId($search, $order, $_SESSION['user']['id'], $per_page, $offset);
    }

    if (isset($_GET['ajax'])) {
        require 'views/lists/sponsorships_list.php';
        exit;
    }

    require 'views/sponsorships.php';
}

function createSponsorship()
{
    $errors = [];
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $animal_id = trim($_POST['id'] ?? '');
        $amount = trim($_POST['amount'] ?? '');
        $message = trim($_POST['message'] ?? '');
        $user_id = $_SESSION['user']['id'];

        if (empty($animal_id) || empty($user_id)) {
            $errors[] = "Error con los ids.";
        }

        $animal = getAnimalById($animal_id);

        if (!$animal || !$animal['active'] || $animal['status'] != "sin adoptar") {
            $errors[] = "No puedes apadrinar este animal.";
        }

        if (empty($amount) || (float) $amount <= 0) {
            $errors[] = "El importe debe ser mayor a 0.";
        }

        if (empty($message)) {
            $errors[] = "El mensaje es obligatorio.";
        }

        if (!empty($errors)) {
            $sponsorship = [
                'amount' => $amount,
                'message' => $message
            ];
            require 'views/sponsor_animal.php';
            return;
        }

        createStripeSession($user_id, $animal, $amount, $message);

    } else {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header("Location: " . BASE_URL . "animales");
            exit();
        }
        $animal = getAnimalById($id);
        if (!$animal || !$animal['active'] || $animal['status'] != "sin adoptar") {
            header("Location: " . BASE_URL . "animales");
            exit();
        }
        $sponsorship = [
            'amount' => '',
            'message' => ''
        ];
        require 'views/sponsor_animal.php';
    }
}

function paymentSuccess()
{
    require_once 'vendor/autoload.php';

    $config = require __DIR__ . '/../config/stripe.php';
    \Stripe\Stripe::setApiKey($config['secret_key']);

    $session_id = $_GET['id'] ?? null;

    if (!$session_id) {
        header("Location: " . BASE_URL . "animales");
        exit();
    }

    try {
        $session = \Stripe\Checkout\Session::retrieve($session_id);

        if ($session->payment_status != 'paid') {
            header("Location: " . BASE_URL . "animales");
            exit();
        }

        $data = $_SESSION['sponsorship_data'] ?? null;

        if (!$data) {
            header("Location: " . BASE_URL . "animales");
            exit();
        }

        insertSponsorship(
            $data['user_id'],
            $data['animal_id'],
            $data['message'],
            $data['amount']
        );

        unset($_SESSION['sponsorship_data']);

        $_SESSION['success'] = "Apadrinamiento realizado correctamente";

        header("Location: " . BASE_URL . "apadrinamientos");
        exit();

    } catch (\Exception $e) {
        unset($_SESSION['sponsorship_data']);
        header("Location: " . BASE_URL . "animales");
        exit();
    }
}

function paymentCancel()
{
    $id = $_GET['id'] ?? null;

    unset($_SESSION['sponsorship_data']);

    header("Location: " . BASE_URL . "animal/" . $id);
    exit();
}

?>