<div class="col-12 col-sm-6 col-md-4 col-lg-3">
    <div class="bg-orange-primary rounded card p-3 h-100">
        <h4 class="card-title"><?= htmlspecialchars($reservation['date']) ?></h4>
        <p>Usuario: <?= htmlspecialchars($reservation['user_name']) ?></p>
        <p>Animal: <?= htmlspecialchars($reservation['animal_name']) ?></p>
        <p>Sala: <?= htmlspecialchars($reservation['room_code']) ?></p>
        <p>Monitor: <?= htmlspecialchars($reservation['monitor_user_name']) ?></p>
        <a href="<?= BASE_URL ?>reserva/<?= $reservation['id'] ?>"
            class="btn bg-orange-primary border-dark border-1">Más
            información</a>
        <button class="btn btn-sm change-status-btn btn-danger" data-id="<?= $reservation['id'] ?>">
            <i class="bi bi-trash3"></i>
            Eliminar
        </button>
    </div>
</div>