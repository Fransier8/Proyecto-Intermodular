<?php
require_once 'models/animals_model.php';
require_once 'models/users_model.php';
require_once 'models/rooms_model.php';
require_once 'models/species_model.php';
require_once 'models/reservations_model.php';
require_once 'models/room_photos_model.php';
require_once 'models/room_schedules_model.php';
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

    $animals = getAnimals("", "name_asc", "", "", "", 1, null, 0, true);
    $deactivated_animals = getAnimals("", "name_asc", "", "", "", "0", null, 0, true);

    $html = "";

    $html .= "<style>" . file_get_contents(__DIR__ . "/../styles/pdf_styles.css") . "</style>";
    $html .= renderAnimalsSection("Lista de animales activos", $animals);
    $html .= "<div style='page-break-before: always;'></div>";
    $html .= renderAnimalsSection("Lista de animales inactivos", $deactivated_animals);

    $dompdf = new Dompdf();

    $dompdf->loadHtml($html);

    $dompdf->setPaper('A4', 'portrait');

    $dompdf->render();

    while (ob_get_level()) {
        ob_end_clean();
    }

    $dompdf->stream("animales.pdf", ["Attachment" => true]);

    exit;
}

function renderAnimalsSection($title, $animals)
{
    ob_start();
    ?>

    <h1><?= htmlspecialchars($title) ?></h1>

    <?php foreach ($animals as $animal): ?>

        <div class="card">

            <div class="header">
                <h2 class="name">
                    <?= htmlspecialchars($animal['name']) ?>
                </h2>
            </div>

            <div class="content">

                <?php
                if (!empty($animal['photo'])) {

                    $path = __DIR__ . '/../uploads/animals/' . $animal['photo'];

                    if (file_exists($path)) {

                        $type = pathinfo($path, PATHINFO_EXTENSION);
                        $data = file_get_contents($path);

                        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                        ?>

                        <img class="image" src="<?= $base64 ?>">

                        <?php
                    }
                }
                ?>

                <table class="info-table">

                    <tr>
                        <td class="label">Especie</td>
                        <td><?= htmlspecialchars($animal['species']) ?></td>
                    </tr>

                    <tr>
                        <td class="label">Raza</td>
                        <td>
                            <?= !empty($animal['breed'])
                                ? htmlspecialchars($animal['breed'])
                                : 'Sin especificar' ?>
                        </td>
                    </tr>

                    <tr>
                        <td class="label">Estado</td>
                        <td><?= ucfirst(htmlspecialchars($animal['status'])) ?></td>
                    </tr>

                    <tr>
                        <td class="label">Género</td>
                        <td><?= ucfirst(htmlspecialchars($animal['gender'])) ?></td>
                    </tr>

                    <tr>
                        <td class="label">Fecha de nacimiento</td>
                        <td>
                            <?= !empty($animal['birth_day'])
                                ? date('d/m/Y', strtotime($animal['birth_day']))
                                : 'Sin especificar' ?>
                        </td>
                    </tr>

                    <tr>
                        <td class="label">Dueño</td>
                        <td>
                            <?= !empty($animal['user'])
                                ? htmlspecialchars($animal['user'])
                                : 'Sin asignar' ?>
                        </td>
                    </tr>

                    <tr>
                        <td class="label">Activo</td>
                        <td><?= $animal['active'] ? 'Sí' : 'No' ?></td>
                    </tr>

                </table>

                <div class="description">
                    <strong>Descripción:</strong>

                    <p>
                        <?= !empty($animal['description'])
                            ? nl2br(htmlspecialchars($animal['description']))
                            : 'Sin descripción' ?>
                    </p>
                </div>

            </div>

        </div>

    <?php endforeach; ?>
    <?php
    return ob_get_clean();
}

function downloadUsersPdf()
{
    require_once 'vendor/autoload.php';

    $users = getUsers("", "user_name_asc", "", 1, null, 0);
    $deactivated_users = getUsers("", "user_name_asc", "", "0", null, 0);

    $html = "";

    $html .= "<style>" . file_get_contents(__DIR__ . "/../styles/pdf_styles.css") . "</style>";
    $html .= renderUsersSection("Lista de usuarios activos", $users);
    $html .= "<div style='page-break-before: always;'></div>";
    $html .= renderUsersSection("Lista de usuarios inactivos", $deactivated_users);

    $dompdf = new Dompdf();

    $dompdf->loadHtml($html);

    $dompdf->setPaper('A4', 'portrait');

    $dompdf->render();

    while (ob_get_level()) {
        ob_end_clean();
    }

    $dompdf->stream("usuarios.pdf", ["Attachment" => true]);

    exit;
}

