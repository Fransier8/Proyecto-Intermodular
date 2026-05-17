<div class="col-12 col-sm-6 col-md-4 col-lg-3 d-flex">
    <div class="bg-orange-primary rounded card p-3 w-100 h-100">
        <h4 class="card-title"><?= htmlspecialchars($animal['name']) ?></h4>
        <img class="card-img-top mb-3" alt="Animal" loading="lazy" src="<?=
            !empty($animal['photo'])
            ? BASE_URL . "uploads/animals/" . $animal['photo']
            : BASE_URL . "img/placeholder.webp";
        ?>">
        <p>Especie: <?= htmlspecialchars($animal['species']) ?></p>
        <p>Raza: <?= !empty($animal['breed']) ? htmlspecialchars($animal['breed']) : 'Sin especificar' ?></p>
        <div class="mt-auto d-flex flex-column gap-2">
            <button type="button" class="btn border-dark border-1 select-animal-btn" data-id="<?= $animal['id'] ?>"
                data-name="<?= htmlspecialchars($animal['name']) ?>">
                Seleccionar
            </button>
        </div>
    </div>
</div>