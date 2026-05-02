<div class="col-12 col-sm-6 col-md-4 col-lg-3 d-flex">
    <div class="bg-orange-primary rounded card p-3 w-100 h-100">
        <h4 class="card-title"><?= htmlspecialchars($room['code']) ?></h4>
        <img class="card-img-top mb-3 room-img" alt="Sala" loading="lazy" src="<?=
            !empty($room['photo'])
            ? BASE_URL . "uploads/rooms/" . $room['photo']
            : BASE_URL . "img/placeholder.webp";
        ?>">
        <p>Nombre: <?= htmlspecialchars($room['name']) ?></p>
        <p>Ubicación: <?= htmlspecialchars($room['location']) ?></p>
        <p>Capacidad: <?= htmlspecialchars($room['capacity']) ?></p>
        <div class="mt-auto d-flex flex-column gap-2">
            <a href="<?= BASE_URL ?>sala/<?= $room['id'] ?>" class="btn bg-orange-primary border-dark border-1">Más
                información</a>
            <?php if ($_SESSION['user']['role'] == "administrador"): ?>
                <button class="btn change-status-btn <?= $room['active'] ? 'btn-warning' : 'btn-success' ?>"
                    data-id="<?= $room['id'] ?>" data-active="<?= $room['active'] ? 0 : 1 ?>">
                    <i class="bi <?= $room['active'] ? 'bi-person-x' : 'bi-person-check' ?>"></i>
                    <?= $room['active'] ? 'Desactivar' : 'Activar' ?>
                </button>
            <?php endif ?>
        </div>
    </div>
</div>