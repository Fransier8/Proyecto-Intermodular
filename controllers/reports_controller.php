<?php
require_once 'models/animals_model.php';
use Dompdf\Dompdf;

function viewReports()
{
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