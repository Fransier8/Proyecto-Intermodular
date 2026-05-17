<div class="col-12 col-sm-6 col-md-4 col-lg-3 d-flex">
    <div class="bg-orange-primary rounded card p-3 w-100 h-100">
        <h4 class="card-title"><?= htmlspecialchars($room['code']) ?></h4>
        <img class="card-img-top mb-3 room-img" alt="Sala" loading="lazy" src="<?=
            !empty($room['photo'])
            ? BASE_URL . "uploads/rooms/" . $room['photo']
            : BASE_URL . "img/placeholder.webp";
        ?>">
        <p>Nombre: <?= htmlspecialchars($room['name']) ?></p>
        <p>Ubicación: <?= !empty($room['location'])
            ? htmlspecialchars($room['location'])
            : 'Sin especificar' ?></p>
        <p>Capacidad: <?= htmlspecialchars($room['capacity']) ?></p>
        <div class="mt-auto d-flex flex-column gap-2">
            <button type="button" class="btn border-dark border-1 select-room-btn" data-id="<?= $room['id'] ?>"
                data-name="<?= htmlspecialchars($room['code']) ?>">
                Seleccionar
            </button>
        </div>
    </div>
</div>