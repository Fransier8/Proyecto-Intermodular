<div class="col-12 col-sm-6 col-md-4 col-lg-3">
    <div class="bg-orange-primary rounded card p-3">
        <h4 class="card-title"><?= htmlspecialchars($animal['name']) ?></h4>
        <img class="card-img-top mb-3" alt="Animal" loading="lazy"
            src="https://www.purina.es/sites/default/files/styles/ttt_image_510/public/2024-02/sitesdefaultfilesstylessquare_medium_440x440public2022-07Dalmatian1.jpg?itok=B_1aRoJh">
        <p>Especie: <?= htmlspecialchars($animal['species']) ?></p>
        <p>Raza: <?= htmlspecialchars($animal['breed']) ?></p>
        <p>Estado: <?= htmlspecialchars($animal['status']) ?></p>
        <a href="<?= BASE_URL ?>animal" class="btn bg-orange-primary border-dark border-1">Más
            información</a>
    </div>
</div>