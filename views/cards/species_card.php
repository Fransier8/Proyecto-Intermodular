<div class="col-12 col-sm-6 col-md-4 col-lg-3">
    <div class="bg-orange-primary rounded card p-3">
        <h4 class="card-title"><?= htmlspecialchars($individual_species['name']) ?></h4>
        <a href="<?= BASE_URL ?>especie/<?= $individual_species['id'] ?>" class="btn bg-orange-primary border-dark border-1">Más
            información</a>
        <button class="btn btn-sm delete-btn btn-danger" data-id="<?= $individual_species['id'] ?>">
            <i class="bi bi-trash3"></i>
            Eliminar
        </button>
    </div>
</div>