<?php
require_once 'vendor/autoload.php';

use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;

function createStripeSession($user_id, $animal, $amount, $message)
{
    $config = require __DIR__ . '/../config/stripe.php';
    Stripe::setApiKey($config['secret_key']);

    try {

        $DOMINIO = 'http://localhost/Proyecto/Proyecto-Intermodular/';

        $_SESSION['sponsorship_data'] = [
            'user_id' => $user_id,
            'animal_id' => $animal['id'],
            'amount' => $amount,
            'message' => $message
        ];

        $checkout_session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => 'eur',
                        'product_data' => [
                            'name' => 'Apadrinamiento de ' . $animal['name'],
                        ],
                        'unit_amount' => intval($amount * 100),
                    ],
                    'quantity' => 1,
                ]
            ],
            'mode' => 'payment',
            'success_url' => $DOMINIO . 'pago_exitoso/{CHECKOUT_SESSION_ID}',
            'cancel_url' => $DOMINIO . 'pago_cancelado/' . $animal['id']
        ]);

        header("Location: " . $checkout_session->url);
        exit();

    } catch (ApiErrorException $e) {
        header('Location: ' . BASE_URL . 'animal/' . $animal['id']);
        exit();
    }
}
?>