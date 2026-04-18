<div class="col-12 col-sm-6 col-md-4 col-lg-3">
    <div class="bg-orange-primary rounded card p-3">
        <h4 class="card-title"><?= htmlspecialchars($animal['name']) ?></h4>
        <img class="card-img-top mb-3" alt="Animal" loading="lazy"
            src="<?=
            !empty($animal['photo'])
            ? BASE_URL . "uploads/animals/" . $animal['photo']
            : BASE_URL . "img/placeholder.webp";
        ?>">
        <p>Especie: <?= htmlspecialchars($animal['species']) ?></p>
        <p>Raza: <?= htmlspecialchars($animal['breed']) ?></p>
        <p>Estado: <?= htmlspecialchars($animal['status']) ?></p>
        <a href="<?= BASE_URL ?>animal/<?= $animal['id'] ?>" class="btn bg-orange-primary border-dark border-1">Más
            información</a>
        <button class="btn btn-sm change-status-btn <?= $animal['active'] ? 'btn-warning' : 'btn-success' ?>"
            data-id="<?= $animal['id'] ?>" data-active="<?= $animal['active'] ? 0 : 1 ?>">
            <i class="bi <?= $animal['active'] ? 'bi-person-x' : 'bi-person-check' ?>"></i>
            <?= $animal['active'] ? 'Desactivar' : 'Activar' ?>
        </button>
    </div>
</div>