<main class="flex-fill container-fluid bg-orange-300 d-flex flex-column overflow-hidden">
    <section class="row flex-fill">
        <?php
        require 'views/aside.php';
        ?>
        <section class="col p-3 overflow-auto">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>salas">Salas</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Sala</li>
                </ol>
            </nav>
            <h1>Sala</h1>
            <article class="row g-4">
                <div class="col-12 col-md-12 fs-5">
                    <h2 class="mb-3 text-break"><?= htmlspecialchars($room['code']) ?></h2>
                    <div class="row row-cols-1 row-cols-md-2 g-3">
                        <div class="col">
                            <p class="mb-1"><span class="fw-bold">Nombre:</span>
                                <span class="text-break"><?= htmlspecialchars($room['name']) ?></span>
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
                                <span class="text-break"><?= !empty($room['location'])
                                    ? htmlspecialchars($room['location'])
                                    : 'Sin especificar' ?></span>
                            </p>
                        </div>

                        <?php if ($_SESSION['user']['role'] == "administrador"): ?>
                            <div class="col">
                                <p class="mb-1"><span class="fw-bold">Activa:</span>
                                    <span><?= $room['active'] ? 'Sí' : 'No' ?></span>
                                </p>
                            </div>
                        <?php endif; ?>

                        <div class="col-md-12">
                            <p class="mb-1 text-break"><span class="fw-bold">Descripción:</span>
                                <span class="text-break">
                                    <?= !empty($room['description']) ? htmlspecialchars($room['description']) : 'Sin descripción' ?>
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
                        <?php if (!empty($photos)): ?>
                            <div class="col-12">

                                <p class="fw-bold mb-2">Fotos:</p>

                                <div id="roomCarousel<?= $room['id'] ?>" class="carousel slide" data-bs-ride="carousel">

                                    <div class="carousel-indicators">
                                        <?php foreach ($photos as $i => $photo): ?>
                                            <button type="button" data-bs-target="#roomCarousel<?= $room['id'] ?>"
                                                data-bs-slide-to="<?= $i ?>" class="<?= $i === 0 ? 'active' : '' ?>"
                                                aria-current="<?= $i === 0 ? 'true' : 'false' ?>">
                                            </button>
                                        <?php endforeach; ?>
                                    </div>

                                    <div class="carousel-inner rounded text-center">

                                        <?php foreach ($photos as $i => $photo): ?>
                                            <div class="carousel-item <?= $i === 0 ? 'active' : '' ?>">

                                                <img src="<?= BASE_URL ?>uploads/rooms/<?= htmlspecialchars($photo['photo']) ?>"
                                                    class="img-fluid mx-auto d-block"
                                                    style="max-height: 400px; object-fit: contain;" alt="Foto sala">

                                            </div>
                                        <?php endforeach; ?>

                                    </div>

                                    <button class="carousel-control-prev" type="button"
                                        data-bs-target="#roomCarousel<?= $room['id'] ?>" data-bs-slide="prev">
                                        <span class="carousel-control-prev-icon"></span>
                                        <span class="visually-hidden">Anterior</span>
                                    </button>

                                    <button class="carousel-control-next" type="button"
                                        data-bs-target="#roomCarousel<?= $room['id'] ?>" data-bs-slide="next">
                                        <span class="carousel-control-next-icon"></span>
                                        <span class="visually-hidden">Siguiente</span>
                                    </button>

                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    <?php if ($_SESSION['user']['role'] == "administrador"): ?>
                        <div class="d-flex flex-column flex-sm-row gap-2 mt-4">
                            <a href="<?= BASE_URL ?>modificar_sala/<?= $room['id'] ?>"
                                class="btn bg-orange-primary border-dark border-1 flex-fill">Modificar</a>
                        </div>
                    <?php elseif ($_SESSION['user']['role'] == "usuario"): ?>
                        <div class="d-flex flex-column flex-sm-row gap-2 mt-4">
                            <a href="<?= BASE_URL ?>solicitar_reserva?room_id=<?= $room['id'] ?>"
                                class="btn bg-orange-primary border-dark border-1 flex-fill">Reservar</a>
                        </div>
                    <?php endif ?>
                </div>
            </article>
        </section>
    </section>
</main>