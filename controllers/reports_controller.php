<?php
require_once 'models/animals_model.php';
require_once 'models/users_model.php';
require_once 'models/rooms_model.php';
require_once 'models/species_model.php';
use Dompdf\Dompdf;

function viewReports()
{
    $users_with_most_animals = getUsersWithMostAnimals();
    $users_with_most_adoption_applications = getUsersWithMostAdoptionApplications();
    $users_with_most_sponsorships = getUsersWithMostSponsorships();
    $users_by_spending = getUsersBySpending();
    $users_with_most_reservations = getUsersWithMostReservations();
    $monitors_with_most_reservations = getMonitorsWithMostReservations();
    $animals_with_most_reservations = getAnimalsWithMostReservations();
    $animals_with_most_adoption_applications = getAnimalsWithMostAdoptionApplications();
    $animals_with_most_sponsorships = getAnimalsWithMostSponsorships();
    $animals_by_earnings = getAnimalsByEarnings();
    $rooms_with_most_reservations = getRoomsWithMostReservations();
    $species_with_most_animals = getSpeciesWithMostAnimals();
    require 'views/reports.php';
}

function downloadAnimalsPdf()
{
    require_once 'vendor/autoload.php';

    $animals = getAnimals("", "name_asc", "", "", "", 1, null, 0);

    ob_start();
    ?>
    <h1>Lista de animales</h1>

    <style>
        body {
            font-family: Arial;
        }

        .animal {
            margin-bottom: 20px;
        }

        img {
            width: 120px;
            height: auto;
        }
    </style>

    <?php foreach ($animals as $animal): ?>
        <div class="animal">
            <h3><?= htmlspecialchars($animal['name']) ?></h3>
            <p><?= htmlspecialchars($animal['breed']) ?></p>

            <?php if (!empty($animal['photo'])):
                $path = __DIR__ . '/../uploads/animals/' . $animal['photo'];

                if (file_exists($path)) {
                    $type = pathinfo($path, PATHINFO_EXTENSION);
                    $data = file_get_contents($path);
                    $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                    ?>
                    <img src="<?= $base64 ?>">
                    <?php
                }
            endif; ?>
        </div>
    <?php endforeach; ?>

    <?php
    $html = ob_get_clean();

    $dompdf = new Dompdf();
    $dompdf->loadHtml($html);

    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    if (ob_get_length())
        ob_end_clean();

    $dompdf->stream("animales.pdf", ["Attachment" => true]);
    exit;
}
?>