function renderUsersSection($title, $users)
{
    ob_start();
    ?>

    <h1><?= htmlspecialchars($title) ?></h1>

    <?php foreach ($users as $user): ?>

        <div class="card">

            <div class="header">
                <h2 class="name">
                    <?= htmlspecialchars($user['user_name']) ?>
                </h2>
            </div>

            <div class="content">

                <table class="info-table">

                    <tr>
                        <td class="label">Nombre</td>
                        <td><?= htmlspecialchars($user['name']) ?></td>
                    </tr>

                    <tr>
                        <td class="label">Email</td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                    </tr>

                    <tr>
                        <td class="label">Identificación</td>
                        <td><?= htmlspecialchars($user['identification']) ?></td>
                    </tr>

                    <tr>
                        <td class="label">Rol</td>
                        <td><?= ucfirst(htmlspecialchars($user['role'])) ?></td>
                    </tr>

                    <tr>
                        <td class="label">Teléfono</td>
                        <td>
                            <?= !empty($user['phone'])
                                ? htmlspecialchars($user['phone'])
                                : 'Sin especificar' ?>
                        </td>
                    </tr>

                    <tr>
                        <td class="label">Dirección</td>
                        <td>
                            <?= !empty($user['address'])
                                ? htmlspecialchars($user['address'])
                                : 'Sin especificar' ?>
                        </td>
                    </tr>

                    <tr>
                        <td class="label">Activo</td>
                        <td><?= $user['active'] ? 'Sí' : 'No' ?></td>
                    </tr>

                </table>

            </div>

        </div>

    <?php endforeach; ?>
    <?php
    return ob_get_clean();
}

function downloadRoomsPdf()
{
    require_once 'vendor/autoload.php';

    $rooms = getRooms("", "code_asc", 1, null, 0);
    $deactivated_rooms = getRooms("", "code_asc", "0", null, 0);

    foreach ($rooms as &$room) {
        $room['schedules'] = getRoomSchedulesByRoomId($room['id']);
        $room['photos'] = getRoomPhotosByRoomId($room['id']);
    }

    unset($room);

    foreach ($deactivated_rooms as &$room) {
        $room['schedules'] = getRoomSchedulesByRoomId($room['id']);
        $room['photos'] = getRoomPhotosByRoomId($room['id']);
    }

    unset($room);

    $html = "";

    $html .= "<style>" . file_get_contents(__DIR__ . "/../styles/pdf_styles.css") . "</style>";
    $html .= renderRoomsSection("Lista de salas activas", $rooms);
    $html .= "<div style='page-break-before: always;'></div>";
    $html .= renderRoomsSection("Lista de salas inactivas", $deactivated_rooms);

    $dompdf = new Dompdf();

    $dompdf->loadHtml($html);

    $dompdf->setPaper('A4', 'portrait');

    $dompdf->render();

    while (ob_get_level()) {
        ob_end_clean();
    }

    $dompdf->stream("salas.pdf", ["Attachment" => true]);

    exit;
}

function renderRoomsSection($title, $rooms)
{
    ob_start();
    ?>

    <h1><?= htmlspecialchars($title) ?></h1>

    <?php foreach ($rooms as $room): ?>

        <div class="card">

            <div class="header">
                <h2 class="name">
                    <?= htmlspecialchars($room['code']) ?>
                </h2>
            </div>

            <div class="content">

                <table class="info-table">

                    <tr>
                        <td class="label">Nombre</td>
                        <td><?= htmlspecialchars($room['name']) ?></td>
                    </tr>

                    <tr>
                        <td class="label">Ubicación</td>
                        <td> <?= !empty($room['location'])
                            ? htmlspecialchars($room['location'])
                            : 'Sin especificar' ?></td>
                    </tr>

                    <tr>
                        <td class="label">Capacidad</td>
                        <td><?= htmlspecialchars($room['capacity']) ?></td>
                    </tr>

                    <tr>
                        <td class="label">Activa</td>
                        <td><?= $room['active'] ? 'Sí' : 'No' ?></td>
                    </tr>

                </table>

                <div class="description">
                    <strong>Descripción:</strong>

                    <p>
                        <?= !empty($room['description'])
                            ? nl2br(htmlspecialchars($room['description']))
                            : 'Sin descripción' ?>
                    </p>
                </div>

                <div class="schedules">
                    <strong>Horarios:</strong>
                    <ul>
                        <?php if (!empty($room['schedules'])): ?>
                            <?php foreach ($room['schedules'] as $s): ?>
                                <li>
                                    <?= htmlspecialchars($s['day_of_week']) ?>:
                                    <?= date('H:i', strtotime($s['start_time'])) ?> -
                                    <?= date('H:i', strtotime($s['end_time'])) ?>
                                </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li>Sin horarios</li>
                        <?php endif; ?>
                    </ul>
                </div>

                <?php if (!empty($room['photos'])): ?>
                    <div class="photos">
                        <?php foreach ($room['photos'] as $photo): ?>

                            <?php
                            $path = __DIR__ . '/../uploads/rooms/' . $photo['photo'];

                            if (file_exists($path)) {
                                $type = pathinfo($path, PATHINFO_EXTENSION);
                                $data = file_get_contents($path);
                                $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                                ?>
                                <img class="image" src="<?= $base64 ?>">
                            <?php } ?>

                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

            </div>

        </div>

    <?php endforeach; ?>
    <?php
    return ob_get_clean();
}

