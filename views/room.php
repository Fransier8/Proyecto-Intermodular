<main class="flex-fill container-fluid bg-orange-300 d-flex flex-column">
    <section class="row flex-fill">
        <?php
        require 'views/aside.php';
        ?>
        <section class="col p-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>salas">Salas</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Sala</li>
                </ol>
            </nav>
            <h1>Sala</h1>
            <article class="row g-4">
                <div class="col-12 col-md-12 fs-5">
                    <h2 class="mb-3"><?= htmlspecialchars($room['code']) ?></h2>
                    <div class="row row-cols-1 row-cols-md-2 g-3">
                        <div class="col">
                            <p class="mb-1"><span class="fw-bold">Nombre:</span>
                                <span><?= htmlspecialchars($room['name']) ?></span>
                            </p>
                        </div>
                        <div class="col">
                            <p class="mb-1"><span class="fw-bold">Capacidad:</span>
                                <span>
                                    <?= htmlspecialchars($room['capacity']) ?>
                                </span>
                            </p>
                        </div>

                        <div class="col">
                            <p class="mb-1"><span class="fw-bold">Ubicación:</span>
                                <span><?= htmlspecialchars($room['location']) ?></span>
                            </p>
                        </div>

                        <div class="col">
                            <p class="mb-1"><span class="fw-bold">Activa:</span>
                                <span><?= $room['active'] ? 'Sí' : 'No' ?></span>
                            </p>
                        </div>

                        <div class="col-md-12">
                            <p class="mb-1 text-break"><span class="fw-bold">Descripción:</span>
                                <span>
                                    <?= htmlspecialchars($room['description']) ?>
                                </span>
                            </p>
                        </div>

                        <div class="col">
                            <p class="fw-bold">Horarios:</p>
                            <ul>
                                <?php foreach ($schedules as $s): ?>
                                    <li>
                                        <?= htmlspecialchars($s['day_of_week']) ?>:
                                        <?= htmlspecialchars(date('H:i', strtotime($s['start_time']))) ?> -
                                        <?= htmlspecialchars(date('H:i', strtotime($s['end_time']))) ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>

                        <div class="col">
                            <p class="fw-bold">Fotos:</p>

                            <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 g-3">
                                <?php foreach ($photos as $photo): ?>
                                    <div class="col">
                                        <img src="<?= BASE_URL ?>uploads/rooms/<?= htmlspecialchars($photo['photo']) ?>"
                                            class="room-photo img-fluid rounded" alt="Foto sala">
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex flex-column flex-sm-row gap-2 mt-4">
                        <a href="<?= BASE_URL ?>modificar_sala/<?= $room['id'] ?>"
                            class="btn bg-orange-primary border-dark border-1 flex-fill">Modificar</a>
                    </div>
                </div>
            </article>
        </section>
    </section>
</main>