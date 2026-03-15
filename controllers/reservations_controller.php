<?php
require_once 'models/reservations_model.php';

function listReservations()
{
    require 'views/reservations.php';
}

function viewReservationDetails()
{
    require 'views/reservation.php';
}

function listMyReservations()
{
    require 'views/my_reservations.php';
}
?>