function downloadSpeciesPdf()
{
    require_once 'vendor/autoload.php';

    $species = getSpecies("", "name_asc", null, 0);

    $html = "";

    $html .= "<style>" . file_get_contents(__DIR__ . "/../styles/pdf_styles.css") . "</style>";
    $html .= renderSpeciesSection("Lista de especies", $species);

    $dompdf = new Dompdf();

    $dompdf->loadHtml($html);

    $dompdf->setPaper('A4', 'portrait');

    $dompdf->render();

    while (ob_get_level()) {
        ob_end_clean();
    }

    $dompdf->stream("especies.pdf", ["Attachment" => true]);

    exit;
}

function renderSpeciesSection($title, $species)
{
    ob_start();
    ?>

    <h1><?= htmlspecialchars($title) ?></h1>

    <?php foreach ($species as $individual_species): ?>

        <div class="card">

            <div class="header">
                <h2 class="name">
                    <?= htmlspecialchars($individual_species['name']) ?>
                </h2>
            </div>

        </div>

    <?php endforeach; ?>
    <?php
    return ob_get_clean();
}

function downloadReservationsPdf()
{
    require_once 'vendor/autoload.php';

    $reservations = getReservations("", "date_desc", "", null, 0);

    $html = "";

    $html .= "<style>" . file_get_contents(__DIR__ . "/../styles/pdf_styles.css") . "</style>";
    $html .= renderReservationsSection("Lista de reservas", $reservations);
    $dompdf = new Dompdf();

    $dompdf->loadHtml($html);

    $dompdf->setPaper('A4', 'portrait');

    $dompdf->render();

    while (ob_get_level()) {
        ob_end_clean();
    }

    $dompdf->stream("reservas.pdf", ["Attachment" => true]);

    exit;
}

function renderReservationsSection($title, $reservations)
{
    ob_start();
    ?>

    <h1><?= htmlspecialchars($title) ?></h1>

    <?php foreach ($reservations as $reservation): ?>

        <div class="card">

            <div class="header">
                <h2 class="name">
                    <?= date('d/m/Y', strtotime($reservation['date'])) ?> | <?= date('H:i', strtotime($reservation['start_time'])) ?> - <?= date('H:i', strtotime($reservation['end_time'])) ?>
                </h2>
            </div>

            <div class="content">

                <table class="info-table">

                    <tr>
                        <td class="label">Usuario</td>
                        <td><?= htmlspecialchars($reservation['user_user_name']) ?></td>
                    </tr>

                    <tr>
                        <td class="label">Animal</td>
                        <td><?= htmlspecialchars($reservation['animal_name']) ?></td>
                    </tr>

                    <tr>
                        <td class="label">Sala</td>
                        <td><?= htmlspecialchars($reservation['room_code']) ?></td>
                    </tr>

                    <tr>
                        <td class="label">Monitor</td>
                        <td>
                            <?= !empty($reservation['monitor_user_name'])
                                ? htmlspecialchars($reservation['monitor_user_name'])
                                : 'Sin especificar' ?>
                        </td>
                    </tr>

                    <tr>
                        <td class="label">Acompañantes</td>
                        <td><?= htmlspecialchars($reservation['companions']) ?></td>
                    </tr>

                    <tr>
                        <td class="label">Estado</td>
                        <td><?= ucfirst(htmlspecialchars($reservation['status'])) ?></td>
                    </tr>

                </table>

                <div class="description">
                    <strong>Motivo:</strong>

                    <p>
                        <?= nl2br(htmlspecialchars($reservation['reason'])) ?>
                    </p>
                </div>

            </div>

        </div>

    <?php endforeach; ?>
    <?php
    return ob_get_clean();
}

function downloadUserReservationsPdf()
{
    require_once 'vendor/autoload.php';

    $reservations = getReservationsByUserId("", "date_desc", "", $_SESSION['user']['id'], null, 0);

    $html = "";

    $html .= "<style>" . file_get_contents(__DIR__ . "/../styles/pdf_styles.css") . "</style>";
    $html .= renderReservationsSection("Lista de reservas", $reservations);
    $dompdf = new Dompdf();

    $dompdf->loadHtml($html);

    $dompdf->setPaper('A4', 'portrait');

    $dompdf->render();

    while (ob_get_level()) {
        ob_end_clean();
    }

    $dompdf->stream("reservas.pdf", ["Attachment" => true]);

    exit;
}

function downloadMonitorReservationsPdf()
{
    require_once 'vendor/autoload.php';

    $reservations = getReservationsByMonitorId("", "date_desc", "", $_SESSION['user']['id'], null, 0);

    $html = "";

    $html .= "<style>" . file_get_contents(__DIR__ . "/../styles/pdf_styles.css") . "</style>";
    $html .= renderReservationsSection("Lista de reservas", $reservations);
    $dompdf = new Dompdf();

    $dompdf->loadHtml($html);

    $dompdf->setPaper('A4', 'portrait');

    $dompdf->render();

    while (ob_get_level()) {
        ob_end_clean();
    }

    $dompdf->stream("reservas.pdf", ["Attachment" => true]);

    exit;
}
?>