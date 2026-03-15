<?php
require_once 'models/animals_model.php';

function listAnimals()
{
    require 'views/animals.php';
}

function viewAnimalDetails()
{
    require 'views/animal.php';
}

function listMyAnimals()
{
    require 'views/my_animals.php';
}
?>